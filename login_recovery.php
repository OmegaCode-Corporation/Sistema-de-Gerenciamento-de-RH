<?php
    @session_start();
    @$recovery = isset($_GET['recovery']);
    if(isset($_SESSION['logged']) && !$recovery){
        echo "<script>window.location.href = 'index.php'</script>";
        die();
    } else if(!isset($_GET['email']) || !isset($_GET['app'])){
        echo "<script>window.location.href = 'login.php'</script>";
        die();
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $recovery ? 'Alteração de Senha' : 'Recuperação de Acesso' ?></title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/primeiro_login.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<?php require_once "navbar.php"; ?>
<body>       
    <div class="div-center all-center">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post" id="formlogin" <?php echo $recovery ? 'style="height: 300px"' : '' ?>>
            <div id="flex">
                <i class="bi bi-person-fill"></i><p><b><?php echo $recovery ? 'Alteração de Senha' : 'Recuperação de Acesso' ?></b></p>
            </div>
            <input type="text" name="email" value="<?php echo @$_GET['email'] ?>" hidden>
            <input type="text" name="app" value="<?php echo @$_GET['app'] ?>" hidden>
            <?php if(@$_GET['app'] == 1){ ?>
                <div class="senha_wrp">
                    <div class="form-floating mb-3">
                        <input class="form-control" type="password" name="nova_senha" id="senha" placeholder="Digite sua nova senha:" required>
                        <label for="senha">Nova Senha:</label>
                        <button type="button" id="versenha_btn"><i class="bi bi-eye" id="versenha_icon"></i></button>
                    </div>
                </div>
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" name="2FA" id="2FA" placeholder="Digite seu código 2FA:" required>
                    <label for="senha">Código 2FA:</label>
                </div>
                <?php
                    echo !$recovery ? "<button class='opts_btns'><a href='login_recovery.php?app=0&email=" . @$_GET['email'] . "'>Não tem acesso ao app de autenticação?</a></button>" : "";
                ?>
            <?php } else { ?>
                <div class="form-floating mb-3">
                    <input class="form-control" type="text" name="recovery_code" id="recovery_code" placeholder="Digite seu Código de Recuperação:" required>
                    <label for="senha">Código de Recuperação:</label>
                </div>
                <button class="opts_btns"><a href="login_recovery.php?app=1&email=<?php echo @$_GET['email'] ?>">Tenho acesso ao app de autenticação</a></button>
            <?php } ?>
            <?php
                echo !$recovery ? "<button class='opts_btns'><a href='login.php'>Realizar Login</a></button>" : "";
            ?>
            <!-- <button class="opts_btns"><a href="login.php">Realizar Login</a></button> -->
            <button type="submit" class="btn" id="btn_submit">Alterar</button>
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
        $class_usuario = new Usuario();
        $email = $_POST['email'];
        $dadosUser = $class_usuario->listarUsuarios(null, null, $email)[0];    
        if(@$_POST['app'] == 1){
            $senha_padrao = $dadosUser['senha_padrao'];
            $senha_atual = $dadosUser['senha'];
            $nova_senha = md5($_POST['nova_senha']);

            if($nova_senha === $senha_padrao) {
                echo "<script>finishAction('A nova senha deve ser diferente da original!', null, 0);</script>";
                die();
            } else if($nova_senha === $senha_atual) {
                echo "<script>finishAction('A nova senha deve ser diferente da atual!', null, 0);</script>";
                die();
            }
            
            $id_user = $dadosUser['id_usuario'];
            $OTP_secret = $dadosUser['OTP_secret'];
            $OTP_code = $_POST['2FA'];
            $class_usuario->atualizarAcesso($id_user, $nova_senha, $OTP_secret, $OTP_code);
        } else {
            $recovery_code = $_POST['recovery_code'];
            $class_usuario->loginRecovery($dadosUser['id_usuario'], $recovery_code);
        }
    }
?>