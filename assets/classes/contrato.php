<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "<script>window.location.href = '../index.php'</script>";
} 

require_once "db.php";
require_once "diretorio.php";
require_once "historico.php";
require_once "documentos.php";
class Contrato{
    protected $db;
    protected $id_contrato;
    protected $id_colaborador;
    protected $id_empresa;
    protected $id_unidade;
    protected $admissao;
    protected $optante_fgts;
    protected $data_opcao;
    protected $conta_fgts;
    protected $cargo;
    protected $cbo;
    protected $organograma;
    protected $remuneracao;
    protected $forma_pagamento;
    protected $periodo_pagamento;
    protected $escala_trabalho;
    protected $situacao; 
    protected $dados_familiar;

    public function __construct(){
        $this->db = new Conexao_BD();
        //$this->diretorio = new Diretorio();
    }
    public function __set($atributo,$valor){
        $this->$atributo = $valor;
    }
    public function CadContrato(){
        try{
            $this->situacao = "1";
            $query = "INSERT INTO contratos_colaboradores (id_colaborador, id_empresa, id_unidade, admissao, optante_fgts, data_opcao, conta_fgts, cargo, cbo, organograma, remuneracao, forma_pagamento, periodo_pagamento, escala_trabalho, situacao)
            VALUES ($this->id_colaborador,'$this->id_empresa','$this->id_unidade','$this->admissao','$this->optante_fgts','$this->data_opcao','$this->conta_fgts','$this->cargo','$this->cbo','$this->organograma','$this->remuneracao','$this->forma_pagamento','$this->periodo_pagamento','$this->escala_trabalho','$this->situacao')";
            $valores_atributos = get_object_vars($this);
            $array_valores = $this->Fifo($valores_atributos);
            $this->db->fetchData($query,$array_valores,"CADASTRO");

            $query = "SELECT * FROM contratos_colaboradores WHERE id_colaborador = '$this->id_colaborador' and id_empresa = '$this->id_empresa' and id_unidade = '$this->id_unidade'";
            $resultado_id = $this->db->fetchData($query);
            foreach($resultado_id as $linha){
                $dados_contrato = $linha;
            }

            $this->id_contrato = $dados_contrato['id_contrato'];
            foreach($this->dados_familiar as $linha){
                $this->AddFichaFamiliar($linha['nome_familiar'],$linha['parentesco']);
            }

            $diretorio = new Diretorio();
            $diretorio->__set("identidade", "diretorio_contrato");
            $diretorio->__set("id", $dados_contrato['id_contrato']);
            if($diretorio->AddDiretorio()){
                $historico = new Historico();
                $dados = ["id_contrato" => $dados_contrato['id_contrato']];
                $movimentacao = $historico->processarDados("CADASTRO","CONTRATO",$dados);
                $historico->__set("movimentacao",$movimentacao);
                $historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $historico->__set("id_usuario", $_SESSION['id_usuario']);
                if($historico->adicionarHistorico()){
                    return $this->id_contrato;
                }
            }else{
                $this->db->padroes_finalizar("ERROR");
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function SituacaoContrato($acao, $clean2FA=true, $sendReturnMsg=true){ 
        try{
            switch($acao){
                case 0:
                    $this->db->alterarSituacao($this->id_contrato,"id_contrato","contratos_colaboradores","CONTRATO",["CONTRATO"=>$this->id_contrato,"Situação"=>"Inativo"],0,$this->id_contrato,"diretorio_contrato", $clean2FA, $sendReturnMsg);                                                                  
                    break;
                case 1:
                    $this->db->alterarSituacao($this->id_contrato,"id_contrato","contratos_colaboradores","CONTRATO",["CONTRATO"=>$this->id_contrato,"Situação"=>"Ativo"],1,$this->id_contrato,"diretorio_contrato", $clean2FA, $sendReturnMsg);                                                                  
                    break;
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function AtualizarContrato(){
        try{
            $dadosProcessados = $this->db->processarNovosDados(get_object_vars($this),$this->DadosContrato($this->id_contrato),["id_empresa","id_colaborador","id_unidade","id_contrato"]);
            $array_valores = $this->Fifo($dadosProcessados['novosDados']);
            $query = "UPDATE contrato_colaboradores SET ".$dadosProcessados['query_parcial']." WHERE id_contrato = :id_contrato";
            if($this->db->fetchData($query,array_merge($array_valores,[$this->id_contrato]),"ATUALIZAR")){
                $historico = new Historico();
                $dados = ["id_contrato" => $this->id_contrato];
                $movimentacao = $historico->processarDados("ATUALIZAÇÃO","CONTRATO",$dados);
                $historico->__set("movimentacao",$movimentacao);
                $historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $historico->__set("id_usuario", $_SESSION['id_usuario']);
                if($historico->adicionarHistorico()){
                    if ($this->db->padroes_finalizar("CADASTRO")) {
                        return true;
                    }
                }
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function DadosContrato($id_contrato = null, $pesquisa = null, $offset=null, $limit=null){
        try{
            if ($id_contrato !== null) {
                return $this->db->fetchData("SELECT * FROM contratos_colaboradores WHERE id_contrato = :id_contrato", [$id_contrato]);
            } else if($pesquisa !== null){
                return $this->db->fetchData("SELECT * FROM contratos_colaboradores WHERE id_contrato = '$pesquisa' OR situacao = '$pesquisa' ORDER BY id_contrato DESC" . ($offset !== null && $limit !== null ? " LIMIT $offset, $limit" : ""));
            } else if ($offset !== null && $limit !== null) {
                return $this->db->fetchData("SELECT * FROM contratos_colaboradores ORDER BY id_contrato DESC LIMIT $offset, $limit");
            } else {
                return $this->db->fetchData("SELECT * FROM contratos_colaboradores");
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function hasContrato($id_contrato){
        return count($this->db->fetchData("SELECT * FROM contratos_colaboradores WHERE id_contrato = :id_contrato AND situacao = :situacao", [$id_contrato, 1])) > 0;
    }
    public function AddFichaFamiliar($nome_familiar,$parentesco){
        try{
            $query = "INSERT INTO ficha_familar_contrato(id_contrato,nome_familar,parentesco_familar) VALUES('$this->id_contrato','$nome_familiar','$parentesco')";
            $this->db->fetchData($query,[$this->id_contrato,$nome_familiar,$parentesco],"CADASTRO");
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function DadosFamiliar(){
        try{
            $query = "SELECT * FROM ficha_familar_contrato WHERE id_contrato = $this->id_contrato";
            $resultado = $this->db->fetchData($query);
            return $resultado;
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

class Prorrogacao extends Contrato{
    private $data_prorrogacao = null;

    public function __set($atributo,$valor){
        $this->$atributo = $valor;
    }
    public function AddProrrogacao(){
        try{
            $query = "INSERT INTO prorrogacao_contratos(id_contrato,data_prorrogacao) VALUES('$this->id_contrato','$this->data_prorrogacao')";
            $valores_atributos = get_object_vars($this);
            $array_valores = $this->Fifo($valores_atributos);
            $this->db->fetchData($query,$array_valores,"CADASTRO");
            $historico = new Historico();
            $dados = ["id_contrato" => $this->id_contrato];
            $movimentacao = $historico->processarDados("CADASTRO","PRORROGAÇÃO DE CONTRATO",$dados);
            $historico->__set("movimentacao",$movimentacao);
            $historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
            $historico->__set("id_usuario", $_SESSION['id_usuario']);
            if($historico->adicionarHistorico()){
                if ($this->db->padroes_finalizar("ATUALIZAR")) {
                    return true;
                }
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
    public function DadosProrrogacao($id_prorrogacao = null, $id_contrato = null, $offset=null, $limit=null){
        try{
            if($id_prorrogacao !== null){
                return $this->db->fetchData("SELECT * FROM prorrogacao_contratos WHERE id_prorrogacao = :id_prorrogacao", [$id_prorrogacao]);
            } else if($id_contrato !== null){
                return $this->db->fetchData("SELECT * FROM prorrogacao_contratos WHERE id_contrato = '$id_contrato' ORDER BY id_prorrogacao DESC" . ($offset !== null && $limit !== null ? " LIMIT $offset, $limit" : ""));
            } else if ($offset !== null && $limit !== null) {
                return $this->db->fetchData("SELECT * FROM prorrogacao_contratos ORDER BY id_prorrogacao DESC LIMIT $offset, $limit");
            } else {
                return $this->db->fetchData("SELECT * FROM prorrogacao_contratos");
            }
        }catch(Exception $error){
            $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
            return false;
        }
    }
}
// class FichaFamiliar extends Contrato{
//     private $id_familiar = null;
//     private $nome_familiar = null;
//     private $nascimento = null;
//     private $parentesco_familiar = null;
//     // private $dados_familiar = null;
//     public function AddFamiliar(){
//         try{
//             $query = "INSERT INTO ficha_familiar_contrato()";
//             $valores_atributos = get_object_vars($this);
//             $array_valores = $this->Fifo($valores_atributos);
//             $this->db->fetchData($query,$array_valores,"CADASTRO");
//             $historico = new Historico();
//             $dados = ["id_contrato" => $this->id_contrato];
//             $movimentacao = $historico->processarDados("CADASTRO","FICHA_FAMILIAR",$dados);
//             if($historico->adicionarHistorico($movimentacao,date("Y-m-d H:i:s"),$_SESSION['id_usuario'])){
//                 $this->db->padroes_finalizar("CADASTRO");
//                 return true;
//             }
//         }catch(Exception $error){
//             $this->db->padroes_finalizar("ERROR", ["error" => $error->getMessage()]);
//             return false;
//         }
//     }
// }
?>