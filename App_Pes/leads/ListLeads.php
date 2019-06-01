<?
require_once("../config.php");
require_once("../conexao.php");
require_once("../funcoes.php");
require_once("../toolbarPreVendas.php");

###### PAGINAÇÂO ######
$pagina = intval($_GET['pagina']);
$registros = 15;

#######################


// Inicia variável de controle da  cláusura WHERE
$where = array();

if(isset($_POST) && !empty($_POST)){
    $chars = array("(", ")", "-", "_", ".", ",", "[", "]", "{", "}", " ");

    if(!empty($_POST['lead_nome'])){
        $where['lead_nome'] = " l.lead_nome LIKE '%{$_POST['lead_nome']}%'";
	}
	
	if(!empty($_POST['lead_contato_data_ini']) && !empty($_POST['lead_contato_data_fim'])){
        $lead_contato_data_ini = convertDateToBd($_POST['lead_contato_data_ini']);
        $lead_contato_data_fim = convertDateToBd($_POST['lead_contato_data_fim']);

        $where['lead_contato_data'] = " lc.lead_contato_data BETWEEN '$lead_contato_data_ini' AND '$lead_contato_data_fim'";
    }
    
    if(!empty($_POST['lead_agendamento_recisao_ini']) && !empty($_POST['lead_agendamento_recisao_fim'])){
        $lead_agendamento_recisao_ini = convertDateToBd($_POST['lead_agendamento_recisao_ini']);
        $lead_agendamento_recisao_fim = convertDateToBd($_POST['lead_agendamento_recisao_fim']);

        $where['lead_agendamento_recisao'] = " l.lead_agendamento_recisao BETWEEN '$lead_agendamento_recisao_ini' AND '$lead_agendamento_recisao_fim'";
    }
    
    if(!empty($_POST['lead_contato_agendamento_ini']) && !empty($_POST['lead_contato_agendamento_fim'])){
        $lead_contato_agendamento_ini = convertDateToBd($_POST['lead_contato_agendamento_ini']);
        $lead_contato_agendamento_fim = convertDateToBd($_POST['lead_contato_agendamento_fim']);

        $where['lead_contato_agendamento'] = " lc.lead_contato_agendamento BETWEEN '$lead_contato_agendamento_ini' AND '$lead_contato_agendamento_fim'";
	}
	
	if(!empty($_POST['cidade'])){
        $where['cidade'] = " lend.lead_endereco_cidade LIKE '%{$_POST['cidade']}%'";
    }
    
    if(!empty($_POST['lead_colaborador'])){
        $where['lead_colaborador'] = " l.lead_colaborador = {$_POST['lead_colaborador']}";
    }
    
    if(!empty($_POST['lead_etapa_ID'])){
        $where['lead_etapa_ID'] = " l.lead_etapa_ID = {$_POST['lead_etapa_ID']}";
    }
    
    if(!empty($_POST['lead_status_ID'])){
        $where['lead_status_ID'] = " l.lead_status_ID = {$_POST['lead_status_ID']}";
	}
	
	if(!empty($_POST['unidade_negocio'])){
        $where['unidade_negocio'] = " l.unidade_negocio = {$_POST['unidade_negocio']}";
	}
	
	if(!empty($_POST['lead_hubpop_ID'])){
        $where['lead_hubpop_ID'] = " l.lead_hubpop_ID = {$_POST['lead_hubpop_ID']}";
    }

	$where = empty($where) ? "" : " WHERE ".implode(" AND ", $where);

	$_SESSION['login']['where_leads']   = $where;
}else{
	$where = $_SESSION['login']['where_leads'];
}

$sql_leads = "SELECT DISTINCT l.lead_ID, l.lead_nome, l.lead_cnpj, l.lead_data_create, le.lead_etapa_nome, ls.lead_status_nome FROM leads l LEFT JOIN lead_contatos lc ON l.lead_ID = lc.lead_ID INNER JOIN lead_etapa le ON l.lead_etapa_ID = le.lead_etapa_ID INNER JOIN lead_status ls ON ls.lead_status_ID = l.lead_status_ID LEFT JOIN lead_endereco lend ON l.lead_ID = lend.lead_ID $where $where_contato ORDER BY lead_contato_data, lead_nome ";
#echo $sql_leads;
$title = utf8_decode("Lista de Leads");
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
	<link rel="stylesheet" type="text/css"  href="<?=$caminho?>/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?=$caminho?>/obj_js/jquery-confirm/jquery-confirm.min.css">
	<link href="<?=$caminho?>/obj_js/jquery-ui.css" rel="stylesheet">
    
	<script language="JavaScript"  src="<?=$jscript?>"></script>
	<script language="JavaScript"  src="<?=$jsfuncao?>"></script>
	<script type="text/javascript" src="<?=$caminho?>/jquery/jquery-3.3.1.js"></script>
    <script language="JavaScript" src="<?=$caminho?>/obj_js/jquery-ui.js"></script>
	<script language="JavaScript" src="<?=$caminho?>/obj_js/jquery.tooltip.js"></script>
	<script type="text/javascript" src="<?=$caminho?>/bootstrap/js/bootstrap.min.js"></script>
