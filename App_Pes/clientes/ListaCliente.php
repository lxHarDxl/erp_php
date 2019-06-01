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
$where['status_cliente'] = " c.ds_status = 'A' ";

if(isset($_POST) && !empty($_POST)){
    $chars = array("(", ")", "-", "_", ".", ",", "[", "]", "{", "}", " ");

    if(!empty($_POST['nm_pessoa'])){
        $where['nm_pessoa'] = "p.nm_pessoa LIKE '%{$_POST['nm_pessoa']}%' OR p.nm_fantasia LIKE '%{$_POST['nm_pessoa']}%' ";
    }
    
    if(!empty($_POST['nr_cnpjcpf'])){
        $_POST['nr_cnpjcpf'] = str_replace($chars, "", $_POST['nr_cnpjcpf']);

        $where['nr_cnpjcpf'] = "p.nr_cnpjcpf LIKE '%{$_POST['nr_cnpjcpf']}%' ";
    }
    
    if(!empty($_POST['nr_ierg'])){
        $_POST['nr_ierg'] = str_replace($chars, "", $_POST['nr_ierg']);
        $where['nr_ierg'] = "p.nr_ierg LIKE '%{$_POST['nr_ierg']}%' ";
    }
    
    if(!empty($_POST['nr_empresa'])){
        $where['nr_empresa'] = " c.nr_empresa = {$_POST['nr_empresa']}";
    }

    if(!empty($_POST['nr_colaborador'])){
        $where['nr_colaborador'] = " (c.nr_colaborador = {$_POST['nr_colaborador']} OR g.nr_func = {$_POST['nr_colaborador']})";
    }
	
	if(!empty($_POST['nr_hubpop'])){
        $where['nr_hubpop'] = " (c.nr_hubpop = {$_POST['nr_hubpop']} OR ctrt.nr_hubpop = {$_POST['nr_hubpop']} )";
	}

	if(!empty($_POST['nr_status_contrato'])){
		
		$where['nr_status_contrato'] = " ctrt.nr_status_contrato = {$_POST['nr_status_contrato']}";
	}

	if(!empty($_POST['dt_vencto'])){
		$dt_vencto = convertDateToBd($_POST['dt_vencto']);
		
        $dt_vencto_fim = !empty($_POST['dt_vencto_fim']) ? convertDateToBd($_POST['dt_vencto_fim']) : $dt_vencto;

        $where['lead_contato_agendamento'] = " ctrt.dt_vencto BETWEEN '$dt_vencto' AND '$dt_vencto_fim'";
	}

	if(!empty($_POST['dt_cancelamento'])){
		$dt_cancelamento = convertDateToBd($_POST['dt_cancelamento']);
		
        $dt_cancelamento_fim = !empty($_POST['dt_cancelamento_fim']) ? convertDateToBd($_POST['dt_cancelamento_fim']) : $dt_cancelamento;

        $where['lead_contato_agendamento'] = " ctrt.dt_cancelamento BETWEEN '$dt_cancelamento' AND '$dt_cancelamento_fim'";
	}
	
	$where = empty($where) ? "" : " WHERE ".implode(" AND ", $where);

	$_SESSION['login']['where_cliente']   = $where;
}else{
	$where = $_SESSION['login']['where_cliente'];
}

$sql_cliente = "SELECT DISTINCT
                        p.nr_pessoa,
                        p.nm_pessoa,
                        p.nr_cnpjcpf,
                        c.nr_cliente,
                        IF(co.nm_pessoa IS NULL, (SELECT nm_pessoa FROM pessoa WHERE nr_pessoa = (SELECT nr_pessoa FROM func WHERE nr_func = g.nr_func LIMIT 1)), co.nm_pessoa) AS nm_colaborador,
                        emp.nm_fantasia
					FROM
						pessoa p
							INNER JOIN
						cliente c ON p.nr_pessoa = c.nr_pessoa
							LEFT JOIN
						func f ON c.nr_colaborador = f.nr_func
							LEFT JOIN
						pessoa co ON f.nr_pessoa = co.nr_pessoa
							LEFT JOIN
						empresa emp ON c.nr_empresa = emp.nr_empresa
							LEFT JOIN
						gerenteconta g ON c.nr_cliente = g.nr_cliente
							LEFT JOIN
						ctrt ON p.nr_pessoa = ctrt.nr_pessoa
					$where GROUP BY p.nr_pessoa ORDER BY p.nm_pessoa , p.nr_pessoa";

if(isset($_POST) && !empty($_POST) && empty($_SESSION['login']['clientes_contrato'])){
	$lista_nr_cliente = "";
	$nr_cliente = executar($sql_cliente);
	foreach($nr_cliente as $v){
		$lista_nr_cliente = empty($lista_nr_cliente) ? $v['nr_cliente'] : "$lista_nr_cliente, {$v['nr_cliente']}";
	}

	$_SESSION['login']['clientes_contrato'] = $lista_nr_cliente;
} else {
	$lista_nr_cliente = $_SESSION['login']['clientes_contrato'];
}

