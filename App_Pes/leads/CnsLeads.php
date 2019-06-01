<?
require_once("../config.php");
require_once("../conexao.php");
require_once("../funcoes.php");
require_once("../App_Pes/FuncoesNFSE.php");

require_once("../toolbarPreVendas.php");

$codEmpresa = $_SESSION['login']['nr_empresa'];

$comboUN = executar('SELECT nr_empresa, UPPER(nm_fantasia) as nm_fantasia FROM empresa ORDER BY nm_fantasia');
$title = utf8_decode("Consulta de Leads");
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
	
    <link rel="stylesheet" type="text/css"  href="<?=$caminho?>/bootstrap/css/bootstrap.min.css">
    <link href="https://unpkg.com/gijgo@1.9.11/css/gijgo.min.css" rel="stylesheet" type="text/css" />
	
    <script type="text/javascript" src="<?=$caminho?>/jquery/jquery-3.3.1.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.11/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script type="text/javascript" src="<?=$caminho?>/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="http://code.jquery.com/qunit/qunit-1.11.0.js"></script>
    <script type="text/javascript" src="../obj_js/jQuery-Mask-Plugin-master/test/sinon-1.10.3.js"></script>
	<script type="text/javascript" src="../obj_js/jQuery-Mask-Plugin-master/test/sinon-qunit-1.0.0.js"></script>
	

	<script src="../amcharts4/core.js"></script>
	<script src="../amcharts4/charts.js"></script>
	<script src="../amcharts4/themes/kelly.js"></script>
    <script src="../amcharts4/themes/animated.js"></script>

	
    <style>
        #botoes {
            padding-top: 20px;
        }

		.form-group {
			margin-bottom: 1px !important; 
			font-size: 12px !important;
		}
		.linha-vertical {
			height: 90%;/*Altura da linha*/
			border-left: 1px solid;/* Adiciona borda esquerda na div como ser fosse uma linha.*/
		}
		.chart-funnel {
			font-size: 12px;
			padding-top: 20px;
			padding-left: 5px;
			height: 458px !important;
			width: 200px !important;
		}
		#chartdiv-funnel {
			margin-top: -5px;
			padding-left: 30px;
			height: 200px;
		}
		#chartdiv-line {
			width: 200px;
		}
		#btn-chart-goal-per {
			margin-bottom: 10px;
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
					
					<div id="chartdiv-funnel" class="col-6">
					</div>
					<div class="col-6">
						<select id="sel-chart" name="sel-chart" class="custom-select custom-select-sm w-100" onload="infoChart($(this).val());" onchange="infoChart($(this).val());">
							<option value="funilmetas">Funil/Metas</option>
							<option value="funil">Funil</option>
							<option value="metas">Metas</option>
						</select>
						<select id="un-chart" name="un-chart" class="custom-select custom-select-sm w-100">
							<option value="geral">GERAL</option>
						<?
							foreach ($comboUN as $op) {
						?>
								<option value="<?=$op['nr_empresa']?>"><?=$op['nm_fantasia']?></option>
						<?
							}
						?>
						</select>

						<!-- CHART MONTH GOALS -->
						<select id="sel-month" name="sel-month" class="custom-select custom-select-sm w-100" onchange="verMonth($(this).val());">
							<option value="12-meses"><?=utf8_decode("12 Meses")?></option>
							<option value="6-meses"><?=utf8_decode("6 Meses")?></option>
							<option value="2018-ano"><?=utf8_decode("Ano 2018")?></option>
							<option value="2019-ano"><?=utf8_decode("Ano 2019")?></option>
							<option value="personalizado"><?=utf8_decode("Personalizado")?></option>
						</select>

						<div id="chart-goals" class="row justify-content-center pt-3">
							<h6 style="font-size: 12px;"><?=utf8_decode("Período:")?></h6>
							<button id="btn-chart-goal-per" class="ml-3 btn btn-sm btn-primary" onclick="pesquisaMetasPersonalizada();">Enviar</button><input type="text" id="chart-goal-beg" class="ml-5 col-5" name="chart-goal-beg"><input type="text" id="chart-goal-end" class="ml-5 col-5" name="chart-goal-end">
						</div>
					</div>

					<div id="chartdiv-line" class="col-11 pb-2">
					</div>
				</div>
                <form id="form-pesquisa" name="formulario" action="../App_Pes/ListLeads.php" method="POST" class="mx-auto w-100 col-md-7 pt-2 border border-info shadows bg-light mb-2">
                    <div class="container">
						<div class="pl-2 pt-2 mb-3 bg-primary border rounded container-fluid"> 
							<h4 class="font-weight-bold text-light"><?=$title?></h4>
						</div>
						<div class="form-group row pl-3">
							<label for="opt" class="col-sm-2 col-form-label"> HubPop: </label>
							<div class="col-sm-4">
								<select id="lead_hubpop_ID" class="custom-select custom-select-sm w-100 key" name="lead_hubpop_ID">
								<?
									$comboHubpop = executar('SELECT nr_hubpop, UPPER(nm_hubpop) as nm_hubpop FROM hubpop WHERE status_hubpop="S" ORDER BY nm_hubpop');

									?>
										<option id="" value=""></option>
									<?
									foreach ($comboHubpop as $op) {
								?>
										<option id="<?=$op['nr_hubpop']?>" <?=$selecao?> value="<?=$op['nr_hubpop']?>"><?=$op['nm_hubpop']?></option>
								<?
									}
								?>
								</select>
							</div>
							<label for="cidade" class="col-sm-2 col-form-label"> Cidade: </label>
							<div class="col-sm-4">
								<input type="text" id="cidade" name="cidade" class="form-control form-control-sm" autocomplete="off"/>
							</div>
                        </div>
						<div class="form-group row pl-3">
							<label for="opt" class="col-sm-2 col-form-label"> Perfil: </label>
							<div class="col-sm-4">
								<select id="lead_etapa_ID" class="custom-select custom-select-sm w-75 key" name="lead_etapa_ID">
								<?
									$comboEtapa   = executar("SELECT lead_etapa_ID, lead_etapa_nome FROM lead_etapa ORDER BY lead_etapa_prioridade");
									?>
										<option id="" value=""></option>
									<?
									foreach ($comboEtapa as $op) {
								?>
										<option id="<?=$op['lead_etapa_ID']?>" <?=$selecao?> value="<?=$op['lead_etapa_ID']?>"><?=$op['lead_etapa_nome']?></option>
								<?
									}
								?>
								</select>
							</div>
							<label for="opt" class="col-sm-2 col-form-label pl-5"> Status: </label>
							<div class="col-sm-4">
								<select id="lead_status_ID" class="custom-select custom-select-sm w-75 key" name="lead_status_ID">
								<?
									$comboStatus  = executar("SELECT lead_status_ID, lead_status_nome FROM lead_status ORDER BY lead_status_nome");
	
									?>
										<option id="" value=""></option>
									<?
									foreach ($comboStatus as $op) {
								?>
										<option id="<?=$op['lead_status_ID']?>" <?=$selecao?> value="<?=$op['lead_status_ID']?>"><?=$op['lead_status_nome']?></option>
								<?
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group row pl-3">
							<label for="opt" class="col-sm-2 col-form-label"> Colaborador: </label>
							<div class="col-sm-10">
								<select id="lead_colaborador" class="custom-select custom-select-sm w-50 key" name="lead_colaborador">
								<?
									$comboColaborador = executar('SELECT nr_func, UPPER(nm_pessoa) as nm_pessoa, ativo FROM func JOIN pessoa USING(nr_pessoa)JOIN login USING(nr_pessoa) WHERE ativo="S" ORDER BY nm_pessoa');

									?>
										<option id="" value=""></option>
									<?
									foreach ($comboColaborador as $op) {
								?>
										<option id="<?=$op['nr_func']?>" <?=$selecao?> value="<?=$op['nr_func']?>"><?=$op['nm_pessoa']?></option>
								<?
									}
								?>
								</select>
							</div>
                        </div>
                        <div class="form-group row pl-3">
						    <label for="ctrt_sva" class="col-sm-2 col-form-label"> Empresa: </label>
						    <div class="col-sm-10">
						    	<input type="text" id="lead_nome" name="lead_nome" class="form-control-sm f w-75 key" value="" />
						    </div>
                        </div>
						<div class="form-group row pl-3">
						    <label for="lead_agendamento_recisao_ini" class="col-sm-2 col-form-label"> <?=utf8_decode("Rescisão de Contrato:")?></label>
						    <div class="col-sm-4">
						    	<input type="text" id="lead_agendamento_recisao_ini" class="form-control-sm f key" name="lead_agendamento_recisao_ini" placeholder="01/01/2000">
							</div>
							<?=utf8_decode(" à ")?>
							<div class="col-sm-4">
								<input type="text" id="lead_agendamento_recisao_fim" class="form-control-sm f key" name="lead_agendamento_recisao_fim" placeholder="31/12/2018">
							</div>
						</div>
						<div class="form-group row pl-3">
						    <label for="lead_contato_data_ini" class="col-sm-2 col-form-label"> Data de Contato: </label>
						    <div class="col-sm-4">
						    	<input type="text" id="lead_contato_data_ini" class="form-control-sm f key" name="lead_contato_data_ini" placeholder="01/01/2000">
							</div>
							<?=utf8_decode(" à ")?>
							<div class="col-sm-4">
								<input type="text" id="lead_contato_data_fim" class="form-control-sm f key" name="lead_contato_data_fim" placeholder="31/12/2018">
							</div>
						</div>
                        <div class="form-group row pl-3">
						    <label for="lead_contato_agendamento_ini" class="col-sm-2 col-form-label"> Agendamento de Retorno: </label>
						    <div class="col-sm-4">
						    	<input type="text" id="lead_contato_agendamento_ini" class="form-control-sm f key" name="lead_contato_agendamento_ini" placeholder="01/01/2000">&nbsp;  &nbsp;
							</div>
							<?=utf8_decode(" à ")?>
							<div class="col-sm-4">
							<input type="text" id="lead_contato_agendamento_fim" class="form-control-sm f key" name="lead_contato_agendamento_fim" placeholder="31/12/2018">
							</div>
						</div>
						<div class="form-group row pl-3">
							<label for="opt" class="col-sm-2 col-form-label"> <?=utf8_decode("Unidade de Negócio:")?> </label>
							<div class="col-sm-10">
								<select id="unidade_negocio" class="custom-select custom-select-sm w-50 key" name="unidade_negocio">
									<option id="" value=""></option>

								<?
									foreach ($comboUN as $op) {
										#$selecao = $op['nr_empresa'] == $_SESSION['login']['nr_empresa'] ? "selected" : "";
								?>
										<option id="<?=$op['nr_empresa']?>" <?=$selecao?> value="<?=$op['nr_empresa']?>"><?=$op['nm_fantasia']?></option>
								<?
									}
								?>
								</select>
							</div>
                        </div>
                        
                        <div class="form-group row pl-3">
                        </div>
                        <div id="botoes" class="container mb-3">
                            <div class="row justify-content-md-center">
								<button type="button" class="ml-5 btn btn-primary btn-sm" id="gerar"> Consulta Leads </button>
								<button type="button" class="ml-2 btn btn-outline-secondary btn-sm  pesquisa" id="consulta"> Limpar </button>
								<button type="button" class="ml-5 btn btn-outline-dark btn-sm" id="cria-lead"> Criar Lead </button>
                            </div>
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
		var chart_goal_end               = $('#chart-goal-end');
		var chart_goal_begin             = $('#chart-goal-beg');
		var lead_contato_data_ini        = $('#lead_contato_data_ini');
        var lead_contato_data_fim        = $('#lead_contato_data_fim');
        var lead_contato_agendamento_ini = $('#lead_contato_agendamento_ini');
        var lead_contato_agendamento_fim = $('#lead_contato_agendamento_fim');
		var lead_agendamento_recisao_ini = $('#lead_agendamento_recisao_ini');
        var lead_agendamento_recisao_fim = $('#lead_agendamento_recisao_fim');
		var botao = $("#gerar");

		$(function(){
			$("#chart-goals").hide();

			lead_contato_data_ini.datepicker({ uiLibrary: 'bootstrap4', autoclose: true, format: 'dd/mm/yyyy', language: 'pt-BR' });
			lead_contato_data_fim.datepicker({ uiLibrary: 'bootstrap4', autoclose: true, format: 'dd/mm/yyyy', language: 'pt-BR' });

		    lead_contato_agendamento_ini.datepicker({ uiLibrary: 'bootstrap4', autoclose: true, format: 'dd/mm/yyyy', language: 'pt-BR' });
		    lead_contato_agendamento_fim.datepicker({ uiLibrary: 'bootstrap4', autoclose: true, format: 'dd/mm/yyyy', language: 'pt-BR' });

			lead_agendamento_recisao_ini.datepicker({ uiLibrary: 'bootstrap4', autoclose: true, format: 'dd/mm/yyyy', language: 'pt-BR' });
		    lead_agendamento_recisao_fim.datepicker({ uiLibrary: 'bootstrap4', autoclose: true, format: 'dd/mm/yyyy', language: 'pt-BR' });

			chart_goal_begin.datepicker({ uiLibrary: 'bootstrap4', autoclose: true, format: 'dd/mm/yyyy', language: 'pt-BR' });
		    chart_goal_end.datepicker({ uiLibrary: 'bootstrap4', autoclose: true, format: 'dd/mm/yyyy', language: 'pt-BR' });

			enterPesquisa();
			validaCamposData();
			infoChart();
			infoChartFunnel();
			infoChartGoals();
			criarLead();
        });

		function enterPesquisa(){
			$('.key').keypress(function (e) {
				if (e.which == 13) {
					botao.trigger('click');
					return false;
				}
			});
		}

		function validaCamposData(){
			var lead_contato_data_ini_val = lead_contato_data_ini.val();
			var lead_contato_data_fim_val = lead_contato_data_fim.val();
			var lead_contato_agendamento_ini_val = lead_contato_agendamento_ini.val();
			var lead_contato_agendamento_fim_val = lead_contato_agendamento_fim.val();
			var lead_agendamento_recisao_ini_val = lead_agendamento_recisao_ini.val();
			var lead_agendamento_recisao_fim_val = lead_agendamento_recisao_fim.val();

			botao.click(function(){
				if((lead_contato_data_ini_val && !lead_contato_data_fim_val) || (!lead_contato_data_ini_val && lead_contato_data_fim_val)){
					alert("Deve preencher o periodo completo da Data de Contato!");
				}else if((lead_contato_agendamento_ini_val && !lead_contato_agendamento_fim_val) || (!lead_contato_agendamento_ini_val && lead_contato_agendamento_fim_val)){
					alert("Deve preencher o periodo completo da Data de Agendamento!");
				}else if((lead_agendamento_recisao_ini_val && !lead_agendamento_recisao_fim_val) || (!lead_agendamento_recisao_ini_val && lead_agendamento_recisao_fim_val)){
					alert("Deve preencher o periodo completo da aata de agendamento da Recisão de Contratos!");
				}else{
					$("#form-pesquisa").submit();
				}
			});

		}

		function infoChart(type = null){

			var selectRegional = $("#un-chart");
			if(type == "funilmetas" || !type){
				selectRegional.attr("onchange", "infoChartFunnel($(this).val());infoChartGoals();");
			}else if(type == "funil"){
				selectRegional.attr("onchange", "infoChartFunnel($(this).val());");
			}else if(type == "metas"){
				selectRegional.attr("onchange", "infoChartGoals();");
			}

		}

		function infoChartFunnel(type = null){
			if(!type){
				type = "geral";
			}

			// Themes begin
			am4core.useTheme(am4themes_animated);
			// Themes end

			var chart = am4core.create("chartdiv-funnel", am4charts.SlicedChart);
			chart.hiddenState.properties.opacity = 0.5; // this makes initial fade in effect

			$.post("../App_Pes/ControlLeads.php", {action: 'busca-funil-leads', type: type}, function(info){
				var obj = JSON.parse(info);
				chart.data = obj;
			});

			var series = chart.series.push(new am4charts.FunnelSeries());
			series.colors.step = 1;
			series.dataFields.value = "value";
			series.dataFields.category = "name";
			series.alignLabels = true;

			series.labelsContainer.paddingLeft = 10;
			series.labelsContainer.width = 160;

			series.labels.template.text = "{category}: [bold]{value}[/]";
		}

		function infoChartGoals(){
			var regional = $("#un-chart").val();
			var tempo    = $("#sel-month").val();
			var data1    = chart_goal_begin.val();
			var data2    = chart_goal_end.val();

			// Themes begin
			am4core.useTheme(am4themes_kelly);
			am4core.useTheme(am4themes_animated);
			// Themes end

			// Create chart instance
			var chart = am4core.create("chartdiv-line", am4charts.XYChart);

			// Cria barras com as metas mensais dentro do período selecionado
			var dateAxis1 = chart.xAxes.push(new am4charts.DateAxis());
			dateAxis1.renderer.grid.template.location = 0;
			dateAxis1.renderer.minGridDistance = 40;

			var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());

			var series1 = chart.series.push(new am4charts.ColumnSeries());
			series1.dataFields.valueY = "value";
			series1.dataFields.dateX = "date";
			series1.xAxis = dateAxis1;
			series1.yAxis = valueAxis1;
			series1.tooltipText = "Meta em {dateX.formatDate('yyyy/MM')}: [bold]{valueY}[/]";

			// Cria linha para mostrar o que foi realizado dentro do período selecionado
			var dateAxis2 = chart.xAxes.push(new am4charts.DateAxis());
			dateAxis2.renderer.grid.template.location = 0;
			dateAxis2.renderer.minGridDistance = 40;
			dateAxis2.renderer.labels.template.disabled = true;
			dateAxis2.renderer.grid.template.disabled = true;
			dateAxis2.renderer.tooltip.disabled = true;

			var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
			valueAxis2.renderer.opposite = true;
			valueAxis2.renderer.grid.template.disabled = true;
			valueAxis2.renderer.labels.template.disabled = true;
			valueAxis2.renderer.tooltip.disabled = true;

			var series2 = chart.series.push(new am4charts.LineSeries());
			series2.dataFields.valueY = "value";
			series2.dataFields.dateX = "date";
			series2.xAxis = dateAxis2;
			series2.yAxis = valueAxis2;
			series2.strokeWidth = 3;
			series2.tooltipText = "Contatos feitos: [bold]{valueY}[/]";

			$.post("../App_Pes/ControlLeads.php", 
					{action: 'busca-meta-leads', regional: regional, tempo: tempo, data1: data1, data2: data2}, 
						function(info){
							var obj = JSON.parse(info);
							series1.data = obj.meta;
							series2.data = obj.realizado;
				});

			// Add cursor
			chart.cursor = new am4charts.XYCursor();
		}

		function verMonth(month){
			if(month == "personalizado"){
				$("#chart-goals").show();
			}else{
				$("#chart-goals").hide();
				infoChartGoals();
			}
		}

		function pesquisaMetasPersonalizada(){
			var data1    = chart_goal_begin.val();
			var data2    = chart_goal_end.val();

			if(data1 && data2){
				infoChartGoals();
			}else{
				alert("As duas datas devem ser preenchidas!");
			}
		}

		function criarLead(){
			$("#cria-lead").click(function(){
				window.location="../App_Pes/Leads.php";
			});
		}
    </script>
</body>
</html>