</head>
<body class="body-boots">
	<div id="cabecalho">
	        <?php require_once("../topo.php"); ?>
	</div>
		<?=toolBar($opcoes, $empresa)?>
	<div id="corpo">
	<br />
	<div class="container-fluid">
		<div class="container">
    	    <div class="row justify-content-md-center">
				<div id="header-title" class="my-3 border-bottom border-info d-inline-block  ml-4 px-4">
					<h3><?=$title?></h3>
				</div>
			</div>
			<div class="mb-3 row justify-content-md-center">
				<button type="button" class="ml-5 btn btn-outline-dark btn-sm" id="cria-lead"> Criar Lead </button>
				<button class="btn btn-outline-secondary btn-sm ml-5" type="button" id="voltar"> Voltar </button>
			</div>
		</div>
		<div class="table-responsive-sm">
			<table class="table table-sm border table-hover shadow w-100 text-center">
				<thead class="bg-info">
				  <tr>
				    <th scope="col">Empresa</th>
				    <th scope="col">CNPJ</th>
				    <th scope="col"><?=utf8_decode("Último Contato")?></th>
					<th scope="col"><?=utf8_decode("Agendamento de Retorno")?></th>
					<th scope="col">Etapa</th>
                    <th scope="col">Status </th>
                    <th scope="col"> </th>
				  </tr>
				</thead>
				<tbody class="bg-light">
					<?
					$ret_leads = calculaPaginacao($sql_leads, $registros, $pagina);
					$num_paginas = $ret_leads['paginas'];

					if($ret_leads){
						foreach($ret_leads['sql'] as $lead):
							$sql_contato = "SELECT lead_contato_data, lead_contato_agendamento FROM lead_contatos WHERE lead_ID = {$lead['lead_ID']} ORDER BY id DESC, lead_contato_data DESC";
							$lead_contato = executar($sql_contato);

							$lead_contato_data = empty($lead_contato[0]['lead_contato_data']) ? convertDateToBr($lead['lead_data_create']) : convertDateToBr($lead_contato[0]['lead_contato_data']);
							$lead_contato_agendamento = convertDateToBr($lead_contato[0]['lead_contato_agendamento']);

							$lead_cnpj = mascara($lead['lead_cnpj'], 'cnpj');
					?>


							<tr >  
						      	<td class="lead_nome"><?=$lead['lead_nome']?></td>
						      	<td class="lead_cnpj"><?=$lead_cnpj?></td>
								<td class="lead_contato_data"><?=$lead_contato_data?></td>
                                <td class="lead_contato_agendamento"><?=$lead_contato_agendamento?></td>
                                <td class="lead_etapa_ID"><?=$lead['lead_etapa_nome']?></td>
                                <td class="lead_status_ID"><?=$lead['lead_status_nome']?></td>
						      	<td class="acoes">
									<? if($_SESSION['login']['nr_func'] == 211 || $_SESSION['login']['nr_func'] == 127){ ?>
								  		<img id="exclui-lead" onclick="excluiLead(<?=$lead['lead_ID']?>, '<?=$lead['lead_nome']?>');" src="../imagens/icones/exclui.gif"/>
									<? } ?>
									<a href="../App_Pes/Leads.php?lead_ID=<?=$lead['lead_ID']?>"><img src="../imagens/icones/edit_rounded.ico"/></a>
						      	</td>
							</tr>
							
					<? endforeach ?>
					<? }else{
					?>
						<tr>
					      <th scope="row" class="text-alert">Nao foi possivel encontrar nenhum registro.</th>
					    </tr>
					<? } ?>
				</tbody>
			</table>
		</div>
	</div>
	</div>
	<?php
		navPaginacao("ListLeads.php", $pagina, $num_paginas);
	?>
    <div id="rodape">
<?php 
	require_once("../base.php")
?>
	</div>
	<script>
		$(function(){
			voltar();
			criarLead();
		});

		function voltar(){
			$("#voltar").click(function(){
				window.location="../App_Pes/CnsLeads.php";
			});
		}

		function criarLead(){
			$("#cria-lead").click(function(){
				window.location="../App_Pes/Leads.php";
			});
		}

		function excluiLead(id, nome){
			var answer = confirm("Realmente deseja excluir a lead " + nome + "?");

			if(answer){
				$.post("../App_Pes/ControlLeads.php", {action: 'exclui', type: 'lead', id: id}, function(data){
					alert(data);
					location.reload();
				});
			}
		}
	</script>
</body>
</html>