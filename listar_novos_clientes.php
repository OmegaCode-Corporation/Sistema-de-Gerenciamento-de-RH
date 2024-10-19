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
    <title>Listagem de Novos Clientes</title>
    <link rel="stylesheet" href="assets/css/listar_novos_clientes.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
        <center><h1>Listagem de Novos Clientes</h1></center>
        <form method="GET" action="" id="form-busca">
            <div class="flex" style="display: flex;">
                <label for="input-busca" style="font-size: 26px;">O que você procura?</label>
                <div class="dropdown" id="searchInfo"><i class="bi bi-info-circle" style="font-size: 26px;"></i>
                    <div class="dropdown-content" id="searchInfo_content">
                        <p> <p>Opções de Procura:</p><b>ID Cliente;</b> <br> <b>ID Unidade;</b> <br> <b>Razão Social;</b> <br> <b>CNPJ;</b> <br>
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
            require_once "assets/classes/novos_clientes.php";

            $class_db = new Conexao_BD();
            $class_novos_clientes = new NovosClientes();
        
            $registros_por_pagina = 10;
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $registros_por_pagina;
            $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM novos_clientes")[0]['total'] / $registros_por_pagina);
            $novos_clientes = $class_novos_clientes->listarNovosClientes(null, null, $offset, $registros_por_pagina);

            if(count($novos_clientes) > 0){
        ?>
                <table class="table table-hover table-striped">
                    <thead style="background-color: #1f84d6; color: white;">
                        <tr>
                            <th>ID Cliente</th>
                            <th>Razão Social</th>
                            <th>CNPJ</th>
                            <th>N° de Lojas</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-colaborador" class="tabela-listagem">
        <?php
                        foreach($novos_clientes as $novo_cliente){
                            $id_clientes = $novo_cliente['id_clientes'];
                            $razao_social = $novo_cliente['razao_social'];
                            $cnpj = $novo_cliente['cnpj'];
                            $qtd_lojas = $novo_cliente['qtd_lojas'];
                            $valor_total_lojas = $novo_cliente['valor_total_lojas'];

                            echo "<tr>
                                <td>$id_clientes</td>
                                <td>$razao_social</td>
                                <td>$cnpj</td>
                                <td>$qtd_lojas</td>
                                <td>R$$valor_total_lojas</td>
                            </tr>";
                        }
        ?>
                    </tbody>
                </table>
                <center>
                <nav aria-label="Paginação">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $page <= 1 ? '#' : './listar_novos_clientes.php?page=' . ($page - 1); ?>" tabindex="-1" aria-disabled="true"><<</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="./listar_novos_clientes.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $page >= $total_pages ? '#' : './listar_novos_clientes.php?page=' . ($page + 1); ?>">>></a>
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
    <script src="assets/js/modal.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>