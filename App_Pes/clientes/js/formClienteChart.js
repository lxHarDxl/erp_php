$(function(){
    infoChart($("#un-chart").val());
    infoClienteContrato($("#un-chart").val());
    changeEmpresa();
});


function changeEmpresa(){
    $("#un-chart").change(function(){
        infoChart($(this).val());
        infoClienteContrato($(this).val());
    });
}

/**
 * Recebe o código referente à filial e busca as informações pertinentes à ela
 * Envia os dados para a função 'montaChart' para que transforme os dados em informações'
 * 
 * @param {String} empresa 
 */
function infoChart(empresa){

    $.post("../App_Pes/ControlCliente.php",
        {type: 'info', action: 'chart', nr_empresa: empresa}, 
            function(data){
                console.log(data);
                montaChart(data);
            }, 'json');
}

/**
 * Recebe o código referente à filial e busca as informações pertinentes à ela
 * Envia os dados para a função 'montaClienteContrato' para que transforme os dados em informações
 * 
 * @param {String} empresa 
 */
function infoClienteContrato(empresa){

    $.post("../App_Pes/ControlCliente.php",
        {type: 'info', action: 'contratoCliente', nr_empresa: empresa}, 
            function(data){
                montaClienteContrato(data);
            });
}


function montaClienteContrato(data){
    data = JSON.parse(data);
    $("#clientes_ativos").text(data.cliente);
    $("#contratos_ativos").text(data.contrato);
}


/**
 * Recebe JSON com dados que serão alocados no chart
 * 
 * @param {JSON} data 
 */
function montaChart(data){
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
    series1.data  = data;
    series1.xAxis = dateAxis1;
    series1.yAxis = valueAxis1;
    series1.tooltipText = "Faturado em {dateX.formatDate('yyyy/MM')}: [bold]{valueY}[/]";

    // Add cursor
    chart.cursor = new am4charts.XYCursor();
}