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
    <title>Contrato do Colaborador</title>
    <link rel="stylesheet" href="assets/css/contrato_colaborador.css">
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
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" id="formContratoColaborador" enctype="multipart/form-data">
        <script src="assets/js/contrato_colaborador.js"></script>
        <div class="card card-body mt-5">
            <h1>Contrato Colaborador</h1>
            <br>
            <p style="font-size: 1.75rem;">Preencha com os dados do contrato:</p>
            <div class="progressbar">
                <div class="progress" id="progress"></div>
                <div class="progress-step progress-step-active" data-title="Gerais"></div>
                <div class="progress-step" data-title="Familiar"></div>
                <div class="progress-step" data-title="Contrato"></div>
            </div>
            <div class="form-step form-step-active">
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px; border-radius: 5px 0 0 5px;" class="form-control" type="text" name="nome_colaborador" id="nome_colaborador" placeholder="Selecione o Colaborador:" readonly>
                            <label for="nome_colaborador">Selecione o Colaborador:</label>
                            <input type="text" name="id_colaborador" id="id_colaborador" hidden>
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
                                        <input class="form-control" type="text" id="input_busca_colaborador" placeholder="Digite...">
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
                                                    require_once "assets/classes/colaborador.php";
                                                    $class_colaborador = new Colaborador();
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
                        <div class="form-floating mb-3">
                            <input style="height: 50px; border-radius: 5px 0 0 5px;" class="form-control" type="text" name="nome_empresa" id="nome_empresa" placeholder="Selecione a Empresa:" readonly>
                            <label for="nome_empresa">Selecione a Empresa:</label>
                            <input type="text" name="id_empresa" id="id_empresa" hidden>
                        </div>
                    </div>
                    <div class="col" style="padding: 0; margin-left: calc(var(--bs-gutter-x)* .5* -1); margin-right: 7px; flex: 0 0 0%;">
                        <button class="btn_modal" type="button" data-toggle='modal' data-target='#viewModal2'><i class="bi bi-search"></i></button>
                        <div class='modal fade' id='viewModal2' tabindex='-1' role='dialog' aria-labelledby='viewModal2Label' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-scrollable modal-lg' role='document'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <p class='modal-title' id='viewModal2'>Pesquise a Empresa</p>
                                    </div>
                                    <div class='modal-body'>
                                        <input class="form-control" type="text" id="input_busca_empresa" placeholder="Digite...">
                                        <br>
                                        <table class="table table-hover table-striped">
                                            <thead style="background-color: #1f84d6; color: white;">
                                                <tr>
                                                    <th>ID Empresa</th>
                                                    <th>CNPJ</th>
                                                    <th>Nome da Empresa</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="tabela_listagem_empresa" id="tabela_listagem_empresa">
                                                <?php
                                                require_once "assets/classes/empresa.php";
                                                $empresa = new Empresa();
                                                $dados_empresa = $empresa->DadosEmpresa();
                                                foreach($dados_empresa as $linha){
                                                    $situacao = $linha['situacao'];
                                                    if($situacao === 1){
                                                        $id_empresa = $linha['id_empresa'];
                                                        $cnpj = $linha['cnpj'];
                                                        $nome_empresa = strtoupper($linha['nome_empresa']);
                                                        echo  "
                                                            <tr>
                                                                <td id='id_empresa_td'>$id_empresa</td>
                                                                <td><script>document.write(formatText('$cnpj', 'cnpj'));</script></td>
                                                                <td id='nome_empresa_td'>$nome_empresa</td>
                                                                <td><button type='button' id='$id_empresa' class='btn btn-primary btn-sm btn_editar_colaborador'>Selecionar</button></td>
                                                            </tr>";
                                                    }
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
                        <div class="form-floating mb-3">
                            <input style="height: 50px; border-radius: 5px 0 0 5px;" class="form-control" type="text" name="nome_unidade" id="nome_unidade" placeholder="Selecione a Unidade:" readonly>
                            <label for="nome_unidade">Selecione a Unidade:</label>
                            <input type="text" name="id_unidade" id="id_unidade" hidden>
                        </div>
                    </div>
                    <div class="col" style="padding: 0; margin-left: calc(var(--bs-gutter-x)* .5* -1); margin-right: 7px; flex: 0 0 0%;">
                        <button class="btn_modal" type="button" data-toggle='modal' data-target='#viewModal3'><i class="bi bi-search"></i></button>
                        <div class='modal fade' id='viewModal3' tabindex='-1' role='dialog' aria-labelledby='viewModal3Label' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-scrollable modal-lg' role='document'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <p class='modal-title' id='viewModal3'>Pesquisa a Unidade</p>
                                    </div>
                                    <div class='modal-body'>
                                        <input class="form-control" type="text" id="input_busca_unidade" placeholder="Digite...">
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
                                                <?php
                                                require_once "assets/classes/unidade.php";
                                                $unidade = new Unidade();
                                                $dados_unidade = $unidade->listarUnidades();
                                                foreach($dados_unidade as $linha){
                                                    $situacao = $linha['situacao'];
                                                    if($situacao === 1){
                                                        $id_unidade = $linha['id_unidade'];
                                                        $empresa_unidade = strtoupper($linha['empresa_unidade']);
                                                        $endereco = ucwords(strtolower($linha['endereco_unidade']));
                                                        $cep = $linha['cep_unidade'];
                                                        $cep_formated = substr($cep, -8, -3)."-".substr($cep, -3);
                                                        echo "
                                                        <tr>
                                                            <td id='id_unidade_td'>$id_unidade</td>
                                                            <td id='nome_unidade_td'>$empresa_unidade</td>
                                                            <td id='endereco_unidade_td'>$endereco, $cep_formated;</script></td>
                                                            <td><button type='button' id='$id_unidade' class='btn btn-primary btn-sm btn_editar_colaborador'>Selecionar</button></td>
                                                        </tr>
                                                        ";
                                                    }
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
                    <div class="col-3">
                        <div class="form-floating mb-3">
                            <input style="height: 50px; font-weight: 700;" class="form-control" type="date" name="admissao" id="admissao">
                            <label for="adimissao">Admissão:</label>
                        </div>
                    </div>
                    <div class="col">
                        <label class="label_select" for="opt_fgts">Optante FGTS ?</label>                       
                        <select class="form-select form-select-lg mb-3" name="opt_fgts" id="opt_fgts">
                            <option selected>Selecione:</option>
                            <option value="sim">Sim</option>
                            <option value="nao">Não</option>
                        </select>                        
                    </div>
                    <div class="col-3">
                        <div class="form-floating mb-3">
                            <input style="height: 50px; font-weight: 700;" class="form-control" type="date" name="data_opcao" id="data_opcao">
                            <label for="data_opcao">Data Opção:</label>
                        </div>
                    </div>
                </div>
                <div class="row">              
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px;" class="form-control" type="text" name="conta_fgts" id="conta_fgts" placeholder="Conta FGTS:">
                            <label for="conta_fgts">Conta FGTS:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px;" class="form-control" type="text" name="cargo" id="cargo" placeholder="Cargo:">
                            <label for="cargo">Cargo:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px;" class="form-control" type="text" name="cbo" id="cbo" placeholder="CBO:">
                            <label for="cbo">CBO:</label>
                        </div>
                    </div>
                </div>
                <div class="row">                        
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px;" class="form-control" type="text" name="organograma" id="organograma" placeholder="Organograma:">
                            <label for="organograma">Organograma:</label>
                        </div>
                    </div>                        
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px;" class="form-control" type="text" name="remuneracao"  id="remuneracao" placeholder="Remuneração:">
                            <label for="remuneracao">Remuneração:</label>
                        </div>
                    </div>  
                    <div class="col-5">
                        <div class="form-floating mb-3">
                            <input style="height: 50px; align-items: right; text-align: left;" class="form-control" type="time" name="escala_trabalho" id="escala_trabalho">
                            <label for="escala_trabalho">Escala de Trabalho:</label>
                        </div>
                    </div>                 
                </div>              
                <div class="row">
                    <div class="col">
                        <label class="label_select" for="forma_pagamento">Forma de Pagamento:</label>                       
                        <select class="form-select form-select-lg mb-3" name="forma_pagamento" id="forma_pagamento">
                            <option selected>Selecione:</option>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="pix">Pix</option>
                            <option value="transferencia">Transferência</option>
                        </select>                        
                    </div>
                    <div class="col">
                        <label class="label_select" for="periodo_pagamento">Período de Pagamento:</label>                       
                        <select class="form-select form-select-lg mb-3" name="periodo_pagamento" id="periodo_pagamento">
                            <option selected>Selecione:</option>
                            <option value="mensal">Mensal</option>
                            <option value="quinzenal">Quinzenal</option>
                            <option value="semanal">Semanal</option>
                            <option value="diario">Diáro</option>
                        </select>                   
                    </div>
                </div>
                <div class="btns-group">
                    <a href="#" class="btn-next" style="margin-left: auto;">Avançar</a>
                </div>
            </div>
            <div class="form-step" id="familiar">
                <div class="step-familiar">               
                    <div class="doc">
                        <div class="row">
                            <div class="col-5">
                                <div class="form-floating mb-3">
                                    <input class="form-control" type="text" name="nome_familiar[]" id="nome_familiar" placeholder="Nome do Familiar:">
                                    <label for="nome_familiar">Nome do Familiar:</label>                        
                                </div>                                               
                            </div>
                            <div class="col-3">
                                <div class="form-floating mb-3">
                                    <input style="height: 50px; font-weight: 700;" class="form-control" type="date" name="nascimento[]" id="nascimento">
                                    <label for="adimissao">Nascimento:</label>
                                </div>
                            </div>                  
                            <div class="col">
                                <label class="label_select" for="parentesco">Parentesco :</label>
                                <select class="form-select form-select-lg mb-3" name="parentesco[]" id="parentesco">
                                    <option value=" " selected disabled hidden>Selecione:</option>
                                    <option value="Pai/Mãe">Pai/Mãe</option>
                                    <option value="Marido/Esposa">Marido/Esposa</option>
                                    <option value="Irmão/Irmã">Irmão/Irmã</option>
                                    <option value="Filha/Filho">Filha/Filho</option>
                                </select>
                            </div>                  
                            <a href="#" class="botao" id="adc_familiar"><i class="bi bi-plus"></i></a>
                            <br>
                        </div>                 
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
                        <center>
                            <p style="font-size: 2rem">Já possui contrato ?</p>
                            <input class="btn-check" type="radio" name="btn_ctrto" value="sim" id="sim_btn_ctrto" autocomplete="off" checked>
                            <label class="btn btn-primary" for="sim_btn_ctrto">Sim</label>
                            <input class="btn-check" type="radio" name="btn_ctrto" value="nao" id="nao_btn_ctrto" autocomplete="off">
                            <label class="btn btn-primary" for="nao_btn_ctrto">Não</label>
                        </center>
                        <br>
                    </div>
                </div>
                <div class="form_file" id="form_file">
                    <center>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="file">Selecione o arquivo do contrato: </label>
                                <input class="form-control form-control-lg"type="file" name="file" id="file" style="height: 27px !important; padding: 0.475rem .75rem; outline: none;">
                            </div>
                        </div>
                    </center>
                </div>
                <div class="btns-group">
                    <a href="#" class="btn-prev">Voltar</a>
                    <input type="button" class="btn-enviar" nome="enviar" id="enviar" value ="Enviar">
                </div>
            </div>
        </div>
        <div class="responseMsg alert"></div>
        <script src="assets/js/main.js"></script>
    </form>
</body>    
</html>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    require_once  "assets/classes/contrato.php";
    $contrato = new Contrato();

    $contrato->__set("id_colaborador",$_POST['id_colaborador']);
    $contrato->__set("id_empresa",$_POST['id_empresa']);
    $contrato->__set("id_unidade",$_POST['id_unidade']);
    $contrato->__set("admissao", date("Y-m-d", strtotime($_POST['admissao'])));
    $contrato->__set("optante_fgts",$_POST['opt_fgts']);
    $contrato->__set("data_opcao", date("Y-m-d", strtotime($_POST['data_opcao'])));
    $contrato->__set("conta_fgts",$_POST['conta_fgts']);
    $contrato->__set("cargo",$_POST['cargo']);
    $contrato->__set("cbo",$_POST['cbo']);
    $contrato->__set("organograma", $_POST['organograma']);
    $contrato->__set("remuneracao",$_POST['remuneracao']);
    $contrato->__set("forma_pagamento",$_POST['forma_pagamento']);
    $contrato->__set("periodo_pagamento",$_POST['periodo_pagamento']);
    $contrato->__set("escala_trabalho", date("H:i", strtotime($_POST['escala_trabalho'])));

    $dadosFamiliar = [];
    foreach($_POST['nome_familiar'] as $key => $value){
        array_push($dadosFamiliar, [
            'nome_familiar' => $value,
            'nascimento' => date("Y-m-d", strtotime($_POST['nascimento'][$key])),
            'parentesco' => $_POST['parentesco'][$key]
        ]);
    }
    
    $contrato->__set("dados_familiar", $dadosFamiliar);

    if($id_contrato = $contrato->CadContrato()){
        if($_POST['btn_ctrto'] == "nao"){
            echo "
            <form action='fetch_control.php' method='post' class='modal-form' id='contrato_form'>
                <input type='text' name='id_contrato' id='id_contrato' value='$id_contrato' hidden>
                <input type='text' name='id_colaborador' id='id_colaborador' value='" . $_POST['id_colaborador'] . "' hidden>
                <input type='text' name='id_unidade' id='id_unidade' value='" . $_POST['id_unidade'] . "' hidden>
                <input type='text' name='control_class' id='control_class' value='contratos' hidden>
                <input type='text' name='control_action' id='control_action' value='create' hidden>
            </form>
            <div class='responseMsg alert' style='display: none'></div>
            // <script>document.querySelector('#contrato_form').submit();</script>
            ";
            require_once  "assets/classes/documentos.php";
            $doc = new Documento();
            $doc->__set("id_contrato",$id_contrato);
            $doc->DocContrato();
        }else{
            require_once  "assets/classes/diretorio.php";
            $diretorio = new Diretorio();
            $diretorio->__set("identidade","diretorio_contrato");
            $diretorio->__set("id",$id_contrato);
            $diretorio->__set("arquivo",$_FILES["file"]);
            $diretorio->UploadArquivo();
        }
    }
}
?>