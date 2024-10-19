<?php
    @session_start();
    if(!isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'login.php'</script>";
        die();
    }
    
    $allowed_access = ['master', 'visualizar'];
    $perfil_usuario = $_SESSION['perfil_usuario'];
    $hasAccess = in_array($perfil_usuario, $allowed_access);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Colaboradores</title>
    <link rel="stylesheet" href="assets/css/listar_colaboradores.css">
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
    ?>
    <div class="card card-body mt-5 container" id="cardbox">
        <center><h1>Listagem de Colaboradores</h1></center>
        <form method="GET" action="" id="form-busca">
            <div class="flex" style="display: flex;">
                <label class="texto" for="input-busca" style="font-size: 26px;">O que você procura?</label>
                <div class="dropdown" id="searchInfo"><i class="bi bi-info-circle" style="font-size: 26px;"></i>
                    <div class="dropdown-content" id="searchInfo_content">
                        <p> <p>Opções de Procura:</p><b>ID Colaborador;</b> <br> <b>Nome Colaborador;</b> <br> <b>CPF;</b> <br> <b>RG;</b><br>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <input id="input-busca" type="text" value="<?php echo isset($_GET['pesquisa']) ? $_GET['pesquisa'] : "" ?>" name="pesquisa" class="form-control mt-3 mb-3" placeholder="Digite..." style="border-radius: .375rem 0 0 .375rem; margin-left: 7px">
                </div>
                <div class="col" style="padding: 0; margin-top: 10px; margin-left: calc(var(--bs-gutter-x)* .5* -1); margin-right: 15px; flex: 0 0 0%;">
                    <?php
                    echo isset($_GET['pesquisa']) && !empty($_GET['pesquisa']) ? "<button id='btn-clean' type='button' style='border: none; background-color: #dc3545; color: white; padding: 7px 10px; border-radius: 0 5px 5px 0;'><i class='bi bi-x-lg'></i></button>" : "<button id='btn-busca' type='submit' style='border: none; background-color: #1f84d6; color: white; padding: 7px 10px; border-radius: 0 5px 5px 0;'><i class='bi bi-search'></i></button>";
                    ?>
                </div>
            </div>
        </form>
        <?php
            require_once "assets/classes/db.php";
            require_once "assets/classes/colaborador.php";
            require_once "assets/classes/diretorio.php";

            $class_db = new Conexao_BD();
            $class_colaborador = new Colaborador();
            $class_usuario = new Usuario();
            $class_epi = new EPI();
            $class_diretorio = new Diretorio();
        
            $registros_por_pagina = 10;
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($page - 1) * $registros_por_pagina;

            if(isset($_GET['pesquisa']) && !empty($_GET['pesquisa'])){
                $pesquisa = $_GET['pesquisa'];
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM colaboradores WHERE id_colaborador = '$pesquisa' OR cpf = '$pesquisa' OR rg = '$pesquisa' OR nome_colaborador LIKE '%$pesquisa%'")[0]['total'] / $registros_por_pagina);
                $colaboradores = $class_colaborador->listarColaboradores(null, $pesquisa, $offset, $registros_por_pagina);
            } else {
                $total_pages = ceil($class_db->fetchData("SELECT COUNT(*) AS total FROM colaboradores")[0]['total'] / $registros_por_pagina);
                $colaboradores = $class_colaborador->listarColaboradores(null, null, $offset, $registros_por_pagina);
            }

            if(count($colaboradores) > 0){
        ?>
            <div class="table-responsive">    
                <table class="table table-hover table-striped">
                    <thead style="background-color: #1f84d6; color: white;">
                        <tr>
                            <th>ID Colaborador</th>
                            <th>Nome Colaborador</th>
                            <th>CPF Colaborador</th>
                            <th>RG Colaborador</th>
                            <th>Orgão Emissor</th>
                            <th>Emissão RG</th>
                            <th>Situação</th>
                            <th>Ver Outras Info.</th>
                            <th>Mais Opções</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-colaborador" class="tabela-listagem">
        <?php
                        foreach($colaboradores as $colaborador){

                            $id_colaborador = $colaborador['id_colaborador'];
                            $nome_colaborador = ucwords(strtolower($colaborador['nome_colaborador']));
                            $cpf = $colaborador['cpf'];
                            $rg = $colaborador['rg'];
                            $orgao = strtoupper($colaborador['orgao']);
                            $rg_uf = ucwords(strtoupper($colaborador['rg_uf']));
                            $emissao_rg = date("d/m/Y", strtotime($colaborador['emissao_rg']));
                            $mae_colaborador = ucwords(strtolower($colaborador['mae_colaborador']));
                            $pai_colaborador = ucwords(strtolower($colaborador['pai_colaborador']));
                            $nascimento = date("d/m/Y", strtotime($colaborador['nascimento']));
                            $sexo = ucwords(strtolower($colaborador['sexo']));
                            $estado_civil = ucwords(strtolower($colaborador['estado_civil']));
                            $raca_cor = ucwords(strtolower($colaborador['raca_cor']));
                            $naturalidade = ucwords(strtolower($colaborador['naturalidade']));
                            $nacionalidade = ucwords(strtolower($colaborador['nacionalidade']));
                            $cidade = ucwords(strtolower($colaborador['cidade']));
                            $estado = strtoupper($colaborador['estado']);
                            $bairro = ucwords(strtolower($colaborador['bairro']));
                            $rua = ucwords(strtolower($colaborador['rua']));
                            $numero = $colaborador['numero'];
                            $complemento = ucwords(strtolower($colaborador['complemento']));
                            $cep = $colaborador['cep'];
                            $numero_ctps = $colaborador['numero_ctps'];
                            $serie_ctps = $colaborador['serie_ctps'];
                            $estado_ctps = strtoupper($colaborador['estado_ctps']);
                            $expedicao_ctps = date("d/m/Y", strtotime($colaborador['expedicao_ctps']));
                            $numero_pis = $colaborador['numero_pis'];
                            $cadastro_pis = $colaborador['cadastro_pis'];
                            $instrucao_escolaridade = $colaborador['instrucao_escolaridade'];
                            $cnh = $colaborador['cnh'];
                            $categoria_cnh = $colaborador['categoria_cnh'];
                            $validade_cnh = date("d/m/Y", strtotime($colaborador['validade_cnh']));
                            $reservista = $colaborador['reservista'];
                            $categoria_reservista = $colaborador['categoria_reservista'];
                            $titulo_eleitoral = $colaborador['titulo_eleitoral'];
                            $zona_eleitoral = $colaborador['zona_eleitoral'];
                            $secao_eleitoral = $colaborador['secao_eleitoral'];
                            $sindicato = ucwords(strtolower($colaborador['sindicato']));
                            $cons_profis = $colaborador['cons_profis'];
                            $registro_profis = $colaborador['registro_profis'];
                            $data_registro_profis = date("d/m/Y", strtotime($colaborador['data_registro_profis']));
                            $banco = strtoupper($colaborador['banco']);
                            $conta_banco = $colaborador['conta_banco'];
                            $digito_conta = $colaborador['digito_conta'];
                            $agencia_banco = $colaborador['agencia_banco'];
                            $codigo_ficha = $colaborador['codigo_ficha'];
                            $nr_recibo_ficha = $colaborador['nr_recibo_ficha'];
                            $matricula_esocial = $colaborador['matricula_esocial'];
                            $estg_data_chegada = date("d/m/Y", strtotime($colaborador['estg_data_chegada']));
                            $estg_tipo_visto = ucwords(strtolower($colaborador['estg_tipo_visto']));
                            $estg_data_portaria = date("d/m/Y", strtotime($colaborador['estg_data_portaria']));
                            $estg_nr_portaria = $colaborador['estg_nr_portaria'];
                            $estg_carteira_rne = $colaborador['estg_carteira_rne'];
                            $estg_validade_rne = date("d/m/Y", strtotime($colaborador['estg_validade_rne']));
                            $situacao = $colaborador['situacao'];
                            $endereco_completo = $colaborador['endereco_completo'];
                            $hasUser = $class_usuario->hasUser($id_colaborador);

                            $lista_epi = $class_epi->listarEPIS($id_colaborador);
                            if(count($lista_epi) > 0){
                                $dadosEPI = $lista_epi[0];
                                $id_epi = $dadosEPI['id_epi'];
                                $japona = strtoupper($dadosEPI['japona']);
                                $calca = strtoupper($dadosEPI['calca']);
                                $bota = $dadosEPI['bota'];
                                $luva = ucwords(strtolower($dadosEPI['luva']));
                                $meiao = $dadosEPI['meiao'];
                            }

                            $class_diretorio->identidade = "diretorio_colaborador";
                            $class_diretorio->id = $id_colaborador;
                            $hasDiretorio = $class_diretorio->ListaArquivos();

                            echo "<tr>
                                <td>$id_colaborador</td>
                                <td>$nome_colaborador</td>
                                <td><script>document.write(formatText('$cpf', 'cpf'))</script></td>
                                <td><script>document.write(formatText('$rg', 'rg'))</script></td>
                                <td>$orgao</td>
                                <td>$emissao_rg</td>
                                <td>"; echo $situacao === 1 ? "Contratado" : "Desligado"; echo "</td>
                                <td><center><button class='btn' data-toggle='modal' data-target='#viewModal__$id_colaborador' alt='Mais opções'><i class='bi bi-info-circle'></i></button></center></td>
                                <td>
                                    <div class='btns'>
                                        <center>"; echo $perfil_usuario === 'master' ? "<button class='btn' data-toggle='modal' data-target='#optionModal__$id_colaborador' alt='Ver Mais'><i class='bi bi-plus-circle'></i></button>" : "<button class='btn' data-dismiss='modal' onclick='window.location.href = \"listar_exames.php?id=$id_colaborador\"'><i class='bi bi-clipboard-pulse'></i></button>"; echo "</center>
                                    </div>
                                    ";
                                    echo "
                                    <div class='modal fade' id='viewModal__$id_colaborador' tabindex='-1' role='dialog' aria-labelledby='viewModalLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl' role='document'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h4 class='modal-title' id='viewModalLabel__$id_colaborador'>Visualizando: $nome_colaborador</h4>
                                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                        <span aria-hidden='true'>&times;</span>
                                                    </button>
                                                </div>
                                                <div class='modal-body'>
                                                    <h4 class='modal-title'>Dados Pessoais</h4><p></p>                                                    
                                                    <strong>Nome da Mãe:</strong> <br> <p>$mae_colaborador</p>
                                                    <strong>Nome do Pai:</strong> <br> <p>$pai_colaborador</p>
                                                    <strong>Data de Nascimento:</strong> <br> <p>$nascimento</p>
                                                    <strong>Sexo:</strong> <br> <p>$sexo</p>
                                                    <strong>Estado Civil:</strong> <br> <p>$estado_civil</p>
                                                    <strong>Etnia:</strong> <br> <p>$raca_cor</p>
                                                    <strong>Naturalidade:</strong> <br> <p>$naturalidade</p>
                                                    <strong>Nacionalidade:</strong> <br> <p>$nacionalidade</p>

                                                    <hr style='border: 0.1px solid #000000;'>

                                                    <h4 class='modal-title'>Endereço</h4><p></p>
                                                    <strong>Cidade:</strong> <br> <p>$cidade</p>
                                                    <strong>Estado:</strong> <br> <p>$estado</p>
                                                    <strong>Bairro:</strong> <br> <p>$bairro</p>
                                                    <strong>Rua:</strong> <br> <p>$rua</p>
                                                    <strong>Número:</strong> <br> <p>$numero</p>";
                                                    echo $complemento !== "" && $complemento !== null ? "<strong>Complemento:</strong> <br> <p>$complemento</p>" : ""; echo "
                                                    <strong>CEP:</strong> <br> <p><script>document.write(formatText('$cep', 'cep'))</script></p>

                                                    <hr style='border: 0.1px solid #000000;'>

                                                    <h4 class='modal-title'>Documentos</h4><p></p>
                                                    <strong>N° CTPS:</strong> <br> <p><script>document.write(formatText('$numero_ctps', 'numero_ctps'))</script></p>
                                                    <strong>Série CTPS:</strong> <br> <p>$serie_ctps</p>
                                                    <strong>Estado CTPS:</strong> <br> <p>$estado_ctps</p>
                                                    <strong>Expedição CTPS:</strong> <br> <p>$expedicao_ctps</p>
                                                    <strong>Número PIS:</strong> <br> <p>$numero_pis</p>
                                                    <strong>Cadastro PIS:</strong> <br> <p>$cadastro_pis</p>

                                                    <hr style='border: 0.1px solid #000000;'>

                                                    <h4 class='modal-title'>Dados Complementares</h4><p></p>
                                                    <strong>Instrução/Escolaridade:</strong> <br> <p>$instrucao_escolaridade</p>
                                                    <strong>CNH:</strong> <br> <p>$cnh</p>
                                                    <strong>Categoria:</strong> <br> <p>$categoria_cnh</p>
                                                    <strong>Validade:</strong> <br> <p>$validade_cnh</p>
                                                    <strong>Reservista:</strong> <br> <p>"; echo $reservista === 0 ? "Não" : "Sim"; echo "</p>
                                                    <strong>Categoria:</strong> <br> <p>$categoria_reservista</p>
                                                    <strong>Titulo Eleitoral:</strong> <br> <p><script>document.write(formatText('$titulo_eleitoral', 'titulo_eleitoral'))</script></p>
                                                    <strong>Zona Eleitoral:</strong> <br> <p>$zona_eleitoral</p>
                                                    <strong>Seção:</strong> <br> <p>$secao_eleitoral</p>";

                                                    echo "<hr style='border: 0.1px solid #000000;'>

                                                    <h4 class='modal-title'>Dados Bancários</h4><p></p>";
                                                    echo $banco !== "" && $banco !== null ? "<strong>Banco:</strong> <br> <p>$banco</p>" : "";
                                                    echo $conta_banco !== "" && $conta_banco !== null ? "<strong>Numero da Conta:</strong> <br> <p><script>document.write(formatText('$conta_banco ', 'conta_banco'))</script></p>" : "";
                                                    echo $digito_conta !== "" && $digito_conta !== null ? "<strong>Digito da Conta:</strong> <br> <p>$digito_conta</p>" : "";
                                                    echo $agencia_banco !== "" && $agencia_banco !== null ? "<strong>Agência:</strong> <br> <p>$agencia_banco</p>" : "";

                                                    echo"<hr style='border: 0.1px solid #000000;'>";

                                                    echo"<h4 class='modal-title'>Sindicato</h4><p></p>";
                                                    echo $sindicato !== "" && $sindicato !== null ? "<strong>Sindicato:</strong> <br> <p>$sindicato</p>" : "";
                                                    echo $registro_profis !== "" && $registro_profis !== null ? "<strong>Registro Profissional:</strong> <br> <p>$registro_profis</p>" : "";
                                                    echo $cons_profis !== "" && $cons_profis !== null ? "<strong>Consulta Profissional:</strong> <br> <p>$cons_profis</p>" : "";
                                                    echo $data_registro_profis !== date("d/m/Y", strtotime("31/12/1969")) && $data_registro_profis !== null ? "<strong>Data Registro Profissional:</strong> <br> <p>$data_registro_profis</p>" : "";

                                                    echo"<hr style='border: 0.1px solid #000000;'>";

                                                    echo"<h4 class='modal-title'>Registro</h4><p></p>";
                                                    echo $codigo_ficha !== "" && $codigo_ficha !== null ? "<strong>Código:</strong> <br> <p>$codigo_ficha</p>" : "";
                                                    echo $nr_recibo_ficha !== "" && $nr_recibo_ficha !== null ? "<strong>Número do Recibo:</strong> <br> <p>$nr_recibo_ficha</p>" : "";
                                                    echo $matricula_esocial !== "" && $matricula_esocial !== null ? "<strong>Matrícula eSocial:</strong> <br> <p>$matricula_esocial</p>" : "";

                                                    echo"<hr style='border: 0.1px solid #000000;'>";

                                                    echo"<h4 class='modal-title'>Estrangeiro</h4><p></p>";
                                                    echo $estg_data_chegada !== date("d/m/Y", strtotime("31/12/1969")) && $estg_data_chegada !== null ? "<strong>Data de Chegada:</strong> <br> <p>$estg_data_chegada</p>" : "";
                                                    echo $estg_tipo_visto !== "" && $estg_tipo_visto !== null ? "<strong>Tipo de Visto:</strong> <br> <p>$estg_tipo_visto</p>" : "";
                                                    echo $estg_data_portaria !== date("d/m/Y", strtotime("31/12/1969")) && $estg_data_portaria !== null ? "<strong>Data da Portaria:</strong> <br> <p>$estg_data_portaria</p>" : "";
                                                    echo $estg_nr_portaria !== "" && $estg_nr_portaria !== null ? "<strong>Número da Portaria:</strong> <br> <p>$estg_nr_portaria</p>" : "";
                                                    echo $estg_carteira_rne !== "" && $estg_carteira_rne !== null ? "<strong>RNE:</strong> <br> <p>$estg_carteira_rne</p>" : "";
                                                    echo $estg_validade_rne !== date("d/m/Y", strtotime("31/12/1969")) && $estg_validade_rne !== null ? "<strong>Validade RNE:</strong> <br> <p><script>document.write(formatText('$estg_validade_rne', 'estg_carteira_rne'))</script></p>" : ""; 

                                                    echo"
                                                </div>
                                                <div class='modal-footer'>
                                                    ";echo $perfil_usuario === 'master' ? "<a href='editar_colaborador.php?id=$id_colaborador' class='btn btn-primary'>Editar</a>" : ""; echo "
                                                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>";
                                    if($perfil_usuario === 'master'){
                                        echo "
                                        <div class='modal fade' id='optionModal__$id_colaborador' tabindex='-1' role='dialog' aria-labelledby='optionModalLabel' aria-hidden='true'>
                                        <div class='modal-dialog modal-dialog-centered' role='document'>
                                            <div class='modal-content'>
                                                <div class='modal-header'>
                                                    <h4 class='modal-title' id='optionModalLabel__$id_colaborador'>Opções do Colaborador: $nome_colaborador</h4>
                                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                        <span aria-hidden='true'>&times;</span>
                                                    </button>
                                                </div>
                                                <div class='modal-body'>
                                                    <div> 
                                                        <button class='btn' data-toggle='modal' data-target='#exameModal__$id_colaborador' alt='Exame Médico'><i class='bi bi-clipboard-pulse'></i></button>
                                                        <strong class='strong'>Exame Médico</strong>
                                                    </div>
                                                    <div>
                                                        "; 
                                                        if($situacao == 1) {
                                                            echo "<button type='button' class='btn' data-toggle='modal' data-target='#desligarModal__$id_colaborador' alt='Desligar Colaborador'><i class='bi bi-person-fill-slash'></i></button>
                                                            <strong class='strong'>Desligar Colaborador</strong>";
                                                        }
                                                        echo "
                                                    </div>
                                                    <div>
                                                        ";
                                                        if($situacao == 1) {
                                                            if(count($hasUser) === 0) {
                                                                echo "<button type='button' class='btn' data-toggle='modal' data-target='#cadUserModal__$id_colaborador' alt='Cadastro de Usuário'><i class='bi bi-person-plus-fill'></i></button>
                                                                <strong class='strong'>Cadastrar Usuario</strong>";
                                                            } else {
                                                                echo $hasUser[0]['situacao'] === 1 ? 
                                                                "<button type='button' class='btn' data-toggle='modal' data-target='#deleteModal__$id_colaborador' alt='Desativar Usuario'><i class='bi bi-person-fill-slash'></i></button>
                                                                <strong class='strong'>Desativar Usuario</strong>" : 
                                                                "<button type='button' class='btn' data-toggle='modal' data-target='#activeModal__$id_colaborador' alt='Ativar Usuário'><i class='bi bi-person-fill-check'></i></button>
                                                                <strong class='strong'>Ativar Usuario</strong>";
                                                            }
                                                        }
                                                        echo "
                                                    </div>
                                                    <div> 
                                                        <button type='button' class='btn' data-dismiss='modal' onclick='window.location.href = \"cadastro_sancoes.php?id_colaborador=$id_colaborador\"'><i class='bi bi-clipboard2-plus'></i></button>
                                                        <strong class='strong'>Cadastrar Sanção</strong>
                                                    </div>
                                                    <div>
                                                        <button type='button' class='btn' data-dismiss='modal' onclick='window.location.href = \"cadastro_acidentes_trabalho.php?id_colaborador=$id_colaborador\"'><i class='bi bi-file-medical'></i></button>
                                                        <strong class='strong'>Cadastrar Acidente de Trabalho</strong>
                                                    </div>";
                                                    echo count($lista_epi) > 0 ? 
                                                            "<div>
                                                                <button class='btn' data-toggle='modal' data-target='#epiModal__$id_colaborador' alt='EPI'><i class='bi bi-cone-striped'></i></button>
                                                                <strong class='strong'>Ver EPIs</strong>
                                                            </div>" : "";
                                                    echo $hasDiretorio !== false ?
                                                    "<div>
                                                        <button type='button' class='btn' data-dismiss='modal' onclick='window.location.href = \"listar_documentos.php?id_entidade=$id_colaborador&entidade=diretorio_colaborador\"'><i class='bi bi-archive'></i></button>
                                                        <strong class='strong'>Ver Documentos</strong>
                                                    </div>
                                                    " : ""; echo "
                                                    <!--<div> 
                                                        <button class='btn' data-toggle='modal' data-target='#transporteModal__$id_colaborador' alt='transporte'><i class='bi bi-bus-front'></i></button>
                                                        <strong class='strong'>Custo de Transportes</strong>
                                                    </div>-->
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        
                                        <div class='modal fade childModal' id='desligarModal__$id_colaborador' tabindex='-1' role='dialog' aria-labelledby='deleteModalLabel' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h4 class='modal-title' id='desligarModalLabel__$id_colaborador'>Desligar Colaborador</h4>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                            <span aria-hidden='true'>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <form action='' method='' class='modal-form'>
                                                            <input type='text' name='id' id='id' value='$id_colaborador' hidden>
                                                            <input type='text' name='control_class' id='control_class__$id_colaborador' value='colaboradores' hidden>
                                                            <input type='text' name='control_action' id='control_action__$id_colaborador' value='delete' hidden>
                                                            <input type='text' name='route' id='route__$id_colaborador' value='"; echo basename(__FILE__); echo "' hidden>
                                                            <div class='info'>
                                                                <label for='data_desligamento__$id_colaborador'>Data do Desligamento:</label>
                                                                <input type='datetime-local' class='form-control' name='data_desligamento' id='data_desligamento__$id_colaborador'>
                                                            </div>
                                                            <div class='info'>
                                                                <label for='observacao__$id_colaborador'>Motivo do Desligamento:</label>
                                                                <textarea class='form-control' style='height:125px' name='observacao' id='observacao__$id_colaborador'></textarea>
                                                            <button type='submit' class='btn btn-primary enviarModal' hidden></button>
                                                        </div>
                                                        </form>
                                                        <div class='responseMsg alert' style='display: none'></div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
                                                        <button type='submit' class='btn btn-danger' onclick='this.parentElement.parentElement.querySelector(`.enviarModal`).click()'>Desligar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='modal fade childModal' id='cadUserModal__$id_colaborador' tabindex='-1' role='dialog' aria-labelledby='deleteModalLabel' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h4 class='modal-title' id='cadUserLabel__$id_colaborador'>Cadastrar Usuário para Colaborador</h4>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                            <span aria-hidden='true'>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <form action='' method='' class='modal-form'>
                                                            <input type='text' name='id' id='id__$id_colaborador' value='$id_colaborador' hidden>
                                                            <input type='text' name='control_class' id='control_class__$id_colaborador' value='usuarios' hidden>
                                                            <input type='text' name='control_action' id='control_action__$id_colaborador' value='create' hidden>
                                                            <div class='form-floating mb-5'>
                                                                <input style='height: 50px;' class='form-control' type='email' name='email' id='email__$id_colaborador' placeholder='Email:' required>
                                                                <label for='email__$id_colaborador'>Email:</label>
                                                            </div>
                                                            <div class='form-floating mb-5'>
                                                                <select class='form-select form-select-lg mb-5' name='perfil' id='perfil__$id_colaborador' style='height: 50px; font-size: 1.5rem; font-weight: 750;' required>
                                                                    <option value='' selected>Perfil Usuário:</option>
                                                                    <option value='master'>Master</option>
                                                                    <option value='cadastro'>Somente Cadastro</option>
                                                                    <option value='visualizar'>Somente Visualização</option>
                                                                </select>
                                                                <label for='perfil__$id_colaborador' hidden>Perfil Usuário:</label>
                                                            </div>
                                                            <button type='submit' class='btn btn-primary enviarModal' hidden></button>
                                                        </form>
                                                        <div class='responseMsg alert' style='display: none'></div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
                                                        <button type='submit' class='btn btn-primary' onclick='this.parentElement.parentElement.querySelector(`.enviarModal`).click()'>Cadastrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='modal fade childModal' id='deleteModal__$id_colaborador' tabindex='-1' role='dialog' aria-labelledby='deleteModalLabel' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h4 class='modal-title' id='deleteModalLabel__$id_colaborador'>Remover Acesso de Colaborador</h4>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                            <span aria-hidden='true'>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <form action='' method='' class='modal-form'>
                                                            <input type='text' name='id' id='id__$id_colaborador' value='"; echo @$hasUser[0]['id_usuario']; echo "' hidden>
                                                            <input type='text' name='email' id='email__$id_colaborador' value='"; echo @$hasUser[0]['email']; echo "' hidden>
                                                            <input type='text' name='control_class' id='control_class__$id_colaborador' value='usuarios' hidden>
                                                            <input type='text' name='control_action' id='control_action__$id_colaborador' value='delete' hidden>
                                                            <input type='text' name='route' id='route__$id_colaborador' value='"; echo basename(__FILE__); echo "' hidden>
                                                            <p>Tem certeza que deseja remover o acesso ao sistema de $nome_colaborador?</p>
                                                            <button type='submit' class='btn btn-primary enviarModal' hidden></button>
                                                        </form>
                                                        <div class='responseMsg alert' style='display: none'></div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
                                                        <button type='submit' class='btn btn-danger' onclick='this.parentElement.parentElement.querySelector(`.enviarModal`).click()'>Excluir</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='modal fade childModal' id='activeModal__$id_colaborador' tabindex='-1' role='dialog' aria-labelledby='activeModalLabel' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h4 class='modal-title' id='activeModalLabel__$id_colaborador'>Ativar Acesso de Colaborador</h4>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                            <span aria-hidden='true'>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <form action='' method='' class='modal-form'>
                                                            <input type='text' name='id' id='id__$id_colaborador' value='"; echo @$hasUser[0]['id_usuario']; echo "' hidden>
                                                            <input type='text' name='email' id='email__$id_colaborador' value='"; echo @$hasUser[0]['email']; echo "' hidden>
                                                            <input type='text' name='control_class' id='control_class__$id_colaborador' value='usuarios' hidden>
                                                            <input type='text' name='control_action' id='control_action__$id_colaborador' value='active' hidden>
                                                            <input type='text' name='route' id='route__$id_colaborador' value='"; echo basename(__FILE__); echo "' hidden>
                                                            <p>Tem certeza que deseja ativar o acesso ao sistema de $nome_colaborador?</p>
                                                            <button type='submit' class='btn btn-primary enviarModal' hidden></button>
                                                        </form>
                                                        <div class='responseMsg alert' style='display: none'></div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
                                                        <button type='submit' class='btn btn-success' onclick='this.parentElement.parentElement.querySelector(`.enviarModal`).click()'>Ativar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='modal fade childModal' id='transporteModal__$id_colaborador' tabindex='-1' role='dialog' aria-labelledby='transporteModalLabel' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h4 class='modal-title' id='transporteModalLabel__$id_colaborador'>visualizando: $nome_colaborador</h4>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                            <span aria-hidden='true'>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <strong>Transporte Motorizado:</strong> <br> <p>transporte_motorizado</p>
                                                        <strong>Transporte Publico:</strong> <br> <p>transporte_publico</p>
                                                        <strong>Data de Deposito:</strong> <br> <p>data_deposito</p>
                                                        <strong>Gastos Totais:</strong> <br> <p>gastos_totais</p>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='modal fade childModal' id='exameModal__$id_colaborador' tabindex='-1' role='dialog' aria-labelledby='exameModalLabel' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered' role='document'>
                                                <div class='modal-content'>
                                                    <div class='modal-header'>
                                                        <h4 class='modal-title' id='exameModalLabel__$id_colaborador'>Visualizando: $nome_colaborador</h4>
                                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                                                    </div>
                                                    <div class='modal-body'>
                                                        <center><button type='button' class='botao_exame' data-dismiss='modal' onclick='window.location.href = \"listar_exames.php?id=$id_colaborador\"'>Ver Exames do Colaborador</button></center>
                                                        <hr>
                                                        <h2 class='cad_texto'>Cadastrar Novo Exame:</h2>
                                                        <br>
                                                        <form action='' method='' class='modal-form' id='formExame__$id_colaborador'>
                                                            <input type='hidden' name='id_colaborador' id='id_colaborador__$id_colaborador' value='$id_colaborador' hidden>
                                                            <input type='text' name='control_class' id='control_class__$id_colaborador' value='exames' hidden>
                                                            <input type='text' name='control_action' id='control_action__$id_colaborador' value='create' hidden>
                                                            <input type='text' name='route' id='route__$id_colaborador' value='"; echo basename(__FILE__); echo "' hidden>
                                                            <div class='form-floating mb-5'>
                                                                <input style='height: 50px;' class='form-control' type='text' name='relacao_clinicas' id='relacao_clinicas__$id_colaborador' placeholder='Clínica:'>
                                                                <label for='relacao_clinicas__$id_colaborador'>Clínica:</label>
                                                            </div>
                                                            <div class='form-floating mb-5'>
                                                                <input style='height: 50px;' class='form-control' type='text' name='telefone_clinicas' id='telefone_clinicas__$id_colaborador' placeholder='Telefone:'>
                                                                <label for='telefone_clinicas__$id_colaborador'>Telefone:</label>
                                                            </div>
                                                            <div class='form-floating mb-5'>
                                                                <input style='height: 50px; font-weight: 700;' class='form-control' type='date' name='data_ultimo_exame' id='data_ultimo_exame__$id_colaborador'>
                                                                <label for='data_ultimo_exame__$id_colaborador'>Data do Último Exame:</label>
                                                            </div>
                                                            <div class='form-floating mb-5'>
                                                                <input style='height: 50px; font-weight: 700;' class='form-control' type='date' name='data_agendamento' id='data_agendamento__$id_colaborador'>
                                                                <label for='data_agendamento__$id_colaborador'>Data do Agendamento:</label>
                                                            </div>
                                                            <button type='submit' class='btn btn-primary enviarModal' hidden></button>
                                                        </form>
                                                        <div class='responseMsg alert' style='display: none'></div>
                                                    </div>
                                                    <div class='modal-footer'>
                                                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
                                                        <button type='submit' class='btn btn-primary' onclick='this.parentElement.parentElement.querySelector(`.enviarModal`).click()'>Cadastrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                                        if(count($lista_epi) > 0) {
                                            echo "
                                            <div class='modal fade childModal' id='epiModal__$id_colaborador' tabindex='-1' role='dialog' aria-labelledby='epiModalLabel' aria-hidden='true'>
                                                <div class='modal-dialog modal-dialog-centered' role='document'>
                                                    <div class='modal-content'>
                                                        <div class='modal-header'>
                                                            <h4 class='modal-title' id='epiModalLabel__$id_colaborador'>Visualizando: $nome_colaborador</h4>
                                                            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                                                <span aria-hidden='true'>&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class='modal-body'>";
                                                            echo $id_epi !== "" && $id_epi !== null ? "<strong>ID:</strong> <br> <p>$id_epi</p>" : "";
                                                            echo $japona !== "" && $japona !== null ? "<strong>Japona:</strong> <br> <p>$japona</p>" : "";
                                                            echo $calca !== "" && $calca !== null ? "<strong>Calça:</strong> <br> <p>$calca</p>" : "";
                                                            echo $bota !== "" && $bota !== null ? "<strong>Bota:</strong> <br> <p>$bota</p>" : "";
                                                            echo $luva !== "" && $luva !== null ? "<strong>Luva:</strong> <br> <p>$luva</p>" : "";
                                                            echo $meiao !== "" && $meiao !== null ? "<strong>Meião:</strong> <br> <p>$meiao</p>" : "";
                                                            echo"
                                                        </div>
                                                        <div class='modal-footer'>
                                                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Sair</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>";
                                        };
                                    };
                                    echo "
                                </td>
                            </tr>";
                        }
        ?>
                    </tbody>
                </table>
            </div>
                <center>
                <nav aria-label="Paginação">
                    <ul class="pagination justify-content-center">
                        <?php $pesquisa = isset($_GET['pesquisa']) ? '&pesquisa=' . $_GET['pesquisa'] : ''; ?>
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $page <= 1 ? '#' : './listar_colaboradores.php?page=' . ($page - 1) . $pesquisa; ?>" tabindex="-1" aria-disabled="true"><<</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="./listar_colaboradores.php?page=<?php echo $i . $pesquisa; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo $page >= $total_pages ? '#' : './listar_colaboradores.php?page=' . ($page + 1) . $pesquisa; ?>">>></a>
                        </li>
                    </ul>
                </nav>
                </center>
    <?php
        } else {
            $hasPesquisa = isset($_GET['pesquisa']) ? "" : "<script>document.querySelector('#form-busca').style.display = 'none';</script>";
            echo $hasPesquisa . "<br><div class='alert alert-dark' role='alert'>Nenhum registro encontrado!</div>";
        }
    ?>
    </div>
    <div class="responseMsg alert"></div>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/exame.js"></script>
    <script src="assets/js/modal.js"></script>
</body>
</html>