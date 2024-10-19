<?php
    @session_start();
    if(!isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'login.php'</script>";
        die();
    }
    
    $allowed_access = ['master', 'cadastro'];
    $perfil_usuario = $_SESSION['perfil_usuario'];
    $hasAccess = in_array($perfil_usuario, $allowed_access);
?> 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Empresa</title>
    <link rel="stylesheet" href="assets/css/cadastro_empresa.css">
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
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="formEmpresa">
        <div class="card card-body mt-5" style="padding: 10px 60px 50px 50px;">

            <h1>Cadastro de Empresas</h1>
            <br>
            <h4><b><p>Dados da Empresa</p></b></h4>
            <div class="form-step">
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="cnpj" id="cnpj" placeholder="CNPJ:" >
                            <label for="cnpj">CNPJ:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="nome_empresa" id="nome_empresa" placeholder="Nome da Empresa:" >
                            <label for="nome_empresa">Nome da Empresa:</label>
                        </div>
                    </div>
                </div>

                <h4><b><p>Endereço da Empresa</p></b></h4>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="cep" id="cep" placeholder="CEP:" >
                            <label for="cep">CEP:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="rua" id="rua" placeholder="rua:" >
                            <label for="rua">Rua:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="numero" id="numero" placeholder="Número:" >
                            <label for="numero">Número:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="complemento" id="complemento" placeholder="Complemento:" >
                            <label for="complemento">Complemento:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="bairro" id="bairro" placeholder="bairro:" >
                            <label for="bairro">Bairro:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="cidade" id="cidade" placeholder="Cidade:" >
                            <label for="cidade">Cidade:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="estado" id="estado" placeholder="Estado:" >
                            <label for="estado">Estado(UF):</label>
                        </div>
                    </div>
                </div>

                <h4><b><p>Contatos</p></b></h4>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="email" name="email" id="email" placeholder="Email:" >
                            <label for="email">Email:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="telefone" id="telefone" placeholder="Telefone:" >
                            <label for="telefone">Telefone:</label>
                        </div>
                    </div>
                </div>
                <center>
                    <input id="enviar" class="btn btn-primary" type="button" style="width: 200px;" nome="cadastrar" id="cadastrar" value="Cadastrar">
                </center>

            </div>
        </div>
        <div class="responseMsg alert"></div>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/cadastro_empresa.js"></script>
    </form>
</body>    
</html>
<?php
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        require_once "assets/classes/empresa.php";
        $empresa = new Empresa();
        $empresa->__set("cnpj",$_POST['cnpj']);
        $empresa->__set("nome_empresa",$_POST['nome_empresa']);
        $empresa->__set("cep",$_POST['cep']);
        $empresa->__set("bairro",$_POST['bairro']);
        $empresa->__set("cidade",$_POST['cidade']);
        $empresa->__set("estado",$_POST['estado']);
        $empresa->__set("rua",$_POST['rua']);
        $empresa->__set("numero",$_POST['numero']);
        $empresa->__set("completo",$_POST['complemento']);
        $empresa->__set("email",$_POST['email']);
        $empresa->__set("telefone",$_POST['telefone']);
        
        $empresa->CadEmpresa();
    }
?>