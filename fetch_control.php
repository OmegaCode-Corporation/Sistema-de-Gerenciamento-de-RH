<?php
    @session_start();
    if(!isset($_SESSION['logged']) && !isset($_POST['-37vXj0zPm10RI'])){
        echo "<script>window.location.href = 'login.php'</script>";
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        require_once "assets/classes/db.php";
        $class_db = new Conexao_BD();

        $control_class = $_POST['control_class'];
        $control_action = $_POST['control_action'];
        @$_SESSION['route'] = $_POST['route'] !== null ? $_POST['route'] : 'index.php';
        $_SESSION['returnType'] = 'json';

        switch($control_class){
            case 'usuarios':
                require_once "assets/classes/usuario.php";
                $class_usuario = new Usuario();
                switch ($control_action) {
                    case 'read':
                        $dadosUser = $class_usuario->listarUsuarios(null, null, $_POST['search']);
                        print_r('["'.count($dadosUser).'", '.json_encode($dadosUser).']');
                        break;
                    case 'create':
                        $class_usuario->novoUsuario($_POST['email'], $_POST['perfil'], $_POST['id']);
                        break;
                    case 'update':
                        break;
                    case 'delete':
                        $_SESSION['openWindow'] = true;
                        $class_db->alterarSituacao($_POST['id'], 'id_usuario', 'usuarios', 'USUÁRIO', ["E-mail" => $_POST['email'], "Situação" => "Inativo"], 0);
                        break;
                    case 'active':
                        $_SESSION['openWindow'] = true;
                        $class_db->alterarSituacao($_POST['id'], 'id_usuario', 'usuarios', 'USUÁRIO', ["E-mail" => $_POST['email'], "Situação" => "Ativo"], 1);
                        break;
                    default:
                        break;
                }
                break;
            case 'contratos':
                require_once "assets/classes/contrato.php";
                $class_contrato = new Contrato();
                switch ($control_action) {
                    case 'read':
                        break;
                    case 'create':
                        require_once  "assets/classes/documentos.php";
                        $class_documentos = new Documento();

                        $class_documentos->__set("id_contrato", $_POST['id_contrato']);
                        $class_documentos->__set("id_colaborador", $_POST['id_colaborador']);
                        $class_documentos->__set("id_unidade", $_POST['id_unidade']);
                        $class_documentos->DocContrato();
                        break;
                    case 'create_ficha':
                        require_once  "assets/classes/documentos.php";
                        $class_documentos = new Documento();

                        $class_documentos->__set("id_contrato", $_POST['id_contrato']);
                        $class_documentos->__set("id_colaborador", $_POST['id_colaborador']);
                        $class_documentos->__set("id_unidade", $_POST['id_unidade']);
                        $class_documentos->FichaRegistroEmprego();
                        break;
                    case 'update':
                        break;
                    case 'change_status':
                        $_SESSION['openWindow'] = true;
                        $class_contrato->__set("id_contrato",$_POST['id_contrato']);
                        $class_contrato->SituacaoContrato($_POST['situacao'] === 'Ativa' ? 0 : 1);
                        break;
                    case 'prorrogar':
                        $class_prorrogacao = new Prorrogacao();
                        $class_prorrogacao->__set("id_contrato", $_POST['id_contrato']);
                        $class_prorrogacao->__set("data_prorrogacao", date("Y-m-d", strtotime($_POST['data_prorrogacao'])));
                        $class_prorrogacao->AddProrrogacao();
                        break;
                    case 'download_doc':
                        require_once  "assets/classes/documentos.php";
                        $class_documentos = new Documento();

                        $class_documentos->__set("id_prorrogacao", $_POST['id_prorrogacao']);
                        $class_documentos->__set("id_contrato", $_POST['id_contrato']);
                        $class_documentos->__set("id_colaborador", $_POST['id_colaborador']);
                        $class_documentos->__set("id_unidade", $_POST['id_unidade']);
                        if($class_documentos->DocProrrogacao()){
                            $class_db->fetchData("UPDATE prorrogacao_contratos SET download_doc = 0 WHERE id_contrato = :id_contrato", ['id_contrato' => $_POST['id_contrato']]);
                        }
                    default:
                        break;
                }
                break;
            case 'colaboradores':
                require_once "assets/classes/colaborador.php";
                $class_colaborador = new Colaborador();
                $class_desligamento = new Desligamento();
                switch ($control_action) {
                    case 'read':
                        break;
                    case 'create':
                        break;
                    case 'update':
                        break;
                    case 'delete':
                        $_SESSION['openWindow'] = true;
                        $class_desligamento->__set("id_colaborador", $_POST['id']);
                        $class_desligamento->__set("data_desligamento", date("Y-m-d", strtotime($_POST['data_desligamento'])));
                        $class_desligamento->__set("observacao", $_POST['observacao']);
                        $class_desligamento->novoDesligamento();
                        break;
                    default:
                        break;
                }
                break;
            case 'exames':
                require_once "assets/classes/exame.php";
                $class_exame = new Exame();
                switch ($control_action) {
                    case 'read':
                        break;
                    case 'create':
                        $class_exame->__set("id_colaborador", $_POST['id_colaborador']);
                        $class_exame->__set("relacao_clinicas", $_POST['relacao_clinicas']);
                        $class_exame->__set("telefone_clinicas", $_POST['telefone_clinicas']);
                        $class_exame->__set("data_ultimo_exame", date("Y-m-d", strtotime($_POST['data_ultimo_exame'])));
                        $class_exame->__set("data_agendamento", date("Y-m-d", strtotime($_POST['data_agendamento'])));
                        $class_exame->AddExame();
                        break;
                    case 'update':
                        break;
                    case 'delete':
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
        $_SESSION['returnType'] = null;
        $_SESSION['openWindow'] = null;
    } else {
        echo json_encode(['responseMsg' => 'ERRO AO PROCESSAR A REQUISIÇÃO', 'responseType' => 'alert-danger'], JSON_UNESCAPED_UNICODE);
    }
?>