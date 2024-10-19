<?php
    @session_start();
    if(!isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'login.php'</script>";
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
    <title>Listagem de Desligamentos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/listar_desligamento.css">
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
    ?>
    <div class="card card-body mt-5 container" id="cardbox">
        <center><h1>Listagem de Desligamentos</h1></center>
        <form method="GET" action="" id="form-busca">
            <div class="flex" style="display: flex;">
                <label class="texto" for="input-busca" style="font-size: 26px;">O que você procura?</label>
                <div class="dropdown" id="searchInfo"><i class="bi bi-info-circle" style="font-size: 26px;"></i>
                    <div class="dropdown-content" id="searchInfo_content">
                        <p> <p>Opções de Procura:</p><b>ID Desligamento;</b> <br> <b>ID Contrato;</b> <br> <b>Observação;</b> 
                        </p>
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
            require_once "assets/classes/db.php";
            require_once "assets/classes/colaborador.php";

            $class_db = new Conexao_BD();
            $class_desligamento = new Desligamento();
        
            $registros_por_pagina = 10;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $registros_por_pagina;

            if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])){
                $pesquisa = $_GET['pesquisa'];
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM desligamento_colaboradores WHERE id_desligamento = '$pesquisa' OR id_contrato = '$pesquisa' or observacao LIKE '%$pesquisa%'")[0]['total'] / $registros_por_pagina);
                $desligamentos = $class_desligamento->listarDesligamentos(null, $pesquisa, $offset, $registros_por_pagina);
            } else {
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM desligamento_colaboradores")[0]['total'] / $registros_por_pagina);
                $desligamentos = $class_desligamento->listarDesligamentos(null, null, $offset, $registros_por_pagina);
            }

            if(count($desligamentos) > 0){
        ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead style="background-color: #1f84d6; color: white;">
                    <tr>
                        <th>ID Desligamento</th>
                        <th>ID Contrato</th>
                        <th>Data e Hora</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody id="tabela-desligamento" class="tabela-listagem">
        <?php
                foreach($desligamentos as $desligamento){
                    $id_desligamento = $desligamento['id_desligamento'];
                    $id_contrato = $desligamento['id_desligamento'];
                    $observacao = $desligamento['observacao'];
                    $data_desligamento = date("d/m/Y | H:i:s", strtotime($desligamento['data_desligamento']));

                    echo "<tr>
                        <td>$id_desligamento</td>
                        <td>$id_contrato</td>
                        <td>$data_desligamento</td>
                        <td>"; echo ucwords(strtolower($observacao)); echo "</td>
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
                        <a class="page-link" href="<?php echo $page <= 1 ? '#' : './listar_desligamentos.php?page=' . ($page - 1) . $pesquisa; ?>" tabindex="-1" aria-disabled="true"><<</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="./listar_desligamentos.php?page=<?php echo $i . $pesquisa; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo $page >= $total_pages ? '#' : './listar_desligamentos.php?page=' . ($page + 1) . $pesquisa; ?>">>></a>
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
    <div class="responseMsg alert"></div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/modal.js"></script>
</body>
</html>