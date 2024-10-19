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
    <title>Edição de Empresa</title>
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

        require_once "assets/classes/db.php";
        require_once "assets/classes/empresa.php";

        $empresa = new Empresa();
        $empresas = $empresa->DadosEmpresa($_GET['id']);
        if(!empty($empresas)) {
            $dados_empresa = $empresas[0];
        }
    ?>
    <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'])?>" style="margin-top: 5%;" id="formEmpresa" method="POST">
        <input type="hidden" name="id_empresa" value="<?php echo $_GET['id']; ?>">
        <div class="card card-body mt-5" style="padding: 10px 60px 50px 50px;">
            <h1>Edição de Registro de Empresa</h1>
            <br>
            <p style="font-size: 1.75rem;"></p>
            <div class="row">
                <div class="col">
                    <div class="form-floating mb-5">
                        <input class="form-control" type="text" name="cnpj" id="cnpj" value="<?php echo $dados_empresa['cnpj']; ?>" readonly>
                        <label for="cnpj">CNPJ:</label>
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating mb-5">
                        <input class="form-control" type="text" name="nome_empresa" id="nome_empresa" value="<?php echo strtoupper($dados_empresa['nome_empresa']); ?>" readonly>
                        <label for="nome_empresa">Nome da Empresa:</label>
                    </div>
                </div>
            </div>
                <b><p>Endereço</p></b>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="cep" id="cep" placeholder="CEP:" value="<?php echo $dados_empresa['cep']; ?>" required>
                            <label for="cep">CEP:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="cidade" id="cidade" placeholder="Cidade:" value="<?php echo ucwords(strtolower($dados_empresa['cidade'])); ?>" required>
                            <label for="cidade">Cidade:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="estado" id="estado" placeholder="Estado:" value="<?php echo strtoupper($dados_empresa['estado']); ?>" required>
                            <label for="estado">Estado:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="bairro" id="bairro" placeholder="Bairro:" value="<?php echo ucwords(strtolower($dados_empresa['bairro'])); ?>" required>
                            <label for="estado">Bairro:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="rua" id="endereco" placeholder="Endereco:" value="<?php echo ucwords(strtolower($dados_empresa['rua'])); ?>" required>
                            <label for="endereco">Logradouro:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="numero" id="numero" placeholder="Número:" value="<?php echo $dados_empresa['numero']; ?>" required>
                            <label for="numero">Número:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="complemento" id="complemento" placeholder="Complemento:" value="<?php echo ucwords(strtolower($dados_empresa['complemento'])); ?>" required>
                            <label for="complemento">Complemento:</label>
                        </div>
                    </div>
                </div>
                <b><p>Contato:</p></b>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="email" name="email" id="email" placeholder="Email:" value="<?php echo $dados_empresa['email']; ?>" required>
                            <label for="email">Email:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="telefone" id="telefone" placeholder="Telefone:" value="<?php echo $dados_empresa['telefone']; ?>" required>
                            <label for="telefone">Telefone:</label>
                        </div>
                    </div>
                </div>
                <center><button type="submit" class="btn btn-primary" style="width: 200px;" nome="enviar" id="enviar">Enviar</button></center>
            </div>
        </div>
        <script src="assets/js/cadastro_empresa.js"></script>
        <script src="assets/js/main.js"></script>
    </form>
    <script>
        window.onload = () => {
            const formEmpresa = document.querySelector("#formEmpresa")

            formEmpresa.querySelectorAll("input").forEach(element => {
                let minLength;
                let maxLength;

                switch (element.id) {
                    case 'cnpj':
                        maxLength = 14
                        blockText(maxLength, true, element)
                        element.value = formatText(element.value, element.id, 'remove')
                        switch (element.value.length) {
                            case maxLength:
                                element.value = formatText(element.value, element.id)
                                break;
                            case maxLength+1:
                                element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                                break;
                            default:
                                break;
                        }
                        break;
                    case 'cep':
                        maxLength = 8
                        blockText(maxLength, true, element)
                        element.value = formatText(element.value, element.id, 'remove')
                        switch (element.value.length) {
                            case 8:
                                if(!element.value.includes('-')){
                                    element.value = formatText(element.value, element.id)
                                }
                                break;
                            case maxLength:
                                element.value = formatText(element.value, element.id)
                                break;
                            case maxLength+1:
                                element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                                break;
                            default:
                                break;
                        }
                        break;
                    case 'telefone':
                        maxLength = 11
                        blockText(maxLength, true, element)
                        element.value = formatText(element.value, element.id, 'remove')
                        switch (element.value.length) {
                            case 10:
                                element.value = formatText(element.value, 'telefone_fixo')
                                break;
                            case maxLength:
                                element.value = formatText(element.value, element.id)
                                break;
                            case maxLength+1:
                                element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                                break;
                            default:
                                break;
                        }
                        break;
                    case 'numero':
                        maxLength = 4
                        blockText(maxLength, true, element)
                        switch (element.value.length) {
                            case maxLength+1:
                                element.value = element.value.slice(0, element.value.length-1)
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        break
                }
            })
        }
    </script>
</body>    
</html>
<?php
if($_SERVER['REQUEST_METHOD'] == "POST"){
    // require_once "assets/classes/empresa.php";
    $empresa = new Empresa();
    $empresa->__set("id_empresa",$_POST['id_empresa']);
    $empresa->__set("cep",$_POST['cep']);
    $empresa->__set("cidade",$_POST['cidade']);
    $empresa->__set("estado",$_POST['estado']);
    $empresa->__set("bairro",$_POST['bairro']);
    $empresa->__set("rua",$_POST['rua']);
    $empresa->__set("numero",$_POST['numero']);
    $empresa->__set("complemento",$_POST['complemento']);
    $empresa->__set("email",$_POST['email']);
    $empresa->__set("telefone",$_POST['telefone']);

    $empresa->AtualizarEmpresa();
}
?>