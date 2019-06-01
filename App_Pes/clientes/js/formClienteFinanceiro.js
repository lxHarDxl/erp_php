function buscaFinanceiro(nr_ctrt){
    $.post("../App_Pes/ControlCliente.php",
        {
            type: "financeiro",
            action: "selecionar",
            nr_ctrt: nr_ctrt
        },
            function(data){
                console.log(data);
                $("#tbodyModalParcelasFinanceiro").html(data);
            });
}