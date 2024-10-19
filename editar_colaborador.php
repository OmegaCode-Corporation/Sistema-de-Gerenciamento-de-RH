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
    <title>Edição de Colaborador</title>
    <link rel="stylesheet" href="assets/css/cadastro_colaborador.css">
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
        require_once "assets/classes/colaborador.php";
        $class_colaborador = new Colaborador();
        $class_db = new Conexao_BD();

        @$id_colaborador = $_GET['id'];
        if(isset($id_colaborador)){
            $class_colaborador = $class_colaborador->listarColaboradores($id_colaborador, null, null, null);
            if(count($class_colaborador) > 0){
                $dados_colaborador = $class_colaborador[0];
                echo "<script>window.onload = () => document.querySelector('#formColaborador').style.display = 'block';</script>";
            }
        } else {
            echo "<script>
                window.onload = () => {
                    responseMsgField.classList.add('alert-dark');
                    finishAction('Nenhum registro encontrado!<br><br>Redirecionando para a Página de Colaboradores Cadastrados em ', 'listar_colaboradores.php');
                    document.querySelector('#formColaborador .card').style.display = 'none';
                    document.querySelector('#formColaborador').style.display = 'block';
                }
            </script>";
        }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']); ?>" method="POST" id="formColaborador">
        <div class="card card-body mt-5" style="padding: 10px 60px 50px 50px;">
            <h1>Edição de Registro de Colaborador</h1>
            <br>
            <p>Editando os dados do Colaborador <?php echo ucwords(strtolower($dados_colaborador['nome_colaborador'])); ?>:</p>
            <div class="progressbar">
                <div class="progress" id="progress"></div>
                <div class="progress-step progress-step-active" data-title="Pessoais"></div>
                <div class="progress-step" data-title="Endereço"></div>
                <div class="progress-step" data-title="Documentos"></div>
                <div class="progress-step" data-title="Complementares"></div>
                <div class="progress-step" data-title="Banco"></div>
                <div class="progress-step" data-title="Sindicato"></div>
                <div class="progress-step" data-title="Registro"></div>
                <div class="progress-step" data-title="EPI"></div>
                <div class="progress-step" data-title="Estrangeiro"></div>
            </div>

            <div class="form-step form-step-active">
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="nome_colaborador" id="nome_colaborador" value="<?php echo ucwords(strtolower($dados_colaborador['nome_colaborador'])); ?>" required>
                            <label for="nome_colaborador">Nome Colaborador:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="pai_colaborador" id="pai_colaborador" value="<?php echo ucwords(strtolower($dados_colaborador['pai_colaborador'])); ?>" required>
                            <label for="pai_colaborador">Nome  do Pai:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="mae_colaborador" id="mae_colaborador" value="<?php echo ucwords(strtolower($dados_colaborador['mae_colaborador'])); ?>" required>
                            <label for="mae_colaborador">Nome da Mãe:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input style="height: 50px; font-weight: 700;" class="form-control" type="date" name="nascimento" id="nascimento" value="<?php echo $dados_colaborador['nascimento']; ?>" readonly>
                            <label for="nascimento">Nascimento:</label>
                        </div>
                    </div>
                    <div class="col">                       
                        <div class="form-floating mb-5">
                            <input style="height: 50px; font-weight: 700;" class="form-control" type="text" name="sexo" id="sexo" value="<?php echo ucwords(strtolower($dados_colaborador['sexo'])); ?>" readonly>
                            <label for="sexo">Sexo:</label>
                        </div>                      
                    </div>
                    <div class="col">
                        <select class="form-select form-select-lg mb-5"  name="estado_civil" id="estado_civil" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
                            <?php 
                            $estado_civil = ucwords(strtolower($dados_colaborador['estado_civil']));
                            echo "<option value='$estado_civil' selected>Estado Civil: $estado_civil</option>";   
                            ?>
                            <option value="Casado">Estado Civil: Casado</option>
                            <option value="Solteiro">Estado Civil: Solteiro</option>
                            <option value="Separado">Estado Civil: Separado</option>
                            <option value="Divorciado">Estado Civil: Divorciado</option>
                            <option value="Viúvo">Estado Civil: Viúvo</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="raca_cor" id="raca_cor" value="<?php echo ucwords(strtolower($dados_colaborador['raca_cor'])); ?>" readonly>
                            <label for="raca_cor">Raça/Cor:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="naturalidade" id="naturalidade" value="<?php echo ucwords(strtolower($dados_colaborador['naturalidade'])); ?>" readonly>
                            <label for="naturalidade">Naturalidade:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input style="height: 50px;" class="form-control" type="text" name="nacionalidade" id="nacionalidade" value="Brasileiro" value="<?php echo ucwords(strtolower($dados_colaborador['nacionalidade'])); ?>" readonly>
                            <label for="nacionalidade">Nacionalidade:</label>
                        </div>
                    </div>                        
                </div>
                <div class="btns-group">
                    <a href="#" class="btn-next" style="margin-left: auto;">Avançar</a>
                </div>
            </div>

            <div class="form-step">
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="cep" id="cep" value="<?php echo $dados_colaborador['cep'] ?>" required>
                            <label for="cep">CEP:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="rua" id="rua" value="<?php echo ucwords(strtolower($dados_colaborador['rua'])); ?>" required>
                            <label for="rua">Rua:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="numero" id="numero" value="<?php echo $dados_colaborador['numero']; ?>" required>
                            <label for="numero">Número:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="complemento" id="complemento" value="<?php echo ucwords(strtolower($dados_colaborador['complemento'])); ?>" required>
                            <label for="complemento">Complemento:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input style="height: 50px" class="form-control" type="text" name="bairro" id="bairro" value="<?php echo ucwords(strtolower($dados_colaborador['bairro'])); ?>" required>
                            <label for="bairro">Bairro:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input style="height: 50px" class="form-control" type="text" name="cidade" id="cidade" value="<?php echo ucwords(strtolower($dados_colaborador['cidade'])); ?>" required>
                            <label for="cidade">Cidade:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input style="height: 50px" class="form-control" type="text" name="estado" id="estado" value="<?php echo strtoupper($dados_colaborador['estado']); ?>" required>
                            <label for="estado">Estado:</label>
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
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="cpf" id="cpf" value="<?php echo $dados_colaborador['cpf']; ?>" required>
                            <label for="cpf">CPF:</label>
                        </div>
                    </div>           
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="rg" id="rg" value="<?php echo $dados_colaborador['rg']; ?>" required>
                            <label for="rg">RG:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="rg_uf" id="rg_uf" value="<?php echo strtoupper($dados_colaborador['rg_uf']); ?>" required>
                            <label for="rg_uf">UF:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="orgao" id="orgao" value="<?php echo strtoupper($dados_colaborador['orgao']); ?>" required>
                            <label for="orgao">Órgão:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="date" name="emissao_rg" id="emissao_rg" value="<?php echo $dados_colaborador['emissao_rg']; ?>" required>
                            <label for="emissao_rg">Emissão RG:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="numero_ctps" id="numero_ctps" value="<?php echo $dados_colaborador['numero_ctps']; ?>" required>
                            <label for="numero_ctps">Número CTPS:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="serie_ctps" id="serie_ctps" value="<?php echo $dados_colaborador['serie_ctps']; ?>" required>
                            <label for="serie_ctps">Série CTPS:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="estado_ctps" id="estado_ctps" value="<?php echo strtoupper($dados_colaborador['estado_ctps']); ?>" required>
                            <label for="estado_ctps">UF CTPS:</label>
                        </div>
                        </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="date" name="expedicao_ctps" id="expedicao_ctps" value="<?php echo $dados_colaborador['expedicao_ctps']; ?>" required>
                            <label for="expedicao_ctps">Expedição CTPS:</label>
                        </div>
                    </div>
                </div> 
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="numero_pis" id="numero_pis" value="<?php echo $dados_colaborador['numero_pis']; ?>" required>
                            <label for="numero_pis">Número PIS:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="cadastro_pis" id="cadastro_pis" value="<?php echo $dados_colaborador['cadastro_pis']; ?>" required>
                            <label for="cadastro_pis">Cadastro PIS:</label>
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
                        <select class="form-select form-select-lg mb-5" name="instrucao_escolaridade" id="instrucao_escolaridade" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
                            <?php 
                            $instrucao_escolaridade = ucwords(strtolower($dados_colaborador['instrucao_escolaridade']));
                            echo "<option value='$instrucao_escolaridade' selected>Escolaridade: $instrucao_escolaridade</option>";   
                            ?>
                            <option value="Fundamental Incompleto">Escolaridade: Fundamental Incompleto</option>
                            <option value="Fundamental Completo">Escolaridade: Fundamental Completo</option>
                            <option value="Médio Incompleto">Escolaridade: Médio Incompleto</option>
                            <option value="Médio Completo">Escolaridade: Médio Completo</option>
                            <option value="Superior Incompleto">Escolaridade: Superior Incompleto</option>
                            <option value="Superior Completo">Escolaridade: Superior Completo</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="cnh" id="cnh" value="<?php echo strtoupper($dados_colaborador['cnh']); ?>" readonly>
                            <label for="cnh">CNH:</label>
                        </div>
                    </div>
                    <div class="col">
                        <select class="form-select form-select-lg mb-5" name="categoria_cnh" id="categoria_cnh" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
                            <?php 
                            $categoria_cnh = $dados_colaborador['categoria_cnh'];
                            echo "<option value='$categoria_cnh' selected>Categoria da CNH: $categoria_cnh</option>";   
                            ?>
                            <option value="ACC">Categoria da CNH: ACC</option>
                            <option value="A">Categoria da CNH: A</option>
                            <option value="B">Categoria da CNH: B</option>
                            <option value="C">Categoria da CNH: C</option>
                            <option value="D">Categoria da CNH: D</option>
                            <option value="E">Categoria da CNH: E</option>
                        </select>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="date" name="validade_cnh" id="validade_cnh" value="<?php echo $dados_colaborador['validade_cnh']; ?>" required>
                            <label for="validade_cnh">Validade CNH:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <?php
                                $reservista = $dados_colaborador['reservista'] === 1 ? "Sim" : "Não";
                            ?>
                            <input class="form-control" type="text" name="reservista" id="reservista" value="<?php echo $reservista; ?>" readonly>
                            <label for="reservista">Reservista:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="categoria_reservista" id="categoria_reservista" value="<?php echo $dados_colaborador['categoria_reservista']; ?>" readonly>
                            <label for="categoria_reservista">Categoria do Reservista:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="titulo_eleitoral" id="titulo_eleitoral" value="<?php echo $dados_colaborador['titulo_eleitoral']; ?>" readonly>
                            <label for="titulo_eleitoral">Título Eleitoral:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="zona_eleitoral" id="zona_eleitoral" value="<?php echo $dados_colaborador['zona_eleitoral']; ?>" required>
                            <label for="zona_eleitoral">Zona:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-5">
                            <input class="form-control" type="text" name="secao_eleitoral" id="secao_eleitoral" value="<?php echo $dados_colaborador['secao_eleitoral']; ?>" required>
                            <label for="secao_eleitoral">Seção:</label>
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
                            <p style="font-size: 2rem">O Colaborador já possui Conta Bancária?</p>
                            <input class="btn-check" type="radio" name="btn_banco" value="sim" id="sim_btn_banco" autocomplete="off" checked>
                            <label class="btn btn-primary" for="sim_btn_banco">Sim</label>
                            <input class="btn-check" type="radio" name="btn_banco" value="nao" id="nao_btn_banco" autocomplete="off">
                            <label class="btn btn-primary" for="nao_btn_banco">Não</label>
                        </center>
                        <br>
                    </div>
                </div>
                <div class="form_banco" id="form_banco">
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input style="height: 50px;" class="form-control" type="text" name="banco" id="banco" value="<?php echo strtoupper($dados_colaborador['banco']); ?>" required>
                                <label for="banco">Banco:</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="conta_banco" id="conta_banco" value="<?php echo $dados_colaborador['conta_banco']; ?>" required>
                                <label for="conta_banco">Conta:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="digito_conta" id="digito_conta" value="<?php echo $dados_colaborador['digito_conta']; ?>" required>
                                <label for="digito_conta">Dígito:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="agencia_banco" id="agencia_banco" value="<?php echo $dados_colaborador['agencia_banco']; ?>" required>
                                <label for="agencia_banco">Agência:</label>
                            </div>
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
                            <p style="font-size: 2rem">O Colaborador precisa de Sindicato?</p>
                            <input class="btn-check" type="radio" name="btn_sdct" value="sim" id="sim_btn_sdct" autocomplete="off" checked>
                            <label class="btn btn-primary" for="sim_btn_sdct">Sim</label>
                            <input class="btn-check" type="radio" name="btn_sdct" value="nao" id="nao_btn_sdct" autocomplete="off">
                            <label class="btn btn-primary" for="nao_btn_sdct">Não</label>
                        </center>
                        <br>
                    </div>
                </div>
                <div class="form_sdct" id="form_sdct">
                    <div class="row">
                        <p style="font-size: 1.8rem"><b>Preencha com os dados do Sindicato:</b></p>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input style="height: 50px;" class="form-control" type="text" name="sindicato" id="sindicato" value="<?php echo ucwords(strtolower($dados_colaborador['sindicato'])); ?>" required>
                                <label for="sindicato">Sindicato:</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="cons_profis" id="cons_profis" value="<?php echo $dados_colaborador['cons_profis']; ?>" required>
                                <label for="cons_profis">Cons. Profis:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="registro_profis" id="registro_profis" value="<?php echo $dados_colaborador['registro_profis']; ?>" required>
                                <label for="registro_profis">Registro Profis:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="date" name="data_registro_profis" id="data_registro_profis" value="<?php echo $dados_colaborador['data_registro_profis'] !== null && $dados_colaborador['data_registro_profis'] !== date("d/m/Y", strtotime("31/12/1969")) ? $dados_colaborador['data_registro_profis'] : ""; ?>" required>
                                <label for="data_registro_profis">Data Registro:</label>
                            </div>
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
                            <p style="font-size: 2rem">Precisa de dados de Registro?</p>
                            <input class="btn-check" type="radio" name="btn_rgst" value="sim" id="sim_btn_rgst" autocomplete="off" checked>
                            <label class="btn btn-primary" for="sim_btn_rgst">Sim</label>
                            <input class="btn-check" type="radio" name="btn_rgst" value="nao" id="nao_btn_rgst" autocomplete="off">
                            <label class="btn btn-primary" for="nao_btn_rgst">Não</label>
                        </center>
                        <br>
                    </div>
                </div>
                <div class="form_rgst" id="form_rgst">
                    <div class="row">
                        <p style="font-size: 1.8rem"><b>Preencha com os dados de registro da ficha:</b></p>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="codigo_ficha" id="codigo_ficha" value="<?php echo $dados_colaborador['codigo_ficha']; ?>" required>
                                <label for="codigo_ficha">Código:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="nr_recibo_ficha" id="nr_recibo_ficha" value="<?php echo $dados_colaborador['nr_recibo_ficha']; ?>" required>
                                <label for="nr_recibo_ficha">Nr. Recibo:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="matricula_esocial" id="matricula_esocial" value="<?php echo $dados_colaborador['matricula_esocial']; ?>" required>
                                <label for="matricula_esocial">Matrícula eSocial:</label>
                            </div>
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
                            <p style="font-size: 2rem">O Colaborador precisa de EPI?</p>
                            <input class="btn-check" type="radio" name="btn_epi" value="sim" id="sim_btn_epi" autocomplete="off" checked>
                            <label class="btn btn-primary" for="sim_btn_epi">Sim</label>
                            <input class="btn-check" type="radio" name="btn_epi" value="nao" id="nao_btn_epi" autocomplete="off">
                            <label class="btn btn-primary" for="nao_btn_epi">Não</label>
                        </center>
                        <br>
                    </div>
                </div>
                <div class="form_epi" id="form_epi">
                    <?php
                        $class_epi = new EPI();
                        $dados_epi_db = $class_epi->listarEPIS($id_colaborador);
                        $dados_epi = count($dados_epi_db) > 0 ? $dados_epi_db[0] : ["japona" => "", "calca" => "", "bota" => "", "luva" => "", "meiao" => ""];
                    ?>
                    <div class="row">
                        <p style="font-size: 1.8rem"><b>Selecione o tamanho:</b></p>
                        <div class="col">
                            <select class="form-select form-select-lg mb-5" name="japona" id="japona" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
                                <option value="<?php echo $dados_epi['japona']; ?>" selected>Japona: <?php echo strtoupper($dados_epi['japona']); ?></option>
                                <option value="P">Japona: P</option>
                                <option value="M">Japona: M</option>
                                <option value="G">Japona: G</option>
                                <option value="GG">Japona: GG</option>
                                <option value="XG">Japona: XG</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-select form-select-lg mb-5" name="calca" id="calca" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
                                <option value="<?php echo $dados_epi['calca']; ?>" selected>Calça: <?php echo strtoupper($dados_epi['calca']); ?></option>
                                <option value="P">Calça: P</option>
                                <option value="M">Calça: M</option>
                                <option value="G">Calça: G</option>
                                <option value="GG">Calça: GG</option>
                                <option value="XG">Calça: XG</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-select form-select-lg mb-5" name="bota" id="bota" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
                                <option value="<?php echo $dados_epi['bota']; ?>" selected>Bota: <?php echo strtoupper($dados_epi['bota']); ?></option>
                                <?php
                                    for($i=32; $i<=47; $i++)
                                    echo "<option value='$i'>Bota: $i</option>"
                                ?>
                            </select>                            
                        </div>
                    </div>
                    <div class="row">
                        <br>
                        <!-- <p style="font-size: 1.8rem"><b>Selecione se necessário:</b></p> -->
                        <div class="col">
                            <select class="form-select form-select-lg mb-5" name="luva" id="luva" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
                                <option value="<?php echo $dados_epi['luva']; ?>" selected>Luva: <?php echo strtoupper($dados_epi['luva']); ?></option>
                                <option value="termica">Luva: Térmica</option>
                                <option value="tatica">Luva: Tática</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="form-select form-select-lg mb-5" name="meiao" id="meiao" style="height: 50px; font-size: 1.5rem; font-weight: 750;">
                                <option value="<?php echo $dados_epi['meiao']; ?>" selected>Meião: <?php echo strtoupper($dados_epi['meiao']); ?></option>
                                <?php
                                    for($i=32; $i<=47; $i++)
                                    echo "<option value='$i'>Meião: $i</option>"
                                ?>
                                <!-- <option value="Sim">Meião: SIM</option>
                                <option value="Não">Meião: NÃO</option> -->
                            </select>
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
                            <p style="font-size: 2rem">O Colaborador é Estrangeiro?</p>
                            <input class="btn-check" type="radio" name="btn_estg" value="sim" id="sim_btn_estg" autocomplete="off" checked>
                            <label class="btn btn-primary" for="sim_btn_estg">Sim</label>
                            <input class="btn-check" type="radio" name="btn_estg" value="nao" id="nao_btn_estg" autocomplete="off">
                            <label class="btn btn-primary" for="nao_btn_estg">Não</label>
                        </center>
                        <br>
                    </div>
                </div>
                <div class="form_estg" id="form_estg">
                    <div class="row">
                        <p style="font-size: 1.8rem"><b>Preencha com os dados de Estrangeiro:</b></p>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="date" name="estg_data_chegada" id="estg_data_chegada" placeholder="Data de chegada:" value="<?php echo $dados_colaborador['estg_data_chegada'] !== null && $dados_colaborador['estg_data_chegada'] !== date("d/m/Y", strtotime("31/12/1969")) ? date("d/m/Y", strtotime($dados_colaborador['estg_data_chegada'])) : ""; ?>">
                                <label for="estg_data_chegada">Data de chegada:</label>
                            </div>
                        </div>
                        <div class="col">
                            <select class="form-select form-select-lg mb-5" name="estg_tipo_visto" id="estg_tipo_visto" style="height: 50px; font-size: 1.5rem; font-weight: 750;" placeholder="Tipo de visto:">
                                <option value="<?php echo $dados_colaborador['estg_tipo_visto']; ?>" selected>Tipo de visto: <?php echo $dados_colaborador['estg_tipo_visto'] !== null && $dados_colaborador['estg_tipo_visto'] !== date("d/m/Y", strtotime("31/12/1969")) ? $dados_colaborador['estg_tipo_visto'] : "";; ?></option>
                                <option value="Diplomático">Tipo de isto: Visto Diplomático</option>
                                <option value="Oficial">Tipo de visto: Visto Oficial</option>
                                <option value="Cortesia">Tipo de visto: Visto de Cortesia</option>
                                <option value="Turista">Tipo de visto: Visto de Turista</option>
                                <option value="Trânsito">Tipo de visto: Visto de Trânsito</option>
                                <option value="Negócios">Tipo de visto: Visto de Negócios</option>
                                <option value="Permanente">Tipo de visto: Visto Permanente</option>
                                <option value="Temporários">Tipo de visto: Vistos Temporários</option>
                            </select>
                        </div>
                    </div>                   
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="estg_carteira_rne" id="estg_carteira_rne" placeholder="Carteira RNE:" value="<?php echo $dados_colaborador['estg_carteira_rne']; ?>">
                                <label for="estg_carteira_rne">Carteira RNE:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="date" name="estg_validade_rne" id="estg_validade_rne" placeholder="Validade RNE:" value="<?php echo $dados_colaborador['estg_validade_rne'] !== null && $dados_colaborador['estg_validade_rne'] !== date("d/m/Y", strtotime("31/12/1969")) ? date("d/m/Y", strtotime($dados_colaborador['estg_validade_rne'])) : "";?>">
                                <label for="estg_validade_rne">Validade RNE:</label>
                            </div>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="text" name="estg_nr_portaria" id="estg_nr_portaria" placeholder="Número da Portaria:" value="<?php echo $dados_colaborador['estg_nr_portaria']; ?>">
                                <label for="estg_nr_portaria">Número da Portaria:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-5">
                                <input class="form-control" type="date" name="estg_data_portaria" id="estg_data_portaria" placeholder="Data da Portaria:" value="<?php echo $dados_colaborador['estg_data_portaria'] !== null && $dados_colaborador['estg_data_portaria'] !== date("d/m/Y", strtotime("31/12/1969")) ? date("d/m/Y", strtotime($dados_colaborador['estg_data_portaria'])) : ""; ?>">
                                <label for="estg_data_portaria">Data da Portaria:</label>
                            </div>
                        </div>
                    </div> 
                </div>              
                <div class="btns-group">
                    <a href="#" class="btn-prev">Voltar</a>
                    <input type="button" class="btn-enviar" nome="enviar" id="enviar" value="Enviar">
                </div>
            </div>
        </div>
        <div class="responseMsg alert"></div>
        <script src="assets/js/cadastro_colaborador.js"></script>
        <script src="assets/js/main.js"></script>
    </form>
    <script>
        window.onload = () => {
            const formColaborador = document.querySelector("#formColaborador")

            formColaborador.querySelectorAll("input").forEach(element => {
                let minLength;
                let maxLength;

                switch (element.id) {
                    case 'nome_colaborador':
                        maxLength = 0
                        blockText(maxLength, false, element)
                        break;
                    case 'pai_colaborador':
                        maxLength = 0
                        blockText(maxLength, false, element)
                        break;
                    case 'mae_colaborador':
                        maxLength = 0
                        blockText(maxLength, false, element)
                        break;
                    case 'naturalidade':
                        maxLength = 0
                        blockText(maxLength, false, element)
                        break;
                    case 'nacionalidade':
                        maxLength = 0
                        blockText(maxLength, false, element)
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
                    case 'numero':
                        maxLength = 4
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
                    case 'cpf':
                        maxLength = 11
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
                    case 'rg':
                        maxLength = 8
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
                    case 'orgao':
                        maxLength = 6
                        blockText(maxLength, false, element)
                        element.value = formatText(element.value, element.id, 'remove')
                        switch (element.value.length) {
                            case maxLength:
                                element.value = formatText(element.value, element.id).toUpperCase()
                                break;
                            case maxLength+1:
                                element.value = formatText(element.value.slice(0, element.value.length-1), element.id).toUpperCase()
                                break;
                            default:
                                break;
                        }
                        break;
                    case 'rg_uf':
                        maxLength = 2
                        blockText(maxLength, false, element)
                        element.value = formatText(element.value, element.id, 'remove')
                        switch (element.value.length) {
                            case maxLength:
                                element.value = formatText(element.value, element.id).toUpperCase()
                                break;
                            case maxLength+1:
                                element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                                break;
                            default:
                                break;
                        }
                        break;
                    case 'numero_ctps':
                        maxLength = 7
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
                    case 'serie_ctps':
                        maxLength = 5
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
                    case 'estado_ctps':
                        maxLength = 2
                        blockText(maxLength, false, element)
                        element.value = formatText(element.value, element.id, 'remove')
                        switch (element.value.length) {
                            case maxLength:
                                element.value = formatText(element.value, element.id).toUpperCase()
                                break;
                            case maxLength+1:
                                element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                                break;
                            default:
                                break;
                        }
                        break;
                    case 'numero_pis':
                        maxLength = 12
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
                    case 'cnh':
                        maxLength = 11
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
                    case 'titulo_eleitoral':
                        maxLength = 12
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
                    case 'zona_eleitoral':
                        maxLength = 3
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
                    case 'secao_eleitoral':
                        maxLength = 4
                        blockText(maxLength, true, element)
                        element.value = formatText(element.value, element.id, 'remove')
                        switch (element.value.length) {
                            case maxLength:
                                element.value = formatText(element.value, element.id )
                                break;
                            case maxLength+1:
                                element.value = formatText(element.value.slice(0, element.value.length-1), element.id)
                                break;
                            default:
                                break;
                        }
                        break;
                    case 'banco':
                        maxLength = 0
                        blockText(maxLength, false, element)
                        break;
                    case 'conta_banco':
                        maxLength = 8
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
                    case 'digito_conta':
                        maxLength = 2
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
                    case 'agencia_banco':
                        maxLength = 4
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
                    case 'codigo_ficha':
                        maxLength = 0
                        blockText(maxLength, true, element)
                        break;
                    case 'nr_recibo_ficha':
                        maxLength = 0
                        blockText(maxLength, true, element)
                        break;
                    case 'matricula_esocial':
                        maxLength = 0
                        blockText(maxLength, true, element)
                        break;
                    case 'estg_carteira_rne':
                        element.value = formatText(element.value, element.id)
                        break;
                    case 'estg_nr_portaria':
                        maxLength = 0
                        blockText(maxLength, true, element)
                        break;
                    default:
                        break;
                }
            })
        }
    </script>
