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
    <title>Sanções</title>
    <link rel="stylesheet" href="assets/css/cadastro_sancoes.css">
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
    <form action="" method="POST" id="formCadastroSancoes">
        <div class="card card-body mt-5">
            <h1>Cadastro de Sanções</h1>
            <br>
            <div class="form-step">
                <div class="row">
                    <div class="col">
                        <label class="label_select" for="tipo_sancao">Tipo Sanção:</label>
                        <select class="form-select form-select-lg mb-5" type="text" name="tipo_sancao" id="tipo_sancao" placeholder="Tipo Sanção:">
                            <option value="" selected>Selecione:</option>
                            <option value="Advertência Verbal">Advertência Verbal</option>
                            <option value="Advertência Escrita">Advertência Escrita</option>
                            <option value="Suspensão Disciplinar">Suspensão Disciplinar</option>
                            <option value="Dispensa por Justa Causa">Dispensa por Justa Causa</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col">
                        <label for="descricao" class="form-label" style="font-size: 18px; font-weight: 700; margin: 10px 0">Escreva a descrição da Sanção:</label>
                        <textarea class="form-control" name="descricao" id="descricao"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="date" name="data_inicio" id="data_inicio" placeholder="Data Início:" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
                            <label for="data_inicio">Data do Início:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="date" name="data_termino" id="data_termino" placeholder="Data Término:" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
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
                <center>
                    <input type="button" style="width: 200px;" nome="cadastrar" id="cadastrar" value ="Cadastrar">
                </center>
            </div>
        </div>
        <div class="responseMsg alert"></div>
        <script src="assets/js/main.js"></script>
        <script src="assets/js/cadastro_sancoes.js"></script>
    </form>
    <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $tipo_sancao = $_POST['tipo_sancao'];
            $descricao = $_POST['descricao'];
            $data_inicio = $_POST['data_inicio'];
            $data_termino = $_POST['data_termino'];
            $id_colaborador = $_POST['id_colaborador'];

            $class_historico = new Historico();
            $class_historico->__set('tabela', 'historico_sancoes');
            $class_historico->__set('tipo_sancao', $tipo_sancao);
            $class_historico->__set('descricao', $descricao);
            $class_historico->__set('data_inicio', $data_inicio);
            $class_historico->__set('data_termino', $data_termino);
            $class_historico->__set('data_movimentacao', date('Y-m-d H:i:s'));
            $class_historico->__set('id_colaborador', $id_colaborador);
            $class_historico->__set('id_usuario', $_SESSION['id_usuario']);
            $class_historico->adicionarHistorico();
        }
    ?>
</body>    
</html>
