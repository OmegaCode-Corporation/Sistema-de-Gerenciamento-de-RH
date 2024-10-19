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
    <title>Cadastro de Colaborador</title>
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
    ?>
    <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST" id="formColaborador">
        <div class="card card-body mt-5">
            <h1>Ficha de Registro de Colaborador</h1>
            <br>
            <p>Preencha com os dados do Colaborador:</p>
            <div class="progressbar">
                <div class="progress" id="progress"></div>
                <div class="progress-step progress-step-active" data-title="Pessoais"></div>
                <div class="progress-step" data-title="Endereço"></div>
                <div class="progress-step" data-title="Documentos"></div>
                <div class="progress-step" data-title=""></div>
                <div class="progress-step" data-title="Banco"></div>
                <div class="progress-step" data-title="Sindicato"></div>
                <div class="progress-step" data-title="Registro"></div>
                <div class="progress-step" data-title="EPI"></div>
                <div class="progress-step" data-title="Estrangeiro"></div>
            </div>

            <div class="form-step form-step-active">  
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="nome_colaborador" id="nome_colaborador" placeholder="Nome Colaborador:">
                            <label for="nome_colaborador">Nome Colaborador:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="pai_colaborador" id="pai_colaborador" placeholder="Nome do Pai:">
                            <label for="pai_colaborador">Nome  do Pai:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="mae_colaborador" id="mae_colaborador" placeholder="Nome da Mãe:">
                            <label for="mae_colaborador">Nome da Mãe:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px; font-weight: 700;" class="form-control" type="date" name="nascimento" id="nascimento">
                            <label for="nascimento">Nascimento:</label>
                        </div>
                    </div>
                    <div class="col">
                        <label class="label_select" for="sexo">Sexo:</label>                       
                        <select class="form-select form-select-lg mb-3" name="sexo" id="sexo">
                            <option selected>Selecione:</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Feminino">Feminino</option>
                        </select>                                               
                    </div>
                    <div class="col">
                        <label class="label_select" for="estado_civil">Estado Civil:</label>
                        <select class="form-select form-select-lg mb-3"  name="estado_civil" id="estado_civil">
                            <option selected>Selecione:</option>
                            <option value="Casado">Casado</option>
                            <option value="Solteiro">Solteiro</option>
                            <option value="Separado">Separado</option>
                            <option value="Divorciado">Divorciado</option>
                            <option value="Viúvo">Viúvo</option>
                        </select>
                    </div>

                </div>
                <div class="row">
                    <div class="col">
                    <label class="label_select" for="raca_cor">Raça/Cor:</label>
                        <select class="form-select form-select-lg mb-3"  name="raca_cor" id="raca_cor">
                            <option selected>Selecione:</option>
                            <option value="Branco">Branco</option>
                            <option value="Preto">Preto</option>
                            <option value="Pardo">Pardo</option>
                            <option value="Indígena">Indígena</option>
                            <option value="Não Informado">Não Informado</option>
                        </select>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="naturalidade" id="naturalidade" placeholder="Naturalidade:">
                            <label for="naturalidade">Naturalidade:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px;" class="form-control" type="text" name="nacionalidade" id="nacionalidade" value="Brasileiro" placeholder="Nacionalidade:">
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
                            <input style="height: 50px" class="form-control" type="text" name="cep" id="cep" placeholder="CEP:" >
                            <label for="cep">CEP:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="rua" id="rua" placeholder="rua:" >
                            <label for="rua">Rua:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="numero" id="numero" placeholder="Número:" >
                            <label for="numero">Número:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="complemento" id="complemento" placeholder="Complemento:" >
                            <label for="complemento">Complemento:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="bairro" id="bairro" placeholder="bairro:" >
                            <label for="bairro">Bairro:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="cidade" id="cidade" placeholder="Cidade:" >
                            <label for="cidade">Cidade:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input style="height: 50px" class="form-control" type="text" name="estado" id="estado" placeholder="Estado:" >
                            <label for="estado">Estado(UF):</label>
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
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="cpf" id="cpf" placeholder="CPF:">
                            <label for="cpf">CPF:</label>
                        </div>
                    </div>           
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="rg" id="rg" placeholder="RG:">
                            <label for="rg">RG:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="rg_uf" id="rg_uf" placeholder="UF:">
                            <label for="rg_uf">UF:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="orgao" id="orgao" placeholder="Órgão:">
                            <label for="orgao">Órgão:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="date" name="emissao_rg" id="emissao_rg" placeholder="Emissão RG:">
                            <label for="emissao_rg">Emissão RG:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="numero_ctps" id="numero_ctps" placeholder="">
                            <label for="numero_ctps">Número CTPS:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="serie_ctps" id="serie_ctps" placeholder="Série CTPS:">
                            <label for="serie_ctps">Série CTPS:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="estado_ctps" id="estado_ctps" placeholder="UF CTPS:">
                            <label for="estado_ctps">UF CTPS:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="date" name="expedicao_ctps" id="expedicao_ctps" placeholder="Expedição CTPS:">
                            <label for="expedicao_ctps">Expedição CTPS:</label>
                        </div>
                    </div>
                </div> 
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="numero_pis" id="numero_pis" placeholder="Número PIS:">
                            <label for="numero_pis">Número PIS:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="cadastro_pis" id="cadastro_pis" placeholder="Cadastro PIS:">
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
                        <label class="label_select" for="instrucao_escolaridade">Escolaridade:</label>
                        <select class="form-select form-select-lg mb-3" name="instrucao_escolaridade" id="instrucao_escolaridade">
                            <option selected>Selecione:</option>
                            <option value="Fundamental Incompleto">Fundamental Incompleto</option>
                            <option value="Fundamental Completo">Fundamental Completo</option>
                            <option value="Médio Incompleto">Médio Incompleto</option>
                            <option value="Médio Completo">Médio Completo</option>
                            <option value="Superior Incompleto">Superior Incompleto</option>
                            <option value="Superior Completo">Superior Completo</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="cnh" id="cnh" placeholder="CNH:">
                            <label for="cnh">CNH:</label>
                        </div>
                    </div>
                    <div class="col">
                        <label class="label_select" for="categoria_cnh">Categoria da CNH:</label>
                        <select class="form-select form-select-lg mb-3" name="categoria_cnh" id="categoria_cnh">
                            <option selected>Selecione:</option>
                            <option value="ACC">ACC</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            <option value="E">Não possui</option>
                        </select>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="date" name="validade_cnh" id="validade_cnh" placeholder="Validade CNH:">
                            <label for="validade_cnh">Validade CNH:</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label class="label_select" for="reservista">Reservista:</label>
                        <select class="form-select form-select-lg mb-3" name="reservista" id="reservista">
                            <option selected>Selecione:</option>
                            <option value="0">NÃO</option>
                            <option value="1">SIM</option>
                        </select>
                    </div>
                    <div class="col">
                        <label class="label_select" for="categoria_reservista">Categoria do Reservista:</label>
                        <select class="form-select form-select-lg mb-3" name="categoria_reservista" id="categoria_reservista">
                            <option selected>Selecione:</option>
                            <option value="1º">1º Categoria</option>
                            <option value="2º">2º Categoria</option>
                            <option value="3º">3º Categoria</option>
                            <option value="Não é Reservista">Não é Reservista</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="titulo_eleitoral" id="titulo_eleitoral" placeholder="Título Eleitoral:">
                            <label for="titulo_eleitoral">Título Eleitoral:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="zona_eleitoral" id="zona_eleitoral" placeholder="Zona:">
                            <label for="zona_eleitoral">Zona:</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input class="form-control" type="text" name="secao_eleitoral" id="secao_eleitoral" placeholder="Seção:">
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
                            <div class="form-floating mb-3">
                                <input style="height: 50px;" class="form-control" type="text" name="banco" id="banco" value="341 - ITAU UNIBANCO S.A." placeholder="Banco:">
                                <label for="banco">Banco:</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="conta_banco" id="conta_banco" placeholder="Conta:">
                                <label for="conta_banco">Conta:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="digito_conta" id="digito_conta" placeholder="Dígito:">
                                <label for="digito_conta">Dígito:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="agencia_banco" id="agencia_banco" placeholder="Agência:">
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
                            <div class="form-floating mb-3">
                                <input style="height: 50px;" class="form-control" type="text" name="sindicato" id="sindicato" placeholder="Sindicato:">
                                <label for="sindicato">Sindicato:</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="cons_profis" id="cons_profis" placeholder="Cons. Profis:">
                                <label for="cons_profis">Cons. Profis:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="registro_profis" id="registro_profis" placeholder="Registro Profis:">
                                <label for="registro_profis">Registro Profis:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="date" name="data_registro_profis" id="data_registro_profis" placeholder="Data Registro:">
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
                            <p style="font-size: 2rem">O Colaborador precisa de Dados de Registro?</p>
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
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="codigo_ficha" id="codigo_ficha" placeholder="Código:">
                                <label for="codigo_ficha">Código:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="nr_recibo_ficha" id="nr_recibo_ficha" placeholder="Nr. Recibo:">
                                <label for="nr_recibo_ficha">Nr. Recibo:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="matricula_esocial" id="matricula_esocial" placeholder="Matrícula eSocial:">
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
                    <div class="row">
                        <p style="font-size: 1.8rem"><b>Selecione o tamanho:</b></p>
                        <div class="col">
                            <label class="label_select" for="japona">Japona:</label>
                            <select class="form-select form-select-lg mb-3" name="japona" id="japona">
                                <option selected>Selecione:</option>
                                <option value="P">P</option>
                                <option value="M">M</option>
                                <option value="G">G</option>
                                <option value="GG">GG</option>
                                <option value="XG">XG</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="label_select" for="calca">Calça:</label>
                            <select class="form-select form-select-lg mb-3" name="calca" id="calca">
                                <option selected>Selecione:</option>
                                <option value="P">P</option>
                                <option value="M">M</option>
                                <option value="G">G</option>
                                <option value="GG">GG</option>
                                <option value="XG">XG</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="label_select" for="bota">Bota:</label>
                            <select class="form-select form-select-lg mb-3" name="bota" id="bota">
                                <option selected>Selecione:</option>
                                <?php
                                    for($i=32; $i<=47; $i++)
                                    echo "<option value='$i'>Nº:$i</option>"
                                ?>
                            </select>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label class="label_select" for="luva">Luva:</label>
                            <select class="form-select form-select-lg mb-3" name="luva" id="luva">
                                <option selected>Selecione:</option>
                                <option value="termica">Térmica</option>
                                <option value="tatica">Tática</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="label_select" for="meiao">Meião:</label>
                            <select class="form-select form-select-lg mb-3" name="meiao" id="meiao">
                                <option selected>Selecione:</option>
                                <?php
                                    for($i=32; $i<=47; $i++)
                                    echo "<option value='$i'>Nº: $i</option>"
                                ?>
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
                            <div class="form-floating mb-3">
                                <input class="form-control" type="date" name="estg_data_chegada" id="estg_data_chegada" placeholder="Data de chegada:">
                                <label for="estg_data_chegada">Data de chegada:</label>
                            </div>
                        </div>
                        <div class="col">
                            <label class="label_select" for="estg_tipo_visto">Tipo de visto:</label>
                            <select class="form-select form-select-lg mb-3" name="estg_tipo_visto" id="estg_tipo_visto" placeholder="Tipo de visto:">
                                <option selected>Selecione:</option>
                                <option value="Diplomático">Visto Diplomático</option>
                                <option value="Oficial">Visto Oficial</option>
                                <option value="Cortesia">Visto de Cortesia</option>
                                <option value="Turista">Visto de Turista</option>
                                <option value="Trânsito">Visto de Trânsito</option>
                                <option value="Negócios">Visto de Negócios</option>
                                <option value="Permanente">Visto Permanente</option>
                                <option value="Temporários">Vistos Temporários</option>
                            </select>
                        </div>
                    </div>                   
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="estg_carteira_rne" id="estg_carteira_rne" placeholder="Carteira RNE:">
                                <label for="estg_carteira_rne">Carteira RNE:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="date" name="estg_validade_rne" id="estg_validade_rne" placeholder="Validade RNE:">
                                <label for="estg_validade_rne">Validade RNE:</label>
                            </div>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="text" name="estg_nr_portaria" id="estg_nr_portaria" placeholder="Número da Portaria:">
                                <label for="estg_nr_portaria">Número da Portaria:</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input class="form-control" type="date" name="estg_data_portaria" id="estg_data_portaria" placeholder="Data da Portaria:">
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
</body>    
</html>

