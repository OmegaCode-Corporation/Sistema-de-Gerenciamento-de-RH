<?php
    @session_start();
    if(isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'index.php'</script>";
        die();
    } else if($_SERVER['REQUEST_METHOD'] == "GET"){
        @session_destroy();
        @session_start();
        @$_SESSION['route'] = $_SERVER['HTTP_REFERER'] !== null ? (str_contains(basename($_SERVER['HTTP_REFERER']), '.php') ? basename($_SERVER['HTTP_REFERER']) : 'index.php') : 'index.php';
    }
    
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Usuário</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<?php require_once "navbar.php"; ?>
<body>       
    <div class="div-center all-center" style="height: calc(100dvh - 120px); margin: auto; margin-top: 0;">
        <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post" id="formlogin">
            <div id="flex">
                <i class="bi bi-person-fill"></i><p><b>Login Administrativo</b></p>
            </div>
            <div class="form-floating mb-3">
                <input class="form-control" type="email" name="email" id="email" placeholder="Digite o seu email:" required> 
                <label for="email">E-mail:</label>
            </div>
            <div class="senha_wrp">
                <div class="form-floating mb-3">
                    <input class="form-control" type="password" name="senha" id="senha" placeholder="Digite sua senha:" required>
                    <label for="senha">Senha:</label>
                    
                    <button type="button" id="versenha_btn"><i class="bi bi-eye" id="versenha_icon"></i></button>
                </div>
            </div>
            <button type="button" class="btn btn-link opts_btns" onclick="goToRecovery()">Esqueceu sua senha?</button>
            <button type="submit" class="btn" id="btn_submit">Entrar</button>
        </form>
        <div class="responseMsg alert"></div>
    </div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/login.js"></script>
    <script>
        function goToRecovery() {
            const email_field = document.getElementById('email');
            if(email_field.value === '') {
                email_field.focus();
                responseMsgField.innerHTML = 'Preencha o campo de e-mail para prosseguir.';
                responseMsgField.classList.add('alert-danger');
                responseMsgField.style.display = 'block';
            } else {
                const formData = new FormData()
                formData.append('control_class', 'usuarios')
                formData.append('control_action', 'read')
                formData.append('-37vXj0zPm10RI', true)
                formData.append('search', email_field.value)
                fetch('fetch_control.php', {
                    method: 'POST',
                    body: formData
                }).then(res => res.text()).then(text => {
                    const response = JSON.parse(text);
                    if (Number(response[0]) === 0) {
                        email_field.focus();
                        responseMsgField.innerHTML = 'Não foi encontrado nenhum usuário com o e-mail fornecido!';
                        responseMsgField.classList.add('alert-danger');
                        responseMsgField.style.display = 'block';
                    } else if(response[1][0].situacao === 0) {
                        responseMsgField.innerHTML = 'Seu Acesso Está Bloqueado! Entre em contato com o RH.';
                        responseMsgField.classList.add('alert-danger');
                        responseMsgField.style.display = 'block';
                    } else {
                        window.location.href = `login_recovery.php?app=1&email=${email_field.value}`
                    }
                })
            }

            setTimeout(() => {
                responseMsgField.style.display = 'none';
            }, 10000)
        }
    </script>
</body>    
</html>

<?php 
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once "assets/classes/usuario.php";

        $usuario = new Usuario();
        $email = $_POST['email'];
        $senha = md5($_POST['senha']);

        if(!$usuario->verifyLogin($email, $senha)){
            @session_destroy();
            die();
        }
    }
?>