$title = utf8_decode("Lista de Clientes");
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
    <style>
        table td{
            font-size: 12px !important;
        }
        table th{
            font-size: 14px !important;
        }
    </style>
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
				<button type="button" class="ml-5 btn btn-outline-dark btn-sm" id="cadastra-cliente"> Cadastrar Cliente </button>
				<button class="btn btn-outline-secondary btn-sm ml-5" type="button" id="voltar"> Voltar </button>
			</div>
		</div>
		<div class="table-responsive-sm row justify-content-md-center">
			<table class="table table-sm border table-hover shadow w-75 text-center">
				<thead class="bg-info">
				  <tr>
                    <th scope="col">CPF/CNPJ</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Total de Contratos</th>
					<th scope="col">Gerente Conta</th>
                    <th scope="col">Regional</th>
                    <th scope="col"> </th>
				  </tr>
				</thead>
				<tbody class="bg-light">
					<?
					$ret_clientes = calculaPaginacao($sql_cliente, $registros, $pagina);
					$num_paginas = $ret_clientes['paginas'];

					if($ret_clientes){
						foreach($ret_clientes['sql'] as $cliente){
							$sql_count_ctrt = "SELECT count(nr_ctrt) as numCtrt FROM ctrt WHERE nr_cliente = {$cliente['nr_cliente']} AND ctrt_novo = 0";
							$count_ctrt = executar($sql_count_ctrt);

							$nr_cnpjcpf = strlen($cliente['nr_cnpjcpf']) > 11 ?  mascara($cliente['nr_cnpjcpf'], 'cnpj') : mascara($cliente['nr_cnpjcpf'], 'cpf');
							$cliente['nm_pessoa'] = strlen($cliente['nm_pessoa']) > 56 ? substr($cliente['nm_pessoa'], 0, 56) : $cliente['nm_pessoa'];
							$cliente['nm_colaborador'] = strlen($cliente['nm_colaborador']) > 24 ? substr($cliente['nm_colaborador'], 0, 24) : $cliente['nm_colaborador'];
					?>
							<tr >  
                                <td class="nr_cnpjcpf"><?=$nr_cnpjcpf?></td>
						      	<td class="nm_pessoa"><?=$cliente['nm_pessoa']?></td>
								<td class="total_ctrt"><?=$count_ctrt[0]['numCtrt']?></td>
                                <td class="nr_colaborador"><?=$cliente['nm_colaborador']?></td>
                                <td class="nr_empresa"><?=$cliente['nm_fantasia']?></td>
						      	<td class="acoes">
									<a href="../App_Pes/Cliente.php?nr_pessoa=<?=$cliente['nr_pessoa']?>"><img src="../imagens/icones/edit_rounded.ico"/></a>
						      	</td>
							</tr>
							
						<? }
					$where_2   = !empty($_POST['nr_status_contrato']) ? " INNER JOIN status_contrato ON ctrt.nr_status_contrato = status_contrato.nr_status_contrato WHERE ctrt.nr_status_contrato = {$_POST['nr_status_contrato']} AND ctrt.nr_cliente IN ($lista_nr_cliente) AND ctrt_novo = 0" : "WHERE ctrt.nr_cliente IN ($lista_nr_cliente) AND ctrt_novo = 0";
					$ds_status = !empty($_POST['nr_status_contrato']) ? " ,ds_status_contrato " : "";
					$total_contratos = executar("SELECT 
													(SUM(mensal_valor) + SUM(install_valor) + SUM(produto_valor) + SUM(servico_valor)) AS valor
													$ds_status
												FROM
													ctrt
														INNER JOIN
													ctrt_valor ON ctrt.nr_ctrt = ctrt_valor.nr_ctrt
												$where_2");
					$valor_total_contratos = number_format($total_contratos[0][0], 2, ",", ".");
					$ds_status = !empty($_POST['nr_status_contrato']) ? $total_contratos[0][1] : "";
					?>
					<tr class="bg-primary text-light text-right" title="Total de contrados de acordo com o STATUS selecionado">
						<td colspan="6">Valor Total dos Contratos <?=$ds_status?>: R$<?=$valor_total_contratos?></td>
					</tr>
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
		navPaginacao("ListaCliente.php", $pagina, $num_paginas);
	?>
    <div id="rodape">
<?php 
	require_once("../base.php")
?>
	</div>
	<script>
		$(function(){
			voltar();
			cadastraCliente();
		});

		function voltar(){
			$("#voltar").click(function(){
				window.location="../App_Pes/CnsCliente.php";
			});
		}

		function cadastraCliente(){
			$("#cadastra-cliente").click(function(){
				window.location="../App_Pes/Cliente.php";
			});
		}
	</script>
</body>
</html>