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
    <title>Exames</title>
    <link rel="stylesheet" href="assets/css/cadastro_acidentes_trabalho.css">
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
        require_once "assets/classes/colaborador.php";
        $class_colaborador = new Colaborador();
    ?>
    <form action='<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>' method='' id='formExame'>
        <div class="card card-body mt-5">
            <h1>Cadastro de Exames</h1>
            <div class="form-step">
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <?php
                                if(isset($_GET['id_colaborador'])){
                                    $colaborador = $class_colaborador->listarColaboradores($_GET['id_colaborador'])[0];
                                }
                            ?>
                            <input style="height: 50px; border-radius: 5px 0 0 5px;" class="form-control" type="text" name="nome_colaborador" id="nome_colaborador" placeholder="Selecione o Colaborador:" value="<?php echo @$colaborador['nome_colaborador']; ?>" readonly>
                            <label for="nome_colaborador">Selecione o Colaborador:</label>
                            <input type="text" name="id_colaborador" id="id_colaborador" hidden value="<?php echo @$colaborador['id_colaborador']; ?>">
                        </div>           
                    </div>
                    <div class="col" style="padding: 0; margin-left: calc(var(--bs-gutter-x)* .5* -1); margin-right: 7px; flex: 0 0 0%;">
                        <button type="button" data-toggle='modal' data-target='#viewModal' style="border: none; background-color: #1f84d6; color: white; padding: 15px 17px; border-radius: 0 5px 5px 0;" ><i class="bi bi-search"></i></button>
                        <div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='viewModalLabel' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-scrollable modal-lg'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <p class='modal-title' id='viewModal'>Pesquise o Colaborador</p>
                                    </div>
                                    <div class='modal-body'>
                                        <input class="form-control" type="text" id="input_busca_colaborador" placeholder="Digite..." style="height: 50px;">
                                        <br>
                                        <table class="table table-hover table-striped">
                                            <thead style="background-color: #1f84d6; color: white;">
                                                <tr>
                                                    <th>ID Colaborador</th>
                                                    <th>Nome Colaborador</th>
                                                    <th>CPF Colaborador</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tabela_listagem_colaborador" id="tabela_listagem_colaborador">
                                            <?php
                                                    $colaboradores = $class_colaborador->listarColaboradores();

                                                    if(count($colaboradores) > 0){
                                                        foreach($colaboradores as $colaborador){
                                                            $situacao = $colaborador['situacao'];
                                                            if($situacao === 1){
                                                                $id = $colaborador['id_colaborador'];
                                                                $nome = ucwords(strtolower($colaborador['nome_colaborador']));
                                                                $cpf = $colaborador['cpf'];
                                                                echo "
                                                                <tr>
                                                                    <td id='id_colaborador_td'>$id</td>
                                                                    <td id='nome_colaborador_td'>$nome</td>
                                                                    <td id='cpf_colaborador_td'><script>document.write(formatText('$cpf', 'cpf'));</script></td>
                                                                    <td><button type='button' id='$id' class='btn btn-primary btn-sm btn_editar_colaborador'>Selecionar</button></td>
                                                                </tr>";
                                                            }
                                                        }
                                                    } else {
                                                        echo "<script>document.querySelector('#content').innerHTML = `<div class='alert alert-dark' role='alert'>Nenhum registro encontrado!</div>`</script>";
                                                    }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class='form-floating mb-5'>
                            <input style='height: 50px;' class='form-control' type='text' name='relacao_clinicas' id='relacao_clinicas' placeholder='Clínica:'>
                            <label for='relacao_clinicas'>Clínica:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class='form-floating mb-5'>
                            <input style='height: 50px;' class='form-control' type='text' name='telefone_clinicas' id='telefone_clinicas' placeholder='Telefone:'>
                            <label for='telefone_clinicas'>Telefone:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class='form-floating mb-5'>
                            <input style='height: 50px; font-weight: 700;' class='form-control' type='date' name='data_ultimo_exame' id='data_ultimo_exame'>
                            <label for='data_ultimo_exame'>Data do Último Exame:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class='form-floating mb-5'>
                            <input style='height: 50px; font-weight: 700;' class='form-control' type='date' name='data_agendamento' id='data_agendamento'>
                            <label for='data_agendamento'>Data do Agendamento:</label>
                        </div>
                    </div>
                </div>
            </div>
            <center><input type="submit" style="width: 200px;" nome="cadastrar" id="cadastrar" value ="Cadastrar"></center>
        </div>
        <div class="responseMsg alert"></div>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/exame.js"></script>
    </form>
    <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            require_once "assets/classes/exame.php";
            $class_exame = new Exame();
            $class_exame->__set("id_colaborador", $_POST['id_colaborador']);
            $class_exame->__set("relacao_clinicas", $_POST['relacao_clinicas']);
            $class_exame->__set("telefone_clinicas", $_POST['telefone_clinicas']);
            $class_exame->__set("data_ultimo_exame", date("Y-m-d", strtotime($_POST['data_ultimo_exame'])));
            $class_exame->__set("data_agendamento", date("Y-m-d", strtotime($_POST['data_agendamento'])));
            $class_exame->AddExame();
        }
    ?>
</body>    
</html>
