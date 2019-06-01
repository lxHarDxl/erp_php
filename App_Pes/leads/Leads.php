<?
require_once("../config.php");
require_once("../conexao.php");
require_once("../funcoes.php");
require_once("../toolbarPrincipal.php");


$lead_ID = $_GET['lead_ID'];
$action  = "";
$type    = "leads";

if(!empty($lead_ID)){
    $sql = 'SELECT leads.*
                FROM 
                    leads 
                WHERE
                    lead_ID = "'.$lead_ID.'"
                LIMIT 1';
	$res = executar($sql);
    $lead = $res[0];
    
    $lead['lead_agendamento_recisao'] = convertDateToBr($lead['lead_agendamento_recisao']);
    $lead['lead_cnpj'] = mascara($lead['lead_cnpj'], 'cnpj');
    
	//LEAD ENDEREÇO
	$sql	= 'SELECT * FROM lead_endereco WHERE lead_ID = "'.$lead_ID.'" LIMIT 1';
	$res = executar($sql);
    $lead_endereco = $res[0];
    $lead_endereco['lead_endereco_cep'] = strlen($lead_endereco['lead_endereco_cep']) < 8 ? "0".$lead_endereco['lead_endereco_cep'] : $lead_endereco['lead_endereco_cep'];
    $lead_endereco['lead_endereco_cep'] = mascara($lead_endereco['lead_endereco_cep'], 'cep');

    $onclickAdicionaContatos = "resetModalContato();";
    $onclickSalvaContatos = "";
    $titleAdicionaContato = "";
    $action = "Alterar";


    // VERIFICA registro anterior e próximo
    $sql_registro_anterior_posterior = "SELECT l.lead_ID, l.lead_nome FROM leads l INNER JOIN lead_contatos lc ON l.lead_ID = lc.lead_ID {$_SESSION['login']['lead_where']} ORDER BY lead_contato_data, lead_nome ";

    $registros = idAnteriorPosterior($lead_ID, $sql_registro_anterior_posterior, true);    
}else{
    $onclickAdicionaContatos = "onclick='salvaLead(false);'";
    $onclickSalvaContatos = "onclick='atualizaPagina();'";
    $titleAdicionaContato = utf8_decode("Ao clicar nesse botão, além de abrir uma tela para o cadastro de contatos, também será inserido no Banco de Dados uma nova lead com as inforamções que foram preenchidas anteriormente.");
    $action = "Gravar";
}


$comboClients = executar('SELECT pessoa.nr_pessoa, nm_pessoa FROM pessoa LEFT JOIN cliente ON pessoa.nr_pessoa = cliente.nr_pessoa LEFT JOIN ctrt ON pessoa.nr_pessoa = ctrt.nr_pessoa JOIN tipo_cliente ON tipo_cliente.nr_tipo_cliente = cliente.nr_tipo_cliente WHERE ds_status = "A" GROUP BY `nr_pessoa` ORDER BY  pessoa.nm_pessoa');
$comboHubpop  = executar('SELECT nr_hubpop, CONCAT(nm_hubpop, "(", nm_apelido, ")") as lead_hubpop_br FROM hubpop WHERE status_hubpop = "S" ORDER BY nm_hubpop');
$comboEtapa   = executar("SELECT lead_etapa_ID, lead_etapa_nome FROM lead_etapa ORDER BY lead_etapa_prioridade");
$comboStatus  = executar("SELECT lead_status_ID, lead_status_nome FROM lead_status ORDER BY lead_status_nome");

$comboColaborador = executar('SELECT nr_func, UPPER(nm_pessoa) as nm_pessoa, ativo FROM func JOIN pessoa USING(nr_pessoa)JOIN login USING(nr_pessoa) WHERE ativo="S" ORDER BY nm_pessoa');


?>
<!DOCTYPE html>
<html>
<head>
	<title><?=$titulo.' '.$versao?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="Content-Language" content="pt-BR">
	<meta http-equiv="Cache-control" content="no-cache, must-revalidate">
	<meta http-equiv="expires" content="-1">
	<link HREF="<?=$css?>" REL="stylesheet" TYPE="text/css">
	<link HREF="<?=$caminho?>/menu.css" REL="stylesheet" TYPE="text/css">
	<link rel="stylesheet" href="<?=$caminho?>/jquery.tabs.css" type="text/css" media="print, projection, screen">
	<script language="JavaScript"  src="<?=$jscript?>"></script>
    <script language="JavaScript"  src="<?=$jsfuncao?>"></script>
    
    <link href="<?=$caminho?>/obj_js/jquery-ui.css" rel="stylesheet">
    <script language="JavaScript" src="<?=$caminho?>/obj_js/jquery-ui.js"></script>
	<script type="text/javascript" src="<?=$caminho?>/obj_js/jquery.tabs.pack.js"></script>
	<script language="JavaScript" src="<?=$caminho?>/obj_js/jquery.dimensions.js"></script>
	
    <link rel="stylesheet" type="text/css"  href="<?=$caminho?>/bootstrap/css/bootstrap.min.css">
	
    <script type="text/javascript" src="<?=$caminho?>/jquery/jquery-3.3.1.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.11/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.11/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script type="text/javascript" src="<?=$caminho?>/bootstrap/js/bootstrap.min.js"></script>


    <script type="text/javascript" src="http://code.jquery.com/qunit/qunit-1.11.0.js"></script>
  
    <script type="text/javascript" src="../obj_js/jQuery-Mask-Plugin-master/test/sinon-1.10.3.js"></script>
    <script type="text/javascript" src="../obj_js/jQuery-Mask-Plugin-master/test/sinon-qunit-1.0.0.js"></script>
    <script type="text/javascript" src="../obj_js/jQuery-Mask-Plugin-master/src/jquery.mask.js"></script>

    <style>
        #botoes {
            padding-top: -20px;
        }

        .form-group{
            font-size: 10px !important;
            margin-bottom: 4px;
        }
        .form-group input{
            font-size: 10px !important;
            height: -50% !important;
        }
        .form-group select{
            font-size: 10px !important;
            height: -50% !important;
        }
        .form-group textarea{
            font-size: 10px !important;
        }
        .form-group button{
            font-size: 13px !important;
            margin-left: -60px;
        }

        table th{
            font-size: 13px !important;
        }
        table td, .registro-anterior-posterior{
            font-size: 11px !important;
        }
        #box{
            padding-bottom: -100px !important;
        }

        #modalOSInfoLead-div {
            margin-top: -20px;
        }

        #btn-mudar-dono {
            margin-left: 0px !important;
            margin-top: 16px;
        }

        .radios-produto {
            margin-top: 15px;
            margin-bottom: 15px;
            font-size: 12px !important;
        }

        .radios-produto h6 {
            font-size: 15px !important;
        }
        
    </style>
