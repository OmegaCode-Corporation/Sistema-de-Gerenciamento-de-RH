<?php
    if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
        echo "<script>window.location.href = '../index.php'</script>";
    }

    require_once "db.php";
    require_once "historico.php";
    
    class Unidade {
        private $db;
        private $historico;
        private $empresa_unidade;
        private $cnpj_unidade;
        private $atv_federal_unidade;
        private $estado_unidade;
        private $municipio_unidade;
        private $bairro_unidade;
        private $endereco_unidade;
        private $cep_unidade;
        private $situacao;

        function __construct(){
            $this->db = new Conexao_BD();
            $this->historico = new Historico();
        }

        function __set($atributo, $valor){
            $this->$atributo = $valor;
        }

        public function novaUnidade(){
            $dadosProcessados = $this->db->processarNovosDados($this->getVars(), ["null" => null], "INSERT");
            if($dadosProcessados['existe']){
                $this->db->padroes_finalizar("FOUND");
                die();
            }
            $dadosProcessados_toDB = [];
            $i = 0;
            foreach ($dadosProcessados["novosDados"] as $key => $value) {
                $dadosProcessados_toDB[$i] = "$value";
                $i++;
            }
            $query = "INSERT INTO unidades (empresa_unidade, cnpj_unidade, atv_federal_unidade, estado_unidade, municipio_unidade, bairro_unidade, endereco_unidade, cep_unidade) VALUES (" . $dadosProcessados["query_parcial"] . ")";
            // $query = "INSERT INTO unidades (empresa_unidade, cnpj_unidade, atv_federal_unidade, estado_unidade, municipio_unidade, bairro_unidade, endereco_unidade, cep_unidade) VALUES (:empresa_unidade, :cnpj_unidade, :atv_federal_unidade, :estado_unidade, :municipio_unidade, :bairro_unidade, :endereco_unidade, :cep_unidade)";

            // $valores = func_get_args();
            if($this->db->fetchData($query, $dadosProcessados_toDB)){
                $id_unidade = $this->db->fetchData("SELECT LAST_INSERT_ID();")[0]["LAST_INSERT_ID()"];
                $dadosUnidade = $this->listarUnidades($id_unidade)[0];
                $dados = ['ID Unidade' => $id_unidade, 'Nome Empresa' => $dadosUnidade['empresa_unidade']];
                $movimentacao = $this->historico->processarDados("CADASTRO", "UNIDADE", $dados);

                $this->historico->__set("movimentacao", $movimentacao);
                $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $this->historico->__set("id_usuario", $_SESSION['id_usuario']);
                if ($this->historico->adicionarHistorico()) {
                    if($this->db->padroes_finalizar("CADASTRO")){
                        return true;
                    }
                }
            }
            $this->db->padroes_finalizar("ERROR");
            return false;
        }

        public function listarUnidades($id_unidade=null){
            if($id_unidade !== null){
                return $this->db->fetchData("SELECT * FROM unidades WHERE id_unidade = :id_unidade", [$id_unidade]);
            } else {
                return $this->db->fetchData("SELECT * FROM unidades");
            }
        }

        public function atualizarUnidade($id_unidade){
            $dadosProcessados = $this->db->processarNovosDados(get_object_vars($this), $this->listarUnidades($id_unidade));
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
                    $query = "UPDATE unidades SET " . $dadosProcessados["query_parcial"] . " WHERE id_unidade = :id_unidade";

                    if($this->db->fetchData($query, array_merge($dadosProcessados_toDB, [$id_unidade]))){
                        $dados = ['ID Unidade' => $id_unidade, 'Nome Empresa' => $this->listarUnidades($id_unidade)[0]['empresa_unidade']];
                        $movimentacao = $this->historico->processarDados("ATUALIZAÇÃO", "UNIDADE", $dados);

                        $this->historico->__set("movimentacao", $movimentacao);
                        $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                        $this->historico->__set("id_usuario", $_SESSION['id_usuario']);
                        if ($this->historico->adicionarHistorico()) {
                            if($this->db->padroes_finalizar("ATUALIZAR")){
                                return true;
                            }
                        }
                    }
                }
            } else {
                $this->db->padroes_finalizar("NOT_FOUND");
            }
            $this->db->padroes_finalizar("ERROR");
            return false;
        }

        private function getVars(){
            $reflection = new \ReflectionClass($this);
            $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
            $privateVars = [];
            foreach ($properties as $property) {
                if ($property->class === __CLASS__) {
                    $property->setAccessible(true);
                    $privateVars[$property->getName()] = $property->getValue($this);
                }
            }
            return $privateVars;
        }
    }
?>