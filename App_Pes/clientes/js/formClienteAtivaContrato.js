$(function(){
    $(".moeda").mask('000.000.000.000.000,00', {reverse: true});
});

function preAtivarContrato(nr_pessoa, nr_ctrt, nr_unidade_negocio){
    $("#ativa-ctrt-salvar-contrato").attr("onclick", "ativarContrato(" + nr_pessoa + ", " + nr_ctrt + ")");

    $.post('../App_Pes/ControlCliente.php',
            {action: 'selecionar',
            type: 'contrato',
            nr_ctrt: nr_ctrt},
                function(data){

                    $("#ativa-ctrt-nm_ctrt").val(data.nm_ctrt);
                    $("#ativa-ctrt-prazo_contrato").val(data.prazo_contrato);
                    $("#ativa-ctrt-nr_diavencimento").val(data.nr_diavencimento);
                    $("#ativa-ctrt-nr_sla").val(data.nr_sla);
                    $("#ativa-ctrt-nr_tipo_atendimento").val(data.nr_tipo_atendimento);
                    $("#ativa-ctrt-tempo_atendimento").val(data.tempo_atendimento);
                    $("#ativa-ctrt-nr_hubpop_ctrt").val(data.nr_hubpop);
                    $("#ativa-ctrt-valor_produto").val(data.valor_produto);
                    $("#ativa-ctrt-valor_servico").val(data.valor_servico);
                    $("#ativa-ctrt-valor_install").val(data.valor_install);
                    $("#ativa-ctrt-dt_vencimento_nfse").val(data.dt_vencimento_nfse);
                    $("#ativa-ctrt-nm_parcelas").val(data.nm_parcelas);
                    $("#ativa-ctrt-valor_mensal").val(data.valor_mensal);

                    if(nr_unidade_negocio == 3){
                        $('.subitem-11').show();
                        $('.subitem-22').hide();
                        $('.subitem-33').show();
                    } else {
                        $('.subitem-11').hide();
                        $('.subitem-22').show();
                        $('.subitem-33').hide();
                    }
                }, 'json');
}

function ativarContrato(nr_pessoa, nr_ctrt){
    $("#charge-btn").addClass("spinner-border spinner-border-sm");

    var nm_ctrt             = $("#ativa-ctrt-nm_ctrt").val();
    var prazo_contrato      = $("#ativa-ctrt-prazo_contrato").val();
    var notafiscal          = $("#ativa-ctrt-notafiscal").val();
    var nr_diavencimento    = $("#ativa-ctrt-nr_diavencimento").val();
    var nr_day_finish       = $("#ativa-ctrt-nr_day_finish").val();
    var nr_sla              = $("#ativa-ctrt-nr_sla").val();
    var nr_tipo_atendimento = $("#ativa-ctrt-nr_tipo_atendimento").val();
    var tempo_atendimento   = $("#ativa-ctrt-tempo_atendimento").val();
    var nr_hubpop           = $("#ativa-ctrt-nr_hubpop_ctrt").val();
    var valor_produto       = $("#ativa-ctrt-valor_produto").val();
    var valor_servico       = $("#ativa-ctrt-valor_servico").val();
    var valor_install       = $("#ativa-ctrt-valor_install").val();
    var dt_vencimento_nfse  = $("#ativa-ctrt-dt_vencimento_nfse").val();
    var nm_parcelas         = $("#ativa-ctrt-nm_parcelas").val();
    var valor_mensal        = $("#ativa-ctrt-valor_mensal").val();
    

    var msg;
    if(!nm_ctrt){
        msg += "\n- Nome do Contrato;";
        $("#ativa-ctrt-nm_ctrt").addClass('is-invalid');
    }
    if(!prazo_contrato){
        msg += "\n- Prazo do Contrato;";
        $("#ativa-ctrt-prazo_contrato").addClass('is-invalid');
    }
    if(!notafiscal){
        msg += "\n- Nota Fiscal;";
        $("#ativa-ctrt-notafiscal").addClass('is-invalid');
    }
    if(!nr_diavencimento){
        msg += "\n- Dia da Fatura;";
        $("#ativa-ctrt-nr_diavencimento").addClass('is-invalid');
    }
    if(!nr_day_finish){
        msg += "\n- Dias para a Ativacao;";
        $("#ativa-ctrt-nr_day_finish").addClass('is-invalid');
    }

    if(!msg){
        $.post("../App_Pes/ControlCliente.php",
                {action: "ativar",
                type: "contrato",
                nr_ctrt: nr_ctrt,
                nm_ctrt: nm_ctrt,
                nr_pessoa: nr_pessoa,
                prazo_contrato: prazo_contrato,
                notafiscal: notafiscal,
                nr_diavencimento: nr_diavencimento,
                nr_day_finish: nr_day_finish,
                nr_sla: nr_sla,
                nr_tipo_atendimento: nr_tipo_atendimento,
                tempo_atendimento: tempo_atendimento,
                nr_hubpop: nr_hubpop,
                valor_produto: valor_produto,
                valor_servico: valor_servico,
                valor_install: valor_install,
                dt_vencimento_nfse: dt_vencimento_nfse,
                nm_parcelas: nm_parcelas,
                valor_mensal: valor_mensal},
                    function(data){
                        $("#charge-btn").removeClass("spinner-border spinner-border-sm");
                        alert(data.mensagem);
                        
                        if(data.erro == 0){
                            location.href = "../App_Pes/Cliente.php?nr_pessoa=" + nr_pessoa;
                        }
                    }, 'json');
    } else {
        $("#charge-btn").removeClass("spinner-border spinner-border-sm");
        alert("Os seguintes campos precisam ser preenchidos: \n" + msg);
    }
}