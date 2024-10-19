<?php
    @session_start();
    
    if(isset($_SESSION['verified'])){
        $id_usuario = $_SESSION['id_usuario'];
    } else if(isset($_SESSION['logged'])){
        if(isset($_SESSION['2FA_valido']) && $_SESSION['2FA_valido']['status'] === false){
            $id_usuario = $_SESSION['id_usuario'];
        } else {
            echo "<script>window.location.href = 'index.php'</script>";
            die();
        }
    } else {
        echo "<script>window.location.href = 'login.php'</script>";
        die();
    }

    if(@$_SESSION['openWindow'] === true){
        echo "<a id='return'>2FA_page</a>";
        $_SESSION['openWindow'] = null;
        die();
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticação por Dois Fatores</title>
    <link rel="stylesheet" href="assets/css/primeiro_login.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<?php require_once "navbar.php"; ?>
<body>
    <div class="div-center all-center">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post" id="form2FA">
            <div class="card card-body mt-5">
                <h4>Autenticação por Dois Fatores</h4>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input style="height: 50px;" class="form-control" type="text" name="codigo" id="codigo" placeholder="Código:" required>
                            <label for="codigo">Código:</label>
                        </div>
                    </div>
                </div>
                <div class="btns-group">
                    <button class="btn-enviar" type="submit" nome="enviar" id="enviar">ENVIAR</button>
                </div>
            </div>
        </form>
        <div class="responseMsg alert"></div>
    </div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/login.js"></script>
</body>
</html>

<?php 

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once "assets/classes/usuario.php";

        $usuario = new Usuario();
        $OTP_code = $_POST['codigo'];

        $usuario->login($id_usuario, $OTP_code);
    }

?>