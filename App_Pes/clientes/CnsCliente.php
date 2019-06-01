<?
require_once("../config.php");
require_once("../conexao.php");
require_once("../funcoes.php");

require_once("../toolbarPreVendas.php");

$codEmpresa = $_SESSION['login']['nr_empresa'];

$comboUN = executar('SELECT nr_empresa, UPPER(nm_fantasia) as nm_fantasia FROM empresa ORDER BY nm_fantasia');
$title = utf8_decode("Consulta de Clientes");
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
    
    <script src="../amcharts4/core.js"></script>
	<script src="../amcharts4/charts.js"></script>
	<script src="../amcharts4/themes/kelly.js"></script>
    <script src="../amcharts4/themes/animated.js"></script>
	
    <script type="text/javascript" src="../App_Pes/js/Cliente/formCliente.js"></script>
    <script type="text/javascript" src="../App_Pes/js/Cliente/formClienteChart.js"></script>

    <style>
        #botoes {
            padding-top: 20px;
        }
        .form-group {
			margin-bottom: 1px !important; 
			font-size: 14px !important;
		}
		.chart-funnel {
			font-size: 12px;
			padding-top: 20px;
			padding-left: 5px;
			height: 410px !important;
			width: 200px !important;
		}
		#chartdiv-line {
			width: 200px;
		}
	</style>
