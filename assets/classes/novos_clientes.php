<?php   
require_once "db.php";
require_once "diretorio.php";
class NovosClientes{
    protected $db;
    protected $id_clientes ;
    protected $id_unidade;
    protected $razao_social;
    protected $cnpj;
    protected $qtd_lojas;
    protected $valor_total_lojas;

    public function __construct(){
        $this->db = new Conexao_BD();
        //$this->diretorio = new Diretorio();
    }
    public function __set($atributo,$valor){
        $this->$atributo = $valor;
    }
    public function CadNovosClientes(){
        try{

        } catch (Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }

    public function listarNovosClientes($id_clientes = null, $pesquisa = null, $offset=null, $limit=null) {
        if ($id_clientes !== null) {
            return $this->db->fetchData("SELECT * FROM novos_clientes WHERE id_clientes = :id_clientes AND id_unidade = :id_unidade ", [$id_clientes]);
        } else if($pesquisa !== null){
            return $this->db->fetchData("SELECT * FROM novos_clientes WHERE id_unidade LIKE '%$pesquisa%'");
        } else if ($offset !== null && $limit !== null) {
            return $this->db->fetchData("SELECT * FROM novos_clientes ORDER BY id_clientes LIMIT $offset, $limit");
        } else {
            return $this->db->fetchData("SELECT * FROM novos_clientes");
        }
    }

}

?>