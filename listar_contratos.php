<?php
    @session_start();
    if(!isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'index.php'</script>";
        die();
    }
    
    $allowed_access = ['master', 'visualizar'];
    $perfil_usuario = $_SESSION['perfil_usuario'];
    $hasAccess = in_array($perfil_usuario, $allowed_access);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Contratos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/listar_contratos.css">
</head>
<?php require_once "navbar.php"; ?>
<body>
    <?php 
        if(!$hasAccess){
            echo "
            <div class='error-block'>
                <div class='responseMsg alert alert-dark' role='alert'></div>
            </div>
            <script src='assets/js/main.js'></script>
            <script>
                finishAction('Você não tem permissão para acessar essa página! <br><br> Você será redirecionado para a página inicial em ', 'index.php', 10);
            </script>";
            die();
        }
        require_once "assets/classes/db.php";
        require_once "assets/classes/contrato.php";
        require_once "assets/classes/historico.php";
        require_once "assets/classes/usuario.php";
        require_once "assets/classes/empresa.php";
    ?>
    <div class="card card-body mt-5 container" id="cardbox">
        <center><h1>Listagem de Contratos</h1></center>
        <form method="GET" action="" id="form-busca">
            <div class="flex" style="display: flex;">
                <label class="texto" for="input-busca" style="font-size: 26px;">O que você procura?</label>
                <div class="dropdown" id="searchInfo"><i class="bi bi-info-circle" style="font-size: 26px;"></i>
                    <div class="dropdown-content" id="searchInfo_content">
                        <p> <p>Opções de Procura:</p><b>ID Contrato;</b> <br> <b>Situação;</b> </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <input id="input-busca" type="text" value="<?php echo isset($_GET['pesquisa']) ? $_GET['pesquisa'] : "" ?>" name="pesquisa" class="form-control mt-3 mb-3" placeholder="Digite..." style="border-radius: .375rem 0 0 .375rem; margin-left: 7px">
                </div>
                <div class="col" style="padding: 0; margin-top: 10px; margin-left: calc(var(--bs-gutter-x)* .5* -1); margin-right: 15px; flex: 0 0 0%;">
                    <?php
                    echo isset($_GET['pesquisa']) && !empty($_GET['pesquisa']) ? "<button id='btn-clean' type='button' style='border: none; background-color: #dc3545; color: white; padding: 7px 10px; border-radius: 0 5px 5px 0;'><i class='bi bi-x-lg'></i></button>" : "<button id='btn-busca' type='submit' style='border: none; background-color: #1f84d6; color: white; padding: 7px 10px; border-radius: 0 5px 5px 0;'><i class='bi bi-search'></i></button>";
                    ?>
                </div>
            </div>
        </form>
        <?php
            $class_db = new Conexao_BD();
            $class_contrato = new Contrato();
            $class_usuario = new Usuario();
            $class_empresa = new Empresa();
        
            $registros_por_pagina = 10;
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $registros_por_pagina;

            if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])){
                $pesquisa = $_GET['pesquisa'];
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM contratos_colaboradores WHERE id_contrato = '$pesquisa' OR situacao = '$pesquisa'")[0]['total'] / $registros_por_pagina);
                $contratos = $class_contrato->DadosContrato(null, $pesquisa, $offset, $registros_por_pagina);
            } else {
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM contratos_colaboradores")[0]['total'] / $registros_por_pagina);
                    $contratos = $class_contrato->DadosContrato(null, null, $offset, $registros_por_pagina);
            }

            if(count($contratos) > 0){
        ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead style="background-color: #1f84d6; color: white;">
                        <tr>
                            <th>ID Contrato</th>
                            <th>Nome Colaborador</th>
                            <th>Nome Empresa</th>
                            <th>Admissão</th>
                            <th>Situação</th>
                            <th>Mais Opções</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-contrato" class="tabela-listagem">
        <?php
                        foreach($contratos as $contrato){
                            $id_contrato = $contrato['id_contrato'];

                            $id_colaborador = $contrato['id_colaborador'];
                            $dadosUsuario = $class_usuario->listarColaboradores($id_colaborador)[0];
                            $dadosColaborador = $class_usuario->listarColaboradores($dadosUsuario['id_colaborador'])[0];
                            $nome_colaborador = ucwords(strtolower($dadosColaborador['nome_colaborador']));
                            
                            $id_empresa = $contrato['id_empresa'];
                            // $class_empresa->__set("id_empresa",$id_empresa);
                            $dadosEmpresa = $class_empresa->DadosEmpresa($id_empresa)[0];
                            $nome_empresa = strtoupper($dadosEmpresa['nome_empresa']);

                            $id_unidade = $contrato['id_unidade'];
                            $admissao = date("d/m/Y", strtotime($contrato['admissao']));
                            $optante_fgts = $contrato['optante_fgts'];
                            $data_opcao = date("d/m/Y", strtotime($contrato['data_opcao']));
                            $conta_fgts  = $contrato['conta_fgts'];
                            $cargo = ucwords(strtolower($contrato['cargo']));
                            $cbo = $contrato['cbo'];
                            $organograma = ucwords(strtolower($contrato['organograma']));
                            $remuneracao = $contrato['remuneracao'];
                            $forma_pagamento = ucwords(strtolower($contrato['forma_pagamento']));
                            $periodo_pagamento = ucwords(strtolower($contrato['periodo_pagamento']));
                            $escala_trabalho = $contrato['escala_trabalho'];
                            $situacao = $contrato['situacao'] === 1 ? "Ativa" : "Inativa";
                            $hasContrato = $class_contrato->hasContrato($id_contrato);

                            $class_prorrogacao = new Prorrogacao();
                            $hasProrrogacao = $class_prorrogacao->DadosProrrogacao(null, $id_contrato);
                            $data_prorrogacao = count($hasProrrogacao) > 0 ? date("d/m/Y", strtotime($hasProrrogacao[0]['data_prorrogacao'])) : null;

                            require_once "assets/classes/diretorio.php";
                            $class_diretorio = new Diretorio();
                            $class_diretorio->identidade = "diretorio_contrato";
                            $class_diretorio->id = $id_contrato;
                            $hasDiretorio = $class_diretorio->ListaArquivos();

                            echo "<tr>
                                <td>$id_contrato</td>
                                <td>$nome_colaborador</td>
                                <td>$nome_empresa</td>
                                <td>$admissao</td>
                                <td>$situacao</td>
                                <td>
                                    <div class='btns' style='display: flex; flex-direction: row'>
                                        <button class='btn' data-toggle='modal' data-target='#viewModal__$id_contrato' alt='Ver Mais'><i class='bi bi-info-circle'></i></button>"; 
                                        echo $hasContrato === false ? "<button class='btn' data-toggle='modal' data-target='#ativarModal__$id_contrato' alt='Ativar'><i class='bi bi-arrow-up-circle'></i></button>" : "<button class='btn' data-toggle='modal' data-target='#desativarModal__$id_contrato' alt='Desativar'><i class='bi bi-ban'></i></button>";
                                        echo $data_prorrogacao !== null ? "<button class='btn' onclick='window.location.href = \"listar_prorrogacoes.php?pesquisa=$id_contrato\"'><i class='bi bi-card-checklist'></i></button>" : "";
                                        echo $hasContrato === true ? "<button class='btn' data-toggle='modal' data-target='#prorrogarModal__$id_contrato' alt='Prorrogar Contrato'><i class='bi bi-clock-history'></i></button>" : "";
                                        echo $hasDiretorio !== false ?
                                        "<button type='button' class='btn' data-dismiss='modal' onclick='window.location.href = \"listar_documentos.php?id_entidade=$id_contrato&entidade=diretorio_contrato\"'><i class='bi bi-archive'></i></button>" : ""; echo "
                                    </div>
                                    <div class='modal fade' id='desativarModal__$id_contrato' tabindex='-1' role='dialog' aria-labelledby='desativarModalLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered' role='document'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h4 class='modal-title' id='desativarModalLabel__$id_contrato'>Desativar Contrato</h4>
                                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                        <span aria-hidden='true'>&times;</span>
                                                    </button>
                                                </div>
                                                <div class='modal-body'>
                                                    <form action='' method='' class='modal-form'>
                                                        <input type='text' name='id_contrato' id='id_contrato__$id_contrato' value='$id_contrato' hidden>
                                                        <input type='text' name='situacao' id='situacao__$id_contrato' value='$situacao' hidden>
                                                        <input type='text' name='control_class' id='control_class__$id_contrato' value='contratos' hidden>
                                                        <input type='text' name='control_action' id='control_action__$id_contrato' value='change_status' hidden>
                                                        <input type='text' name='route' id='route__$id_contrato' value='"; echo basename(__FILE__); echo "' hidden>
                                                        <p>Tem certeza que deseja desativar o contrato $id_contrato, de $nome_colaborador, no sistema?</p>
                                                        <button type='submit' class='btn btn-primary enviarModal' hidden></button>
                                                    </form>
                                                    <div class='responseMsg alert' style='display: none'></div>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancelar</button>
                                                    <button type='submit' class='btn btn-danger' onclick='this.parentElement.parentElement.querySelector(`.enviarModal`).click()'>Desativar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='modal fade' id='ativarModal__$id_contrato' tabindex='-1' role='dialog' aria-labelledby='ativarModalLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered' role='document'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h4 class='modal-title' id='ativarModalLabel__$id_contrato'>Ativar Contrato</h4>
                                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                        <span aria-hidden='true'>&times;</span>
                                                    </button>
                                                </div>
                                                <div class='modal-body'>
                                                    <form action='' method='' class='modal-form'>
                                                        <input type='text' name='id_contrato' id='id_contrato__$id_contrato' value='$id_contrato' hidden>
                                                        <input type='text' name='situacao' id='situacao__$id_contrato' value='$situacao' hidden>
                                                        <input type='text' name='control_class' id='control_class__$id_contrato' value='contratos' hidden>
                                                        <input type='text' name='control_action' id='control_action__$id_contrato' value='change_status' hidden>
                                                        <input type='text' name='route' id='route__$id_contrato' value='"; echo basename(__FILE__); echo "' hidden>
                                                        <p>Tem certeza que deseja ativar o contrato $id_contrato, de $nome_colaborador, no sistema?</p>
                                                        <button type='submit' class='btn btn-primary enviarModal' hidden></button>
                                                    </form>
                                                    <div class='responseMsg alert' style='display: none'></div>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancelar</button>
                                                    <button type='submit' class='btn btn-success' onclick='this.parentElement.parentElement.querySelector(`.enviarModal`).click()'>Ativar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='modal fade' id='prorrogarModal__$id_contrato' tabindex='-1' role='dialog' aria-labelledby='prorrogarModalLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered' role='document'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h4 class='modal-title' id='prorrogarModalLabel__$id_contrato'>Prorrogar Contrato de $nome_colaborador</h4>
                                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                        <span aria-hidden='true'>&times;</span>
                                                    </button>
                                                </div>
                                                <div class='modal-body'>
                                                    <form action='' method='' class='modal-form'>
                                                        <input type='text' name='id_contrato' id='id_contrato__$id_contrato' value='$id_contrato' hidden>
                                                        <input type='text' name='id_colaborador' id='id_colaborador__$id_colaborador' value='$id_colaborador' hidden>
                                                        <input type='text' name='id_unidade' id='id_unidade__$id_unidade' value='$id_unidade' hidden>
                                                        <input type='text' name='control_class' id='control_class__$id_contrato' value='contratos' hidden>
                                                        <input type='text' name='control_action' id='control_action__$id_contrato' value='prorrogar' hidden>
                                                        <input type='text' name='route' id='route__$id_contrato' value='"; echo basename(__FILE__); echo "' hidden>
                                                        <div class='row'>
                                                            <div class='col'>
                                                                <div class='form-floating mb-3'>
                                                                    <input style='height: 50px;' type='date' class='form-control' name='data_admissao' id='data_admissao__$id_contrato' value='"; echo $contrato['admissao']; echo "' readonly>
                                                                    <label for='data_admissao__$id_contrato'>Data Atual:</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class='row'>
                                                            <div class='col'>
                                                                <div class='form-floating mb-5'>
                                                                    <input style='height: 50px;' type='date' class='form-control' name='data_prorrogacao' id='data_prorrogacao__$id_contrato'>
                                                                    <label for='data_prorrogacao__$id_contrato'>Nova Data:</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type='submit' class='btn btn-primary enviarModal' hidden></button>
                                                    </form>
                                                    <div class='responseMsg alert' style='display: none'></div>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancelar</button>
                                                    <button type='submit' class='btn btn-primary' onclick='this.parentElement.parentElement.querySelector(`.enviarModal`).click()'>Prorrogar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='modal fade' id='viewModal__$id_contrato' tabindex='-1' role='dialog' aria-labelledby='viewModalLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl' role='document'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h4 class='modal-title' id='viewModalLabel__$id_contrato'>Visualizando Contrato de $nome_colaborador</h4>
                                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                        <span aria-hidden='true'>&times;</span>
                                                    </button>
                                                </div>
                                                <div class='modal-body'>
                                                    "; echo $data_prorrogacao !== null ? "<strong>Contrato Prorrogado:</strong> <br> <p>$data_prorrogacao</p>" : ""; echo "
                                                    <strong>Optante FTGS:</strong> <br> <p>"; echo $optante_fgts === 0 ? "Não" : "Sim"; echo "</p>
                                                    <strong>Data Opção:</strong> <br> <p>$data_opcao</p>
                                                    <strong>Conta FGTS:</strong> <br> <p>$conta_fgts</p>
                                                    <strong>Cargo:</strong> <br> <p>$cargo</p>
                                                    <strong>CBO:</strong> <br> <p>$cbo</p>
                                                    <strong>Organograma:</strong> <br> <p>$organograma</p>
                                                    <strong>Remuneração:</strong> <br> <p>R$$remuneracao,00</p>
                                                    <strong>Forma de Pagamento:</strong> <br> <p>$forma_pagamento</p>
                                                    <strong>Período do Pagamento:</strong> <br> <p>$periodo_pagamento</p>
                                                    <strong>Escala de Trabalho:</strong> <br> <p>$escala_trabalho</p>
                                                    <form action='fetch_control.php' method='POST' class='modal-form' id='ficha_form'>
                                                        <input type='text' name='id_contrato' id='id_contrato__$id_contrato' value='$id_contrato' hidden>
                                                        <input type='text' name='id_colaborador' id='id_colaborador__$id_colaborador' value='$id_colaborador' hidden>
                                                        <input type='text' name='id_unidade' id='id_unidade__$id_unidade' value='$id_unidade' hidden>
                                                        <input type='text' name='control_class' id='control_class__$id_contrato' value='contratos' hidden>
                                                        <input type='text' name='control_action' id='control_action__$id_contrato' value='create_ficha' hidden>
                                                        <input type='text' name='route' id='route__$id_contrato' value='"; echo basename(__FILE__); echo "' hidden>
                                                        <button type='submit' class='btn btn-primary enviarModal' hidden></button>
                                                    </form>
                                                    <div class='responseMsg alert' style='display: none'></div>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='submit' class='btn btn-success' onclick='this.parentElement.parentElement.querySelector(`#ficha_form`).submit()'>Download Ficha</button>
                                                    <button type='button' class='btn btn-primary' data-dismiss='modal'>Sair</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>";
                        }
        ?>
                    </tbody>
                </table>
            </div>    
                <center>
                <nav aria-label="Paginação">
                    <ul class="pagination justify-content-center">
                        <?php $pesquisa = isset($_GET['pesquisa']) ? '&pesquisa=' . $_GET['pesquisa'] : ''; ?>
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $page <= 1 ? '#' : './listar_contratos.php?page=' . ($page - 1) . $pesquisa; ?>" tabindex="-1" aria-disabled="true"><<</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="./listar_contratos.php?page=<?php echo $i . $pesquisa; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $page >= $total_pages ? '#' : './listar_contratos.php?page=' . ($page + 1) . $pesquisa; ?>">>></a>
                        </li>
                    </ul>
                </nav>
                </center>
        <?php
            } else {
                $hasPesquisa = isset($_GET['pesquisa']) ? "" : "<script>document.querySelector('#form-busca').style.display = 'none';</script>";
                echo $hasPesquisa . "<br><div class='alert alert-dark' role='alert'>Nenhum registro encontrado!</div>";
            }
        ?>
    </div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/modal.js"></script>
</body>
</html>
