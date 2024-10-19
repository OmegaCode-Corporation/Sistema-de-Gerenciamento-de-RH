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
    <title>Listagem de Empresas</title>
    <link rel="shortcut icon" href="assets/images/icone_logo.ico" type="image/x-icon">
    <script src="assets/js/cadastro_empresa.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/listar_empresas.css">
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
        <center><h1>Listagem de Empresas</h1></center>
        <form method="GET" action="" id="form-busca">
            <div class="flex" style="display: flex;">
                <label class="texto" class="texto" for="input-busca" style="font-size: 26px;">O que você procura?</label>
                <div class="dropdown" id="searchInfo"><i class="bi bi-info-circle" style="font-size: 26px;"></i>
                    <div class="dropdown-content" id="searchInfo_content">
                    <p>Opções de Procura:</p><b>ID Empresa;</b> <br> <b>Razão Social;</b> <br> <b>CNPJ;</b> <br> <b>E-Mail;</b> <br> <b>Status;</b>
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
            require_once "assets/classes/empresa.php";
            
            $class_db = new Conexao_BD();
            $class_empresa = new Empresa();
    
            $registros_por_pagina = 10;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $registros_por_pagina;

            if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])){
                $pesquisa = $_GET['pesquisa'];
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM empresas WHERE id_empresa = '$pesquisa' OR nome_empresa LIKE '%$pesquisa%' OR cnpj = '$pesquisa' OR email = '$pesquisa' OR situacao = '$pesquisa'")[0]['total'] / $registros_por_pagina);
                $empresas = $class_empresa->DadosEmpresa(null, $pesquisa, $offset, $registros_por_pagina);
            } else {
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM empresas")[0]['total'] / $registros_por_pagina);
                $empresas = $class_empresa->DadosEmpresa(null, null, $offset, $registros_por_pagina);
            }

            if(count($empresas) > 0){
        ?>
            <div class="table-responsive">        
                <table class="table table-hover table-striped">
                    <thead style="background-color: #1f84d6; color: white;">
                        <tr>
                            <th>ID Empresa</th>
                            <th>Razão Social</th>
                            <th>CNPJ</th>
                            <th>Contato</th>
                            <th>E-mail</th>
                            <th>Status</th>
                            <th>Mais Opções</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-empresa" class="tabela-listagem">
        <?php
                        foreach($empresas as $empresa){
                            $id_empresa = $empresa['id_empresa'];
                            $cnpj = $empresa['cnpj'];
                            $nome_empresa = strtoupper($empresa['nome_empresa']);
                            $telefone = $empresa['telefone'];
                            $telefone_tp = strlen($telefone) === 10 ? 'telefone_fixo' : 'telefone';
                            $email = $empresa['email'];
                            $situacao = $empresa['situacao'] === 1 ? "Ativa" : "Inativa";
                            $cep = $empresa['cep'];
                            $rua = ucwords(strtolower($empresa['rua']));
                            $bairro = ucwords(strtolower($empresa['bairro']));
                            $numero = $empresa['numero'];
                            $complemento = ucwords(strtolower($empresa['complemento']));
                            $cidade = ucwords(strtolower($empresa['cidade']));
                            $estado = strtoupper($empresa['estado']);
                            $endereco = "$rua, $numero - $complemento - $bairro, $cidade - $estado, $cep";

                            require_once "assets/classes/diretorio.php";
                            $class_diretorio = new Diretorio();
                            $class_diretorio->identidade = "diretorio_empresa";
                            $class_diretorio->id = $id_empresa;
                            $hasDiretorio = $class_diretorio->ListaArquivos();

                            echo "
                            <tr>
                                <td>$id_empresa</td>
                                <td>$nome_empresa</td>
                                <td><script>document.write(formatText('$cnpj', 'cnpj', 'add'))</script></td>
                                <td><script>document.write(formatText('$telefone', '$telefone_tp', 'add'))</script></td>
                                <td>$email</td>
                                <td>$situacao</td>
                                <td>
                                    <div class='btns' style='display: flex; flex-direction: row;'>
                                        <button class='btn' data-toggle='modal' data-target='#viewModal__$id_empresa' alt='Ver Mais'><i class='bi bi-info-circle'></i></button>
                                        <a href='https://www.google.com/maps/search/$endereco' target='_blank' class='btn'><i class='bi bi-geo-alt-fill'></i></a>";
                                        echo $hasDiretorio !== false ?
                                        "<button type='button' class='btn' data-dismiss='modal' onclick='window.location.href = \"listar_documentos.php?id_entidade=$id_empresa&entidade=diretorio_empresa\"'><i class='bi bi-archive'></i></button>" : ""; echo "
                                    </div>
                                    <div class='modal fade' id='viewModal__$id_empresa' tabindex='-1' role='dialog' aria-labelledby='viewModalLabel' aria-hidden='true'>
                                    <div class='modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl' role='document'>
                                        <div class='modal-content'>
                                            <div class='modal-header'>
                                                <h4 class='modal-title' id='viewModalLabel__$id_empresa'>Visualizando: $nome_empresa</h4>
                                                <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                    <span aria-hidden='true'>&times;</span>
                                                </button>
                                            </div>
                                            <div class='modal-body'>
                                                <strong>Rua:</strong> <br> <p>$rua</p>
                                                <strong>Número:</strong> <br> <p>$numero</p>";
                                                echo $complemento !== "" && $complemento !== null ? "<strong>Complemento:</strong> <br> <p>$complemento</p>" : ""; echo "
                                                <strong>Bairro:</strong> <br> <p>$bairro</p>
                                                <strong>Cidade:</strong> <br> <p>$cidade</p>
                                                <strong>Estado:</strong> <br> <p>$estado</p>
                                                <strong>CEP:</strong> <br> <p><script>document.write(formatText('$cep', 'cep', 'add'))</script></p>
                                            </div>
                                            <div class='modal-footer'>
                                                <a href='editar_empresa.php?id=$id_empresa' class='btn btn-primary'>Editar</a>
                                                <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
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
                            <a class="page-link" href="<?php echo $page <= 1 ? '#' : './listar_empresas.php?page=' . ($page - 1) . $pesquisa; ?>" tabindex="-1" aria-disabled="true"><<</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="./listar_empresas.php?page=<?php echo $i . $pesquisa; ?>"><?php echo $i ; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $page >= $total_pages ? '#' : './listar_empresas.php?page=' . ($page + 1) . $pesquisa; ?>">>></a>
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