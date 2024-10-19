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
    <title>Listagem de Prorrogações</title>
    <link rel="stylesheet" href="assets/css/logs.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<?php 
    require_once "navbar.php"; 
?>
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
    ?>
    <div class="card card-body mt-5 container" id="cardbox">
        <h1>Listagem de Prorrogações</h1>
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
            $class_prorrogacao = new Prorrogacao();

            $registros_por_pagina = 10;
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $registros_por_pagina;

            if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])){
                $pesquisa = $_GET['pesquisa'];
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM prorrogacao_contratos WHERE id_prorrogacao = '$pesquisa' OR id_contrato = '$pesquisa'")[0]['total'] / $registros_por_pagina);
                $contratos = $class_prorrogacao->DadosProrrogacao(null, $pesquisa, $offset, $registros_por_pagina);
            } else {
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM prorrogacao_contratos")[0]['total'] / $registros_por_pagina);
                $contratos = $class_prorrogacao->DadosProrrogacao(null, null, $offset, $registros_por_pagina);
            }

            if(count($contratos) > 0){
        ?>
                <table class="table table-hover table-striped">
                    <thead style="background-color: #1f84d6; color: white;">
                        <tr>
                            <th>ID Contrato</th>
                            <th>Data de Prorrogação</th>
                            <th>Download Doc.</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-prorrogacao" class="tabela-listagem">
        <?php
                        foreach($contratos as $contrato){
                            $id_prorrogacao = $contrato['id_prorrogacao'];
                            $id_contrato = $contrato['id_contrato'];
                            $dadosContrato = $class_contrato->DadosContrato($id_contrato);
                            $id_colaborador = $dadosContrato[0]['id_colaborador'];
                            $id_unidade = $dadosContrato[0]['id_unidade'];
                            
                            $data_prorrogacao = date("d/m/Y", strtotime($contrato['data_prorrogacao']));

                            $download_doc = $contrato['download_doc'] === 1 ? "
                            <form method='POST' action='fetch_control.php'>
                                <input type='text' name='id_prorrogacao' id='id_prorrogacao__$id_prorrogacao' value='$id_prorrogacao' hidden>
                                <input type='text' name='id_contrato' id='id_contrato__$id_contrato' value='$id_contrato' hidden>
                                <input type='text' name='id_colaborador' id='id_colaborador__$id_colaborador' value='$id_colaborador' hidden>
                                <input type='text' name='id_unidade' id='id_unidade__$id_unidade' value='$id_unidade' hidden>
                                <input type='text' name='control_class' id='control_class__$id_contrato' value='contratos' hidden>
                                <input type='text' name='control_action' id='control_action__$id_contrato' value='download_doc' hidden>
                                <button type='submit' class='btn'><i class='bi bi-download'></i></button>
                            </form>" : "";

                            echo "<tr>
                                <td>$id_contrato</td>
                                <td>$data_prorrogacao</td>
                                <td>$download_doc</td>
                            </tr>";
                        }

        ?>
                    </tbody>
            </table>
            <center>
            <nav aria-label="Paginação">
                <ul class="pagination justify-content-center">
                    <?php $pesquisa = isset($_GET['pesquisa']) ? '&pesquisa=' . $_GET['pesquisa'] : ''; ?>
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $page <= 1 ? '#' : './listar_prorrogacoes.php?page=' . ($page - 1) . $pesquisa; ; ?>" tabindex="-1" aria-disabled="true"><<</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="./listar_prorrogacoes.php?page=<?php echo $i . $pesquisa; ; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $page >= $total_pages ? '#' : './listar_prorrogacoes.php?page=' . ($page + 1) . $pesquisa; ; ?>">>></a>
                    </li>
                </ul>
            </nav>
            </center>
            </div>
        <?php
                    } else {
                        $hasPesquisa = isset($_GET['pesquisa']) ? "" : "<script>document.querySelector('#form-busca').style.display = 'none';</script>";
                        echo $hasPesquisa . "<br><div class='alert alert-dark' role='alert'>Nenhum registro encontrado!</div>";
                    }
        ?>
    <script src="assets/js/main.js"></script>
</body>
</html>