</body> 
</html>
<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once "assets/classes/colaborador.php";
        $class_colaborador = new Colaborador();

        $class_colaborador->__set("cpf", $_POST['cpf']);
        $class_colaborador->__set("rg", $_POST['rg']);
        $class_colaborador->__set("rg_uf", $_POST['rg_uf']);
        $class_colaborador->__set("orgao", $_POST['orgao']);
        $class_colaborador->__set("emissao_rg", date('Y-m-d', strtotime($_POST['emissao_rg'])));
        $class_colaborador->__set("nome_colaborador", $_POST['nome_colaborador']);
        $class_colaborador->__set("mae_colaborador", $_POST['mae_colaborador']);
        $class_colaborador->__set("pai_colaborador", $_POST['pai_colaborador']);
        $class_colaborador->__set("nascimento", date('Y-m-d', strtotime($_POST['nascimento'])));
        $class_colaborador->__set("sexo", $_POST['sexo']);
        $class_colaborador->__set("estado_civil", $_POST['estado_civil']);
        $class_colaborador->__set("raca_cor", $_POST['raca_cor']);
        $class_colaborador->__set("naturalidade", $_POST['naturalidade']);
        $class_colaborador->__set("nacionalidade", $_POST['nacionalidade']);
        $class_colaborador->__set("cidade", $_POST['cidade']);
        $class_colaborador->__set("estado", $_POST['estado']);
        $class_colaborador->__set("bairro", $_POST['bairro']);
        $class_colaborador->__set("rua", $_POST['rua']);
        $class_colaborador->__set("numero", $_POST['numero']);
        $class_colaborador->__set("complemento", $_POST['complemento']);
        $class_colaborador->__set("cep", $_POST['cep']);
        $class_colaborador->__set("numero_ctps", $_POST['numero_ctps']);
        $class_colaborador->__set("serie_ctps", $_POST['serie_ctps']);
        $class_colaborador->__set("estado_ctps", $_POST['estado_ctps']);
        $class_colaborador->__set("expedicao_ctps", date('Y-m-d', strtotime($_POST['expedicao_ctps'])));
        $class_colaborador->__set("numero_pis", $_POST['numero_pis']);
        $class_colaborador->__set("cadastro_pis", $_POST['cadastro_pis']);
        $class_colaborador->__set("instrucao_escolaridade", $_POST['instrucao_escolaridade']);
        $class_colaborador->__set("cnh", $_POST['cnh']);
        $class_colaborador->__set("categoria_cnh", $_POST['categoria_cnh']);
        $class_colaborador->__set("validade_cnh", date('Y-m-d', strtotime($_POST['validade_cnh'])));
        $class_colaborador->__set("reservista", $_POST['reservista']);
        $class_colaborador->__set("categoria_reservista", $_POST['categoria_reservista']);
        $class_colaborador->__set("titulo_eleitoral", $_POST['titulo_eleitoral']);
        $class_colaborador->__set("zona_eleitoral", $_POST['zona_eleitoral']); 
        $class_colaborador->__set("secao_eleitoral", $_POST['secao_eleitoral']);

        if($_POST['btn_banco'] === 'sim'){
            $class_colaborador->__set("banco", $_POST['banco']);
            $class_colaborador->__set("conta_banco", $_POST['conta_banco']);
            $class_colaborador->__set("digito_conta", $_POST['digito_conta']);
            $class_colaborador->__set("agencia_banco", $_POST['agencia_banco']);
        } else {
            $class_colaborador->__set("banco", null);
            $class_colaborador->__set("conta_banco", null);
            $class_colaborador->__set("digito_conta", null);
            $class_colaborador->__set("agencia_banco", null);
        }

        if($_POST['btn_sdct'] === 'sim'){
            $class_colaborador->__set("sindicato", $_POST['sindicato']);
            $class_colaborador->__set("cons_profis", $_POST['cons_profis']);
            $class_colaborador->__set("registro_profis", $_POST['registro_profis']);
            $class_colaborador->__set("data_registro_profis", date('Y-m-d', strtotime($_POST['data_registro_profis'])));
        } else {
            $class_colaborador->__set("sindicato", null);
            $class_colaborador->__set("cons_profis", null);
            $class_colaborador->__set("registro_profis", null);
            $class_colaborador->__set("data_registro_profis", null);
        }

        if($_POST['btn_rgst'] === 'sim'){
            $class_colaborador->__set("codigo_ficha", $_POST['codigo_ficha']);
            $class_colaborador->__set("nr_recibo_ficha", $_POST['nr_recibo_ficha']);
            $class_colaborador->__set("matricula_esocial", $_POST['matricula_esocial']);
        } else {
            $class_colaborador->__set("codigo_ficha", null);
            $class_colaborador->__set("nr_recibo_ficha", null);
            $class_colaborador->__set("matricula_esocial", null);
        }

        if($_POST['btn_estg'] === 'sim'){
            $class_colaborador->__set("estg_data_chegada", date('Y-m-d', strtotime($_POST['estg_data_chegada'])));
            $class_colaborador->__set("estg_tipo_visto", $_POST['estg_tipo_visto']);
            $class_colaborador->__set("estg_data_portaria", date('Y-m-d', strtotime($_POST['estg_data_portaria'])));
            $class_colaborador->__set("estg_nr_portaria", $_POST['estg_nr_portaria']);
            $class_colaborador->__set("estg_carteira_rne", $_POST['estg_carteira_rne']);
            $class_colaborador->__set("estg_validade_rne", date('Y-m-d', strtotime($_POST['estg_validade_rne'])));
        } else {
            $class_colaborador->__set("estg_data_chegada", null);
            $class_colaborador->__set("estg_tipo_visto", null);
            $class_colaborador->__set("estg_data_portaria", null);
            $class_colaborador->__set("estg_nr_portaria", null);
            $class_colaborador->__set("estg_carteira_rne", null);
            $class_colaborador->__set("estg_validade_rne", null);
        }

        $class_colaborador->atualizarColaborador($id_colaborador);

        if($_POST['btn_epi'] === 'sim'){
            $class_epi = new EPI();
            $class_epi->__set("id_colaborador", $id_colaborador);
            $class_epi->__set("japona", $_POST['japona']);
            $class_epi->__set("calca", $_POST['calca']);
            $class_epi->__set("luva", $_POST['luva']);
            $class_epi->__set("meiao", $_POST['meiao']);
            $class_epi->atualizarEPI();
        }
    }
?>