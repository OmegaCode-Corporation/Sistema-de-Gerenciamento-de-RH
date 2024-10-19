<?php
    date_default_timezone_set('America/Sao_Paulo');
    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
        echo "<script>window.location.href = '../index.php'</script>";
    }

    class Conexao_BD {
        private $conn;
        private $server_addr = "localhost";
        private $username = "";
        private $passwd = "";

        private $db_name = "";

        function __construct(){
            try {
                $conn = new PDO("mysql:host=$this->server_addr;dbname=$this->db_name", $this->username, $this->passwd);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn = $conn;
            } catch(PDOException $error) {
                // echo "<script>responseMsgField.classList.remove('alert-success'); responseMsgField.classList.add('alert-danger')</script>";
                // $this->finalizar("Houve um Erro de Conexão com o Banco de Dados.<br><br>Mensagem de Erro: " . $error->getMessage(), "null, 0");
                $this->padroes_finalizar("ERROR", ['error' => $error->getMessage()]);
            }
        }

        public function __set($atributo, $valor){
            $this->$atributo = $valor;
        }

        public function fetchData($query, $valores=null, $msg_id=null, $returnMsg=null, $returnPage=null){
            try{
                $acao_db = substr($query, 0, strpos($query, ' '));
                switch ($acao_db) {
                    case 'SELECT':
                        $mysql = $this->conn->prepare($query);
                        if($valores !== null){
                            $params = substr($query, strpos($query, 'WHERE ')+6);
                            if(str_contains($params, " AND ")){
                                $params = explode(" AND ", $params);
                                for($i=0; $i < count($params); $i++){
                                    $params[$i] = explode(" = ", $params[$i])[1];
                                }
                            } else {
                                $params = [explode(" = ", $params)[1]];
                            }
                            for($i=0; $i < count($params); $i++){
                                if(count($params) === count($valores)){
                                    $mysql->bindValue($params[$i], $valores[$i]);
                                }
                            }
                        }
                        if($mysql->execute()){
                            return $mysql->fetchAll(PDO::FETCH_ASSOC);
                        } else {
                            return false;
                        }
                    case 'INSERT':
                        $mysql = $this->conn->prepare($query);
                        $params = substr($query, strpos($query, 'VALUES (')+8, strpos($query, ')') - strpos($query, ' VALUES ('));
                        $params = explode(", ", $params);

                        for($i=0; $i < count($params); $i++){
                            if(count($params) === count($valores)){
                                $mysql->bindValue($params[$i], $valores[$i]);
                            }
                        }
                        $mysql->execute();
                        break;
                    case 'UPDATE':
                        $mysql = $this->conn->prepare($query);
                        $params = substr($query, strpos($query, 'SET ')+4, strpos($query, ' WHERE') - strpos($query, 'SET ')-4);
                        $params = explode(", ", $params);

                        $params_where = substr($query, strpos($query, 'WHERE ')+6);
                        if(str_contains($params_where, " AND ")){
                            $params_where = explode(" AND ", $params_where);
                            for($i=0; $i < count($params_where); $i++){
                                $params_where[$i] = explode(" = ", $params_where[$i])[1];
                            }
                        } else {
                            $params_where = [explode(" = ", $params_where)[1]];
                        }

                        $params = array_merge($params, $params_where);

                        for($i=0; $i < count($params); $i++){
                            if(count($params) === count($valores)){
                                $params_f = str_contains($params[$i], " = ") ? explode(" = ", $params[$i])[1] : $params[$i];
                                $mysql->bindValue($params_f, $valores[$i]);
                            }
                        }
                        $mysql->execute();
                        break;
                    case 'DELETE':
                        $mysql = $this->conn->prepare($query);
                        if($valores !== null){
                            if(str_contains($query, 'WHERE ')){
                                $params = substr($query, strpos($query, 'WHERE ')+6);
                                if(str_contains($params, " AND ")){
                                    $params = explode(" AND ", $params);
                                    for($i=0; $i < count($params); $i++){
                                        $params[$i] = explode(" = ", $params[$i])[1];
                                    }
                                } else {
                                    $params = [explode(" = ", $params)[1]];
                                }

                                for($i=0; $i < count($params); $i++){
                                    if(count($params) === count($valores)){
                                        $mysql->bindValue($params[$i], $valores[$i]);
                                    }
                                }
                            } else if(str_contains($query, 'LIMIT ')) {
                                $params = substr($query, strpos($query, 'LIMIT ')+6);
                                $mysql->bindValue(substr($params, 1, strlen($params)), $valores[0], PDO::PARAM_INT);
                            }
                        }
                        $mysql->execute();
                        break;
                    default:
                        $this->conn->exec($query);
                        break;
                }
                $msg_id !== null ? $this->padroes_finalizar($msg_id) : ($returnMsg !== null && $returnPage !== null ? $this->finalizar($returnMsg, $returnPage) : null);
                return true;
            } catch(PDOException $error) {
                $this->padroes_finalizar("ERROR", ["error" => $error->getMessage(), "query" => $query]);
                return false;
            }
        }

        public function processarNovosDados($classe_attr, $retorno_db, $dataType='UPDATE', $restricoesExtra=null){
            if(count($retorno_db) > 0 || $dataType === 'INSERT'){
                $query_parcial = "";

                $novosDadosArray = [];
                $restricoes = ["db", "historico", "colaborador", "usuario"];
                $restricoesExtra !== null ? $restricoes = array_merge($restricoes, $restricoesExtra) : null;
    
                foreach($classe_attr as $nome_atributo => $valor_atributo){
                    if(!in_array($nome_atributo, $restricoes) && ($valor_atributo !== null && $valor_atributo !== @$retorno_db[0][$nome_atributo])){
                        $paramsCount = count(explode(":", $query_parcial));
                        switch($dataType){
                            case 'UPDATE':
                                $query_parcial .= $paramsCount === 1 ? "$nome_atributo = :$nome_atributo" : ", $nome_atributo = :$nome_atributo";
                                break;
                            case 'INSERT':
                                $query_parcial .= $paramsCount === 1 ? ":$nome_atributo" : ", :$nome_atributo";
                                break;
                            default:
                                break;
                        }
                        $novosDadosArray[$nome_atributo] = $valor_atributo;
                    }
                }
                if(count($novosDadosArray) > 0){
                    return ["existe" => $dataType === "INSERT" ? false : true, "dadosIguais" => false, "query_parcial" => $query_parcial, "novosDados" => $novosDadosArray];
                } else {
                    $this->padroes_finalizar("NO_CHANGES");
                    return ["existe" => true, "dadosIguais" => true];
                    die();
                }
            } else {
                $this->padroes_finalizar("NOT_FOUND");
                return ["existe" => false, "dadosIguais" => false];
                die();
            }
        }

        public function finalizar($returnMsg, $returnPage){
            echo "<script>finishAction(`$returnMsg`, $returnPage)</script>";
            die();
        }

        public function padroes_finalizar($msg_id, $extraData=null){
            $msg = "";
            $responseMsg = "";
            $responseType = "alert-success";
            switch ($msg_id){
                case 'ERROR':
                    @$error = $extraData['error'] !== null ? $extraData['error'] : "Nenhum";
                    @$query = $extraData['query'] !== null ? $extraData['query'] : "Nenhuma";
                    $msg = "Houve um Erro com Sua Solicitação!.<br><br>Mensagem de Erro: $error<br><br>Query: $query";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    $responseType = "alert-danger";
                    break;
                case 'NOT_FOUND':
                    $msg = "Não Foi Encontrado Nenhum Dado Com Essas Informações!";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    $responseType = "alert-dark";
                    break;
                case 'FOUND':
                    $msg = "Foram Encontrados Dados Com Essas Informações! Por Favor Verifique e Tente Novamente.";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    $responseType = "alert-dark";
                    break;
                case 'NO_CHANGES':
                    $msg = "Nenhum Dado Foi Alterado!";
                    $responseMsg = "finishAction(`$msg<br><br>A página irá recarregar em `, window.location.pathname)";
                    $responseType = "alert-dark";
                    break;
                case 'CADASTRO':
                    $msg = "Cadastro Realizado com Sucesso!";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    break;
                case 'CADASTRO_USUARIO':
                    @$email = $extraData['email'] !== null ? $extraData['email'] : "Nenhum";
                    @$senha = $extraData['senha'] !== null ? $extraData['senha'] : "Nenhuma";
                    $msg = "Cadastro Realizado com Sucesso!<br><br>E-mail: $email<br><p onclick=\"navigator.clipboard.writeText(\"$senha\");alert(\"Senha Copiada para a Área de Transferência!\");\">Senha: $senha</p>";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    break;
                case 'ATUALIZAR':
                    $route = "null";
                    $time = 0;
                    if(@$extraData['route'] !== null){
                        $route = $extraData['route'];
                        $time = 5;
                    }
                    $msg = "Atualização Realizada com Sucesso!";
                    $responseMsg = "responseMsgField.classList.remove('alert-success');finishAction(`$msg`, $route, $time)";
                    break;
                case 'DELETAR':
                    $msg = "Exclusão Realizada com Sucesso!";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    break;
                case 'LOGIN_SUCCESS':
                    @$extraMsg = $extraData['extraMsg'] !== null ? $extraData['extraMsg'] : "";
                    @$route = $extraData['route'] !== null ? $extraData['route'] : "index.php";
                    $msg = "Login Realizado com Sucesso!";
                    $responseMsg = "finishAction(`$msg<br><br>Você será redirecionado em `, '$route', 5, '$extraMsg')";
                    break;
                case 'LOGIN_RECOVERY':
                    @$extraMsg = $extraData['extraMsg'] !== null ? $extraData['extraMsg'] : "";
                    @$route = $extraData['route'] !== null ? $extraData['route'] : "index.php";
                    $msg = "Login Recuperado com Sucesso!";
                    $responseMsg = "finishAction(`$msg<br><br>Você será redirecionado em `, '$route', 15, '$extraMsg')";
                    break;
                case 'LOGIN_DENIED':
                    $msg = "Seu Acesso Está Bloqueado! Entre em contato com o RH.";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    $responseType = "alert-danger";
                    break;
                case '2FA_VALIDADO':
                    @$route = $extraData['route'] !== null ? $extraData['route'] : "index.php";
                    $msg = "Autenticação Confirmada!";
                    $responseMsg = "finishAction(`$msg<br><br>Você será redirecionado em `, '$route')";
                    break;
                case '2FA_INVALIDO':
                    @$route = $extraData['route'] !== null ? $extraData['route'] : "index.php";
                    $msg = "Houve um Erro com a Autenticação. Tente Novamente.";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    $responseType = "alert-danger";
                    break;
                case 'ACESSO_ATUALIZADO':
                    $recovery_codes_str = $extraData['recovery_codes'];
                    // $recovery_codes = json_decode($extraData['recovery_codes'], true)['recovery_codes'];
                    // $recovery_codes_str = "";
                    // foreach($recovery_codes as $key => $recovery_code){
                    //     $i = $key+1;
                    //     $recovery_codes_str .= "$i: $recovery_code<br>";
                    // }
                    $msg = "Acesso Atualizado com Sucesso!<br>Você deverá realizar o Login novamente!";
                    $recovery_codes_str === "" ? null : $msg .= "<br><br>Códigos de Recuperação (Guarde os em um local seguro, eles serão sua forma de acesso caso perca sua senha ou aplicativo 2FA)<br><b onclick=\"navigator.clipboard.writeText('$recovery_codes_str');alert('Códigos de Recuperação Copiados para a Área de Transferência!');\">$recovery_codes_str</b><br>";
                    $responseMsg = "
                        finishAction(`$msg`, null, 0);
                        const btns = document.querySelector(\".btns\");
                        btns.innerHTML = `<input type=\"button\" value=\"Login\" class=\"btnsChild btn btn-dark\" onclick=\"window.location.href = 'login.php'\">` + btns.innerHTML;";
                    break;
                case 'HISTORICO':
                    if(@$_SESSION['returnType'] === "json"){
                        return;
                    }
                    $msg = "Histórico Enviado com Sucesso!";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    break;
                case 'DESLIGAMENTO':
                    $msg = "O Colaborador foi Desligado com Sucesso!";
                    $responseMsg = "finishAction(`$msg`, null, 0)";
                    break;
                default:
                    break;
            }
            $msgFinal = "
                    <script>
                        $responseMsg;
                        responseMsgField.classList.add('$responseType');
                    </script>";

            $returnMsg = @$_SESSION['returnType'] === "json" ? json_encode(['responseMsg' => $msg, 'responseType' => $responseType], JSON_UNESCAPED_UNICODE): $msgFinal;
            @$_SESSION['returnType'] = null;
            @$_SESSION['openWindow'] = null;
            echo $returnMsg;
            return $returnMsg;
        }

        /**  
             * ALTERAÇÃO DA SITUACAO DA ENTIDADE
             * 
             * @param int $id_entidade ID da entidade que será alterada (Ex: ID do Usuário)
             * @param string $id_entidade_str ID da entidade que será alterada em texto, de acordo com a coluna do BD (Ex: id_usuario)
             * @param string $nome_bd Nome da tabela no BD (Ex: usuarios)
             * @param string $nome_entidade Nome da Entidade (Ex: USUÁRIO)
             * @param array $dadosEntidade Dados da Entidade (Ex: dados do Usuário -- Usar a função de listagem de cada classe para obter esses dados)
             * @param array $dadosHistorico Dados que Serão adicionados ao historico (Ex: ["Nome Usuário" => "Fulano", "Situação" => "Inativo"])
             * @param int $situacao Situação da Entidade (Ex: 0=Inativo, 1=Ativo)
             * 
        */
        public function alterarSituacao($id_entidade, $id_entidade_str, $nome_bd, $nome_entidade, $dadosHistorico, $situacao, $id_diretorio=null, $identidade_diretorio=null, $clean2FA=true, $sendReturnMsg=true){ //"Excluir"
            $id_logged_user = $_SESSION['id_usuario'];
            if($_SESSION['2FA_valido']['status'] === true){
                if($id_diretorio !== null && $identidade_diretorio !== null){
                    require_once "diretorio.php";
                    $diretorio = new Diretorio();
                    $diretorio->__set("id", $id_diretorio);
                    $diretorio->__set("identidade", $identidade_diretorio);
                    switch($situacao){
                        case 0:
                            @$diretorio->DesativarDiretorio();
                            break;
                        case 1:
                            @$diretorio->AtivarDiretorio();
                            break;
                        default:
                            break;
                    }
                }
                $query = "UPDATE $nome_bd SET situacao = :situacao WHERE $id_entidade_str = :$id_entidade_str";
                if($this->fetchData($query, [$situacao, $id_entidade])){
                    require_once "historico.php";
                    $historico = new Historico();
                    $movimentacao = $historico->processarDados("ALTERAÇÃO DE SITUAÇÃO", strtoupper($nome_entidade), $dadosHistorico);

                    $historico->__set("movimentacao", $movimentacao);
                    $historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                    $historico->__set("id_usuario", $id_logged_user);
                    if ($historico->adicionarHistorico()) {
                        if($clean2FA === true){
                            $_SESSION['2FA_valido'] = null;
                        }
                        if($sendReturnMsg === true){
                            $this->padroes_finalizar("ATUALIZAR");
                        }
                        return true;
                    }
                }
            } else {
                $_SESSION['2FA_valido'] = ['status' => false, 'id_entidade' => $id_entidade, 'id_entidade_str' => $id_entidade_str, 'nome_bd' => $nome_bd, 'nome_entidade' => $nome_entidade, 'dadosHistorico' => $dadosHistorico, 'situacao' => $situacao, 'id_diretorio' => $id_diretorio, 'identidade_diretorio' => $identidade_diretorio, 'route' => $_SESSION['route'], 'clean2FA' => $clean2FA, 'sendReturnMsg' => $sendReturnMsg];
                // echo "<script>window.location.href = '2FA.php'</script>";
                header("Location: 2FA.php");
                die();
            }

            $this->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
?>