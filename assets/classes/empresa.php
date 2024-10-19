<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
echo "<script>window.location.href = '../index.php'</script>";
}
require_once "db.php";
require_once "diretorio.php";
require_once "usuario.php";
require_once "historico.php";

@session_start();
class Empresa{
    private $db = null;
    private $id_empresa = null;
    private $cnpj = null;
    private $nome_empresa = null;
    private $estado = null;
    private $cidade = null;
    private $bairro  = null;
    private $rua = null;
    private $numero = null;
    private $cep = null;
    private $complemento = null;
    private $telefone = null;   
    private $email = null;   
    private $situacao = null;
    public function __construct(){
        $this->db = new Conexao_BD();
    }
    public function __set($atributo,$valor){
        $this->$atributo = $valor;
    }
    public function CadEmpresa(){
        try{
            $this->situacao = "1";
            $query = "INSERT INTO empresas (cnpj, nome_empresa, estado, cidade, bairro,rua, numero, CEP, complemento, telefone, email, situacao)
            VALUES ($this->cnpj,'$this->nome_empresa','$this->estado','$this->cidade','$this->bairro','$this->rua','$this->numero','$this->cep','$this->complemento','$this->telefone','$this->email','$this->situacao')";
            $valores_atributos = get_object_vars($this);
            $array_valores = $this->Fifo($valores_atributos);
            $this->db->fetchData($query,$array_valores,"CADASTRO");
            $query = "SELECT * FROM empresas WHERE cnpj ='$this->cnpj'";
            $dadosEmpresa = $this->db->fetchData($query)[0];
            $diretorio = new Diretorio();
            $diretorio->__set("identidade","diretorio_empresa");
            $diretorio->__set("id",$dadosEmpresa['id_empresa']);

            if($diretorio->AddDiretorio()){
                $historico = new Historico();
                $dados = ["id_empresa" => $this->id_empresa];
                $movimentacao = $historico->processarDados("CADASTRO","EMPRESA",$dados);
                $historico->__set("movimentacao", $movimentacao);
                $historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $historico->__set("id_usuario", $_SESSION['id_usuario']);
                if ($historico->adicionarHistorico()) {
                    $this->db->padroes_finalizar("CADASTRO");
                    return true;
                // }
            }
            }else{
                $this->db->padroes_finalizar("ERROR");
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function SituacaoEmpresa($acao){ 
        try{
            switch($acao){
                case 0:
                    $this->db->alterarSituacao($this->id_empresa,"id_empresa","empresas","EMPRESA",["Empresa"=>$this->id_empresa,"Situação"=>"inativa"],0,$this->id_empresa,"diretorio_empresa");
                    break;
                case 1:
                    $this->db->alterarSituacao($this->id_empresa,"id_empresa","empresas","EMPRESA",["Empresa"=>$this->id_empresa,"Situação"=>"ativa"],1,$this->id_empresa,"diretorio_empresa");
                    break;
            }                                                                             
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }

    public function DadosEmpresa($id_empresa = null, $pesquisa = null, $offset=null, $limit=null){
        try{
            if ($id_empresa !== null) {
                return $this->db->fetchData("SELECT * FROM empresas WHERE id_empresa = :id_empresa", [$id_empresa]);
            } else if($pesquisa !== null){
                return $this->db->fetchData("SELECT * FROM empresas WHERE id_empresa = '$pesquisa' OR nome_empresa LIKE '%$pesquisa%' OR cnpj = '$pesquisa' OR email = '$pesquisa' OR situacao = '$pesquisa' ORDER BY id_empresa DESC" . ($offset !== null && $limit !== null ? " LIMIT $offset, $limit" : ""));
            } else if ($offset !== null && $limit !== null) {
                return $this->db->fetchData("SELECT * FROM empresas ORDER BY id_empresa DESC LIMIT $offset, $limit");
            } else {
                return $this->db->fetchData("SELECT * FROM empresas");
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function AtualizarEmpresa(){
        try{
            $dadosProcessados = $this->db->processarNovosDados(get_object_vars($this),$this->DadosEmpresa($this->id_empresa));
            $array_valores = $this->Fifo($dadosProcessados['novosDados']);
            $query = "UPDATE empresas SET ".$dadosProcessados['query_parcial']." WHERE id_empresa = :id_empresa";
            if($this->db->fetchData($query,array_merge($array_valores,[$this->id_empresa]),"ATUALIZAR")){
                $historico = new Historico();
                $dados = ["id_empresa" => $this->id_empresa];
                $movimentacao = $historico->processarDados("ATUALIZAÇÃO","EMPRESA",$dados);
                $historico->__set("movimentacao",$movimentacao);
                $historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $historico->__set("id_usuario", $_SESSION['id_usuario']);
                if ($historico->adicionarHistorico()) {
                    if ($this->db->padroes_finalizar("ATUALIZAR", ["route" => "listar_empresas.php"])) {
                        return true;
                    }
                }
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }

    // public function listarEmpresas($id_empresa = null){
    //     if ($id_empresa !== null) {
    //         return $this->db->fetchData("SELECT * FROM empresas WHERE id_empresa = :id_empresa", [$id_empresa]);
    //     } else {
    //         return $this->db->fetchData("SELECT * FROM empresas");
    //     }
    // }
    private function Fifo($array){
        $array_valores = [];
        foreach($array as $key=>$value){
            array_push($array_valores,$value);
        }
        return $array_valores;
    }
}
?>