<?php
    @session_start();
    if(!isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'login.php'</script>";
        die();
    }

    $allowed_access = ['master'];
    $perfil_usuario = $_SESSION['perfil_usuario'];
    $hasAccess = in_array($perfil_usuario, $allowed_access);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link rel="stylesheet" href="assets/css/primeiro_login.css">
    <link rel="stylesheet" href="assets/css/style
    .css">
</head>
<?php require_once "navbar.php"; ?>
<body>
    <?php 
        if(!$hasAccess){
            echo "
            <div class='error-block'>
                <div class='responseMsg alert' role='alert' style='display: block; font-size: 30px;'>
                    <p>Seja Bem-Vindo ao Sistema da Gerenciamento de RH!</p>
                </div>
            </div>";
            die();
        }
    ?>
    <iframe src="https://lookerstudio.google.com/embed/reporting/94d425b4-0700-420a-a064-34c6140c8570/page/uP29D" frameborder="0" style="border:0; width: 100%; height: 87dvh;" allowfullscreen sandbox="allow-downloads allow-storage-access-by-user-activation allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"></iframe>
</body>
</html>