<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        require_once "assets/classes/colaborador.php";
        $colaborador = new Colaborador();

        $colaborador->__set("cpf", $_POST['cpf']);
        $colaborador->__set("rg", $_POST['rg']);
        $colaborador->__set("rg_uf", $_POST['rg_uf']);
        $colaborador->__set("orgao", $_POST['orgao']);
        $colaborador->__set("emissao_rg", date('Y-m-d', strtotime($_POST['emissao_rg'])));
        $colaborador->__set("nome_colaborador", $_POST['nome_colaborador']);
        $colaborador->__set("mae_colaborador", $_POST['mae_colaborador']);
        $colaborador->__set("pai_colaborador", $_POST['pai_colaborador']);
        $colaborador->__set("nascimento", date('Y-m-d', strtotime($_POST['nascimento'])));
        $colaborador->__set("sexo", $_POST['sexo']);
        $colaborador->__set("estado_civil", $_POST['estado_civil']);
        $colaborador->__set("raca_cor", $_POST['raca_cor']);
        $colaborador->__set("naturalidade", $_POST['naturalidade']);
        $colaborador->__set("nacionalidade", $_POST['nacionalidade']);
        $colaborador->__set("cidade", $_POST['cidade']);
        $colaborador->__set("estado", $_POST['estado']);
        $colaborador->__set("bairro", $_POST['bairro']);
        $colaborador->__set("rua", $_POST['rua']);
        $colaborador->__set("numero", $_POST['numero']);
        $colaborador->__set("complemento", $_POST['complemento']);
        $colaborador->__set("cep", $_POST['cep']);
        $colaborador->__set("numero_ctps", $_POST['numero_ctps']);
        $colaborador->__set("serie_ctps", $_POST['serie_ctps']);
        $colaborador->__set("estado_ctps", $_POST['estado_ctps']);
        $colaborador->__set("expedicao_ctps", date('Y-m-d', strtotime($_POST['expedicao_ctps'])));
        $colaborador->__set("numero_pis", $_POST['numero_pis']);
        $colaborador->__set("cadastro_pis", $_POST['cadastro_pis']);
        $colaborador->__set("instrucao_escolaridade", $_POST['instrucao_escolaridade']);
        $colaborador->__set("cnh", $_POST['cnh']);
        $colaborador->__set("categoria_cnh", $_POST['categoria_cnh']);
        $colaborador->__set("validade_cnh", date('Y-m-d', strtotime($_POST['validade_cnh'])));
        $colaborador->__set("reservista", $_POST['reservista']);
        $colaborador->__set("categoria_reservista", $_POST['categoria_reservista']);
        $colaborador->__set("titulo_eleitoral", $_POST['titulo_eleitoral']);
        $colaborador->__set("zona_eleitoral", $_POST['zona_eleitoral']); 
        $colaborador->__set("secao_eleitoral", $_POST['secao_eleitoral']);

        if($_POST['btn_banco'] === 'sim'){
            $colaborador->__set("banco", $_POST['banco']);
            $colaborador->__set("conta_banco", $_POST['conta_banco']);
            $colaborador->__set("digito_conta", $_POST['digito_conta']);
            $colaborador->__set("agencia_banco", $_POST['agencia_banco']);
        } else {
            $colaborador->__set("banco", null);
            $colaborador->__set("conta_banco", null);
            $colaborador->__set("digito_conta", null);
            $colaborador->__set("agencia_banco", null);
        }

        if($_POST['btn_sdct'] === 'sim'){
            $colaborador->__set("sindicato", $_POST['sindicato']);
            $colaborador->__set("cons_profis", $_POST['cons_profis']);
            $colaborador->__set("registro_profis", $_POST['registro_profis']);
            $colaborador->__set("data_registro_profis", date('Y-m-d', strtotime($_POST['data_registro_profis'])));
        } else {
            $colaborador->__set("sindicato", null);
            $colaborador->__set("cons_profis", null);
            $colaborador->__set("registro_profis", null);
            $colaborador->__set("data_registro_profis", null);
        }

        if($_POST['btn_rgst'] === 'sim'){
            $colaborador->__set("codigo_ficha", $_POST['codigo_ficha']);
            $colaborador->__set("nr_recibo_ficha", $_POST['nr_recibo_ficha']);
            $colaborador->__set("matricula_esocial", $_POST['matricula_esocial']);
        } else {
            $colaborador->__set("codigo_ficha", null);
            $colaborador->__set("nr_recibo_ficha", null);
            $colaborador->__set("matricula_esocial", null);
        }

        if($_POST['btn_estg'] === 'sim'){
            $colaborador->__set("estg_data_chegada", date('Y-m-d', strtotime($_POST['estg_data_chegada'])));
            $colaborador->__set("estg_tipo_visto", $_POST['estg_tipo_visto']);
            $colaborador->__set("estg_data_portaria", date('Y-m-d', strtotime($_POST['estg_data_portaria'])));
            $colaborador->__set("estg_nr_portaria", $_POST['estg_nr_portaria']);
            $colaborador->__set("estg_carteira_rne", $_POST['estg_carteira_rne']);
            $colaborador->__set("estg_validade_rne", date('Y-m-d', strtotime($_POST['estg_validade_rne'])));
        } else {
            $colaborador->__set("estg_data_chegada", null);
            $colaborador->__set("estg_tipo_visto", null);
            $colaborador->__set("estg_data_portaria", null);
            $colaborador->__set("estg_nr_portaria", null);
            $colaborador->__set("estg_carteira_rne", null);
            $colaborador->__set("estg_validade_rne", null);
        }

        $colaborador->novoColaborador();

        if($_POST['btn_epi'] === 'sim'){
            $epi = new EPI();
            $epi->__set("id_colaborador", $_SESSION['last_id']);
            $epi->__set("japona", $_POST['japona']);
            $epi->__set("calca", $_POST['calca']);
            $epi->__set("bota", $_POST['bota']);
            $epi->__set("luva", $_POST['luva']);
            $epi->__set("meiao", $_POST['meiao']);
            $epi->adicionarEPIS();
            $_SESSION['last_id'] = null;
        }
    }
?>  
