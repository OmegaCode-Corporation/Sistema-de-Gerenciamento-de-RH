<?php
    @session_start();
    if(!isset($_SESSION['logged'])){
        echo "<script>window.location.href = 'index.php'</script>";
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
    <title>Lista de Arquivos</title>
    <link rel="stylesheet" href="assets/css/logs.css">
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
    <?php
        if((isset($_GET['entidade']) && $_GET['entidade'] !== "") && (isset($_GET['id_entidade']) && $_GET['id_entidade'] !== "")){
    ?>
        <div class="card card-body mt-5 container" id="cardbox">
            <center><h1>Lista de Arquivos</h1></center>
            <form action="" method="GET" id="form-busca">
                <div class="flex" style="display: flex;">
                    <input type="text" name="id_entidade" value="<?php echo $_GET['id_entidade']; ?>" hidden>
                    <input type="text" name="entidade" value="<?php echo $_GET['entidade']; ?>" hidden>
                    <label for="input-busca" style="font-size: 26px;">O que você procura?</label>
                    <div class="dropdown" id="searchInfo"><i class="bi bi-info-circle" style="font-size: 26px;"></i>
                        <div class="dropdown-content" id="searchInfo_content">
                            <p><p>Opções de Procura:</p><b>Nome do Arquivo;</b><br></p>
                        </div>
                    </div>
                </div>
                <input id="input-busca" type="text" name="pesquisa" class="form-control mt-3 mb-3" placeholder="Digite...">
            </form>
            <?php 
                $id = $_GET['id_entidade'];
                $identidade = $_GET['entidade'];

                require_once "assets/classes/diretorio.php";
                $class_diretorio = new Diretorio();
                $class_diretorio->__set("id",$id);
                $class_diretorio->__set("identidade",$identidade);
                $arquivo = $class_diretorio->ListaArquivos();
                $count_arquivos = 0;
                $files = "";

                while(($nome_arquivo = $arquivo->read()) !== false){
                    if($nome_arquivo!=="." && $nome_arquivo!==".."){
                        $files .= "
                        <tr>
                            <td>$nome_arquivo</td>
                            <td>
                                <div class='btns' style='display: flex; flex-direction: row;'>
                                    <a href='diretorio/$identidade/$id/$nome_arquivo' target='_blank' class='btn'><i class='bi bi-file-earmark-arrow-down'></i></a>
                                </div>
                            </td>
                        </tr>";
                        $count_arquivos++;
                    }
                }

                if($count_arquivos > 0){
            ?>
                <table class="table table-hover table-striped">
                    <thead style="background-color: #1f84d6; color: white;">
                        <tr>
                            <th>Nome do Arquivo</th> 
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-historico" class="tabela-listagem">
                        <?php echo $files; ?>
                    </tbody>
                </table>
                <div class="btns" style="display: flex; justify-content: center; align-items: center; gap: 10px">
                    <input type="button" class="btn btn-primary" nome="voltar" id="voltar" value="Limpar Busca" onclick="window.location.search = ''">
                </div>
            <?php
                } else {
                    $hasPesquisa = isset($_GET['pesquisa']) ? "" : "<script>document.querySelector('#form-busca').style.display = 'none';</script>";
                    echo $hasPesquisa . "<br><div class='alert alert-dark' role='alert'>Nenhum registro encontrado!</div>";
                }
            ?>
        </div>
    <?php
        } else {
    ?>
            <link rel="stylesheet" href="assets/css/relatorio_dia.css">
            <div class="div-center all-center">
                <form action="" method="GET" id="formDocumentos">
                    <div class="card card-body mt-5">
                        <h1>Busca de Documentos</h1>
                        <div class="form-step">
                            <?php
                                if(!isset($_GET['entidade'])) {
                            ?>
                                    <div class="row" style="margin: 20px 0;">
                                        <div class="col">
                                            <select class="form-select form-select-lg" style="height: 50px; font-size: 1.5rem; font-weight: 750;" name="entidade" id="entidade">
                                                <option value="">Tipo:</option>
                                                <option value="diretorio_colaborador">Tipo: Colaborador</option>
                                                <option value="diretorio_contrato">Tipo: Contrato</option>
                                                <option value="diretorio_empresa">Tipo: Empresa</option>
                                            </select>
                                        </div>
                                    </div>
                            <?php
                                }
                            ?>
                            <?php
                                if(isset($_GET['entidade']) && $_GET['entidade'] !== ""){
                                    $entidade_str = ucwords(substr($_GET['entidade'], strpos($_GET['entidade'], "_") + 1, strlen($_GET['entidade'])));
                            ?>
                                    <div class="row" style="margin: 20px 0;">
                                        <div class="col">
                                            <div class="form-floating mb-3">
                                                <input style="height: 50px; border-radius: 5px 0 0 5px;" class="form-control" type="text" id="nome_entidade" placeholder="Selecione a Unidade:" readonly>
                                                <label for="nome_entidade"><?php echo $entidade_str; ?>...</label>
                                                <input type="text" name="id_entidade" id="id_entidade" hidden>
                                                <input type="text" name="entidade" id="entidade" value="<?php echo $_GET['entidade']; ?>" hidden>
                                            </div>
                                        </div>
                                        <div class="col" style="padding: 0; margin-left: calc(var(--bs-gutter-x)* .5* -1); margin-right: 7px; flex: 0 0 0%;">
                                            <button type="button" data-toggle='modal' data-target='#viewModal' style="border: none; background-color: #1f84d6; color: white; padding: 15px 17px; border-radius: 0 5px 5px 0;" ><i class="bi bi-search"></i></button>
                                            <div class='modal fade' id='viewModal' tabindex='-1' role='dialog' aria-labelledby='viewModalLabel' aria-hidden='true'>
                                                <div class='modal-dialog modal-dialog-scrollable modal-lg' role='document'>
                                                    <div class='modal-content'>
                                                        <div class='modal-header'>
                                                            <p class='modal-title' id='viewModal'><?php echo $entidade_str; ?>...</p>
                                                        </div>
                                                        <div class='modal-body'>
                                                            <input class="form-control" type="text" id="input_busca_listagem" placeholder="Digite..." oninput="search(document.querySelector('#input_busca_listagem'), document.querySelector('#tabela_listagem_entidade'))">
                                                            <br>
                                                            <table class="table table-hover table-striped">
                                                                <thead style="background-color: #1f84d6; color: white;">
                                                                    <tr>
                                                                        <th id="modal_id_entidade">ID <?php echo $entidade_str; ?></th>
                                                                        <th id="modal_nome_entidade">Nome <?php echo $entidade_str === "Contrato" ? "Colaborador" : $entidade_str; ?></th>
                                                                        <th>Ações</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="tabela_listagem_entidade" id="tabela_listagem_entidade">
                                                                    <?php
                                                                        $dados = [];
                                                                        switch($_GET['entidade']){
                                                                            case "diretorio_colaborador":
                                                                                require_once "assets/classes/colaborador.php";
                                                                                $colaborador = new Colaborador();
                                                                                $dados = $colaborador->listarColaboradores();
                                                                                break;
                                                                            case "diretorio_contrato":
                                                                                require_once "assets/classes/contrato.php";
                                                                                $contrato = new Contrato();
                                                                                $colaborador = new Colaborador();
                                                                                $dados = $contrato->DadosContrato();
                                                                                break;
                                                                            case "diretorio_empresa":
                                                                                require_once "assets/classes/empresa.php";
                                                                                $empresa = new Empresa();
                                                                                $dados = $empresa->DadosEmpresa();
                                                                                break;
                                                                        }
                                                                        foreach($dados as $linha){
                                                                            $entidade_str = strtolower($entidade_str);
                                                                            $id_entidade = $linha["id_$entidade_str"];
                                                                            $nome_entidade = "";
                                                                            
                                                                            if($entidade_str === "contrato"){
                                                                                $nome_entidade = ucwords(strtolower($colaborador->listarColaboradores($linha['id_colaborador'])[0]['nome_colaborador']));
                                                                            } else if($entidade_str === "colaborador"){
                                                                                $nome_entidade = ucwords(strtolower($linha["nome_$entidade_str"]));
                                                                            } else if($entidade_str === "empresa"){
                                                                                $nome_entidade = strtoupper($linha["nome_$entidade_str"]);
                                                                            }

                                                                            require_once "assets/classes/diretorio.php";
                                                                            $class_diretorio = new Diretorio();
                                                                            $class_diretorio->identidade = $_GET['entidade'];
                                                                            $class_diretorio->id = $id_entidade;
                                                                            $hasDiretorio = $class_diretorio->ListaArquivos();

                                                                            if($hasDiretorio !== false){
                                                                    ?>
                                                                                <tr>
                                                                                    <td id='id_entidade_td'><?php echo $id_entidade; ?></td>
                                                                                    <td id='nome_entidade_td'><?php echo $nome_entidade; ?></td>
                                                                                    <td><button type='button' id='<?php echo $id_entidade; ?>' class='btn btn-primary btn-sm btn_editar_colaborador'>Selecionar</button></td>
                                                                                </tr>
                                                                    <?php
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
                            <?php
                                }
                            ?>
                            <div class="btns" style="display: flex; justify-content: center; align-items: center; gap: 10px">
                                <input type="submit" class="btn btn-primary" nome="enviar" id="submit" value="Enviar">
                                <input type="button" class="btn btn-primary" nome="voltar" id="voltar" value="Limpar Busca" onclick="window.location.search = '';">
                            </div>
                        </div>
                    </div>
                </form>
                <div class="responseMsg alert"></div>
            </div>
    <?php
        }
    ?>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/modal.js"></script>.
    <script src="assets/js/listar_documentos.js"></script>
</body>
</html>