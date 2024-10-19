<?php
require_once "db.php";
require_once "diretorio.php";
require_once "usuario.php";
require_once "historico.php";
class Exame{
    private $db;
    private $id_exame;
    private $id_colaborador;
    private $relacao_clinicas;
    private $telefone_clinicas;
    private $data_ultimo_exame;
    private $data_agendamento;

    public function __construct(){
        $this->db = new Conexao_BD();
    }
    public function __set($atributo,$valor){
        $this->$atributo = $valor;
    }
    public function AddExame(){
        try{
            $query = "INSERT INTO exames(id_colaborador, relacao_clinicas, telefone_clinicas, data_ultimo_exame, data_agendamento) VALUES ('$this->id_colaborador','$this->relacao_clinicas','$this->telefone_clinicas','$this->data_ultimo_exame','$this->data_agendamento')";
            $valores_atributos = get_object_vars($this);
            $array_valores = $this->Fifo($valores_atributos);
            $this->db->fetchData($query,$array_valores,"CADASTRO");
            $query = "SELECT * FROM exames WHERE id_colaborador = '$this->id_colaborador' and relacao_clinicas = '$this->relacao_clinicas'";
            $resultado_id = $this->db->fetchData($query);
            foreach($resultado_id as $linha){
                $dados_exame = $linha;
            }
            $historico = new Historico();
            $dados = ["id_exame"=>$dados_exame["id_exame"]];
            $movimentacao = $historico->processarDados("CADASTRO","EXAME",$dados);
            $historico->__set("movimentacao",$movimentacao);
            $historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
            $historico->__set("id_usuario", $_SESSION['id_usuario']);
            if($historico->adicionarHistorico()){
                if ($this->db->padroes_finalizar("CADASTRO")) {
                    return true;
                }
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function DadosExame($id_exame = null, $id_colaborador = null, $pesquisa = null, $offset=null, $limit=null){
        try{
            if ($id_exame !== null) {
                return $this->db->fetchData("SELECT * FROM exames WHERE id_exame = :id_exame", [$id_exame]);
            } else if($id_colaborador !== null){
                return $this->db->fetchData("SELECT * FROM exames WHERE id_colaborador = '$id_colaborador' ORDER BY id_exame DESC" . ($offset !== null && $limit !== null ? " LIMIT $offset, $limit" : ""));
            } else if($pesquisa !== null){
                return $this->db->fetchData("SELECT * FROM exames WHERE id_exame = '$pesquisa' OR relacao_clinicas LIKE '%$pesquisa%' ORDER BY id_exame DESC" . ($offset !== null && $limit !== null ? " LIMIT $offset, $limit" : ""));
            } else if ($offset !== null && $limit !== null) {
                return $this->db->fetchData("SELECT * FROM exames ORDER BY id_exame DESC LIMIT $offset, $limit");
            } else {
                return $this->db->fetchData("SELECT * FROM exames ORDER BY id_exame DESC");
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function AtualizarExame(){
        try{
            $dadosProcessados = $this->db->processarNovosDados(get_object_vars($this),$this->DadosExame($this->id_exame),["id_exame","id_colaborador"]); 
            $array_valores = $this->Fifo($dadosProcessados['novosDados']);
            $query = "UPDATE exames SET ".$dadosProcessados['query_parcial']." WHERE id_exame = :id_exame";
            if($this->db->fetchData($query,array_merge($array_valores,[$this->id_exame]),"ATUALIZAR")){
                $historico = new Historico();
                $dados = ["id_exame"=>$this->id_exame];
                $movimentacao = $historico->processarDados("ATUALIZAÇÃO","EXAME",$dados);
                $historico->__set("movimentacao",$movimentacao);
                $historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $historico->__set("id_usuario", $_SESSION['id_usuario']);
                if($historico->adicionarHistorico()){
                    if ($this->db->padroes_finalizar("ATUALIZAR")) {
                        return true;
                    }
                }
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    protected function Fifo($array){
        $array_valores = [];
        foreach($array as $key=>$value){
            array_push($array_valores,$value);
        }
        return $array_valores;
    }
}

?>