</head>
<body style="background-color: #DCDCDC">
	<div id="cabecalho">
	        <? require_once("../topo.php"); ?>
	</div>
		<?=toolBar($opcoes, $empresa)?>
	<div id="corpo">
    <br />
        <div class="container-fluid">
            <div id="pesquisa" class="row pl-4">
                <div class="col-md-5 w-100 border border-info shadows bg-light chart-funnel row justify-content-left"  >
					<div class="col-6  mx-auto">
						<select id="un-chart" name="un-chart" class="custom-select custom-select-sm w-100">
							<option value="geral">GERAL</option>
						<?
							foreach ($comboUN as $op) {
						?>
								<option value="<?=$op['nr_empresa']?>" ><?=$op['nm_fantasia']?></option>
						<?
							}
						?>
						</select>

						<!-- CHART MONTH GOALS -->
						
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row" style="margin-top: -50px;">
                            <label for="clientes_ativos" class="col-sm-3 col-form-label"> Clientes Ativos: </label>
                            <div class="col-sm-3">
                                <p id="clientes_ativos" class="mt-2"></p>
                            </div>
                            <label for="contratos_ativos" class="col-sm-4 col-form-label"> Contratos Ativos: </label>
                            <div class="col-sm-2">
                                <p id="contratos_ativos" name="contratos_ativos" class="mt-2"> </p>
                            </div>
                        </div>
                    </div>

					<div id="chartdiv-line" class="col-11 pb-2">
					</div>
				</div>
                <form id="form-pesquisa" name="formulario" action="../App_Pes/ListaCliente.php" method="POST" class="mx-auto w-100 col-md-7 pt-2 border border-info shadows bg-light mb-2">
                    <div class="pl-2 pt-2 mb-3 bg-primary border rounded container-fluid"> 
                        <h4 class="font-weight-bold text-light"><?=$title?></h4>
                    </div>
                    <div class="form-group row">
                        <label for="nm_pessoa" class="col-sm-2 col-form-label"> Cliente: </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm " id="nm_pessoa" name="nm_pessoa" value="<?=$cliente[0]['nm_pessoa']?>" placeholder="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="nr_cnpjcpf" class="col-sm-2 col-form-label"> CNPJ: </label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control form-control-sm " id="nr_cnpjcpf" name="nr_cnpjcpf" value="<?=$cliente[0]['nr_cnpjcpf']?>" placeholder="">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="nr_colaborador" class="col-sm-2 col-form-label"> Colaborador: </label>
                        <div class="col-sm-4">
                            <select id="nr_colaborador" class="custom-select custom-select-sm w-100 key" name="nr_colaborador">
                                <option id="" value=""></option>
                            <?
                                $comboColaborador = executar('SELECT nr_func, UPPER(nm_pessoa) as nm_pessoa, ativo FROM func JOIN pessoa USING(nr_pessoa)JOIN login USING(nr_pessoa) WHERE ativo="S" ORDER BY nm_pessoa');
                                foreach ($comboColaborador as $op) {
                            ?>
                                    <option id="<?=$op['nr_func']?>" value="<?=$op['nr_func']?>"><?=$op['nm_pessoa']?></option>
                            <?
                                }
                            ?>
                            </select>
                        </div>

                        <label for="nr_status_contrato" class="col-sm-2 col-form-label"> Status Contrato: </label>
                        <div class="col-sm-4">
                            <select id="nr_status_contrato" class="custom-select custom-select-sm w-100 key" name="nr_status_contrato">
                                <option id="" value=""></option>
                            <?
                                $statusContrato = executar('SELECT nr_status_contrato, UPPER(ds_status_contrato) as ds_status_contrato FROM status_contrato ORDER BY ds_status_contrato');
                                foreach ($statusContrato as $op) {
                            ?>
                                    <option id="<?=$op['nr_status_contrato']?>" value="<?=$op['nr_status_contrato']?>"><?=$op['ds_status_contrato']?></option>
                            <?
                                }
                            ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dt_vencto" class="col-sm-2 col-form-label"> Vencimento dos Contratos: </label>
                        <div class="col-sm-5">
                            <input id="dt_vencto" name="dt_vencto" class="form-control form-control-sm ">
                        </div>
                        <div class="col-sm-5">
                            <input id="dt_vencto_fim" name="dt_vencto_fim" class="form-control form-control-sm ">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dt_cancelamento" class="col-sm-2 col-form-label"> Cancelamento dos Contratos: </label>
                        <div class="col-sm-5">
                            <input id="dt_cancelamento" name="dt_cancelamento" class="form-control form-control-sm ">
                        </div>
                        <div class="col-sm-5">
                            <input id="dt_cancelamento_fim" name="dt_cancelamento_fim" class="form-control form-control-sm ">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="nr_empresa" class="col-sm-2 col-form-label"> Regional: </label>
                        <div class="col-sm-4">
                            <select id="nr_empresa" name="nr_empresa" class="custom-select custom-select-sm w-100 ">
                                <option value=""></option>
                                <?
                                $empresa = executar("SELECT nr_empresa, nm_fantasia FROM empresa ORDER BY nm_fantasia");
                                foreach($empresa as $value){
                                    $selected = $value['nr_empresa'] == $_SESSION['login']['nr_empresa'] ? "selected" : "";
                                    ?>
                                    <option id="<?=$value['nr_empresa']?>" value="<?=$value['nr_empresa']?>" <?=$selected?>> <?=$value['nm_fantasia']?> </option>
                                    <?
                                }
                                ?>
                            </select>
                        </div>
                        <label for="nr_hubpop" class="col-sm-2 col-form-label"> HubPop: </label>
                        <div class="col-sm-4">
                            <select id="nr_hubpop" name="nr_hubpop" class="custom-select custom-select-sm w-100 ">
                                <option value=""></option>
                                <?
                                $hubpop = executar("SELECT nr_hubpop, CONCAT(nm_hubpop, '(', nm_apelido, ')') as lead_hubpop_br FROM hubpop WHERE status_hubpop = 'S' ORDER BY nm_hubpop");
                                foreach($hubpop as $value){
                                    ?>
                                    <option id="<?=$value['nr_hubpop']?>" value="<?=$value['nr_hubpop']?>"> <?=$value['lead_hubpop_br']?> </option>
                                    <?
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div id="botoes" class="container mb-3">
                        <div class="row justify-content-md-center">
                            <button type="submit" class="ml-5 btn btn-primary btn-sm"> Consultar </button>
                            <button type="reset" class="ml-2 btn btn-outline-secondary btn-sm pesquisa" id="consulta"> Limpar </button>
                            <button type="button" class="ml-5 btn btn-outline-dark btn-sm" id="cadastra-cliente"> Cadastrar Cliente </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
	</div>
    <div id="rodape">
<? 
	require_once("../base.php")
?>
    </div>
    <script>
        $(function(){
            criarLead();

            $("#dt_vencto").datepicker({
                uiLibrary: 'bootstrap4',
                autoclose: true,
                format: 'dd/mm/yyyy', 
                language: 'pt-BR'
            });
            $("#dt_vencto_fim").datepicker({
                uiLibrary: 'bootstrap4',
                autoclose: true,
                format: 'dd/mm/yyyy', 
                language: 'pt-BR'
            });
            $("#dt_cancelamento").datepicker({
                uiLibrary: 'bootstrap4',
                autoclose: true,
                format: 'dd/mm/yyyy', 
                language: 'pt-BR'
            });
            $("#dt_cancelamento_fim").datepicker({
                uiLibrary: 'bootstrap4',
                autoclose: true,
                format: 'dd/mm/yyyy', 
                language: 'pt-BR'
            });
        });
		function criarLead(){
			$("#cadastra-cliente").click(function(){
				window.location="../App_Pes/Cliente.php";
			});
		}
    </script>
</body>
</html>