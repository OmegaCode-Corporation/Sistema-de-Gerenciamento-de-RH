<?php
    @session_start();
    if(!isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'login.php'</script>";
        die();
    }

    $allowed_access = ['developer'];
    $perfil_usuario = $_SESSION['perfil_usuario'];
    $hasAccess = in_array($perfil_usuario, $allowed_access);
?> 
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Cliente</title>
    <link rel="stylesheet" href="assets/css/cadastro_novo_cliente.css">
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
    <form action="" method="POST" id="formCadastroNovoCliente">
        <div class="card card-body mt-5">
            <h1>Cadastro de Novos Clientes</h1>
            <b><p>Dados do Cliente:</p></b>
            <div class="form-step">
                <div class="row">
                    <div class="col-11">
                        <div class="form-floating mb-5">
                            <input style="height: 50px;" class="form-control" type="text" name="nome_unidade" id="nome_unidade" placeholder="Selecione a Unidade:">
                            <label for="nome_unidade">Selecione a Unidade:</label>
                            <input type="text" name="id_unidade" id="id_unidade" hidden>
                        </div>
                    </div>
                    <div class="col-1">
                        <button type="button" data-toggle='modal' data-target='#viewModal3' style="border: none; background-color: #1f84d6; color: white; padding: 15px 20px 15px 20px; border-radius: 5px;" ><i class="bi bi-search"></i></button>
                        <div class='modal fade' id='viewModal3' tabindex='-1' role='dialog' aria-labelledby='viewModal3Label' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-scrollable modal-lg' role='document'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <p class='modal-title' id='viewModal3'>Pesquise a Unidade</p>
                                    </div>
                                    <div class='modal-body'>
                                        <input class="form-control" type="text" id="input_busca_unidade" placeholder="Digite..." style="height: 50px;">
                                        <br>
                                        <table class="table table-hover table-striped">
                                            <thead style="background-color: #1f84d6; color: white;">
                                                <tr>
                                                    <th>ID Unidade</th>
                                                    <th>Nome Unidade</th>
                                                    <th>Endereço</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tabela_listagem_unidade" id="tabela_listagem_unidade">
                                                <tr>
                                                    <td id="id_unidade_td">0001</td>
                                                    <td id="nome_unidade_td">Unidade Alpha</td>
                                                    <td id="endereco_unidade_td">São Paulo, Centro, Rua A, 123, 01000-000</td>
                                                    <td><button type="button" style="background-color: #1f84d6; color: white;">Selecionar <i class="bi bi-plus"></i></button></td>
                                                </tr>
                                                <tr>
                                                    <td id="id_unidade_td">0002</td>
                                                    <td id="nome_unidade_td">Unidade Beta</td>
                                                    <td id="endereco_unidade_td">Rio de Janeiro, Copacabana, Avenida B , 456 , 22000-000</td>
                                                    <td><button type="button" style="background-color: #1f84d6; color: white;">Selecionar <i class="bi bi-plus"></i></button></td>
                                                </tr>
                                                <tr>
                                                    <td id="id_unidade_td">0003</td>
                                                    <td id="nome_unidade_td">Unidade Omega</td>
                                                    <td id="endereco_unidade_td">Belo Horizonte, Savassi , Rua C , 789 , 30100-000</td> 
                                                    <td><button type="button" style="background-color: #1f84d6; color: white;">Selecionar <i class="bi bi-plus"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>               
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="cnpj" id="cnpj" placeholder="CNPJ:" >
                            <label for="cnpj">CNPJ:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="nome_empresa" id="nome_empresa" placeholder="Razão Social:" >
                            <label for="nome_empresa">Razão Social:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="qtd_lojas" id="qtd_lojas" placeholder="Quantidade de Lojas:" >
                            <label for="qtd_lojas">Quantidade de Lojas:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="valor_total_lojas" id="valor_total_lojas" placeholder="Valor Total das Lojas:" >
                            <label for="valor_total_lojas">Valor Total das Lojas:</label>
                        </div>
                    </div>
                </div>
                <center><input type="button" style="width: 200px;" nome="cadastrar" id="cadastrar" value ="Cadastrar"></center>
            </div>
        </div>
        <div class="responseMsg alert"></div>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/cadastro_novo_cliente.js"></script>
    </form>
</body>    
</html>
