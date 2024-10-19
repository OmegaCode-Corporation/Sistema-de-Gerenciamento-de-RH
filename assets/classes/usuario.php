<?php
    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
        echo "<script>window.location.href = '../index.php'</script>";
    }

    require_once "colaborador.php";

    @session_start();

    class Usuario extends Colaborador {
        private $id_usuario;
        private $email;
        private $senha;
        private $senha_padrao;
        private $OTP_secret;
        private $recovery_codes;
        private $perfil_usuario;
        private $id_colaborador;
        private $situacao;

        function __construct(){
            $this->db = new Conexao_BD();
            $this->historico === null ? $this->historico = new Historico() : null;
        }

        function __set($atributo, $valor){
            $this->$atributo = $valor;
        }

        public function verifyLogin($email, $senha) {
            $query = "SELECT * FROM usuarios WHERE email = :email AND senha = :senha";

            $valores = func_get_args();

            $dados_db = $this->db->fetchData($query, $valores);
            if(count($dados_db) > 0){
                $situacao = $dados_db[0]['situacao'];
                if($situacao === 0){
                    $this->db->padroes_finalizar("LOGIN_DENIED");
                } else {
                    $senha_padrao = $dados_db[0]['senha_padrao'];
                    $isFirstLogin = $senha === $senha_padrao ? true : false;
                    if($isFirstLogin){
                        $_SESSION['first_access'] = true;
                        $_SESSION['id_usuario'] = $dados_db[0]['id_usuario'];
                        $_SESSION['id_colaborador'] = $dados_db[0]['id_colaborador'];
                        $_SESSION['senhas'] = ["senha_padrao" => $dados_db[0]['senha_padrao'], "senha_atual" => $dados_db[0]['senha']];
                        $_SESSION['2FA'] = $this->gerar2FA();
                        echo "<script>window.location.href = 'primeiro_login.php'</script>";
                    } else {
                        $_SESSION['verified'] = true;
                        $_SESSION['id_usuario'] = $dados_db[0]['id_usuario'];
                        $_SESSION['id_colaborador'] = $dados_db[0]['id_colaborador'];
                        echo "<script>window.location.href = '2FA.php'</script>";
                    }
                }
                return true;
                die();
            } else {
                $this->db->padroes_finalizar("NOT_FOUND");
            }
            return false;
        }

        public function atualizarAcesso($id_usuario, $nova_senha, $OTP_secret=null, $OTP_code=null){
            $dados_db = $this->db->fetchData("SELECT * FROM usuarios WHERE id_usuario = :id_usuario", [$id_usuario])[0];
            $senha_antiga = $dados_db['senha'];
            $senha_padrao = $dados_db['senha_padrao'];

            if($nova_senha === $senha_antiga){
                echo "<script>responseMsgField.classList.add('alert-danger')</script>";
                $this->db->finalizar("Você deve alterar sua senha!", "null, 0");
                die();
            } else if($nova_senha === $senha_padrao){
                echo "<script>responseMsgField.classList.add('alert-danger')</script>";
                $this->db->finalizar("Você não pode alterar para a senha padrão!", "null, 0");
                die();
            } else if(($OTP_secret !== null && $OTP_code !== null) && $this->verificar2FA($OTP_secret, $OTP_code) === false){
                $this->db->padroes_finalizar("2FA_INVALIDO");
                die();
            }

            $this->__set("senha", $nova_senha);
            $this->__set("OTP_secret", $OTP_secret);

            $recovery_codes = $dados_db['recovery_codes'] === null ? "{'recovery_codes': []}" : $dados_db['recovery_codes'];
            $recovery_codes_str = "";
            if($dados_db['recovery_codes'] === null || count(json_decode($recovery_codes, true)['recovery_codes']) === 0){
                for($i = 0; $i < 10; $i++){
                    $recovery_code = $this->generatePass(16);
                    $recovery_codes_json = json_decode($recovery_codes, true);
                    $recovery_codes_json["recovery_codes"][$i] = md5($recovery_code);
                    $recovery_codes = json_encode($recovery_codes_json);
                    $recovery_codes_str .= $i+1 . ": $recovery_code<br>";
                }
                $this->__set("recovery_codes", $recovery_codes);
            }
            if($this->atualizarUsuario($id_usuario, false)){
                // $this->db->padroes_finalizar("ACESSO_ATUALIZADO", ['recovery_codes' => json_decode($recovery_codes, true)]['recovery_codes']);
                $this->db->padroes_finalizar("ACESSO_ATUALIZADO", ['recovery_codes' => $recovery_codes_str]);
                return true;
            }

            return false;
        }

        public function login($id_usuario, $code){
            $dados_db = $this->db->fetchData("SELECT * FROM usuarios WHERE id_usuario = :id_usuario", [$id_usuario])[0];
            if($this->verificar2FA($dados_db['OTP_secret'], $code)){
                @$OTP_info = $_SESSION['2FA_valido'];
                if(@$OTP_info['status'] === false){
                    $OTP_info['status'] = true;
                    $_SESSION['2FA_valido'] = $OTP_info;
                    $this->db->alterarSituacao($OTP_info['id_entidade'], $OTP_info['id_entidade_str'], $OTP_info['nome_bd'], $OTP_info['nome_entidade'], $OTP_info['dadosHistorico'], $OTP_info['situacao'], $OTP_info['id_diretorio'], $OTP_info['identidade_diretorio'], $OTP_info['clean2FA'], $OTP_info['sendReturnMsg']);
                    $this->db->padroes_finalizar("2FA_VALIDADO", ['route' => $OTP_info['route']]);
                } else {
                    @$route = $_SESSION['route'];
                    session_unset();
                    $_SESSION['logged'] = true;
                    $_SESSION['id_usuario'] = $dados_db['id_usuario'];
                    $_SESSION['id_colaborador'] = $dados_db['id_colaborador'];
                    $_SESSION['nome_colaborador'] = $this->listarColaboradores($dados_db['id_colaborador'])[0]['nome_colaborador'];
                    $_SESSION['perfil_usuario'] = $dados_db['perfil_usuario'];
                    $this->db->padroes_finalizar("LOGIN_SUCCESS", ['route' => $route]);
                }
                return true;
            } else {
                $this->db->padroes_finalizar("2FA_INVALIDO");
            }
            return false;
        }

        public function loginRecovery($id_usuario, $recovery_code){
            $dados_db = $this->db->fetchData("SELECT * FROM usuarios WHERE id_usuario = :id_usuario", [$id_usuario])[0];

            $recovery_codes = json_decode($dados_db['recovery_codes'], true)['recovery_codes'];
            for($i = 0; $i < count($recovery_codes); $i++){
                if($recovery_codes[$i] === md5($recovery_code)){
                    array_splice($recovery_codes, $i, 1);
                    $recovery_codes_length = count($recovery_codes);
                    $this->__set("recovery_codes", json_encode(['recovery_codes' => $recovery_codes]));
                    $this->db->fetchData("UPDATE usuarios SET recovery_codes = :recovery_codes WHERE id_usuario = :id_usuario", [$this->recovery_codes, $id_usuario]);

                    session_unset();
                    $newPass = $this->generatePass();
                    $this->__set("senha", md5($newPass));
                    $this->__set("senha_padrao", md5($newPass));
                    if($this->atualizarUsuario($id_usuario, false)){
                        $this->db->padroes_finalizar("LOGIN_RECOVERY", ['extraMsg' => "<br><br><br>Você utilizou um de seus códigos de recuperação de conta! Você possui <b>$recovery_codes_length</b> códigos de recuperação restantes.<br><br>Utilize a senha <b onclick=\"navigator.clipboard.writeText(`$newPass`);alert(`Senha Copiada para a Área de Transferência!`);\">$newPass</b> para acessar o sistema.", 'route' => 'login.php']);
                        return true;
                    }
                }
            }
            $this->db->padroes_finalizar("NOT_FOUND");
            return false;
        }

        public function logout(){
            session_destroy();
            echo "<script>window.location.href = 'index.php'</script>";
            return true;
        }

        private function gerar2FA(){
            require_once "2FA.php";

            $authenticator = new Authenticator();
            $secret = $authenticator->createSecret();
            $qrCodeUrl = $authenticator->getQRCodeGoogleUrl('NOME 2FA', $secret);

            return ['secret' => $secret, 'qrcode' => $qrCodeUrl];
        }

        private function verificar2FA($secret, $code){
            require_once "2FA.php";

            $authenticator = new Authenticator();
            if($authenticator->verifyCode($secret, $code)){
                return true;
            }
            return false;
        }

        public function novoUsuario($email, $perfil_usuario, $id_colaborador){
            $id_logged_user = $_SESSION['id_usuario'];
            $colaborador_existe = $this->listarColaboradores($id_colaborador);
            if(count($colaborador_existe) === 0){
                $this->db->padroes_finalizar("NOT_FOUND");
                die();
            } else if(count($this->db->fetchData("SELECT * FROM usuarios WHERE id_colaborador = :id_colaborador AND situacao = :situacao", [$id_colaborador, 1])) > 0){
                $this->db->padroes_finalizar("NO_CHANGES");
                die();
            }
            $senha_o = $this->generatePass();
            $senha_padrao = $senha = md5($senha_o);
            $situacao = 1;
            
            $query = "INSERT INTO usuarios (email, perfil_usuario, id_colaborador, senha_padrao, senha, situacao) VALUES (:email, :perfil_usuario, :id_colaborador, :senha_padrao, :senha, :situacao)";

            $valores = func_get_args();
            array_push($valores, $senha_padrao, $senha, $situacao);

            if($this->db->fetchData($query, $valores)){
                $id_aUsuario = $this->db->fetchData("SELECT LAST_INSERT_ID();")[0]["LAST_INSERT_ID()"];

                $dados = ['ID Usuário' => $id_aUsuario, 'Nome Colaborador' => $this->listarColaboradores($id_colaborador)[0]['nome_colaborador']];
                $movimentacao = $this->historico->processarDados("CADASTRO", "USUÁRIO", $dados);

                $this->historico->__set("movimentacao", $movimentacao);
                $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $this->historico->__set("id_usuario", $id_logged_user);
                if($this->historico->adicionarHistorico()){
                    if($this->db->padroes_finalizar("CADASTRO_USUARIO", ["email" => "$email", "senha" => "$senha_o"])){
                        return true;
                    }
                }
            }
            return false;
        }

        public function listarUsuarios($id_usuario=null, $id_colaborador=null, $pesquisa = null, $offset=null, $limit=null){
            if($id_usuario !== null){
                return $this->db->fetchData("SELECT * FROM usuarios WHERE id_usuario = :id_usuario", [$id_usuario]);
            } else if($id_colaborador !== null){
                return $this->db->fetchData("SELECT * FROM usuarios WHERE id_colaborador = :id_colaborador", [$id_colaborador]);
            } else if($pesquisa !== null){
                return $this->db->fetchData("SELECT * FROM usuarios WHERE email = '$pesquisa' OR perfil_usuario = '$pesquisa'");
            } else if ($offset !== null && $limit !== null) {
                return $this->db->fetchData("SELECT * FROM usuarios ORDER BY id_usuario DESC LIMIT $offset, $limit");
            } else {
                return $this->db->fetchData("SELECT * FROM usuarios");
            }
        }

        public function hasUser($id_colaborador){
            return $this->db->fetchData("SELECT * FROM usuarios WHERE id_colaborador = :id_colaborador", [$id_colaborador]);
        }

        public function atualizarUsuario($id_usuario, $showMsg = true){
            @$id_logged_user = $_SESSION['id_usuario'] === null ? $id_usuario : $_SESSION['id_usuario'];
            $dadosProcessados = $this->db->processarNovosDados(get_object_vars($this), $this->listarUsuarios($id_usuario));
            if(@$dadosProcessados["existe"]){
                if($dadosProcessados["dadosIguais"]){
                    $this->db->padroes_finalizar("NO_CHANGES");
                    die();
                } else {
                    $dadosProcessados_toDB = [];
                    $i = 0;
                    foreach($dadosProcessados["novosDados"] as $key => $value){
                        $dadosProcessados_toDB[$i] = "$value";
                        $i++;
                    }
                    $query = "UPDATE usuarios SET " . $dadosProcessados["query_parcial"] . " WHERE id_usuario = :id_usuario";
                    if($this->db->fetchData($query, array_merge($dadosProcessados_toDB, [$id_usuario]))){
                        $dados = ['ID Usuário' => $id_usuario, 'Nome Colaborador' => $this->listarColaboradores($this->listarUsuarios($id_usuario)[0]['id_colaborador'])[0]['nome_colaborador']];
                        $movimentacao = $this->historico->processarDados("ATUALIZAÇÃO", "USUÁRIO", $dados);

                        $this->historico->__set("movimentacao", $movimentacao);
                        $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                        $this->historico->__set("id_usuario", $id_logged_user);
                        if($this->historico->adicionarHistorico()){
                            if($showMsg){
                                $this->db->padroes_finalizar("ATUALIZAR");
                            }
                            return true;
                        }
                    }
                }
            } else {
                $this->db->padroes_finalizar("NOT_FOUND");
            }
            return false;
        }

        private function generatePass($length = 24) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.!?/-+{}[]';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
            return $randomString;
        }
    }
?>