<?php
    @session_start();
    if(!isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'login.php'</script>";
        die();
    }
    
    $allowed_access = ['master', 'cadastro', 'visualizar'];
    $perfil_usuario = $_SESSION['perfil_usuario'];
    $hasAccess = in_array($perfil_usuario, $allowed_access);
?> 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório do Dia</title>
    <link rel="stylesheet" href="assets/css/relatorio_dia.css">
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
    <div class="div-center">
        <form action="" method="POST" id="formRelatorioDia">
            <div class="card card-body mt-5">
                <h1>Relatório do Dia</h1>
                <div class="form-step">
                    <div class="row">
                        <div class="col">
                            <center><label for="descricao" class="form-label" style="font-size: 18px; font-weight: 700; margin: 10px 0">Escreva um resumo de suas atividades:</label></center>
                            <textarea style="width:100%; height:100px" class="form-control" name="descricao" id="descricao"></textarea>
                        </div>
                    </div>
                    <br>
                    <center><input type="button" style="width: 200px;" nome="enviar" id="enviar" value ="Enviar"></center>
                </div>
            </div>
        </form>
        <div class="responseMsg alert"></div>
    </div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/relatorio_dia.js"></script>
    <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            require_once "assets/classes/historico.php";
            $descricao = $_POST['descricao'];

            $class_historico = new Historico();
            $class_historico->__set('tabela', 'historico_usuarios');
            $class_historico->__set('descricao', $descricao);
            $class_historico->__set('data_movimentacao', date('Y-m-d H:i:s'));
            $class_historico->__set('id_colaborador', $_SESSION['id_colaborador']);
            $class_historico->__set('id_usuario', $_SESSION['id_usuario']);
            $class_historico->adicionarHistorico();
        }
    ?>
</body>    
</html>