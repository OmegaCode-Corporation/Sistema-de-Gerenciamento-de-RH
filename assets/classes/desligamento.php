<?php   
require_once "db.php";
require_once "diretorio.php";
class Desligamento{
    protected $db;
    protected $id_desligamento;
    protected $data_desligamento;
    protected $observacao;
    protected $id_contrato;

    public function __construct(){
        $this->db = new Conexao_BD();
        //$this->diretorio = new Diretorio();
    }
    public function __set($atributo,$valor){
        $this->$atributo = $valor;
    }
    public function CadExame(){
        try{

        } catch (Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }

    public function listarDesligamentos($id_contrato = null, $pesquisa = null, $offset=null, $limit=null) {
        if ($id_contrato !== null) {
            return $this->db->fetchData("SELECT * FROM desligamento_colaboradores WHERE id_contrato = :id_contrato AND id_colaborador = :id_colaborador ", [$id_contrato]);
        } else if($pesquisa !== null){
            return $this->db->fetchData("SELECT * FROM desligamento_colaboradores WHERE id_desligamento = '$pesquisa' OR id_contrato = '$pesquisa' OR observacao LIKE '%$pesquisa%' ORDER BY id_desligamento DESC" . ($offset !== null && $limit !== null ? " LIMIT $offset, $limit" : ""));
        } else if ($offset !== null && $limit !== null) {
            return $this->db->fetchData("SELECT * FROM desligamento_colaboradores ORDER BY id_desligamento DESC LIMIT $offset, $limit");
        } else {
            return $this->db->fetchData("SELECT * FROM desligamento_colaboradores ORDER BY id_desligamento");
        }
    }

}

?>