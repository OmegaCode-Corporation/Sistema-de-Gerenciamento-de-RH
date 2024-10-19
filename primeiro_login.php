<?php
    @session_start();
    if(isset($_SESSION['first_access']) && $_SESSION['first_access'] === true){
        $id_usuario = $_SESSION['id_usuario'];
        $senhas = $_SESSION['senhas'];
        $OTP = $_SESSION['2FA'];
    } else if(isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'index.php'</script>";
        die();
    } else {
        echo "<script>window.location.href = 'login.php'</script>";
        die();
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Primeiro Acesso</title>
    <link rel="stylesheet" href="assets/css/primeiro_login.css">
    <link rel="stylesheet" href="assets/css/style.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.0.0/core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/md5.js"></script>
</head>
<?php require_once "navbar.php"; ?>
<body>
    <div class="div-center all-center">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
            <div class="card card-body mt-5">
                <h4>Defina sua nova senha:</h4>
                <div class="progressbar">
                    <div class="progress" id="progress"></div>
                    <div class="progress-step progress-step-active" data-title="Nova Senha"></div>
                    <div class="progress-step" data-title="QR Code"></div>
                    <div class="progress-step" data-title="Confirmação"></div>
                </div>
                <div class="form-step form-step-active" id="form-step-1">  
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input style="height: 50px;" class="form-control" type="text" name="nova_senha" id="nova_senha" placeholder="Nova Senha:" required>
                                <label for="nova_senha">Nova Senha:</label>
                            </div>
                        </div>
                    </div>
                    <div class="btns-group">
                        <a href="#" class="btn-next">Avançar</a>
                    </div>
                </div>

                <div class="form-step">
                    <div class="row">
                        <div class="col">
                            <center><img id="qrcode" src="<?php echo $OTP['qrcode']; ?>" alt=""></center>
                        </div>
                    </div>
                    <div class="btns-group">
                        <a href="#" class="btn-prev">Voltar</a>
                        <a href="#" class="btn-next">Avançar</a>
                    </div>
                </div>

                <div class="form-step">  
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input style="height: 50px;" class="form-control" type="text" name="codigo" id="codigo" placeholder="Código: " required>
                                <label for="codigo">Código: </label>
                            </div>
                        </div>
                    </div>
                    <div class="btns-group">
                        <a href="#" class="btn-prev">Voltar</a>
                        <button class="btn-enviar" type="submit" nome="enviar" id="enviar">ENVIAR</button>
                    </div>
                </div>

            </div>
            <script>
                const senhas = {
                    'senha_padrao': "<?php echo $senhas['senha_padrao'] ?>",
                    'senha_atual': "<?php echo $senhas['senha_atual'] ?>"
                }
            </script>
        </form> 
        <div class="responseMsg alert"></div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>

<?php 
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once "assets/classes/usuario.php";

        $usuario = new Usuario();
        $nova_senha = md5($_POST['nova_senha']);
        $OTP_code = $_POST['codigo'];

        $usuario->atualizarAcesso($id_usuario, $nova_senha, $OTP['secret'], $OTP_code);
    }
?>