</head>
<body style="background-color: #DCDCDC">
    <script>
		$(function(){
			var $doc = $('html, body');

            $('a').click(function() {
                $doc.animate({
                    scrollTop: $( $.attr(this, 'href') ).offset().top
                }, 500);
                return false;
            });
        });
    </script>

	<div id="cabecalho">
	        <?php require_once("../topo.php"); ?>
	</div>
		<?=toolBar($opcoes, $empresa)?>
	<div id="corpo" class="">
    <br />

        <div class="container-fluid">
            <div class="container border bg-light mb-3">                
                <form id="form-pesquisa" name="formulario" class="mx-auto w-75">
                    <div class="form-row mb-3">

                        <? if(!empty($lead_ID)) { ?>
                        <div class="col-md-10 my-2 registro-anterior-posterior">
                            <div class="row justify-content-center" id="box">
                                <nav class="ml-5 nav border-bottom" >
                                    <a class="nav-link pt-3" href="../App_Pes/Leads.php?lead_ID=<?=$registros['anterior']?>"  title="Vai para a lead: <?=$registros['anterior_title']?>"><?=utf8_decode("<< Anterior")?></a>
                                    <a class="nav-link h5 text-muted" href="#" title="Lead Atual"><?=$lead_ID?></a>
                                    <a class="nav-link pt-3 registro-posterior" href="../App_Pes/Leads.php?lead_ID=<?=$registros['posterior']?>" id="<?=$registros['posterior']?>" title="Vai para a proxima lead: <?=$registros['posterior_title']?>"><?=utf8_decode("Próximo >>")?></a>
                                </nav>
                            </div>
                        </div>
                        <? } ?>

                        <div class="col-md-10 my-3">
                            <nav class="nav">
                                <a class="nav-link h3 text-muted" href="#cabecalho"><?=utf8_decode("Leads")?></a>
                                <a class="nav-link pt-3" href="#comercial"><?=utf8_decode("Comercial")?></a>
                                <a class="nav-link pt-3" href="#contatos"><?=utf8_decode("Histórico de Contatos")?></a>
                                <a class="nav-link pt-3" href="#ordem-servico"><?=utf8_decode("Ordem de Serviço")?></a>
                            </nav>
                        </div>

                        <!-- Botão responsável pelo registro quando a lead se torna um cliente -->

                        <div class="col-md-2 pl-5" style="padding-top: 3%">

                        <? if(!empty($lead_ID) && empty($lead['nr_cliente'])){ 
                                if(!empty($lead['lead_cnpj'])){ ?>
                                    <button type="button" id="registra-cliente" class="btn btn-sm btn-outline-dark">Cadastro Cliente</button>
                                <? }?>

                            <img title="Voltar" src="<?=$caminho?>/imagens/icones/seta_esquerda.gif" alt="botao-voltar" class="voltar ml-5 mt-2" />
                        <? } ?>
                            
                        </div>

                        <!-- Informações Básicas do Cliente -->
                        <div class="col-md-12">
                            <p class="h6 text-muted mb-2" id="info-basica"><?=utf8_decode("Informações do Cliente")?></p>
                        </div>
                        <div class="form-group col-md-8">
                            Empresa
                            <input type="text" class="form-control form-control-sm" id="lead_nome" name="lead_nome" value="<?=$lead['lead_nome']?>" placeholder="">
                        </div>
                        <div class="form-group col-md-8" title="Quando preenchido corretamente, libera o botão 'Cadastro Cliente'">
                            CNPJ
                            <input type="text" class="form-control form-control-sm cnpj" id="lead_cnpj" name="lead_cnpj" value="<?=$lead['lead_cnpj']?>" placeholder="">
                            <div class="invalid-feedback" id="lead_cnpj_feedback">
                                <?=utf8_decode("Por favor, forneça um CNPJ válido.")?>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            Site
                            <input type="text" class="form-control form-control-sm" id="lead_site" name="lead_site" value="<?=$lead['lead_site']?>">
                        </div>

                        <!-- Endereço -->
                        <div class="col-md-12">
                            <p class="h6 text-muted mb-2 mt-3" id="endereco"><?=utf8_decode("Endereço")?></p>
                        </div>
                        <div class="form-group col-md-3">   
                            CEP
                            <input type="text" class="form-control form-control-sm w-50 cep" id="lead_endereco_cep" name="lead_endereco_cep" value="<?=$lead_endereco['lead_endereco_cep']?>" placeholder="00000-000">
                        </div>
                        <div class="form-group col-md-9 pt-3">
                            <button class="btn btn-outline-secondary btn-sm" type="button" name="busca-cep" id="busca-cep">Pesquisar CEP</button>
                        </div>
                        <div class="form-group col-md-3">
                            Tipo
                            <select class="form-control form-control-sm w-75" id="lead_endereco_tipo" name="lead_endereco_tipo">
                                <option></option>

                                <?
                                $arr = executar("SELECT descricao, codigo FROM tipo_logradouro ORDER BY descricao");

                                foreach($arr as $key ){

                                    if(!empty($lead_ID)){
                                        $verifOP = $key['codigo'] == $lead_endereco['lead_endereco_tipo'];
										$selecao = $verifOP ? "selected='selected'" : "";
                                    }

                                    ?>
                                    <option id="<?=$key['codigo']?>" <?=$selecao?> value="<?=$key['codigo']?>"><?=$key['descricao']?></option>
                                    <?
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group col-md-7">
                            Logradouro
                            <input type="text" class="form-control form-control-sm" id="lead_endereco_logradouro" name="lead_endereco_logradouro" value="<?=$lead_endereco['lead_endereco_logradouro']?>">
                        </div>
                        <div class="form-group col-md-2">
                            <?=utf8_decode("Número")?>
                            <input type="text" class="form-control form-control-sm" id="lead_endereco_numero" name="lead_endereco_numero" value="<?=$lead_endereco['lead_endereco_numero']?>">
                        </div>
                        <div class="form-group col-md-8">
                            Complemento
                            <input type="text" class="form-control form-control-sm" id="lead_endereco_complemento" name="lead_endereco_complemento" value="<?=$lead_endereco['lead_endereco_complemento']?>">
                        </div>
                        <div class="form-group col-md-4">
                            Bairro
                            <input type="text" class="form-control form-control-sm" id="lead_endereco_bairro" name="lead_endereco_bairro" value="<?=$lead_endereco['lead_endereco_bairro']?>">
                        </div>
                        <div class="form-group col-md-4">
                            Cidade
                            <input type="text" class="form-control form-control-sm" id="lead_endereco_cidade" name="lead_endereco_cidade" value="<?=$lead_endereco['lead_endereco_cidade']?>">
                        </div>
                        <div class="pl-2 form-group col-md-2">
                            UF
                            <input type="text" class="form-control form-control-sm" id="lead_endereco_estado" name="lead_endereco_estado" value="<?=$lead_endereco['lead_endereco_estado']?>">
                        </div>
                        
                        <!-- Comercial -->
                        <div class="col-md-12">
                            <p class="h6 text-muted mb-2 mt-3" id="comercial"><?=utf8_decode("Comercial")?></p>
                        </div>
                        <div class="form-group col-md-10">
                            Colaborador
                            <select class="form-control form-control-sm w-75" id="lead_colaborador" name="lead_colaborador" value="<?=$lead['lead_colaborador']?>" required>

                                <option>SELECIONE</option>

                                <?
                                foreach($comboColaborador as $colaborador ){

                                    if(!empty($lead_ID)){
                                        $verifOP = $colaborador['nr_func'] == $lead['lead_colaborador'];
										$selecao = $verifOP ? "selected='selected'" : "";
                                    }else{
                                        $verifOP = $colaborador['nr_func'] == $_SESSION['login']['nr_func'];
										$selecao = $verifOP ? "selected='selected'" : "";
                                    }

                                    ?>
                                    <option id="<?=$colaborador['nr_func']?>" <?=$selecao?> value="<?=$colaborador['nr_func']?>"><?=$colaborador['nm_pessoa']?></option>
                                    <?
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            Regional
                            <select class="form-control form-control-sm w-100" id="unidade_negocio" name="unidade_negocio">
                                <option></option>

                                <?
                                $arr = executar("SELECT nr_empresa, nm_fantasia FROM empresa ORDER BY nr_empresa");

                                foreach($arr as $key ){

                                    if(!empty($lead_ID)){
                                        $verifOP = $key['nr_empresa'] == $lead['unidade_negocio'];
										$selecao = $verifOP ? "selected='selected'" : "";
                                    }

                                    ?>
                                    <option id="<?=$key['nr_empresa']?>" <?=$selecao?> value="<?=$key['nr_empresa']?>"><?=$key['nm_fantasia']?></option>
                                    <?
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group col-md-8">
                            HubPop
                            <select class="form-control form-control-sm w-100" id="lead_hubpop_ID" name="lead_hubpop_ID" value="<?=$lead['lead_hubpop_ID']?>" required>

                                <option>SELECIONE</option>

                                <?
                                foreach($comboHubpop as $hubpop ){

                                    if(!empty($lead_ID)){
                                        $verifOP = $hubpop['nr_hubpop'] == $lead['lead_hubpop_ID'];
                                        $selecao = $verifOP ? "selected='selected'" : "";
                                        echo $selecao;
                                    }

                                    ?>
                                    <option id="<?=$hubpop['nr_hubpop']?>" <?=$selecao?> value="<?=$hubpop['nr_hubpop']?>"><?=$hubpop['lead_hubpop_br']?></option>
                                    <?
                                }
                                ?>

                            </select>
                        </div>
                        <div class="pl-5 form-group col-md-3">
                            <?=utf8_decode("Renovação de Contrato")?>
                            <input autocomplete="off" type="text" class="form-control form-control-sm calendar" id="lead_agendamento_recisao" name="lead_agendamento_recisao" value="<?=$lead['lead_agendamento_recisao']?>" placeholder="01/01/2000">
                        </div>
                        <div class="form-group col-md-4">
                            Etapa
                            <select class="form-control form-control-sm w-50" id="lead_etapa_ID" name="lead_etapa_ID" value="<?=$lead['lead_etapa_ID']?>">
                                
                                <option>SELECIONE</option>
                                
                                <?
                                foreach($comboEtapa as $etapa ){
                                    
                                    if(!empty($lead_ID)){
                                        $verifOP = $etapa['lead_etapa_ID'] == $lead['lead_etapa_ID'];
										$selecao = $verifOP ? "selected='selected'" : "";
                                    }
                                    
                                    ?>
                                    <option id="<?=$etapa['lead_etapa_ID']?>" <?=$selecao?> value="<?=$etapa['lead_etapa_ID']?>"><?=$etapa['lead_etapa_nome']?></option>
                                    <?
                                }
                                ?>

                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            Status
                            <select class="form-control form-control-sm w-50" id="lead_status_ID" name="lead_status_ID" value="<?=$lead['lead_status_ID']?>">
                        
                                <option>SELECIONE</option>
                        
                                <?
                                foreach($comboStatus as $status ){
                        
                                    if(!empty($lead_ID)){
                                        $verifOP = $status['lead_status_ID'] == $lead['lead_status_ID'];
                                        $selecao = $verifOP ? "selected='selected'" : "";
                                    }
                        
                                    ?>
                                    <option id="<?=$status['lead_status_ID']?>" <?=$selecao?> value="<?=$status['lead_status_ID']?>"><?=$status['lead_status_nome']?></option>
                                    <?
                                }
                                ?>
                        
                            </select>
                        </div>

                        <!-- PRODUTOS -->
                        <div class="col-md-12 radios-produto">
                            <h6>Produtos</h6>
                            <div class="form-check form-check-inline">
                                <?
                                    $sql_produtos = "SELECT lead_produto_id, lead_produto_nome FROM lead_produto ORDER BY lead_produto_nome";
                                    $select_produtos = executar($sql_produtos);

                                    foreach($select_produtos as $produtos){
                                        $checked = strstr($lead['lead_produto'], $produtos['lead_produto_id']) ? "checked" : "";
                                ?>
                                    <input class="form-check-input" type="checkbox" name="lead_produto" id="produto_<?=$produtos['lead_produto_id']?>" value="<?=$produtos['lead_produto_id']?>" <?=$checked?>>
                                    <label class="form-check-label mr-4" style="padding-top: 3px;" for="produto_<?=$produtos['lead_produto_id']?>"><?=utf8_decode($produtos['lead_produto_nome'])?></label>
                                <? } ?>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <?=utf8_decode("Anotações")?>
                            <textarea class="form-control" id="lead_anotacao" name="lead_anotacao" value="<?=$lead['lead_anotacao']?>" rows="3"><?=$lead['lead_anotacao']?></textarea>
                        </div>
                        
                        <!-- Contatos -->
                        <div id="contatos" class="mt-4 col-md-12">
                            <p class="h6 text-muted mb-2 mt-3" id="comercial"><?=utf8_decode("Histórico de Contatos")?></p>
                        </div>
                        <div class="col-md-12 border pb-2 bg-white" >
                            <div style="overflow: auto; height: 125px;">
                                <table class="table table-sm table-hover w-100">
                                    <thead>
                                        <tr colspan="7" class="bg-light">
                                            <th colspan="1"></th>
                                            <th colspan="1">
                                                Nome
                                            </th>
                                            <th colspan="1">
                                                Telefone
                                            </th>
                                            <th colspan="1">
                                                Vendedor
                                            </th>
                                            <th colspan="1">
                                                <?=utf8_decode("Último Contato")?>
                                            </th>
                                            <th colspan="1">
                                            <?=utf8_decode("Próximo Contato")?>
                                            </th>
                                            <th colspan="1">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        <?
                                            $select_historico_contatos = executar("SELECT 
                                                                                        id,
                                                                                        lead_contato_nome,
                                                                                        lead_contato_telefone,
                                                                                        pessoa.nm_pessoa,
                                                                                        lead_contato_data,
                                                                                        lead_contato_agendamento
                                                                                    FROM
                                                                                        lead_contatos
                                                                                            LEFT JOIN
                                                                                        func ON lead_contatos.lead_contato_vendedor_ID = func.nr_func
                                                                                            LEFT JOIN
                                                                                        pessoa ON func.nr_pessoa = pessoa.nr_pessoa
                                                                                    WHERE
                                                                                        lead_ID = $lead_ID");
        
                                            if($select_historico_contatos){
                                                $numero_contato = 1;
                                                foreach($select_historico_contatos as $value){
                                                    if(substr($value['lead_contato_telefone'], 0, 4) == "0800" || substr($value['lead_contato_telefone'], 0, 4) == "0300"){
                                                        $value['lead_contato_telefone'] = mascara($value['lead_contato_telefone'], "telefone-0800");
                                                    }else{
                                                        $length_tel = strlen($value['lead_contato_telefone']);
                                                        $value['lead_contato_telefone']    = $length_tel <= 9 ? "11".$value['lead_contato_telefone'] : $value['lead_contato_telefone'];
                                                        $value['lead_contato_telefone']    = $length_tel > 10 ? mascara($value['lead_contato_telefone'], 'celular') : mascara($value['lead_contato_telefone'], 'telefone');
                                                    }
                                                    
                                                    if(!empty($value['lead_contato_agendamento']) && $value['lead_contato_agendamento'] != "0000-00-00"){
                                                        $value['lead_contato_agendamento'] = convertDateToBr($value['lead_contato_agendamento']);
                                                    }else{
                                                        $value['lead_contato_agendamento'] = "";
                                                    }
                                                    ?>
                                                        <tr colspan="7">
                                                            <td colspan="1"><?=$numero_contato?></td>
                                                            <td colspan="1"><?=$value['lead_contato_nome']?></td>
                                                            <td colspan="1"><?=$value['lead_contato_telefone']?></td>
                                                            <td colspan="1"><?=$value['nm_pessoa']?></td>
                                                            <td colspan="1"><?=convertDateToBr($value['lead_contato_data'])?></td>
                                                            <td colspan="1"><?=$value['lead_contato_agendamento']?></td>
                                                            <td colspan="1"><img src="../imagens/icones/edit_rounded.ico" class="editar-contato" data-toggle="modal" data-target="#ExemploModalCentralizado" title="Editar Contato" alt="Edição de contatos" onclick="autopreenchimentoEditarContato(<?=$value['id']?>);" id="<?=$value['id']?>" /></td>
                                                        </tr>
                                                    <?
                                                    $numero_contato += 1;
                                                }
                                            }else{
                                                ?>
                                                    <tr colspan="7">
                                                        <td colspan="7" class="text-danger text-center"><b><?=utf8_decode("Não foi possível localizar nenhum contato anterior.")?></b></td>
                                                    </tr>
                                                <?
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                <!-- Botão para acionar modal -->
                                <button type="button" title="<?=$titleAdicionaContato?>" <?=$onclickAdicionaContatos?> class="btn btn-sm btn-secondary" data-toggle="modal" id="btn-novo-contato" data-target="#ExemploModalCentralizado">
                                Adicionar Contato
                                </button>
                            </div>
                        </div>
                        <!-- FIM Contatos -->

                        <!-- Ordem de Serviços -->
                        <div id="ordem-servico" class="mt-4 col-md-12">
                            <p class="h6 text-muted mb-2 mt-3" id="comercial"><?=utf8_decode("Ordens de Serviço")?></p>
                        </div>
                        <div class="col-md-12 border pb-2 bg-white">
                            <div style="overflow: auto; height: 135px;">
                                <table class="table table-sm table-hover w-100">
                                    <thead>
                                        
                                        <tr colspan="10" class="bg-light">
                                            <th colspan="1">
                                                <?=utf8_decode("Código")?>
                                            </th>
                                            <th colspan="2">
                                                <?=utf8_decode("Título")?>
                                            </th>
                                            <th colspan="1">
                                                <?=utf8_decode("Data de Abertura")?>
                                            </th>
                                            <th colspan="1">
                                                <?=utf8_decode("Data de Fechamento")?>
                                            </th>
                                            <th colspan="1">
                                                <?=utf8_decode("Para")?>
                                            </th>
                                            <th colspan="1">
                                                <?=utf8_decode("Encaminhado")?>
                                            </th>
                                            <th colspan="1">
                                                <?=utf8_decode("Aberto Por")?>
                                            </th>
                                            <th colspan="1">
                                                teste
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 14px;">
                                        <?
                                            $sql = "SELECT 
                                                        ordem_servico.nr_os,
                                                        ordem_servico.titulo,
                                                        (SELECT nm_depto FROM depto WHERE depto.nr_depto = ordem_servico.nr_depto_destino ) as deptoEncaminhado,
                                                        depto.nm_depto,
                                                        DATE_FORMAT(ordem_servico.dt_abertura,'%d/%m/%Y %H:%i:%s') as dt_abertura,
                                                        ordem_servico.dt_fechamento,
                                                        UPPER(nm_pessoa) as nm_pessoa,
                                                        depto.nr_depto,
                                                        ordem_servico.nr_func,
                                                        ordem_servico.status
                                                    FROM
                                                        leads l
                                                            LEFT JOIN
                                                        ordem_servico ON l.lead_ID = ordem_servico.lead_ID
                                                            LEFT JOIN
                                                        func ON ordem_servico.nr_func = func.nr_func
                                                            LEFT JOIN
                                                        pessoa ON func.nr_pessoa = pessoa.nr_pessoa
                                                            LEFT JOIN
                                                        depto ON ordem_servico.nr_depto = depto.nr_depto
                                                    WHERE
                                                        l.lead_ID = $lead_ID";
                                            $select_os = executar($sql);
        
                                            if($select_os && !empty($select_os[0][0])){
                                                foreach($select_os as $value){
                                                    $os_fechada = $value['status'] == "F" ? "class='bg-danger' title='".utf8_decode("Ordem de Serviço Fechada")."'" : "";
                                                    $dono_os = $value['nr_func'] == $_SESSION['login']['nr_func'] ? "" : "disabled"; 
                                                    ?>
                                                        <tr colspan="10" <?=$os_fechada?>>
                                                            <td id="editar-nr-os" value="<?=$value['nr_os']?>" colspan="1"><?=$value['nr_os']?></td>
                                                            <td id="os_status" value="<?=$value['status']?>" colspan="2"><?=$value['titulo']?></td>
                                                            <td colspan="1"><?=$value['dt_abertura']?></td>
                                                            <td colspan="1"><?=convertDateToBr($value['dt_fechamento'])?></td>
                                                            <td id="os_nr_depto" value="<?=$value['nr_depto']?>" colspan="1"><?=$value['nm_depto']?></td>
                                                            <td colspan="1"><?=$value['deptoEncaminhado']?></td>
                                                            <td id="os_dono" value="<?=$value['nr_func']?>" colspan="1"><?=$value['nm_pessoa']?></td>
                                                            <td colspan="1">
                                                            <? if($value['status'] <> 'F') { ?>
                                                                <img src="../imagens/icones/edit_rounded.ico" id="editar-os" data-toggle="modal" data-target="#modalOrdemServico" title="<?=utf8_decode("Editar Ordem de Serviço")?>" alt="Editar de OS" />
                                                            <? } ?>
                                                            </td>
                                                        </tr>
                                                <? } ?>
                                                    <td colspan="10">
                                                        <table class="table table-hover pt-3">
                                                <?
                                                    $selecr_os_msg = executar("SELECT CONCAT(UPPER(p.nm_pessoa), ' escreveu às ', DATE_FORMAT(dt_mensagem,'%d/%m/%Y %H:%i:%s') ) as info, mensagem FROM ordem_servico_msg osm INNER JOIN func f ON osm.nr_func = f.nr_func INNER JOIN pessoa p ON f.nr_pessoa = p.nr_pessoa WHERE osm.nr_os = {$select_os[0]['nr_os']} ORDER BY dt_mensagem DESC");
                                                    
                                                    foreach($selecr_os_msg as $value_msg){
                                                    ?>
                                                        <tr colspan="10">
                                                            <th colspan="10" class="text-left text-light bg-primary"><?=utf8_decode($value_msg['info'])?></th>
                                                        </tr>
                                                        <tr colspan="10">
                                                            <td colspan="10" class="text-left"><?=$value_msg['mensagem']?></td>
                                                        </tr>
                                                <?  }  ?>
                                                        </table>
                                                    </td>
                                                <? }else{ ?>
                                                    <tr colspan="10">
                                                        <td colspan="10" class="text-danger text-center"><b><?=utf8_decode("Não foi possível localizar nenhuma Ordem de Serviço.")?></b></td>
                                                    </tr>
                                            <? } ?>

                                            
                                    </tbody>
                                </table>
                                
                            </div>
                            <div class="text-center pt-2">
                                <? if($select_os && empty($select_os[0][0])) { ?>
                                <!-- Botão para acionar modal -->
                                <button type="button" data-toggle="modal" data-target="#modalOrdemServico" title="<?=utf8_decode("Adicionar nova Ordem de Serviço")?>" class="btn btn-sm btn-secondary" id="btn-nova-os">
                                    <?=utf8_decode("Adicionar Ordem de Serviço")?>
                                </button>
                                <? } else if($value['status'] <> 'F') {?>
                                <button type="button" title="<?=utf8_decode("Fechar Ordem de Serviço")?>" class="btn btn-sm btn-danger" id="btn-fechar-os"></button>
                                <button type="button" data-toggle="modal" data-target="#modalMensagem" title="<?=utf8_decode("Encaminhar a Ordem de Serviço para um novo departamento")?>" class="btn btn-sm btn-outline-primary ml-5" id="btn-encaminhar-os">
                                    <?=utf8_decode("Encaminhar")?>
                                </button>
                                <button type="button" data-toggle="modal" data-target="#modalMensagem" title="<?=utf8_decode("Adicionar nova mensagem na Ordem de Serviço")?>" class="btn btn-sm btn-outline-primary" id="btn-nova-mensagem">
                                    <?=utf8_decode("Nova Mensagem")?>
                                </button>
                                <? } else { ?>
                                    <button type="button" title="<?=utf8_decode("Reabilitar Ordem de Serviço")?>" class="btn btn-sm btn-outline-secondary" id="btn-reabilitar-os">
                                        <?=utf8_decode("Reabilitar Ordem de Serviço")?>
                                    </button>
                                <? } ?>
                            </div>
                        </div>
                        <!-- FIM Ordem Serviço -->


                        <!-- Botões -->
                        <div class="col align-self-end my-3">
                            <button class="btn btn-primary btn-sm" onclick="salvaLead();" type="button" name="grava" id="grava" value="<?=$action?>"><?=$action?></button>
                            <button class="btn btn-outline-secondary btn-sm voltar" type="button" name="volta" id="volta" onclick="" value="">Voltar</button>
                        </div>
                    </div>

                    <!-- MODAIS -->

                    <!-- Modal Contatos -->
                    <div class="modal in fade" id="ExemploModalCentralizado" role="dialog" aria-labelledby="TituloModalCentralizado" aria-hidden="true">
                        <div class="modal-dialog text-left" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title" id="TituloModalCentralizado"></h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form name="form-contatos" action="../App_Pes/ControlLeads.php" method="POST">
                                <div class="modal-body form-row pb-4">
                                <div class="form-group col-md-12">
                                    Nome
                                    <input type="text" class="form-control form-control-sm" id="lead_contato_nome" name="lead_contato_nome" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    Telefone
                                    <input type="text" class="form-control form-control-sm w-100 phone" id="lead_contato_telefone" name="lead_contato_telefone" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    Telefone (2)
                                    <input type="text" class="form-control form-control-sm w-100 phone" id="lead_contato_telefone2" name="lead_contato_telefone2" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    Email
                                    <textarea class="form-control" id="lead_contato_email" name="lead_contato_email" rows="2" placeholder="seunome@dominio.com.br"></textarea>
                                </div>
                                <div class="form-group col-md-8">
                                    Colaborador
                                    <select class="form-control form-control-sm w-100" id="lead_contato_vendedor_ID" name="lead_contato_vendedor_ID" required>

                                        <option>SELECIONE</option>

                                        <?
                                        foreach($comboColaborador as $colaborador ){

                                            if(!empty($lead_ID)){
                                                $verifOP = $colaborador['nr_func'] == $lead['lead_colaborador'];
                                                $selecao = $verifOP ? "selected='selected'" : "";
                                            }else{
                                                $verifOP = $colaborador['nr_func'] == $_SESSION['login']['nr_func'];
                                                $selecao = $verifOP ? "selected='selected'" : "";
                                            }

                                            ?>
                                            <option id="<?=$colaborador['nr_func']?>" <?=$selecao?> value="<?=$colaborador['nr_func']?>"><?=$colaborador['nm_pessoa']?></option>
                                            <?
                                        }
                                        ?>

                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <?=utf8_decode("Data do contato")?>
                                    <input autocomplete="off" type="text" class="w-75 form-control form-control-sm calendar-modal-contato" id="lead_contato_data" name="lead_contato_data" placeholder="">
                                </div>
                                <div class="form-group col-md-6">
                                    <?=utf8_decode("Agendar próxima ligação")?>
                                    <input autocomplete="off" type="text" class="w-75 form-control form-control-sm calendar-modal-agendamento" id="lead_contato_agendamento" name="lead_contato_agendamento" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <?=utf8_decode("Anotações")?>
                                    <textarea class="form-control" id="lead_contato_anotacoes" name="lead_contato_anotacoes" rows="3"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden"  name="lead_contato_id" id="lead_contato_id" value="" />
                                    <input type="hidden"  name="action-contato" id="action-contato" value="" />
                                    <button type="button" id="fechar-contato" class="btn btn-secondary" data-dismiss="modal"><?=utf8_decode("Fechar")?></button>
                                    <button type="button" id="salvar-contato" <?=$onclickSalvaContatos?> data-dismiss="modal" class="btn btn-primary"><?=utf8_decode("Salvar mudanças")?></button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                    <!-- FIM Modal Contatos -->

                    <!-- Modal Confirma Cliente -->
                    <div class="modal fade" id="modalConfirmaCliente" tabindex="-1" role="dialog" aria-labelledby="modalClienteTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalClienteTitle"><?=utf8_decode("Notificação")?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="bodyConfirmaCliente">
                            <input type="hidden" name="nr_pessoa" id="nr_pessoa" value="" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" id="btnClienteNao"><?=utf8_decode("Não")?></button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnClienteSim">Sim</button>
                        </div>
                        </div>
                    </div>
                    </div>
                    <!-- FIM Modal Confirma Cliente -->

                    <!-- Modal Ordem de Serviço -->
                    <div class="modal fade" id="modalOrdemServico" tabindex="-1" role="dialog" aria-labelledby="modalOrdemServicoTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalOrdemServicoTitle"><?=utf8_decode("Ordem de Serviço")?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body form-row" id="bodyOrdemServico">
                                    <!-- INFORMAÇÕES DA LEAD -->
                                    <div id="modalOSInfoLead-div" class="col-md-12">
                                        <p class="h6 text-muted mb-2 mt-3" id="modalOSInfoLead"><?=utf8_decode("Informações da Lead")?></p>
                                    </div>
                                    <div class="form-group col-md-8">
                                        Empresa
                                        <input type="text" class="form-control form-control-sm" id="os_lead_nome" name="os_lead_nome" value="" placeholder="" disabled>
                                    </div>
                                    <div class="form-group col-md-8" title="Quando preenchido corretamente, libera o botão 'Cadastro Cliente'">
                                        CNPJ
                                        <input type="text" class="form-control form-control-sm cnpj" id="os_lead_cnpj" name="os_lead_cnpj" value="" placeholder="" disabled>
                                        <div class="invalid-feedback" id="lead_cnpj_feedback">
                                            <?=utf8_decode("Por favor, forneça um CNPJ válido.")?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <?=utf8_decode("Endereço")?>
                                        <input type="text" class="form-control form-control-sm" id="os_lead_endereco" name="os_lead_endereco" value="" placeholder="" disabled>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?=utf8_decode("Regional")?>
                                        <input type="text" class="form-control form-control-sm" id="os_lead_regional" name="os_lead_regional" value="" placeholder="" disabled>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?=utf8_decode("Hubpop")?>
                                        <input type="text" class="form-control form-control-sm" id="os_lead_hubpop" name="os_lead_hubpop" value="" placeholder="" disabled>
                                    </div>

                                    <!-- INFORMAÇÕES DO CHAMADO -->
                                    <div id="contatos" class="col-md-12">
                                        <p class="h6 text-muted mb-2 mt-3" id="modalOSInfoLead"><?=utf8_decode("Chamado")?></p>
                                    </div>
                                    <div class="form-group col-md-8">
                                        <?=utf8_decode("Data de Abertura")?>
                                        <input type="text" class="form-control form-control-sm w-75" id="os_lead_dt_abertura" name="os_lead_dt_abertura" value="" placeholder="" disabled>
                                    </div>
                                    <div class="form-group col-md-8 field-editar-os">
                                        <?=utf8_decode("Aberto Por")?>
                                        <select class="form-control form-control-sm" id="os_lead_aberto_por" name="os_lead_aberto_por" required>

                                        <option value="">SELECIONE</option>

                                        <?
                                        $array_func = executar('SELECT nr_func, nm_pessoa FROM func INNER JOIN pessoa ON func.nr_pessoa = pessoa.nr_pessoa ORDER BY nm_pessoa');
                                        foreach($array_func as $func ){            
                                            ?>
                                            <option id="<?=$func['nr_func']?>" value="<?=$func['nr_func']?>"><?=$func['nm_pessoa']?></option>
                                            <?
                                        }
                                        ?>

                                        </select>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <button class="btn btn-sm btn-outline-secondary field-editar-os" id="btn-mudar-dono" <?=$dono_os?>>Mudar dono</button>
                                    </div>
                                    <div class="form-group col-md-6 criar-os-2">
                                        <?=utf8_decode("Encaminhar Para:")?>
                                        <select class="form-control form-control-sm" id="os_lead_encaminhar_cria" name="os_lead_encaminhar_cria" required>

                                            <option value="">SELECIONE</option>

                                            <?
                                            $array_depto = executar('SELECT nr_depto, nm_depto FROM depto ORDER BY nm_depto');
                                            foreach($array_depto as $depto ){            
                                                ?>
                                                <option id="<?=$depto['nr_depto']?>" value="<?=$depto['nr_depto']?>"><?=$depto['nm_depto']?></option>
                                                <?
                                            }
                                            ?>

                                        </select>
                                    </div>
                                    <div class="form-group col-md-6 field-editar-os">
                                        <?=utf8_decode("Enviado Para:")?>
                                        <input type="text" class="form-control form-control-sm" id="os_lead_encaminhar" name="os_lead_encaminhar" value="" placeholder="" disabled>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <?=utf8_decode("Departamento Atual:")?>
                                        <input type="text" class="form-control form-control-sm" id="os_lead_depto_atual" name="os_lead_depto_atual" value="" placeholder="" disabled>
                                    </div>
                                    <div class="form-group col-md-10 criar-os-2">
                                        <?=utf8_decode("Título")?>
                                        <input type="text" class="form-control form-control-sm" id="os_lead_titulo" name="os_lead_titulo" value="" placeholder="" required>
                                    </div>
                                    <div class="form-group col-md-12 criar-os-2">
                                        <?=utf8_decode("Mensagem")?>
                                        <textarea class="form-control form-control-sm w-100" rows="4" id="os_lead_mensagem" name="os_lead_mensagem" value="" placeholder="" required> </textarea>
                                    </div>
                                </div>
                                <div class="modal-footer criar-os-2">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnOSSalva" value=""><?=utf8_decode("Salvar")?></button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnOSCancela">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- FIM Modal Ordem de Serviço -->

                    <!-- Modal Mensagem/Encaminhar/Fechar -->
                    <div class="modal fade" id="modalMensagem" tabindex="-1" role="dialog" aria-labelledby="modalMensagemTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-body form-row" id="bodyOrdemServico">
                                    <div class="form-group col-md-12 field-encaminhar">
                                        <?=utf8_decode("Encaminhar Para:")?>
                                        <select class="form-control form-control-sm w-100" id="os_encaminhar" name="os_encaminhar" required>

                                        <option value="">SELECIONE</option>

                                        <?
                                        $array_depto = executar('SELECT nr_depto, nm_depto FROM depto ORDER BY nm_depto');
                                        foreach($array_depto as $depto ){

                                            
                                            ?>
                                            <option id="<?=$depto['nr_depto']?>" value="<?=$depto['nr_depto']?>"><?=$depto['nm_depto']?></option>
                                            <?
                                        }
                                        ?>

                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <?=utf8_decode("Mensagem")?>
                                        <textarea class="form-control form-control-sm w-100" rows="4" id="os_mensagem" name="os_mensagem" value="" placeholder="" required></textarea>
                                    </div>
                                    <div class="modal-footer criar-os-2">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal" id="salva-mensagem" value=""><?=utf8_decode("Salvar")?></button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancela-mensagem">Cancelar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- FIM Modal Mensagem/Encaminhar/Fechar -->


                    <div class="row">
                    </div>
                    <input type="hidden" name="lead_ID" value="<?=$lead_ID?>" />
                    <input type="hidden" name="action"  value="<?=$action?>" />
                    <input type="hidden" name="type"    value="<?=$type?>" />
                    <input type="hidden" name="usuario-atual" value="<?=$_SESSION['login']['nr_func']?>" />
                </form>
            </div>
        </div>

	</div>
    <div id="rodape">
<?php 
	require_once("../base.php")
?>
    </div>
    <script>
        var btnVoltar = $("#volta");
        var btnCEP    = $("#busca-cep");
        var btnRegCli = $("#registra-cliente");
        var modalCli  = $("#modalConfirmaCliente");
        var btnCliSim = $("#btnClienteSim");
        var btnNovaOS = $("#btn-nova-os");
        var btnSaveOS = $("#btnOSSalva");

        $(function(){
            voltar();
            pesquisaCEP();
            autopreenchimentoNovoContato();
            //autopreenchimentoEditarContato();
            gravaContato();
            verificaCliente();
            mostraCliente();
            validaRegistroAnteriorPosterior();
            toggleDisableOS();
            criaOS();
            saveOS();
            editaOS();
            infoOSlead();
            donoOS();
            postUtilitarios();
            novaMensagem();
            encaminharOS();
            fechaOS();
            reabilitaOS();
            mudaDonoOS();

            $(function(){
                $(".calendar").datepicker({
                    uiLibrary: 'bootstrap4',
                    autoclose: true,
                    format: 'dd/mm/yyyy', 
                    language: 'pt-BR'
                });
		        
                $(".calendar-modal-contato").datepicker({
                    uiLibrary: 'bootstrap4',
                    modal: true,
                    autoclose: true,
                    format: 'dd/mm/yyyy',
                    language: 'pt-BR'
                });

                $(".calendar-modal-agendamento").datepicker({
                    uiLibrary: 'bootstrap4',
                    modal: true,
                    autoclose: true,
                    format: 'dd/mm/yyyy',
                    language: 'pt-BR'
                });

                $(".invalid-feedback").hide();

                var SPMaskBehavior = function (val) {
                    var newVal;
                    var flag = false;
                    if(val.replace(/\D/g, '').length >= 4 && val.replace(/\D/g, '').substr(0, 4) === '0800'){
                        newVal = '0000 000 0000';
                    }else if(val.replace(/\D/g, '').length >= 4 && val.replace(/\D/g, '').substr(0, 4) === '0300'){
                        newVal = '0000 000 0000';
                    }else if(val.replace(/\D/g, '').length === 11 && !flag){
                        newVal = '(00) 00000-0000';
                    }else{
                        newVal = '(00) 0000-00009'; 
                    }
                    return newVal;
                },
                spOptions = {
                    onKeyPress: function(val, e, field, options) {
                        field.mask(SPMaskBehavior.apply({}, arguments), options);
                    }
                };
                
                $('.phone').mask(SPMaskBehavior, spOptions);
                $('.cep').mask('00000-000');
                $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
               
                $(".cnpj").keyup(function(){
                    if($(".cnpj").val().length == 18){
                        verificaCNPJ($(".cnpj"));
                    }else{
                        btnRegCli.hide();
                    }
                });

                if($(".cnpj").val().length == 18){
                    btnRegCli.show();
                }else{
                    btnRegCli.hide();
                }
            });

            $('#modalContatos').on('shown.bs.modal', function () {
                $('#myInput').trigger('focus')
            })

            $(".field-editar-os").hide();
        });

        // FUNÇÃO DA LEAD

        function salvaLead(mensagem_retorno = true){

            var flag    = true;
            var lead_ID = $("input[name='lead_ID']").val();
            
            if(!mensagem_retorno){
                if(lead_ID){
                   flag = false; 
                }
            }

            if(flag){
                var lead_nome                 = alteraUndefined($("input[name='lead_nome']").val());
                var lead_cnpj                 = alteraUndefined($("input[name='lead_cnpj']").val());
                var lead_contato_email        = alteraUndefined($("input[name='lead_contato_email']").val());
                var lead_site                 = alteraUndefined($("input[name='lead_site']").val());
                var lead_hubpop_ID            = alteraUndefined($("select[name='lead_hubpop_ID']").val());
                var lead_etapa_ID             = alteraUndefined($("select[name='lead_etapa_ID']").val());
                var lead_status_ID            = alteraUndefined($("select[name='lead_status_ID']").val());
                var lead_anotacao             = alteraUndefined($("textarea[name='lead_anotacao']").val());
                var lead_colaborador          = alteraUndefined($("select[name='lead_colaborador']").val());
                var unidade_negocio           = alteraUndefined($("select[name='unidade_negocio']").val());
                var lead_agendamento_recisao  = alteraUndefined($("input[name='lead_agendamento_recisao']").val());
                var lead_endereco_cep         = alteraUndefined($("input[name='lead_endereco_cep']").val());
                var lead_endereco_tipo        = alteraUndefined($("select[name='lead_endereco_tipo']").val());
                var lead_endereco_logradouro  = alteraUndefined($("input[name='lead_endereco_logradouro']").val());
                var lead_endereco_numero      = alteraUndefined($("input[name='lead_endereco_numero']").val());
                var lead_endereco_complemento = alteraUndefined($("input[name='lead_endereco_complemento']").val());
                var lead_endereco_bairro      = alteraUndefined($("input[name='lead_endereco_bairro']").val());
                var lead_endereco_cidade      = alteraUndefined($("input[name='lead_endereco_cidade']").val());
                var lead_endereco_estado      = alteraUndefined($("input[name='lead_endereco_estado']").val());
                var action  = $("input[name='action']").val();

                var lead_produto;
                $("input[name='lead_produto']:checked").each(function(){
                    if(!lead_produto){
                        lead_produto = $(this).val();
                    }else{
                        lead_produto = lead_produto + ";" + $(this).val();
                    }
                });

                $.post( "../App_Pes/ControlLeads.php",
                    {action: action, type: 'leads', unidade_negocio: unidade_negocio, lead_ID: lead_ID, lead_nome: lead_nome, lead_cnpj: lead_cnpj, lead_contato_email: lead_contato_email, lead_site: lead_site, lead_hubpop_ID: lead_hubpop_ID, lead_etapa_ID: lead_etapa_ID, lead_status_ID: lead_status_ID, lead_anotacao: lead_anotacao, lead_colaborador: lead_colaborador, lead_agendamento_recisao: lead_agendamento_recisao, lead_endereco_cep: lead_endereco_cep, lead_endereco_tipo: lead_endereco_tipo, lead_endereco_logradouro: lead_endereco_logradouro, lead_endereco_numero: lead_endereco_numero, lead_endereco_complemento: lead_endereco_complemento, lead_endereco_bairro: lead_endereco_bairro, lead_endereco_cidade: lead_endereco_cidade, lead_endereco_estado: lead_endereco_estado, lead_produto: lead_produto},
                        function(data){
                            if(!lead_ID){
                                $("input[name='lead_ID']").val(data.lead_ID);
                            }
                            if(mensagem_retorno){
                                alert(data.mensagem);
                                window.location = "../App_Pes/Leads.php?lead_ID=" + data.lead_ID;
                            }
                        }, 'json');
            }

        }

        // FUNÇÕES DO HISTÓRICO DE CONTATOS

        function autopreenchimentoNovoContato(){
            $("#btn-novo-contato").click(function(){
                var data = new Date(),
                dia  = data.getDate(),
                mes  = data.getMonth() + 1,
                ano  = data.getFullYear();

                if(mes <= 10){
                    mes = "0" + mes;
                }
                
                data = [dia, mes, ano].join('/');

                $("#lead_contato_data").val(data);
                $("#TituloModalCentralizado").text("Novo Contato");
                $("input[name='action-contato']").val("gravar");

                $("#lead_contato_agendamento").val(" ");
                $("#lead_contato_anotacoes").text(" ");
                $("#lead_contato_id").val(" ");

            });
        }

        function autopreenchimentoEditarContato(id){
            $("input[name='action-contato']").val("alterar");

            $.post( "../App_Pes/ControlLeads.php", 
                {action: 'selecionar', type: 'contato', id: id},
                    function(data){
                        $("#lead_contato_nome").val(data.lead_contato_nome);
                        $("#lead_contato_telefone").val(data.lead_contato_telefone);
                        $("#lead_contato_telefone2").val(data.lead_contato_telefone2);
                        $("#lead_contato_email").text(data.lead_contato_email);
                        $("#lead_contato_vendedor_ID").val(data.lead_contato_vendedor_ID);
                        $("#lead_contato_data").val(data.lead_contato_data);
                        $("#lead_contato_agendamento").val(data.lead_contato_agendamento);
                        $("#lead_contato_anotacoes").text(data.lead_contato_anotacoes);
                        $("#lead_contato_id").val(id);

                        $("#TituloModalCentralizado").text("Contato");

                    }, 'json');
        }

        function gravaContato(){
            $("#salvar-contato").click(function(){
                var lead_ID = $("input[name='lead_ID']").val();
                var id      = alteraUndefined($("input[name='lead_contato_id']").val());

                var lead_contato_nome        = alteraUndefined($("input[name='lead_contato_nome']").val());
                var lead_contato_telefone    = alteraUndefined($("input[name='lead_contato_telefone']").val());
                var lead_contato_telefone2   = alteraUndefined($("input[name='lead_contato_telefone2']").val());
                var lead_contato_vendedor_ID = alteraUndefined($("select[name='lead_contato_vendedor_ID']").val());
                var lead_contato_data        = alteraUndefined($("input[name='lead_contato_data']").val());
                var lead_contato_agendamento = alteraUndefined($("input[name='lead_contato_agendamento']").val());
                var lead_contato_email       = alteraUndefined($("textarea[name='lead_contato_email']").val());
                var lead_contato_anotacoes   = alteraUndefined($("textarea[name='lead_contato_anotacoes']").val());

                var action = $("input[name='action-contato']").val();
                $.post( "../App_Pes/ControlLeads.php", 
                    {action: action
                    ,type: 'contato'
                    ,id: id
                    ,lead_ID: lead_ID
                    ,lead_contato_email:lead_contato_email
                    ,lead_contato_nome: lead_contato_nome
                    ,lead_contato_telefone: lead_contato_telefone
                    ,lead_contato_telefone2: lead_contato_telefone2
                    ,lead_contato_vendedor_ID: lead_contato_vendedor_ID
                    ,lead_contato_data: lead_contato_data
                    ,lead_contato_agendamento: lead_contato_agendamento
                    ,lead_contato_anotacoes: lead_contato_anotacoes},
                        function(data){
                            alert(data.mensagem);
                            window.location = "../App_Pes/Leads.php?lead_ID=" + data.lead_ID;

                        }, 'json');
            });

        }

        // REGISTRO DO CLIENTE

        function verificaCliente(){
            btnRegCli.click(function(){
                var cnpj = $("#lead_cnpj").val();

                $.post( "../App_Pes/ControlLeads.php", 
                    {action: "selecionar", type: "cnpj", cnpj: cnpj},
                        function(infoCNPJ){

                            if(infoCNPJ){
                                modalCli.find("#bodyConfirmaCliente").append("<p> CNPJ ja cadastrado!<br/><b> Nome: " + infoCNPJ.nm_pessoa + "</b></p><br/><p>Deseja visualizar o cadastro?</p>");
                                modalCli.find("#nr_pessoa").val(infoCNPJ.nr_pessoa);
                                modalCli.modal('show');

                            }else{
                                cadastraCliente();
                            }
                        }, 'json');
            });
        }

        function cadastraCliente(){
            var lead_ID                   = $("input[name='lead_ID']").val();
            var lead_nome                 = alteraUndefined($("input[name='lead_nome']").val());
            var lead_cnpj                 = alteraUndefined($("input[name='lead_cnpj']").val());
            var lead_endereco_cep         = alteraUndefined($("input[name='lead_endereco_cep']").val());
            var lead_endereco_tipo        = alteraUndefined($("select[name='lead_endereco_tipo']").val());
            var lead_endereco_logradouro  = alteraUndefined($("input[name='lead_endereco_logradouro']").val());
            var lead_endereco_numero      = alteraUndefined($("input[name='lead_endereco_numero']").val());
            var lead_endereco_complemento = alteraUndefined($("input[name='lead_endereco_complemento']").val());
            var lead_endereco_bairro      = alteraUndefined($("input[name='lead_endereco_bairro']").val());
            var lead_endereco_cidade      = alteraUndefined($("input[name='lead_endereco_cidade']").val());
            var lead_endereco_estado      = alteraUndefined($("input[name='lead_endereco_estado']").val());
            var nr_hubpop                 = alteraUndefined($("select[name='lead_hubpop_ID']").val());
            var nr_empresa                = alteraUndefined($("select[name='unidade_negocio']").val())

            $.post( "../App_Pes/ControlLeads.php", 
                {action: 'cadastrar', type: 'cliente', lead_ID: lead_ID, nr_empresa: nr_empresa, nr_hubpop: nr_hubpop, lead_nome: lead_nome, lead_cnpj: lead_cnpj, lead_endereco_cep: lead_endereco_cep, lead_endereco_tipo: lead_endereco_tipo, lead_endereco_logradouro: lead_endereco_logradouro, lead_endereco_numero: lead_endereco_numero, lead_endereco_complemento: lead_endereco_complemento, lead_endereco_bairro: lead_endereco_bairro, lead_endereco_cidade: lead_endereco_cidade, lead_endereco_estado: lead_endereco_estado},
                    function(data){
                        alert(data.mensagem);

                        if(data.nr_pessoa != 0){
                            window.open("../App_Pes/Cliente.php?nr_pessoa=" + data.nr_pessoa);
                        }else{
                            console.log(data.mensagem1SQL);
                        }

                    }, 'json');

        }

        function mostraCliente(){
            btnCliSim.click(function(){
                var nr_pessoa = modalCli.find("#nr_pessoa").val();

                window.open("../App_Pes/Cliente.php?nr_pessoa=" + nr_pessoa);
            });

        }

        function atualizaEtapa(lead_id){
            $.post( "../App_Pes/ControlLeads.php",
                )
        }

        function verificaCNPJ(field){
            var valido = isCNPJValid(field.val()); //implementar a validação
            if (!valido) {
                field.addClass('is-invalid');
                $("#lead_cnpj_feedback").show();
                btnRegCli.hide();

            }else{
                field.removeClass('is-invalid');
                $("#lead_cnpj_feedback").hide();
                btnRegCli.show();
            }
        }

        function isCNPJValid(c) {  
            var b = [6,5,4,3,2,9,8,7,6,5,4,3,2];
            if((c = c.replace(/[^\d]/g,"").split("")).length != 14)
                return false;
            for (var i = 0, n = 0; i < 12; n += c[i] * b[++i]); 
            if(c[12] != (((n %= 11) < 2) ? 0 : 11 - n))
                return false; 
            for (var i = 0, n = 0; i <= 12; n += c[i] * b[i++]); 
            if(c[13] != (((n %= 11) < 2) ? 0 : 11 - n))
                return false; 
            return true; 
        };

        // FUNÇÕES ORDEM DE SERVIÇO
        function toggleDisableOS(){
            var lead_ID = $("input[name='lead_ID']").val();

            if(lead_ID){
                btnNovaOS.attr('disabled', false);
            }else{
                btnNovaOS.attr('disabled', true);
            }
        }

        function infoOSlead(){
            var lead_ID = $("input[name='lead_ID']").val();

            if(lead_ID){
                $.post( "../App_Pes/ControlLeads.php", 
                {action: 'selecionar', type: 'leads', lead_ID: lead_ID},
                    function(data){

                        if(!data.erro){
                            var lead_endereco = data.lead_endereco_tipo+' '+data.lead_endereco_logradouro+', '+data.lead_endereco_numero+' - '+data.lead_endereco_cep+' - '+data.lead_endereco_bairro+', '+data.lead_endereco_cidade+', '+data.lead_endereco_estado;

                            $("#os_lead_nome").val(data.lead_nome);
                            $("#os_lead_cnpj").val(data.lead_cnpj);
                            $("#os_lead_endereco").val(lead_endereco);
                            $("#os_lead_regional").val(data.regional);
                            $("#os_lead_hubpop").val(data.hubpop);
                        }else{
                            alert(data.erro);
                        }
                    }, 'json');
            }
        } 

        function criaOS(){
            btnNovaOS.click(function(){
                btnSaveOS.attr("value", "criar");
                $("#os_lead_dt_abertura").val(dataAtualFormatada());
                $("#os_lead_encaminhar").attr("name", "os_lead_encaminhar");
                $("#os_lead_depto_atual").attr("name", "os_lead_depto_atual");
                $(".field-editar-os").hide();
                $(".criar-os-2").show();
            });
        }

        function editaOS(){
            $("#editar-os").click(function(){
                btnSaveOS.attr("value", "editar");
                var nr_os = $("#editar-nr-os").attr("value");
                $(".field-editar-os").show();
                $(".criar-os-2").hide();
                
                $.post( "../App_Pes/ControlLeads.php", 
                {action: 'selecionar', type: 'ordem-servico', nr_os: nr_os},
                    function(data_os){
                        if(!data_os.erro){

                            $("#os_lead_dt_abertura").val(data_os.dt_abertura);
                            $("#os_lead_aberto_por").val(data_os.func);
                            $("#os_lead_encaminhar").val(data_os.depto);
                            $("#os_lead_depto_atual").val(data_os.depto_destino);
                            $("#os_lead_titulo").val(data_os.titulo);
                        }else{
                            alert(data_os.erro);
                        }
                    }, 'json');
            });
        }

        function saveOS(){
            btnSaveOS.click(function(){
                var action = btnSaveOS.attr("value");
                var titulo = $("#os_lead_titulo").val();

                if(titulo){
                    postOS(action);
                } else {
                    alert("Titulo deve ser preenchido");
                }
            });
        }

        function postOS(action){

            if(action == 'criar'){
                var os_lead_encaminhar  = $("select[name='os_lead_encaminhar_cria']").val();
            }else{
                var os_lead_encaminhar  = $("select[name='os_lead_encaminhar']").val();
            }

            var lead_ID             = $("input[name='lead_ID']").val();
            var os_lead_nome        = $("input[name='os_lead_nome']").val();
            var os_lead_depto_atual = $("select[name='os_lead_depto_atual']").val();
            var os_lead_titulo      = $("input[name='os_lead_titulo']").val();
            var os_lead_mensagem    = $("textarea[name='os_lead_mensagem']").val();
            var os_lead_aberto_por  = $("#os_lead_aberto_por").val();
            var nr_os               = $("#editar-nr-os").attr("value");

            $.post( "../App_Pes/ControlLeads.php",
                {action: action, type: 'ordem-servico', nr_os: nr_os, lead_ID: lead_ID, os_lead_aberto_por: os_lead_aberto_por, lead_nome: os_lead_nome, nr_depto_atual: os_lead_depto_atual, nr_depto: os_lead_encaminhar, titulo: os_lead_titulo, mensagem: os_lead_mensagem},
                    function(data){
                        alert(data);
                        location.reload();
                    });
        }

        function novaMensagem(){
            $("#btn-nova-mensagem").click(function(){
                $("#salva-mensagem").attr("value", "salva-mensagem");
                $(".field-encaminhar").hide();
            });
        }

        function encaminharOS(){
            $("#btn-encaminhar-os").click(function(){
                $("#salva-mensagem").attr("value", "encaminha-os");
                $(".field-encaminhar").show();
            });
        }

        function donoOS(){
            var dono = $("#os_dono").attr("value");
            var usuarioAtual = $("input[name='usuario-atual']").val();

            if(dono == usuarioAtual){
                $("#btn-fechar-os").text("Fechar OS");
                $("#btn-fechar-os").attr("value", "fechar-os");
            }else{
                $("#btn-fechar-os").text("Pedido de Fechamento");
                $("#btn-fechar-os").attr("value", "pedido-fechamento");
            }
        }
        
        function fechaOS(){
            $("#btn-fechar-os").click(function(){
                var action = $("#btn-fechar-os").attr("value");

                if(action == "fechar-os"){
                    $("#salva-mensagem").attr("value", "fecha-os");
                    $(".field-encaminhar").hide();
                    $('#modalMensagem').modal('show');
                }else{
                    $("#salva-mensagem").attr("value", "pedido-fechamento");
                    $("#salva-mensagem").trigger('click');
                }

            });
        }

        function reabilitaOS(){
            $("#btn-reabilitar-os").click(function(){

                $("#salva-mensagem").attr("value", "reabilitar");
                $("#salva-mensagem").trigger('click');

            });
        }

        function mudaDonoOS(){
            $("#btn-mudar-dono").click(function(){
                postOS("mudar-dono");
            });
        }

        function postUtilitarios(){
            $("#salva-mensagem").click(function(){
                var action = $("#salva-mensagem").attr("value");

                var os_encaminhar = $("#os_encaminhar").val();
                var os_mensagem   = $("#os_mensagem").val();
                var nr_os         = $("#editar-nr-os").attr("value");
                var nr_depto      = $("#os_nr_depto").attr("value");
                var status        = $("#os_status").attr("value");
                
                $.post("../App_Pes/ControlLeads.php",
                    {action: action, type: 'ordem-servico', os_encaminhar: os_encaminhar, mensagem: os_mensagem, nr_os: nr_os, nr_depto: nr_depto, status: status},
                        function(data){
                            alert(data);
                            location.reload();
                        });
            });
        }

        // FUNÇÕES AUXILIARES

        function pesquisaCEP(){
            btnCEP.click(function() {
                //Nova variável "cep" somente com dígitos.
                var cep = $("#lead_endereco_cep").val().replace(/\D/g, '');

                //Verifica se campo cep possui valor informado.
                if (cep != "") {

                    //Expressão regular para validar o CEP.
                    var validacep = new RegExp(/^[0-9]{8}$/);

                    //Valida o formato do CEP.
                    if(validacep.test(cep)) {

                        //Consulta o webservice viacep.com.br/
                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                            if (!("erro" in dados)) {
                                //Atualiza os campos com os valores da consulta.
                                var n = dados.logradouro.indexOf(" ") + 1;
                                var logradouro = dados.logradouro.substr(n);
                                var tipoLogradouro = dados.logradouro.substr(0, n)

                                buscaTipoLogradouro(tipoLogradouro);
                                
                                $("#lead_endereco_logradouro").val(logradouro);
                                $("#lead_endereco_bairro").val(dados.bairro);
                                $("#lead_endereco_cidade").val(dados.localidade);
                                $("#lead_endereco_estado").val(dados.uf);
                            } //end if.
                            else {
                                //CEP pesquisado não foi encontrado.
                                alert("CEP não encontrado.");
                            }
                        });
                    } //end if.
                    else {
                        //cep é inválido.
                        alert("Formato de CEP inválido.");
                    }
                } //end if.
                else {
                    alert("Campo do CEP vazio!");
                }
            });
        }
        
        function voltar(){
            $(".voltar").click(function(){
                window.location="../App_Pes/ListLeads.php";
            });
        }

        function buscaTipoLogradouro(logradouro){
            $.post("../App_Pes/ControlLeads.php", {action: "busca", type: "tipo-logradouro", logradouro: logradouro}, function(data){
                $("#lead_endereco_tipo").val(data);
            });
        }

        function atualizaPagina(){
            var lead_ID = $("input[name='lead_ID']").val();
            window.location = "../App_Pes/Leads.php?lead_ID=" + lead_ID;
        }

        function alteraUndefined(campo){
            if(typeof campo == "undefined"){
                campo = "";
            }
                
            return campo;
        }

        function validaRegistroAnteriorPosterior(){
            var lead_ID      = $("input[name='lead_ID']").val();
            var id_anterior  = $(".registro-anterior").attr('id');
            var id_posterior = $(".registro-posterior").attr('id');

            if(lead_ID == id_anterior){
                $(".registro-anterior").attr('disabled', true);
            }else if(lead_ID == id_posterior){
                $(".registro-posterior").attr('disabled', true);
            }
        }

        function dataAtualFormatada(){
            var data = new Date(),
                dia  = data.getDate().toString(),
                diaF = (dia.length == 1) ? '0'+dia : dia,
                mes  = (data.getMonth()+1).toString(), //+1 pois no getMonth Janeiro começa com zero.
                mesF = (mes.length == 1) ? '0'+mes : mes,
                anoF = data.getFullYear();
            return diaF+"/"+mesF+"/"+anoF;
        }

        function isJson(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }

            return true;
        }

    </script>
    
</body>
</html>