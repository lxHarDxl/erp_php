<?php
require_once("../config.php");
require_once("../conexao.php");
require_once("../funcoes.php");
require_once("../toolbarPreVendas.php");


$hostname=gethostbyaddr($_SERVER['REMOTE_ADDR']);
$cor="#FFFCDE";

	$count = 0;

	if(isset($_REQUEST['tipo_customer']) && !empty($_REQUEST['tipo_customer'])) {
		$keyword = $_REQUEST['keyword'];
		$tipo = anti_injection($_GET['tipo']);
		$tipo_customer = $_REQUEST['tipo_customer'];


		if($tipo == 'nome') {
			$where.= '(pessoa.nm_pessoa LIKE "%'.$keyword.'%" OR pessoa.nm_fantasia LIKE "%'.$keyword.'%")';
			$selnome = 'selected';
		} else {
			$where.= 'nr_cnpjcpf = "'.limpaString($keyword).'"';
			$selcnpj = 'selected';
		}


		/*	TIPO DO CLIENTE - ATIVO OU PREVENDA	*/
		if($tipo_customer == 'all'){
			$seltpcstmrall = 'selected';
			$where .= ' ';
		}
	    	else if($tipo_customer == 'ativo'){
			$seltpcstmrativo = 'selected';
			$where .= ' AND cliente.nr_tipo_cliente NOT IN(4)';
		}
		else if($tipo_customer == 'prevendas'){
			$seltpcstmrprevendas = 'selected';
			$where .= ' AND cliente.nr_tipo_cliente IN(4)';
		}

		$count++;
	}

	if(!empty($where)) {
		$where = 'WHERE '.$where;
	} else {
		$where = '';
	}


	$criterios = 'keyword='.$keyword.'&tipo='.$tipo.'&regiao='.$regiao.'&tipo_customer='.$tipo_customer;
	$resultado_por_pagina = 10;
	$pagina = (isset($_GET['pg'])) ? intval($_GET['pg']) : 1;
	$inicio = (!isset($pagina)) ? 0 : ($pagina - 1) * $resultado_por_pagina;
	
	
	#ADICIONEI LEFT JOIN no cliente,
	$sql = 'SELECT '
				.'pessoa.nr_pessoa,'
				.'cliente.nr_cliente,'
				.'nm_pessoa	,'
				.'nr_cnpjcpf,'
				.'tp_pessoa,'
				.'nr_filial,'
				.'ds_tipo_cliente as tp_cliente,'
				.'COUNT(DISTINCT(nr_ctrt)) as totalContrato '
			.'FROM pessoa '
				.'LEFT JOIN cliente ON pessoa.nr_pessoa = cliente.nr_pessoa '
				.'LEFT JOIN ctrt ON pessoa.nr_pessoa = ctrt.nr_pessoa JOIN '
				.'tipo_cliente ON tipo_cliente.nr_tipo_cliente = cliente.nr_tipo_cliente '
			.$where.'  AND ds_status = "A" '
			.'GROUP BY pessoa.nr_pessoa '
			.'ORDER BY pessoa.nm_pessoa';
	$res = executar($sql);

	$total_registros = count($res);
	$total_paginas = ceil($total_registros / $resultado_por_pagina);
	
	$sql = $sql.' LIMIT '.$inicio.','.$resultado_por_pagina;
	$registros = executar($sql);
	
	$proximo = $pagina + 1;
	$anterior = $pagina - 1;

	$link_anterior = ($anterior >= 1) ? '<a href="?'.$criterios.'&pg='.$anterior.'" title="P&aacute;gina Anterior"><img src="'.$icones.'/seta_esquerda.gif"/></a>' : '';
	$link_proximo = ($proximo <= $total_paginas) ? '<a href="?'.$criterios.'&pg='.$proximo.'" title="Pr&oacute;xima P&aacute;gina"><img src="'.$icones.'/seta_direita.gif"/></a>' : '';

	$array_regiao = executar('SELECT nr_filial,nm_filial FROM filial ORDER BY nm_filial');

flush();
?>

<html>
<head>
<title><?php echo $titulo . " " . $versao;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Content-Language" content="pt-BR">
<meta http-equiv="Cache-control" content="no-cache, must-revalidate">
<meta http-equiv="expires" content="-1">
<link HREF="<?=$css?>" REL="stylesheet" TYPE="text/css">
<link HREF="<?=$caminho?>/menu.css" REL="stylesheet" TYPE="text/css">
<script language="JavaScript" src="<?=$jscript?>"></script>
<script language="JavaScript" src="<?=$jsfuncao?>"></script>
<script language="JavaScript" src="<?=$jsjquery?>"></script>
<script>
function geraCliente(exportar){

	var tipo_pessoa		= $('select[name=tipo_pessoa]').val();
	var tipo_cliente	= $('select[name=tipo_cliente]').val();


	window.open('Cliente.php?action=export&type_person='+tipo_pessoa+'&type_client='+tipo_cliente,'name','height=1,width=1');

}
</script>
</head>
<body onload="document.formulario.keyword.focus();">
<div id="cabecalho">
		<?php require_once("../topo.php")?>
</div>
	<?=toolBar($opcoes)?>
<div id="corpo">
	<br />
	<table id="tb_crud" width="90%" cellpadding="2" align="center" cellspacign="0">
	<tr>
		<TH colspan="7"> Gerar Clientes	</TH>
	</tr>
	<tr style="text-align:center;">
		<TD id="label_form"> Tipo de Pessoa</TD>
		<TD id="label_form">
			<select name="tipo_pessoa">
				<option value="">Todos</option>
				<option value="F">Pessoa Fisica</option>
				<option value="J">Pessoa Juridica</option>
			</select>
		</TD>
		<TD id="label_form"> Tipo de Cliente </TD>
		<TD id="label_form">
			<select name="tipo_cliente">
				<option value="">Todos</option>
				<option value="1">Cliente Normal</option>
				<option value="2">Prospect</option>
				<option value="3">Suspect</option>
				<option value="4">Pre-Vendas</option>

			</select>
		</TD>
	</tr>
	<tr>
		<TD colspan="7"> <center>  <input type="button" value="Pesquisar" class="botao" onclick="geraCliente()"> </center> </TD>
	</tr>
	</table>

	<div id='resultado'></div>
</div>
</body>
</html>
