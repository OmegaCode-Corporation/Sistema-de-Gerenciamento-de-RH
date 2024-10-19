<?php
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
   echo "<script>window.location.href = '../index.php'</script>";
}

require_once "contrato.php";
require_once "colaborador.php";
require_once "unidade.php";
require_once 'assets/relatorio/vendor/autoload.php';
use Dompdf\Dompdf;
ob_start();
class Documento{
   private $dados = null;
   private $id_colaborador = null;
   private $id_unidade = null;
   private $id_contrato = null;
   private $id_prorrogacao = null;

   public function __construct(){
      $this->dados = "<!DOCTYPE html>
                      <html lang='pt-br'>
                      <head>
                      <meta charset='UTF-8'>
                      <link rel='stylesheet' href='CAMINHO'>
                      <title>Documentos</title>
                      </head>
                      <body>";
   }
   public function __set($name, $value){
      $this->$name = $value;
   }
   public function DocContrato(){
      $dados_unidade = $this->DadosInformacao("unidade");
      $cnpj_formatadado = substr_replace($dados_unidade['cnpj_unidade'],".",2,0);
      $cnpj_formatadado = substr_replace($cnpj_formatadado,".",6,0);
      $cnpj_formatadado = substr_replace($cnpj_formatadado,"/",10,0);
      $cnpj_formatadado = substr_replace($cnpj_formatadado,"-",15,0);
      $dados_colaborador = $this->DadosInformacao("colaborador");
      $this->dados .= "
                        <h4 class='titulo'>CONTRATO DE TRABALHO A TÍTULO DE EXPERIÊNCIA</h4>
                        <div class='textoDocContrato'>
                        <p>
                        Por   este   instrumento  particular,   que   entre   si   fazem   a   empresa   ".$dados_unidade['empresa_unidade']."   -   EPP   inscrita    no    CNPJ/CPF  
                        sob   n°   ".$cnpj_formatadado."  com   sede   neste   município   de   ".$dados_unidade['municipio_unidade'].",   à   ".$dados_unidade['endereco_unidade'].",   bairro   ".$dados_unidade['bairro_unidade'].",   neste    ato    denominada 
                        'Empregadora',  e  o  Sr.(a)  ".$dados_colaborador['nome_colaborador'].",   portador(a)   da   Carteira   Profissional   nº   ".$dados_colaborador['numero_ctps'].",   série   ".$dados_colaborador['serie_ctps']."   -   ".$dados_colaborador['estado_ctps'].",   inscrito   no   CPF   sob   nº 
                        ".$dados_colaborador['cpf']."     e     cadastrado     no     PIS-PASEP     sob     nº     ".$dados_colaborador['numero_pis'].",     doravante,     chamado,     simplesmente,     'Empregado',     firmam     o     presente 
                        contrato  individual  de   trabalho,   em   caráter   de   experiência,   conforme   a   letra   'c',   parágrafo   2º   do   Artigo   443   da   Consolidação   das   Leis   do   Trabalho,
                        mediante as seguintes condições:<br>
                        <br>
                           1)   Empregado   trabalhará   para   a   empregadora,   exercendo   a   função   de   Promotor   de   Vendas   Motorizado    na    seção    FUNCIONARIO,    percebendo    o 
                        salário de R$ 1.652,22 por mês, pagável de forma Mensal.<br>
                        <br>
                           2) O horário a ser obedecido será o seguinte:<br>
                           segunda-feira à sexta-feira das 08:00 às 12:00 e das 13:00 às 17:00, sábado das 08:00 às 12:00 e domingo DSR.<br>
                        <br>
                           3)   Este   contrato   tem   início   a   partir   de   21/06/2023,   vencendo-se   em    20/07/2023,    podendo    ser    prorrogado,    obedecendo    o    disposto    no    Parágrafo 
                        Único do Artigo 445 da CLT.<br>
                        <br>
                           4)  O   Empregado   se   compromete   a   trabalhar   em   regime   de   compensação   e   de   prorrogação   de   horas,   inclusive   em   período   noturno,   sempre   que   as 
                        necessidades assim exigirem, observadas as formalidades legais.<br>
                        <br>
                           5)   Obriga-se   o   Empregado,   além   de   executar   com   dedicação   e   legalidade   o   seu   serviço,   a   cumprir   o    Regulamento    Interno    da    Empregadora,    as 
                        instruções de sua administração e as ordens de seus chefes e superiores hierárquicos, relativos às peculiaridades dos serviços que lhe forem confiados.<br>
                        <br>
                           6)   Aplicam-se   a   este   contrato   todas   as   normas   em   vigor,   relativas   aos   contratos   a   prazo   determinado,   devendo   sua   rescisão   antecipada,   por    justa 
                        causa, obedecer ao disposto nos artigos 482 e 483 da CLT, conforme o caso.<br>
                        <br>
                           7)   Vencido   o   período   experimental   e   continuando   o   empregado   a   prestar    serviços    à    Empregadora,    por    tempo    indeterminado,    ficam    prorrogadas
                        todas as cláusulas aqui estabelecidas, enquanto não se rescindir o contrato de trabalho.<br>
                        <br>
                           8)    A    Empregadora,    ciente    da    necessidade    de    proteger    direitos    fundamentais    de    liberdade    e    de    privacidade    e    o    livre    desenvolvimento    da 
                        personalidade   da   pessoa   natural,   assume   o   compromisso   de   implementar   as    disposições    previstas    na    Lei    Federal    n°    13.709/2018    ('Lei    Geral    de
                        Proteção de Dados' ou 'LGPD') e também exigirá que todos os seus colaboradores(Empregados) façam o mesmo.<br>
                        §1º.    Para    fins    do    presente    instrumento,    os    termos    'Controlador',    'Dado    Pessoal',    'Operador',    'Titular'    e    'Tratamento',    independentemente    de
                        estarem no plural ou singular, masculino ou feminino, deverão ser lidos e interpretados de acordo com a aludida Lei Federal n° 13.709/2018.<br>
                        §2º. A Empregadora (Controladora de dados, nos termos da LGPD) declara, por meio deste instrumento, que cumpre toda a legislação aplicável sobre
                        privacidade e proteção de dados, inclusive a LGPD, sem exclusão das demais normas setoriais ou gerais sobre o tema, assegurando, ainda, que todas
                        suas instruções, solicitações e determinações decorrentes do Contrato são lícitas e não contrariam a legislação vigente, enquanto o Empregado 
                        (Titular de dados, nos termos da LGPD) se compromete a apenas realizar atividades de Tratamento de Dados Pessoais de acordo com o necessário
                        para a execução do objeto do Contrato.<br>
                        §3º. Os Dados Pessoais recebidos ou acessados pela Empregadora em decorrência do Contrato serão tratados com a devida aplicação de medidas
                        técnicas e administrativas aptas a protegê-los de acessos e utilizações não autorizados e/ou de situações acidentais ou ilícitas de destruição, perda,
                        alteração, comunicação ou difusão.<br>
                        §4º. Fica o Empregado desde já ciente de que a Empregadora poderá envolver terceiros nas atividades de Tratamento de Dados Pessoais decorrentes 
                        deste Contrato, inclusive na modalidade de subcontratação.<br>
                        §5º. O Empregado declara-se ciente de que a Empregadora, visando a plena execução do Contrato e observadas as regulamentações e diretrizes da
                        Autoridade Nacional de Proteção de Dados, poderá, por si ou terceiros, armazenar Dados Pessoais fora do território brasileiro. Assim, nos termos do
                        artigo 33 da LGPD, a Empregadora poderá atuar com empresas estrangeiras, e, seus respectivos países, pactuando somente com países que tenham 
                        legislações equivalentes à LGPD.<br>
                        §6º. Caso tome conhecimento da ocorrência de acesso não autorizado, divulgação indevida e/ou de situação de destruição, perda, alteração, 
                        comunicação ou difusão que afete os Dados Pessoais tratados em decorrência do Contrato, a Empregadora se compromete a enviar comunicação ao
                        empregado por escrito, em prazo razoável, observadas eventuais disposições legais aplicáveis. Referida comunicação conterá as seguintes 
                        informações, sempre que razoavelmente disponíveis:<br>
                        (a) data e hora do evento, se conhecidas;<br>
                        (b) data e hora da ciência;<br>
                        (c) relação dos tipos de dados afetados;<br>
                        (d) relação de dados afetados;<br>
                        (e) dados de contato do Encarregado pelo Tratamento de Dados da Empregadora ou outra pessoa junto à qual seja possível obter maiores
                        informações sobre o evento;<br>
                        (f) descrição das possíveis consequências e riscos para o Titular dos Dados Pessoais afetados; e<br>
                        (g) indicação das medidas de segurança adotadas antes e depois do evento, inclusive daquelas que estiverem sendo implementadas para minimizar o 
                        dano e a probabilidade de novas ocorrências.<br>
                        §7º. Em cumprimento ao artigo 7º, § 5º, da Lei 13.709/2018, o Empregador obteve o consentimento do empregado (titular dos dados pessoais) para 
                        comunicar ou compartilhar os mesmos dados pessoais com terceiros, ressalvadas as hipóteses de dispensa do consentimento previstas na Lei
                        supramencionada.<br>
                        §8º. A Empregadora e o empregado ficam obrigados a manter o mais absoluto sigilo com relação a toda e qualquer informação que venham a ter 
                        conhecimento em razão do presente Contrato, devendo utilizar tais informações exclusivamente com a finalidade de cumprir o objeto do presente 
                        Instrumento.<br>
                        §9º. O Empregado compromete-se a atuar de modo a proteger e a garantir o tratamento adequado dos dados pessoais a que tiverem acesso durante a
                        relação contratual, bem como a cumprir as disposições da Lei nº 13.709/2018.<br>
                        §10º. Cada Parte será individualmente responsável pelo cumprimento de suas obrigações decorrentes da LGPD e das regulamentações emitidas
                        posteriormente pela autoridade reguladora competente.<br>
                        §11º. De acordo com o que determina a Lei Geral de Proteção de Dados, as Partes obrigam-se a tratar os dados pessoais a que tiverem acesso 
                        unicamente para os fins e pelo tempo necessário ao cumprimento das suas obrigações e à adequada execução do objeto contratual, ou, ainda, com
                        fundamento em outra base legal válida e específica. A responsabilidade pela qualidade, correção e autenticidade dos dados transmitidos à Empregadora 
                        é do Empregado. Portanto, o empregado zelará pela veracidade dos dados transmitidos e adotará, de forma imediata, quaisquer medidas corretivas
                        caso se constate alguma anomalia, aferível documentalmente.<br>
                        §12º. Fica acordado, restando previamente notificado o Empregado, que os seus dados, exceto a manutenção dos mesmos dados ao cumprimento de 
                        obrigação legal ou regulatória, serão excluídos ou anonimizados após a rescisão contratual.<br>
                        §13º. O Empregado cederá os dados pessoais necessários para os fins específicos do presente contrato, garantida a proteção destes dados e a sua 
                        confidencialidade em qualquer hipótese, de acordo com este instrumento e com a legislação vigente.<br>
                        §14º. O Empregado fica ciente de que, dependendo como ocorrer a solicitação de dados por alguma autoridade prevista na LGPD, como, por 
                        exemplo, em demanda judicial que tramita sob o manto do segredo de justiça, poderá não ser comunicado, salvo com a autorização específica, quando
                        tratar-se de cumprimento de ordem judicial.<br>
                        §15º. A Empregadora disponibiliza, através do canal de acesso (jother@jtrcontabilidade.com.br), o contato com o seu Encarregado pela Proteção de
                        Dados. Neste canal poderão ser levadas a efeito todas as requisições cabíveis (v.g., direito à informação, portabilidade etc.), com o respectivo
                        atendimento dentro do prazo legal, sem custos. Ademais, quando exigido pela Autoridade Nacional, será apresentado pelo mesmo meio as Regras de
                        Boas Práticas e de Governança e o Relatório de Impacto à Proteção de Dados.<br>
                        <br>
                        E por estarem de pleno acordo, assinam ambas as partes, em duas vias de igual teor, na presença de duas testemunhas.<br>
                        <br>
                        <br>
                        <p class='cidade'>Juiz de Fora, ".date("d")."/".date("m")."/".date("Y").".</p><br>
                        <br>
                        <p class='assinatura'>________________________________________________ <br> Assinatura do Responsável quando menor </p>                   
                        <p class='assinatura'>________________________________________________ <br> Empregado </p>
                        <p class='assinatura3'>________________________________________________ <br> Empresa </p>            
                                    
                        
                        </p>
                        </div>
                        ";

      $filename = "Contrato de Trabalho de " . $dados_colaborador['nome_colaborador'] . ".pdf" ;
      $this->gerarPdf($filename);
      
   }
   public function DocProrrogacao(){
      $dados_unidade = $this->DadosInformacao("unidade");
      $dados_colaborador = $this->DadosInformacao("colaborador");
      $dados_contrato = $this->DadosInformacao("contrato");
      $dados_prorrogacao = $this->DadosInformacao("prorrogacao");

      setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
      date_default_timezone_set('America/Sao_Paulo');

      $data_contrato = strftime("%d de %B de %Y", strtotime(date($dados_contrato['admissao'])));
      $data_prorrogacao = strftime("%d de %B de %Y", strtotime(date($dados_prorrogacao['data_prorrogacao'])));
      // $data_contrato = date("d/m/Y", strtotime($dados_contrato['admissao']));
      // $data_prorrogacao = date("d/m/Y", strtotime($dados_prorrogacao['data_prorrogacao']));

      $this->dados .= "<h4 class='titulo'>PRORROGAÇÃO DO CONTRATO DE TRABALHO POR EXPERIÊNCIA</h4>
                       <div class='textoDocProrrogacao'>
                       <p>
                        Por  este  instrumento   particular,   que   entre   si   fazem   a   empresa   ".$dados_unidade['empresa_unidade']."   E   EVENTOS   LTDA   -   EPP   com   sede   neste   município 
                        de  ".$dados_unidade['municipio_unidade'].",   à   ".$dados_unidade['endereco_unidade']."   ,   neste   ato   denominada   'Empregadora',   e   o   Sr.(a)   ".$dados_colaborador['nome_colaborador'].",   portador(a)   da   Carteira
                        Profissional   nº   ".$dados_colaborador['numero_ctps'].",   série   7664-   MG,   inscrito   no   CPF   sob   nº   126.320.776-64   e   cadastrado    no    PIS-PASEP    sob    nº    160.29282.19.1,    doravante,
                        chamado,   simplesmente,   'Empregado',   fica   ajustada   a   prorrogação   do   contrato   de   trabalho   por   experiência,   firmado   em   $data_contrato, ou seja, até $data_prorrogacao, mantidas as demais cláusulas contratuais.<br>
                        <br>
                        <p class='cidade'>Juiz de Fora, ".date("d")."/".date("m")."/".date("Y").".</p><br>
                        <p class='assinatura3'>________________________________________________ <br> Empresa </p>  
                        <p class='assinatura'>________________________________________________ <br> Empregado </p>
                       </p>
                       </div>";
      $filename = "Prrorrogação Contrato de Trabalho de " . $dados_colaborador['nome_colaborador'] . ".pdf" ;
      $this->gerarPdf($filename);
   }
   public function FichaRegistroEmprego(){
      $dados_contrato = $this->DadosInformacao("contrato");
      if($dados_contrato['optante_fgts'] == "1"){
         $dados_contrato['optante_fgts'] = "Sim";
      }else{
         $dados_contrato['optante_fgts'] = "Não";
      }
      $dados_ficha_familiar = $this->DadosInformacao("ficha");
      $this->id_unidade = $dados_contrato['id_unidade'];
      $dados_unidade = $this->DadosInformacao("unidade");
      $cnpj_formatadado = substr_replace($dados_unidade['cnpj_unidade'],".",2,0);
      $cnpj_formatadado = substr_replace($cnpj_formatadado,".",6,0);
      $cnpj_formatadado = substr_replace($cnpj_formatadado,"/",10,0);
      $cnpj_formatadado = substr_replace($cnpj_formatadado,"-",15,0);
      $this->id_colaborador = $dados_contrato['id_colaborador'];
      $dados_colaborador = $this->DadosInformacao("colaborador");
      if($dados_colaborador['sexo'] == "M"){
         $dados_colaborador['sexo'] = "Masculino";
      }else{
         $dados_colaborador['sexo'] = "Feminino";
      }
      $this->dados .= "<h4 class='titulo'>Ficha de Registro de Empregado</h4>
                        <div class='subtitulo' style='margin-top: -3%; margin-bottom: 3%;'>
                           Dados do Empregador
                        </div>                      
                        <div class='textoFichaRegistro'>   
                           <p>                        
                              Empresa: ".$dados_unidade['empresa_unidade']." - EPP Nº<br>
                              CNPJ/CEI: ".$cnpj_formatadado."<br>
                              Ativ Federal: ".$dados_unidade['atv_federal_unidade']."<br>
                              Endereço: ".$dados_unidade['endereco_unidade']."<br>
                              Bairro: ".$dados_unidade['bairro_unidade']."<br>
                              Município: ".$dados_unidade['municipio_unidade']." - ".$dados_unidade['estado_unidade']." - ".$dados_unidade['cep_unidade']."
                           </p>
                        </div>
                        <div class='subtitulo'>
                           Dados do Empregado
                        </div>
                        <div class='texto2'>                 
                           <p>                            
                              Nome: ".$dados_colaborador['nome_colaborador']."<br>
                              Pai: ".$dados_colaborador['pai_colaborador']."<br>
                              Mãe: ".$dados_colaborador['mae_colaborador']."<br>
                              Nascimento: ".$dados_colaborador['nascimento']."  &nbsp;&nbsp;&nbsp;Sexo: ".$dados_colaborador['sexo']." &nbsp;&nbsp;&nbsp;Est. Civil: ".$dados_colaborador['estado_civil']." &nbsp;&nbsp;&nbsp;Raça/Cor: ".$dados_colaborador['raca_cor']."<br>
                              Naturalidade: ".$dados_colaborador['naturalidade']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nacionalidade: ".$dados_colaborador['nacionalidade']."<br>
                              Endereço: ".$dados_colaborador['rua'].",  ".$dados_colaborador['numero']."<br>
                              Bairro: ".$dados_colaborador['bairro']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CEP: ".$dados_colaborador['cep']."<br>
                              CPF: ".$dados_colaborador['cpf']."<br>
                              <div class='foto'></div>
                              RG: ".$dados_colaborador['rg']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Órgão:".$dados_colaborador['orgao']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Estado: ".$dados_colaborador['rg_uf']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Emissão RG: ".$dados_colaborador['emissao_rg']."<br>
                              Número CTPS: 1263207 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Série CTPS: 7664 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Estado CTPS: MG &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Expedição CTPS: 27/01/2022<br>
                              PIS: ".$dados_colaborador['numero_pis']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Cadastro PIS: ".$dados_colaborador['cadastro_pis']."<br>
                              Instrução: ".$dados_colaborador['instrucao_escolaridade']."<br>
                              CNH: ".$dados_colaborador['cnh']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Categoria CNH: ".$dados_colaborador['categoria_cnh']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Validade CNH: ".$dados_colaborador['validade_cnh']."<br>
                              Reservista: ".$dados_colaborador['reservista']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Categoria: ".$dados_colaborador['categoria_reservista']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tít. Eleitoral: ".$dados_colaborador['titulo_eleitoral']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Zona: ".$dados_colaborador['zona_eleitoral']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Seção: ".$dados_colaborador['secao_eleitoral']."<br>
                              Banco: ".$dados_colaborador['banco']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Conta: ".$dados_colaborador['conta_banco']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Dígito: ".$dados_colaborador['digito_conta']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Agência: ".$dados_colaborador['agencia_banco']."<br>
                              Sindicato: ".$dados_colaborador['sindicato']."<br>
                                 Cons. Profis: ".$dados_colaborador['cons_profis']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Registro Profis: ".$dados_colaborador['registro_profis']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Data Registro: ".$dados_colaborador['data_registro_profis']."<br> 
                              <br>
                           </p>
                           <p class='texto_direita'>
                              Código: ".$dados_colaborador['codigo_ficha']."<br>
                              Nr. Recibo: ".$dados_colaborador['nr_recibo_ficha']."<br>
                              Matrícula eSocial: ".$dados_colaborador['matricula_esocial']."
                           </p>
                        </div>
                        <div class='subtitulo'>
                           Cadastro de Estrangeiro
                        </div>
                        <div class='texto2'>                       
                           <p>                       
                              Data Chegada: ".$dados_colaborador['estg_data_chegada']."<br>
                              Tipo Visto: ".$dados_colaborador['estg_tipo_visto']."<br>
                              Carteira RNE: ".$dados_colaborador['estg_carteira_rne']."<br>
                              Validade RNE: ".$dados_colaborador['estg_validade_rne']."<br>
                              
                           </p>
                           <p class='texto_direita2'>
                              Número da Portaria: ".$dados_colaborador['estg_nr_portaria']."<br>
                              Data da Portaria: ".$dados_colaborador['estg_data_portaria']."
                           </p>
                        </div>
                        <div class='subtitulo'>
                           Contrato de Trabalho
                        </div>
                        <div class='texto2'>                   
                           <p>
                                 
                                 Admissão: ".$dados_contrato['admissao']."<br>
                                 Optante FGTS: ".$dados_contrato['optante_fgts']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Data Opção: ".$dados_contrato['data_opcao']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Conta FGTS:".$dados_contrato['conta_fgts']."<br>
                                 Cargo: ".$dados_contrato['cargo']." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   CBO:  ".$dados_contrato['cbo']."<br>
                                 Organograma: ".$dados_contrato['organograma']."<br>
                                 Ficha Familiar Nome &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nascimento &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Parentesco<br>              
      ";
      foreach($dados_ficha_familiar as $linha){
         $this->dados.= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$linha['nome_familar']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$linha['nascimento']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$linha['parentesco_familar']."<br>";
      }

      $this->dados.= " 
                     </p>
                        <br>
                        <p>Juiz de Fora, ".date("d")."/".date("m")."/".date("Y").".</p>
                        <p style='text-align: center; margin-left: 15%; margin-top: -5%;'>___________________________________________ <br> Assinatura Empregado  
                        <div class='alignpolegar'>
                           <p class='polegar'></p>
                           Polegar Direito 
                        </div>
                        <p>Data da dispensa ________________________,__________ de ____________ de ____________.
                        <p style='text-align: center; margin-left: 15%;'>___________________________________________________ <br> Assinatura Empregado

                     </div>
                     
      ";
      $filename = "Ficha Registro de " . $dados_colaborador['nome_colaborador'] . ".pdf" ;
      $this->gerarPdf($filename);
   }
   private function DadosInformacao($id){
      switch($id){
         case "unidade":
            $unidade = new Unidade();
            $resultado = $unidade->listarUnidades($this->id_unidade);
            foreach($resultado as $linha){
               $dados_unidade = $linha;
            }
            return $dados_unidade;
            break; 
         case "colaborador":
            $colaborador = new Colaborador();
            $resultado = $colaborador->listarColaboradores($this->id_colaborador);
            foreach($resultado as $linha){
               $dados_colaborador = $linha;
            }
            return $dados_colaborador;
            break;
         case "contrato":
            $contrato = new Contrato();
            $contrato->__set("id_contrato", $this->id_contrato);
            $resultado = $contrato->DadosContrato($this->id_contrato);
            foreach($resultado as $linha){
               $dados_contrato = $linha;
            }
            return $dados_contrato;
            break;
         case "prorrogacao":
            $prorrogacao = new Prorrogacao();
            $prorrogacao->__set("id_prorrogacao", $this->id_prorrogacao);
            $resultado = $prorrogacao->DadosProrrogacao($this->id_prorrogacao);
            foreach($resultado as $linha){
               $dados_prorrogacao = $linha;
            }
            return $dados_prorrogacao;
            break;
         case "ficha":
            $contrato = new Contrato();
            $contrato->__set("id_contrato",$this->id_contrato);
            $resultado = $contrato->DadosFamiliar();
            return $resultado;
            break;
      }
   }
   private function gerarPdf($filename){
      $this->dados .= "</body>
                       </html>";
      $dompdf = new Dompdf(['enable_remote' => true]);
      $dompdf->loadHtml($this->dados);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();
      // array("Attachment" => false) colocar isso dentro da stream para não fazer o dowload automaticamente
      $dompdf->stream($filename);

   }
}   
?>