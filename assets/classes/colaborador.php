<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "<script>window.location.href = '../index.php'</script>";
}

require_once "db.php";
require_once "historico.php";

class Colaborador{
    public $db;
    public $historico;
    private $cpf;
    private $rg;
    private $rg_uf;
    private $orgao;
    private $emissao_rg;	
    private $nome_colaborador;	
    private $mae_colaborador;	
    private $pai_colaborador;	
    private $nascimento;	
    private $sexo;	
    private $estado_civil;	
    private $raca_cor;
    private $nacionalidade;
    private $naturalidade;
    private $cidade;	
    private $estado;	
    private $bairro;	
    private $rua;	
    private $numero;	
    private $complemento;	
    private $cep;	
    private $numero_ctps;	
    private $serie_ctps;	
    private $estado_ctps;	
    private $expedicao_ctps;	
    private $numero_pis;	
    private $cadastro_pis;	
    private $instrucao_escolaridade;	
    private $cnh;	
    private $categoria_cnh;	
    private $validade_cnh;	
    private $reservista;	
    private $categoria_reservista;	
    private $titulo_eleitoral;	
    private $zona_eleitoral;	
    private $secao_eleitoral;	
    private $sindicato;	
    private $cons_profis;	
    private $registro_profis;	
    private $data_registro_profis;	
    private $banco;	
    private $conta_banco;	
    private $digito_conta;	
    private $agencia_banco;	
    private $codigo_ficha;	
    private $nr_recibo_ficha;	
    private $matricula_esocial;	
    private $estg_data_chegada;	
    private $estg_tipo_visto;	
    private $estg_data_portaria;	
    private $estg_nr_portaria;	
    private $estg_carteira_rne;	
    private $estg_validade_rne;	
    private $situacao;

    function __construct(){
        $this->db = new Conexao_BD();
        $this->historico = new Historico();
    }

    function __set($atributo, $valor){
        $this->$atributo = $valor;
    }

