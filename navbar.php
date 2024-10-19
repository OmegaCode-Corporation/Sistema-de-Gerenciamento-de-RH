<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'/>
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/icone_logo.ico" type="assets/images/x-icon">
    <script src='https://code.jquery.com/jquery-2.1.3.min.js'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'></script>
</head>

<nav class="navbar navbar-expand-lg fixed-top">
    <a href="index.php"><img src="assets/images/favicon.png" alt="favicon" id="favicon"></a>
    <div class="container-fluid">
    <?php
        @session_start();
        if(isset($_SESSION['logged'])){
            require_once "assets/classes/usuario.php";
            $class_usuario = new Usuario();
            $dadosUsuario = $class_usuario->listarUsuarios($_SESSION['id_usuario'])[0];
            $nome_colaborador = $_SESSION['nome_colaborador'];
            $perfil_usuario = $_SESSION['perfil_usuario'];
            $email_usuario = $dadosUsuario['email'];
    ?>
        <div class="nav navbar_left">
            <?php
                if($perfil_usuario === 'master' || $perfil_usuario === 'visualizar' || $perfil_usuario === 'cadastro'){
            ?>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Empresas
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php
                                if($perfil_usuario === 'master'){
                                    echo '<a class="dropdown-item" href="cadastro_empresa.php">Cadastrar Empresa</a>
                                    <p class="dropdown-breakline"></p>
                                    <a class="dropdown-item" href="listar_empresas.php">Empresas Cadastradas</a>';
                                } else if($perfil_usuario === 'visualizar'){
                                    echo '<a class="dropdown-item" href="listar_empresas.php">Empresas Cadastradas</a>';
                                } else if($perfil_usuario === 'cadastro'){
                                    echo '<a class="dropdown-item" href="cadastro_empresa.php">Cadastrar Empresa</a>';
                                }
                            ?>
                            <!-- <a class="dropdown-item" href="cadastro_empresa.php">Cadastrar Empresa</a>
                            <p class="dropdown-breakline"></p>
                            <a class="dropdown-item" href="listar_empresas.php">Empresas Cadastradas</a> -->
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Colaboradores
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php
                                if($perfil_usuario === 'master'){
                                    echo '<a class="dropdown-item" href="cadastro_colaborador.php">Cadastrar Colaborador</a>
                                    <a class="dropdown-item" href="cadastro_sancoes.php">Cadastrar Sanção</a>
                                    <a class="dropdown-item" href="cadastro_acidentes_trabalho.php">Cadastrar Acidente de Trabalho</a>
                                    <p class="dropdown-breakline"></p>
                                    <a class="dropdown-item" href="listar_colaboradores.php">Colaboradores Cadastrados</a>
                                    <a class="dropdown-item" href="listar_exames.php">Exames Cadastrados</a>';
                                } else if($perfil_usuario === 'visualizar'){
                                    echo '<a class="dropdown-item" href="listar_colaboradores.php">Colaboradores Cadastrados</a>
                                    <a class="dropdown-item" href="listar_exames.php">Exames Cadastrados</a>';
                                } else if($perfil_usuario === 'cadastro'){
                                    echo '<a class="dropdown-item" href="cadastro_colaborador.php">Cadastrar Colaborador</a>
                                    <a class="dropdown-item" href="cadastro_sancoes.php">Cadastrar Sanção</a>
                                    <a class="dropdown-item" href="cadastro_acidentes_trabalho.php">Cadastrar Acidente de Trabalho</a>
                                    <a class="dropdown-item" href="cadastro_exames.php">Cadastrar Exames</a>';
                                }
                            ?>
                            <!-- <a class="dropdown-item" href="cadastro_colaborador.php">Cadastrar Colaborador</a>
                            <a class="dropdown-item" href="cadastro_sancoes.php">Cadastrar Sanção</a>
                            <a class="dropdown-item" href="cadastro_acidentes_trabalho.php">Cadastrar Acidente de Trabalho</a>
                            <p class="dropdown-breakline"></p>
                            <a class="dropdown-item" href="listar_colaboradores.php">Colaboradores Cadastrados</a>
                            <a class="dropdown-item" href="listar_exames.php">Exames Cadastrados</a> -->
                        </div>
                    </div>
            <?php
                }
            ?>
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Históricos
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="cadastro_relatorio_dia.php">Realizar Relátorio do Dia</a>
                    <?php
                        if($perfil_usuario === 'master'){
                            echo '<p class="dropdown-breakline"></p>
                            <a class="dropdown-item" href="logs_sistema.php">Histórico de Ações Do Sistema</a>
                            <a class="dropdown-item" href="logs_usuario.php">Histórico Diário do Usuário</a>
                            <a class="dropdown-item" href="logs_sancoes.php">Histórico de Sanções</a>
                            <a class="dropdown-item" href="logs_acidentes_trabalho.php">Histórico de Acidentes de Trabalho</a>
                            <a class="dropdown-item" href="listar_documentos.php">Histórico de Documentos</a>';
                        } else if($perfil_usuario === 'visualizar'){
                            echo '<a class="dropdown-item" href="logs_usuario.php">Histórico Diário do Usuário</a>
                            <a class="dropdown-item" href="logs_sancoes.php">Histórico de Sanções</a>
                            <a class="dropdown-item" href="logs_acidentes_trabalho.php">Histórico de Acidentes de Trabalho</a>
                            <a class="dropdown-item" href="listar_documentos.php">Histórico de Documentos</a>';
                        }
                    ?>
                    <!-- <a class="dropdown-item" href="cadastro_relatorio_dia.php">Realizar Relátorio do Dia</a>
                    <p class="dropdown-breakline"></p>
                    <a class="dropdown-item" href="logs_sistema.php">Histórico de Ações Do Sistema</a>
                    <a class="dropdown-item" href="logs_usuario.php">Histórico Diário do Usuário</a>
                    <a class="dropdown-item" href="logs_sancoes.php">Histórico de Sanções</a>
                    <a class="dropdown-item" href="logs_acidentes_trabalho.php">Histórico de Acidentes de Trabalho</a>
                    <a class="dropdown-item" href="listar_documentos.php">Histórico de Documentos</a> -->
                </div>
            </div>
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Contratos
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <?php
                        if($perfil_usuario === 'master'){
                            echo '<a class="dropdown-item" href="cadastro_contrato_colaborador.php">Cadastrar Contrato</a>
                            <p class="dropdown-breakline"></p>
                            <a class="dropdown-item" href="listar_contratos.php">Contratos Cadastrados</a>
                            <a class="dropdown-item" href="listar_desligamentos.php">Desligamentos Cadastrados</a>';
                        } else if($perfil_usuario === 'visualizar'){
                            echo '<a class="dropdown-item" href="listar_contratos.php">Contratos Cadastrados</a>
                            <a class="dropdown-item" href="listar_desligamentos.php">Desligamentos Cadastrados</a>';
                        } else if($perfil_usuario === 'cadastro'){
                            echo '<a class="dropdown-item" href="cadastro_contrato_colaborador.php">Cadastrar Contrato</a>';
                        }
                    ?>
                    <!-- <a class="dropdown-item" href="cadastro_contrato_colaborador.php">Cadastrar Contrato</a>
                    <p class="dropdown-breakline"></p>
                    <a class="dropdown-item" href="listar_contratos.php">Contratos Cadastrados</a>
                    <a class="dropdown-item" href="listar_desligamentos.php">Desligamentos Cadastrados</a> -->
                </div>
            </div>
        </div>
        <div class="nav navbar_right">
            <div class="itens">
                <div class="text navbar-btns">
                    <a class="navbar-btn" href="login_recovery.php?recovery=1&app=1&email=<?php echo $email_usuario; ?>"><i class="bi bi-person icon"></i> <?php echo $nome_colaborador;?></a>
                </div>
                <div class="navbar-btns">
                    <a class="navbar-btn" href="logout.php"><i class="bi bi-box-arrow-right icon"></i></a>
                </div>
            </div>
        </div>
        <?php
        } else {
        ?>
        <div class="nav navbar_left">
        </div>
        <?php
        }
        ?>
    </div>
</nav>
</body>    
</html>