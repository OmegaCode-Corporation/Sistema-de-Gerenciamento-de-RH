<?php
    date_default_timezone_set('America/Sao_Paulo');
    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
        echo "<script>window.location.href = '../index.php'</script>";
    }

    require_once "db.php";
    require_once "usuario.php";
    require_once "diretorio.php";

    class Historico{
        private $db;
        private $usuario;
        private $tipo_sancao;
        private $descricao;
        private $cat_file;
        private $cat_dir;
        private $afastamento_inss;
        private $data_inicio;
        private $data_termino;
        private $movimentacao;
        private $data_movimentacao;
        private $id_colaborador;
        private $id_usuario;
        private $tabela = "historico_sistema";

        function __construct(){
            $this->db = new Conexao_BD();
        }

        function __set($atributo, $valor){
            $this->$atributo = $valor;
        }

        public function adicionarHistorico(){
            $dadosProcessados = $this->db->processarNovosDados(get_object_vars($this), ["null" => null], "INSERT", ['tabela']);

            $dadosProcessados_toDB = [];
            $i = 0;
            foreach ($dadosProcessados["novosDados"] as $key => $value) {
                @$dadosProcessados_toDB[$i] = "$value";
                $i++;
            }

            $query = "";

            switch($this->tabela){
                case 'historico_sistema':
                    $query = "INSERT INTO $this->tabela (movimentacao, data_movimentacao, id_usuario) VALUES (" . $dadosProcessados["query_parcial"] . ")";
                    // $query = "INSERT INTO $this->tabela (movimentacao, data_movimentacao, id_usuario) VALUES (\"$this->movimentacao\")";
                    break;
                case 'historico_usuarios':
                    $query = "INSERT INTO $this->tabela (movimentacao, data_movimentacao, id_colaborador, id_usuario) VALUES (" . $dadosProcessados["query_parcial"] . ")";
                    break;
                case 'historico_sancoes':
                    $query = "INSERT INTO $this->tabela (tipo_sancao, descricao, data_inicio, data_termino, data_movimentacao, id_colaborador, id_usuario) VALUES (" . $dadosProcessados["query_parcial"] . ")";
                    break;
                case 'historico_acidentes_trabalho':
                    if($this->cat_file !== null){
                        $diretorio = new Diretorio();
                        $diretorio->__set('identidade', "diretorio_colaborador");
                        $diretorio->__set('id', $this->id_colaborador);
                        $diretorio->__set('arquivo', $this->cat_file);
                        if($diretorio->UploadArquivo()){
                            $dadosProcessados_toDB[1] = $diretorio->caminho.$this->cat_file['name'];
                            $dadosProcessados['query_parcial'] = str_replace(":cat_file", ":cat_dir", $dadosProcessados['query_parcial']);
                            $query = "INSERT INTO $this->tabela (descricao, cat_dir, afastamento_inss, data_inicio, data_termino, data_movimentacao, id_colaborador, id_usuario) VALUES (" . $dadosProcessados['query_parcial'] . ")";
                        }
                    } else {
                        $query = "INSERT INTO $this->tabela (descricao, afastamento_inss, data_inicio, data_termino, data_movimentacao, id_colaborador, id_usuario) VALUES (" . $dadosProcessados['query_parcial'] . ")";
                    }
                    break;
                default:
                    return false;
                    break;
            }

            if($query !== "" && $this->db->fetchData($query, $dadosProcessados_toDB, 'HISTORICO')){
                return true;
            }

            return false;
        }

        public function listarHistoricos($id_historico=null, $pesquisa = null, $offset=null, $limit=null){
            if($id_historico !== null){
                return $this->db->fetchData("SELECT * FROM $this->tabela WHERE id_historico = :id_historico", [$id_historico]);
            } else if($pesquisa !== null){
                return $this->db->fetchData("SELECT * FROM $this->tabela WHERE id_historico = '$pesquisa' ORDER BY id_historico DESC" . ($offset !== null && $limit !== null ? " LIMIT $offset, $limit" : ""));  
            } else if ($offset !== null && $limit !== null) {
                return $this->db->fetchData("SELECT * FROM $this->tabela ORDER BY id_historico DESC LIMIT $offset, $limit");
            } else {
                return $this->db->fetchData("SELECT * FROM $this->tabela");
            }
        }

        public function excluirHistorico($id_historico=null, $exclusaoEmMassa=null){
            $dadosProcessados = $this->db->processarNovosDados(get_object_vars($this), $this->listarHistoricos($id_historico));
            if($dadosProcessados['existe']){
                $query = "DELETE FROM $this->tabela";
                if($id_historico !== null){
                    $query .= " WHERE id_historico = :id_historico";
                    $this->db->fetchData($query, [$id_historico]);
                    return true;
                } else if($exclusaoEmMassa !== null){
                    $query .= " ORDER BY id_historico DESC LIMIT :exclusaoEmMassa";
                    $this->db->fetchData($query, [$exclusaoEmMassa]);
                    return true;
                }
            } else {
                $this->db->padroes_finalizar("NOT_FOUND");
            }
            return false;
        }

        public function processarDados($acao, $entidade, $extraData){
            $this->usuario = new Usuario();

            @$id_usuario = $_SESSION['id_usuario'] === null ? $this->id_usuario : $_SESSION['id_usuario'];
            @$id_colaborador = $_SESSION['id_colaborador'] === null ? $this->id_colaborador : $_SESSION['id_colaborador'];
            $dadosUsuario = $this->usuario->listarUsuarios($id_usuario)[0];
            $dadosColaborador = $this->usuario->listarColaboradores($id_colaborador)[0];
            
            $usuario = ["id_usuario" => $dadosUsuario['id_usuario'], "id_colaborador" => $dadosColaborador['id_colaborador'], "nome_colaborador" => $dadosColaborador['nome_colaborador']];

            // $dadosUsuario_afetado = $this->usuario->listarUsuarios($extraData['id_usuario'])[0];
            // $dadosColaborador_afetado = $this->usuario->listarColaboradores($extraData['id_colaborador'])[0];

            // $dadosAfetados = ["ID Usuário" => $dadosUsuario_afetado['id_usuario'], "Nome Colaborador" => $dadosColaborador_afetado['nome_colaborador']];

            $movimentacao = $this->gerarMensagem($acao, $entidade, $usuario, $extraData);

            return $movimentacao;
        }

        public function gerarMensagem($acao, $entidade, $usuario, $dadosAfetados){
            // $acao = ucwords(strtolower($acao));
            // $entidade = ucwords(strtolower($entidade));

            $id_usuario = $usuario['id_usuario'];
            $id_colaborador = $usuario['id_colaborador'];
            $nome_colaborador = $usuario['nome_colaborador'];
            $dadosAfetados_str = "";
                                
            $i = 0;
            foreach($dadosAfetados as $key => $value){
                $dadosAfetados_str .= "($key: $value)";
                $i === count($dadosAfetados)-1 ? $dadosAfetados_str .= "" : $dadosAfetados_str .= ", ";
                $i++;
            }

            // $movimentacao = "Ação Realizada: $acao de $entidade<br>Usuário que Realizou: $nome_colaborador (ID Usuário: $id_usuario), (ID Colaborador: $id_colaborador)<br>Usuário Afetado: $dadosAfetados_str<br>Descrição Geral: O usuário '$nome_colaborador' realizou um(a) $acao de $entidade.";
            $movimentacao = "Ação Realizada: $acao de $entidade<br>$entidade Afetado: $dadosAfetados_str<br>Descrição Geral: O usuário '$nome_colaborador' realizou um(a) $acao de $entidade.";

            return $movimentacao;
        }
    }
?>