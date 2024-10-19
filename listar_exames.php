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
    <title>Listagem de Exames</title>
    <link rel="stylesheet" href="assets/css/listar_exames.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/cadastro_empresa.js"></script>
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
        <center><h1>Listagem de Exames</h1></center>
        <form method="GET" action="" id="form-busca">
            <div class="flex" style="display: flex;">
                <label class="texto" for="input-busca" style="font-size: 26px;">O que você procura?</label>
                <div class="dropdown" id="searchInfo"><i class="bi bi-info-circle" style="font-size: 26px;"></i>
                    <div class="dropdown-content" id="searchInfo_content">
                        <p> <p>Opções de Procura:</p><b>ID Exame;</b> <br> <b>Clínica;</b> </p>
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
            require_once "assets/classes/exame.php";
            require_once "assets/classes/usuario.php";

            $class_db = new Conexao_BD();
            $class_usuario = new Usuario();
            $class_exame = new Exame();

            $registros_por_pagina = 10;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $registros_por_pagina;

            if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])){
                $pesquisa = $_GET['pesquisa'];
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM exames WHERE id_exame = '$pesquisa' OR relacao_clinicas LIKE '%$pesquisa%'")[0]['total'] / $registros_por_pagina);
                $exames = $class_exame->DadosExame(null, null, $pesquisa, $offset, $registros_por_pagina);
            } else if (isset($_GET['id']) && !empty($_GET['id'])){
                $id_colaborador = $_GET['id'];
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM exames WHERE id_colaborador = '$id_colaborador'")[0]['total'] / $registros_por_pagina);
                $exames = $class_exame->DadosExame(null, $id_colaborador, null, $offset, $registros_por_pagina);
            } else {
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM exames")[0]['total'] / $registros_por_pagina);
                $exames = $class_exame->DadosExame(null, null, null, $offset, $registros_por_pagina);
            }

            if(count($exames) > 0){
        ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead style="background-color: #1f84d6; color: white;">
                        <tr>
                            <th>ID Exame</th>
                            <th>Nome Colaborador</th>
                            <th>Clínica</th>
                            <th>Contato</th>
                            <th>Último Exame</th>
                            <th>Agendamento</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-exame" class="tabela-listagem">
        <?php
                        foreach($exames as $exame){
                            $id_exame = $exame['id_exame'];
                            $id_colaborador = $exame['id_colaborador'];
                            $dadosUsuario = $class_usuario->listarColaboradores($id_colaborador)[0];
                            $dadosColaborador = $class_usuario->listarColaboradores($dadosUsuario['id_colaborador'])[0];
                            $nome_colaborador = ucwords(strtolower($dadosColaborador['nome_colaborador']));

                            $relacao_clinicas = ucwords(strtolower($exame['relacao_clinicas']));
                            $telefone_clinicas  = $exame['telefone_clinicas'];
                            $telefone_tp = strlen($telefone_clinicas) === 10 ? 'telefone_fixo' : 'telefone';
                            $data_ultimo_exame = date("d/m/Y", strtotime($exame['data_ultimo_exame']));
                            $data_agendamento = date("d/m/Y", strtotime($exame['data_agendamento']));

                            echo "<tr>
                                <td>$id_exame</td>
                                <td>$nome_colaborador</td>
                                <td>$relacao_clinicas</td>
                                <td><script>document.write(formatText('$telefone_clinicas', '$telefone_tp', 'add'))</script></td>
                                <td>$data_ultimo_exame</td>
                                <td>$data_agendamento</td>
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
                        <?php 
                            $pesquisa = isset($_GET['pesquisa']) ? '&pesquisa=' . $_GET['pesquisa'] : '';
                            $id = isset($_GET['id']) ? '&id=' . $_GET['id'] : '';
                        ?>
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $page <= 1 ? '#' : './listar_exames.php?page=' . ($page - 1) . $pesquisa . $id; ?>" tabindex="-1" aria-disabled="true"><<</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="./listar_exames.php?page=<?php echo $i . $pesquisa . $id; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $page >= $total_pages ? '#' : './listar_exames.php?page=' . ($page + 1) . $pesquisa . $id; ?>">>></a>
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
    </div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/modal.js"></script>
</body>
</html>