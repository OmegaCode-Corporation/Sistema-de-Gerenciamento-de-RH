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
    <title>Acidentes de Trabalho</title>
    <link rel="stylesheet" href="assets/css/cadastro_acidentes_trabalho.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/cadastro_colaborador.js"></script>
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
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="formAcidenteTrabalho" enctype="multipart/form-data">
        <div class="card card-body mt-5">
            <h1>Cadastro de Acidentes</h1>
            <div class="form-step">
                <div class="row mb-3">
                    <div class="col">
                        <label for="descricao" class="form-label" style="font-size: 18px; font-weight: 700; margin: 10px 0">Escreva a descrição do acidente:</label>
                        <textarea class="form-control" name="descricao" id="descricao"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-5">
                        <label class="label_select" for="afastamento_inss">Afastamento INSS?</label>                    
                        <select class="form-select form-select-lg mb-3" name="afastamento_inss" id="afastamento_inss">
                            <option value="" selected>Selecione:</option>
                            <option value="sim">Sim</option>
                            <option value="nao">Não</option>
                        </select>                        
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="date" name="data_inicio" id="data_inicio" placeholder="Data Início:">
                            <label for="data_inicio">Data do Início:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="date" name="data_termino" id="data_termino" placeholder="Data Término:">
                            <label for="data_termino">Data do Término:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
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
                        <button class="btn_modal" type="button" data-toggle='modal' data-target='#viewModal'><i class="bi bi-search"></i></button>
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
                        <div class="mb-3 input-group">
                            <h4><b>Selecione o arquivo CAT:</b></h4>
                            <input class="form-control form-control-lg" type="file" name="cat_file" id="file" style="height: 27px !important; padding: 0.475rem .75rem; outline: none;">
                        </div>
                    </div>
                </div>              
                <br>
                <center><input type="button" style="width: 200px;" nome="cadastrar" id="cadastrar" value="Cadastrar"></center>
            </div>
        </div>
        <div class="responseMsg alert"></div>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/cadastro_acidentes_trabalho.js"></script>
    </form>
    <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $descricao = $_POST['descricao'];
            $afastamento_inss = $_POST['afastamento_inss'];
            $data_inicio = $_POST['data_inicio'];
            $data_termino = $_POST['data_termino'];
            $id_colaborador = $_POST['id_colaborador'];
            $cat_file = $_FILES['cat_file'];

            $class_historico = new Historico();
            $class_historico->__set('tabela', 'historico_acidentes_trabalho');
            $class_historico->__set('descricao', $descricao);
            $class_historico->__set('afastamento_inss', $afastamento_inss);
            $class_historico->__set('data_inicio', $data_inicio);
            $class_historico->__set('data_termino', $data_termino);
            $class_historico->__set('data_movimentacao', date('Y-m-d H:i:s'));
            $class_historico->__set('id_colaborador', $id_colaborador);
            $class_historico->__set('id_usuario', $_SESSION['id_usuario']);
            $class_historico->__set('cat_file', $cat_file);
            $class_historico->adicionarHistorico();
        }
    ?>
</body>    
</html>