    public function novoColaborador(){
        // $retorno_db = $this->db->fetchData("SELECT * FROM colaboradores WHERE cpf = :cpf OR numero_pis = :numero_pis OR titulo_eleitoral = :titulo_eleitoral OR codigo_ficha = :codigo_ficha OR nr_recibo_ficha = :nr_recibo_ficha OR matricula_esocial = :matricula_esocial OR estg_nr_portaria = :estg_nr_portaria OR estg_carteira_rne = :estg_carteira_rne", [$this->cpf, $this->numero_pis, $this->titulo_eleitoral, $this->codigo_ficha, $this->nr_recibo_ficha, $this->matricula_esocial, $this->estg_nr_portaria, $this->estg_carteira_rne]);
        $retorno_db = $this->db->fetchData("SELECT * FROM colaboradores WHERE cpf = :cpf", [$this->cpf]);
        if(count($retorno_db) > 0){
            $this->db->padroes_finalizar("FOUND");
            die();
        }

        $dadosProcessados = $this->db->processarNovosDados(get_object_vars($this), [], "INSERT");
        
        $dadosProcessados_toDB = [];
        $i = 0;
        foreach ($dadosProcessados["novosDados"] as $key => $value) {
            $dadosProcessados_toDB[$i] = "$value";
            $i++;
        }

        $isEstg = $this->estg_tipo_visto !== null ? ", estg_data_chegada, estg_tipo_visto, estg_data_portaria, estg_nr_portaria, estg_carteira_rne, estg_validade_rne" : "" ;
        $hasSindicato = $this->sindicato !== null ? ", sindicato, cons_profis, registro_profis, data_registro_profis" : "" ;
        $hasBanco = $this->banco !== null ? ", banco, conta_banco, digito_conta, agencia_banco" : "" ;
        $hasFicha = $this->codigo_ficha !== null ? ", codigo_ficha, nr_recibo_ficha, matricula_esocial" : "" ;

        $query = "INSERT INTO colaboradores(cpf, rg, rg_uf, orgao, emissao_rg, nome_colaborador, mae_colaborador, pai_colaborador, nascimento, sexo, estado_civil, raca_cor, nacionalidade, naturalidade, cidade, estado, bairro, rua, numero, complemento, cep, numero_ctps, serie_ctps, estado_ctps, expedicao_ctps, numero_pis, cadastro_pis, instrucao_escolaridade, cnh, categoria_cnh, validade_cnh, reservista, categoria_reservista, titulo_eleitoral, zona_eleitoral, secao_eleitoral" . $hasSindicato . $hasBanco . $hasFicha . $isEstg . ") VALUES (" . $dadosProcessados["query_parcial"] . ")";

        if ($this->db->fetchData($query, $dadosProcessados_toDB)){
            $id_colaborador = $this->db->fetchData("SELECT LAST_INSERT_ID();")[0]["LAST_INSERT_ID()"];
            @session_start();
            $_SESSION['last_id'] = $id_colaborador;
            require_once "diretorio.php";
            $dir = new Diretorio();
            $dir->__set("identidade", "diretorio_colaborador");
            $dir->__set("id", $id_colaborador);
            if ($dir->AddDiretorio()) {
                $dadosColaborador = $this->listarColaboradores($id_colaborador)[0];
                $dados = ['ID Colaborador' => $id_colaborador, 'Nome Colaborador' => $dadosColaborador['nome_colaborador']];
                $movimentacao = $this->historico->processarDados("CADASTRO", "COLABORADOR", $dados);

                $this->historico->__set("movimentacao", $movimentacao);
                $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $this->historico->__set("id_usuario", $_SESSION['id_usuario']);
                if ($this->historico->adicionarHistorico()) {
                    if ($this->db->padroes_finalizar("CADASTRO")) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function listarColaboradores($id_colaborador = null, $pesquisa = null, $offset=null, $limit=null) {
        if ($id_colaborador !== null) {
            return $this->db->fetchData("SELECT * FROM colaboradores WHERE id_colaborador = :id_colaborador", [$id_colaborador]);
        } else if($pesquisa !== null){
            return $this->db->fetchData("SELECT * FROM colaboradores WHERE id_colaborador = '$pesquisa' OR cpf = '$pesquisa' OR rg = '$pesquisa' OR nome_colaborador LIKE '%$pesquisa%' ORDER BY id_colaborador DESC" . ($offset !== null && $limit !== null ? " LIMIT $offset, $limit" : ""));  
        } else if ($offset !== null && $limit !== null) {
            return $this->db->fetchData("SELECT * FROM colaboradores ORDER BY id_colaborador DESC LIMIT $offset, $limit");
        } else {
            return $this->db->fetchData("SELECT * FROM colaboradores");
        }
    }

    public function atualizarColaborador($id_colaborador){
        $dadosProcessados = $this->db->processarNovosDados(get_object_vars($this), $this->listarColaboradores($id_colaborador));
        if (@$dadosProcessados["existe"]) {
            $dadosProcessados_toDB = [];
            $i = 0;
            foreach ($dadosProcessados["novosDados"] as $key => $value) {
                $dadosProcessados_toDB[$i] = "$value";
                $i++;
            }

            $query = "UPDATE colaboradores SET " . $dadosProcessados["query_parcial"] . " WHERE id_colaborador = :id_colaborador";

            if ($this->db->fetchData($query, array_merge($dadosProcessados_toDB, [$id_colaborador]))) {
                $dados = ['ID Colaborador' => $id_colaborador, 'Nome Colaborador' => $this->listarColaboradores($id_colaborador)[0]['nome_colaborador']];
                $movimentacao = $this->historico->processarDados("ATUALIZAÇÃO", "COLABORADOR", $dados);

                $this->historico->__set("movimentacao", $movimentacao);
                $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $this->historico->__set("id_usuario", $_SESSION['id_usuario']);
                if ($this->historico->adicionarHistorico()) {
                    if ($this->db->padroes_finalizar("ATUALIZAR")) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

class Desligamento extends Colaborador{
    private $id_desligamento;
    private $data_desligamento;
    private $observacao;
    private $id_contrato;
    private $id_colaborador;

    function __set($atributo, $valor){
        $this->$atributo = $valor;
    }

    public function novoDesligamento(){
        require_once "contrato.php";
        require_once "usuario.php";
        $class_contrato = new Contrato();
        $class_usuario = new Usuario();

        $dadosColaborador = $this->listarColaboradores($this->id_colaborador)[0];
        $dadosUsuario = $class_usuario->listarUsuarios(null, $this->id_colaborador)[0];
        
        // $desativarUsuario = $this->db->alterarSituacao($dadosUsuario['id_usuario'], 'id_usuario', 'usuarios', 'USUÁRIO', ["E-mail" => $dadosUsuario['email'], "Situação" => "Inativo"], 0, null, null, false, false);
        // $desativarColaborador = $this->db->alterarSituacao($this->id_colaborador, 'id_colaborador', 'colaboradores', 'COLABORADOR', ["Nome Colaborador" => $dadosColaborador['nome_colaborador'], "Situação" => "Inativo"], 0, $this->id_colaborador, "diretorio_colaborador", true, false);

        $contratos = $this->db->fetchData("SELECT id_contrato FROM contratos_colaboradores WHERE id_colaborador = :id_colaborador", [$this->id_colaborador]);
        if(count($contratos) > 0){
            foreach ($contratos as $contrato) {
                $class_contrato->__set("id_contrato", $contrato['id_contrato']);
                $class_contrato->SituacaoContrato(0, false, false);

                $dadosProcessados = $this->db->processarNovosDados(array_merge($this->getVars(), ['id_contrato' => $contrato['id_contrato']]), [], "INSERT", ['id_colaborador']);
                $dadosProcessados_toDB = [];
                $i = 0;
                foreach ($dadosProcessados["novosDados"] as $key => $value) {
                    $dadosProcessados_toDB[$i] = "$value";
                    $i++;
                }
                // $dadosProcessados_toDB = array_merge($dadosProcessados_toDB, [$contrato['id_contrato']]);

                $query = "INSERT INTO desligamento_colaboradores(data_desligamento, observacao, id_contrato) VALUES (" . $dadosProcessados["query_parcial"] . ")";
                $this->db->fetchData($query, $dadosProcessados_toDB);
            }
        }

        $this->db->alterarSituacao($dadosUsuario['id_usuario'], 'id_usuario', 'usuarios', 'USUÁRIO', ["E-mail" => $dadosUsuario['email'], "Situação" => "Inativo"], 0, null, null, false, false);
        $this->db->alterarSituacao($this->id_colaborador, 'id_colaborador', 'colaboradores', 'COLABORADOR', ["Nome Colaborador" => $dadosColaborador['nome_colaborador'], "Situação" => "Inativo"], 0, $this->id_colaborador, "diretorio_colaborador", true, false);

        $dados = ['ID Colaborador' => $this->id_colaborador, 'Nome Colaborador' => $dadosColaborador['nome_colaborador']];
        $movimentacao = $this->historico->processarDados("DESLIGAMENTO", "COLABORADOR", $dados);

        $this->historico->__set("movimentacao", $movimentacao);
        $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
        $this->historico->__set("id_usuario", $_SESSION['id_usuario']);
        if ($this->historico->adicionarHistorico()) {
            if ($this->db->padroes_finalizar("DESLIGAMENTO")) {
                return true;
            }
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

class EPI extends Colaborador{
    private $id_colaborador;
    private $japona;
    private $calca;
    private $bota;
    private $luva;
    private $meiao;

    function __set($atributo, $valor){
        $this->$atributo = $valor;
    }

    public function adicionarEPIS(){
        $dadosProcessados = $this->db->processarNovosDados($this->getVars(), $this->listarEPIS($this->id_colaborador), "INSERT");
        if(@$dadosProcessados['existe']){
            $this->db->padroes_finalizar("FOUND");
            die();
        }
        $dadosProcessados_toDB = [];
        $i = 0;
        foreach ($dadosProcessados["novosDados"] as $key => $value) {
            $dadosProcessados_toDB[$i] = "$value";
            $i++;
        }

        $query = "INSERT INTO epi (id_colaborador, japona, calca, bota, luva, meiao) VALUES (" . $dadosProcessados["query_parcial"] . ")";

        // $valores = func_get_args();
        if ($this->db->fetchData($query, $dadosProcessados_toDB, 'CADASTRO')) {
            $dadosColaborador = $this->listarColaboradores($this->id_colaborador)[0];
            $dados = ['ID Colaborador' => $this->id_colaborador, 'Nome Colaborador' => $dadosColaborador['nome_colaborador']];
            $movimentacao = $this->historico->processarDados("CADASTRO", "EPI", $dados);

            $this->historico->__set("movimentacao", $movimentacao);
            $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
            $this->historico->__set("id_usuario", $_SESSION['id_usuario']);
            if ($this->historico->adicionarHistorico()) {
                if ($this->db->padroes_finalizar("CADASTRO")) {
                    return true;
                }
            }
        }
        // $this->db->padroes_finalizar("ERROR");
        return false;
    }

    public function listarEPIS($id_colaborador = null){
        if ($id_colaborador !== null) {
            return $this->db->fetchData("SELECT * FROM epi WHERE id_colaborador = :id_colaborador", [$id_colaborador]);
        } else {
            return $this->db->fetchData("SELECT * FROM epi");
        }
    }

    public function atualizarEPI(){
        $dadosProcessados = $this->db->processarNovosDados($this->getVars(), $this->listarEPIS($this->id_colaborador));
        if (@$dadosProcessados["existe"]) {
            if ($dadosProcessados["dadosIguais"]) {
                $this->db->padroes_finalizar("NO_CHANGES");
                die();
            } else {
                $dadosProcessados_toDB = [];
                $i = 0;
                foreach ($dadosProcessados["novosDados"] as $key => $value) {
                    $dadosProcessados_toDB[$i] = "$value";
                    $i++;
                }
                $query = "UPDATE epi SET " . $dadosProcessados["query_parcial"] . " WHERE id_colaborador = :id_colaborador";
                if ($this->db->fetchData($query, array_merge($dadosProcessados_toDB, [$this->id_colaborador]))) {
                    $dadosColaborador = $this->listarColaboradores($this->id_colaborador)[0];
                    $dados = ['ID Colaborador' => $this->id_colaborador, 'Nome Colaborador' => $dadosColaborador['nome_colaborador']];
                    $movimentacao = $this->historico->processarDados("ATUALIZAÇÃO", "EPI", $dados);

                    $this->historico->__set("movimentacao", $movimentacao);
                    $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                    $this->historico->__set("id_usuario", $_SESSION['id_usuario']);
                    if ($this->historico->adicionarHistorico()) {
                        if ($this->db->padroes_finalizar("ATUALIZAR")) {
                            return true;
                        }
                    }
                }
            }
        } else {
            $this->db->padroes_finalizar("NOT_FOUND");
        }
        // $this->db->padroes_finalizar("ERROR");
        return false;
    }

    public function excluirEPI($id_colaborador){
        $dadosProcessados = $this->db->processarNovosDados($this->getVars(), $this->listarEPIS($id_colaborador));
        if ($dadosProcessados['existe']) {
            $query = "DELETE FROM epi WHERE id_colaborador = :id_colaborador";
            if ($this->db->fetchData($query, [$id_colaborador], "DELETAR")) {
                $dadosColaborador = $this->listarColaboradores($id_colaborador)[0];
                $dados = ['ID Colaborador' => $id_colaborador, 'Nome Colaborador' => $dadosColaborador['nome_colaborador'], 'ID EPI' => $this->db->fetchData("SELECT LAST_INSERT_ID()")[0]['LAST_INSERT_ID()']];
                $movimentacao = $this->historico->processarDados("EXCLUSÃO", "EPI", $dados);

                $this->historico->__set("movimentacao", $movimentacao);
                $this->historico->__set("data_movimentacao", date("Y-m-d H:i:s"));
                $this->historico->__set("id_usuario", $_SESSION['id_usuario']);
                if ($this->historico->adicionarHistorico()) {
                    if ($this->db->padroes_finalizar("DELETAR")) {
                        return true;
                    }
                }
            }
        } else {
            $this->db->padroes_finalizar("NOT_FOUND");
        }
        // $this->db->padroes_finalizar("ERROR");
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
