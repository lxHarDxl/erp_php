$(function(){
    $("input[name='dt_prevista']").datepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        autoclose: true,
        format: 'dd/mm/yyyy',
        language: 'pt-BR'
    });

    $("input[name='dt_ativacao']").datepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        autoclose: true,
        format: 'dd/mm/yyyy',
        language: 'pt-BR'
    });

    $("input[name='dt_vencimento_nfse']").datepicker({
        uiLibrary: 'bootstrap4',
        modal: true,
        autoclose: true,
        format: 'dd/mm/yyyy',
        language: 'pt-BR'
    });

    $("textarea[name=mensagem]").on('input', function(){
        var numCaracteres = $(this).val().length;

        $("#contador-caracteres").text(numCaracteres);

        if(numCaracteres >= 15){
            $("#reabilita-ctrt-salvar-contrato").attr("disabled", false);
            $("#total-caracteres").addClass("text-success");
            $("#total-caracteres").removeClass("text-danger");
        }else{
            $("#reabilita-ctrt-salvar-contrato").attr("disabled", true);
            $("#total-caracteres").addClass("text-danger");
            $("#total-caracteres").removeClass("text-success");
        }
    });

    $("#reabilita-ctrt-salvar-contrato").click(function(){
        var mensagem = $("textarea[name=mensagem]").val();
        var nr_ctrt  = $("input[name=reabilitar_nr_ctrt]").val();

        $.post("../App_Pes/ControlCliente.php",
            {type: 'contrato',
            action: 'reabilitar',
            nr_ctrt: nr_ctrt,
            mensagem: mensagem},
                function(data){
                    alert(data);
                    location.reload();
                });
    });

    $("textarea[name=cancelar_mensagem]").on('input', function(){
        var numCaracteres = $(this).val().length;

        $("#cancelar_contador-caracteres").text(numCaracteres);

        if(numCaracteres >= 15){
            $("#cancela-ctrt-salvar-contrato").attr("disabled", false);
            $("#cancelar_total-caracteres").addClass("text-success");
            $("#cancelar_total-caracteres").removeClass("text-danger");
        }else{
            $("#cancela-ctrt-salvar-contrato").attr("disabled", true);
            $("#cancelar_total-caracteres").addClass("text-danger");
            $("#cancelar_total-caracteres").removeClass("text-success");
        }
    });

    $("#cancela-ctrt-salvar-contrato").click(function(){
        var nr_tipo  = $("select[name=cancelar_nr_tipo]").val();
        var mensagem = $("textarea[name=cancelar_mensagem]").val();
        var nr_ctrt  = $("input[name=cancelar_nr_ctrt]").val();

        $.post("../App_Pes/ControlCliente.php",
            {type: 'contrato',
            action: 'cancelar',
            nr_ctrt: nr_ctrt,
            nr_tipo: nr_tipo,
            mensagem: mensagem},
                function(data){
                    alert(data);
                    location.reload();
                });
    });
    
    $(".moeda").mask('000.000.000.000.000,00', {reverse: true});

    $(".subitem").attr('style','display: none;');
    $(".contrato-alteracao").hide();
    $(".contrato-info-adicional").show();
    $(".contrato-ativo").hide();
    $(".contrato-renovacao").hide();

    actionNavContrato();
    actionFooterContrato("form-contrato");
});

function preencheTabelaContratos(nr_pessoa, nr_status_contrato){

    if(nr_pessoa && nr_pessoa != 0){
        $.post("../App_Pes/ControlCliente.php",
                {action: "selecionar-lista",
                type: "contrato",
                nr_pessoa: nr_pessoa,
                nr_status_contrato: nr_status_contrato},
                    function(data){
                        $("#tbody-contratos").empty();
                        $("#tbody-contratos").html(data);
                    });
    }
}

function novoContrato(nr_pessoa){
    $('#salvar-contrato').attr('onclick', 'gravaContrato(' + nr_pessoa + ');');
    $(".contrato-alteracao").hide();
    $("#nav-link-contrato").trigger('click');
    $("#nav-contrato").hide();
    
    $("#nm_ctrt").val("");
    $("#dt_prevista").val("");
    $("#ds_tpctrt").val("");
    $("#ds_status_contrato").val("");
    $("#dt_ctrt").val("");
    $("#dt_vencto").val("");
    $("#renovacao").val("");
    $("#prazo_contrato").val("");
    $("#nr_potencial_fechamento").val("");
    $("#nr_diavencimento").val("");
    $("#nr_empresa_ctrt").val("");
    $("#nr_gerenteconta").val("");
    $("#nr_revendedor_ctrt").val("");
    $("#nr_un").val("");
    $("#nr_sla").val("");
    $("#nr_tipo_atendimento").val("");
    $("#tempo_atendimento").val("");
    $("select[name='nr_hubpop_ctrt']").val("");
    $("#valor_produto").val("");
    $("#valor_servico").val("");
    $("#valor_install").val("");
    $("#dt_vencimento_nfse").val("");
    $("#nm_parcelas").val("");
    $("#valor_mensal").val("");
    $("#qtd_ip").val("");
    $("#qos").val("");
    $("#qos_prioridade").val("");
    $("#projeto_especial").val("");
    $("#precisa_repetidora").val("");
    $("#form-contrato select").val("");

    $("#nm_ctrt").attr("disabled", false);
    $("#dt_prevista").attr("disabled", false);
    $("#ds_tpctrt").attr("disabled", false);
    $("#dt_ctrt").attr("disabled", false);
    $("#dt_vencto").attr("disabled", false);
    $("#renovacao").attr("disabled", false);
    $("#prazo_contrato").attr("disabled", false);
    $("#nr_potencial_fechamento").attr("disabled", false);
    $("#nr_diavencimento").attr("disabled", false);
    $("#nr_empresa_ctrt").attr("disabled", false);
    $("#nr_gerenteconta").attr("disabled", false);
    $("#nr_revendedor_ctrt").attr("disabled", false);
    $("#nr_un").attr("disabled", false);
    $("#nr_sla").attr("disabled", false);
    $("#nr_tipo_atendimento").attr("disabled", false);
    $("#tempo_atendimento").attr("disabled", false);
    $("select[name='nr_hubpop_ctrt']").attr("disabled", false);
    $("#valor_produto").attr("disabled", false);
    $("#valor_servico").attr("disabled", false);
    $("#valor_install").attr("disabled", false);
    $("#dt_vencimento_nfse").attr("disabled", false);
    $("#nm_parcelas").attr("disabled", false);
    $("#valor_mensal").attr("disabled", false);
    $("#qtd_ip").attr("disabled", false);
    $("#qos").attr("disabled", false);
    $("#qos_prioridade").attr("disabled", false);
    $("#projeto_especial").attr("disabled", false);
    $("#precisa_repetidora").attr("disabled", false);
    $("#form-contrato select").attr("disabled", false);

    $("input[name='nr_ender[]']").attr('checked', false);
    $("#nm_ctrt").attr('disabled', false);

    $(".contrato-info-adicional").show();
    $(".contrato-alteracao").hide();
    $(".contrato-ativo").hide();
    $(".contrato-prevenda").hide();
    $(".contrato-implantacao").hide();
}

function alteraContrato(nr_pessoa, nr_ctrt, nr_tpctrt, nr_status_contrato = null){
    $('#salvar-contrato').attr('onclick', 'gravaContrato(' + nr_pessoa + ', ' + nr_ctrt + ');'); 
    $("#nr_ctrt_ctrt").val(nr_ctrt);
    $("#nav-link-contrato").trigger('click');

    alteraCamposContrato(nr_tpctrt, nr_status_contrato);

    $.post('../App_Pes/ControlCliente.php',
            {action: 'selecionar',
            type: 'contrato',
            nr_ctrt: nr_ctrt},
                function(data){
                    changeTipoProduto(data.nr_un, data.nr_un_tipo);
                    changeItemProduto(data.nr_un_tipo, data.nr_un_item);
                    console.table(data);

                    $("#nm_ctrt").val(data.nm_ctrt);
                    $("#dt_prevista").val(data.dt_prevista);
                    $("#ds_tpctrt").val(data.ds_tpctrt);
                    $("#dt_ctrt").val(data.dt_ctrt);
                    $("#dt_vencto").val(data.dt_vencto);
                    $("#renovacao").val(data.renovacao);
                    $("#prazo_contrato").val(data.prazo_contrato);
                    $("#nr_potencial_fechamento").val(data.nr_potencial_fechamento);
                    $("#nr_diavencimento").val(data.nr_diavencimento);
                    $("#nr_empresa_ctrt").val(data.nr_empresa);
                    $("#ds_status_contrato").val(data.ds_status_contrato);
                    $("select[name='nr_status_contrato']").val(data.nr_status_contrato);
                    //$("#nr_status_contrato #" + data.nr_status_contrato).attr("selected", true);
                    $("#nr_gerenteconta").val(data.nr_gerenteconta);
                    $("#nr_revendedor_ctrt").val(data.nr_revendedor);
                    $("#nr_un").val(data.nr_un);
                    $("#nr_sla").val(data.nr_sla);
                    $("#nr_tipo_atendimento").val(data.nr_tipo_atendimento);
                    $("#tempo_atendimento").val(data.tempo_atendimento);
                    $("select[name='nr_hubpop_ctrt']").val(data.nr_hubpop);
                    $("#valor_produto").val(data.valor_produto);
                    $("#valor_servico").val(data.valor_servico);
                    $("#valor_install").val(data.valor_install);
                    $("#dt_vencimento_nfse").val(data.dt_vencimento_nfse);
                    $("#nm_parcelas").val(data.nm_parcelas);
                    $("#valor_mensal").val(data.valor_mensal);
                    $("#qtd_ip").val(data.qtd_ip);
                    $("#qos").val(data.qos);
                    $("#qos_prioridade").val(data.qos_prioridade);
                    $("#projeto_especial").val(data.projeto_especial);
                    $("#precisa_repetidora").val(data.precisa_repetidora);

                    if(data.nr_un == 3){
                        $('.subitem-1').attr('style','display: block;');
                        $('.subitem-2').attr('style','display: none;');
                        $('.subitem-3').attr('style','display: block;');
                    }
                    else{
                        $('.subitem-1').attr('style','display: none;');
                        $('.subitem-2').attr('style','display: block;');
                        $('.subitem-3').attr('style','display: none;');
                    }

                    var nr_ender = data.nr_ender.split(";");

                    $.each(nr_ender, function(index, value){
                        $("#nr_ender_" + value).attr("checked", true);
                    });
                }, 'json');
}

function gravaContrato(nr_pessoa, nr_ctrt = null){
    var nm_ctrt                 = alteraUndefined($("#nm_ctrt").val());
    var dt_prevista             = alteraUndefined($("#dt_prevista").val());
    var nr_gerenteconta         = alteraUndefined($("#nr_gerenteconta").val());
    var nr_revendedor           = alteraUndefined($("#nr_revendedor_ctrt").val());
    var nr_potencial_fechamento = alteraUndefined($("#nr_potencial_fechamento").val());
    var nr_empresa              = alteraUndefined($("#nr_empresa_ctrt").val());
    var nr_un                   = alteraUndefined($("#nr_un").val());
    var nr_un_tipo              = alteraUndefined($("#nr_un_tipo").val());
    var nr_un_item              = alteraUndefined($("#nr_un_item").val());
    var nr_sla                  = alteraUndefined($("#nr_sla").val());
    var nr_tipo_atendimento     = alteraUndefined($("#nr_tipo_atendimento").val());
    var tempo_atendimento       = alteraUndefined($("#tempo_atendimento").val());
    var nr_hubpop               = alteraUndefined($("select[name='nr_hubpop_ctrt']"));
    var valor_produto           = alteraUndefined($("#valor_produto").val());
    var valor_servico           = alteraUndefined($("#valor_servico").val());
    var valor_mensal            = alteraUndefined($("#valor_mensal").val());
    var valor_install           = alteraUndefined($("#valor_install").val());
    var nm_parcelas             = alteraUndefined($("#nm_parcelas").val());
    var dt_vencimento_nfse      = alteraUndefined($("#dt_vencimento_nfse").val());
    var qtd_ip                  = alteraUndefined($("#qtd_ip").val());
    var qos                     = alteraUndefined($("#qos").val());
    var qos_prioridade          = alteraUndefined($("#qos_prioridade").val());
    var projeto_especial        = alteraUndefined($("#projeto_especial").val());
    var precisa_repetidora      = alteraUndefined($("#precisa_repetidora").val());
    var motivo_renovacao        = alteraUndefined($("#motivo_renovacao").val());

    var subAction = $("#sub-action").val();

    nr_ender = new Array();
    $("input[name='nr_ender[]']:checked").each(function(){
        nr_ender.push($(this).val());
    });

    var msg;
    var action = $("#action-contrato");

    if(nr_ctrt){
        action.val("atualiza");
        if(!nr_potencial_fechamento){
            msg += "\n- Potencial de Fechamento;";
            $("#nr_potencial_fechamento").addClass('is-invalid');
        }
        if(!nr_empresa){
            msg += "\n- Empresa;";
            $("#nr_empresa_ctrt").addClass('is-invalid');
        }
    } else if (nr_pessoa) {
        action.val("insere");
    } else {
        msg += "nr_ctrt e nr_pessoa não encontrado! Entrar em contato com o suporte.";
    }

    if(!nm_ctrt){
        msg += "\n- Nome;";
        $("#nm_ctrt").addClass('is-invalid');
    }
    if(!nr_gerenteconta){
        msg += "\n- Gerente da Conta;";
        $("#nr_gerenteconta").addClass('is-invalid');
    }
    if(!nr_un){
        msg += "\n- Unidade de Negócio;";
        $("#nr_un").addClass('is-invalid');
    }
    if(!nr_un_tipo){
        msg += "\n- Tipo - Unidade de Negócio;";
        $("#nr_un_tipo").addClass('is-invalid');
    }
    if(!nr_un_item){
        msg += "\n- Item - Unidade de Negócio;";
        $("#nr_un_item").addClass('is-invalid');
    }
    if(nr_un == 3){
        if(!nr_sla){
            msg += "\n- SLA;";
            $("#nr_sla").addClass('is-invalid');
        }
        if(!nr_tipo_atendimento){
            msg += "\n- Tipo Atendimento;";
            $("#nr_tipo_atendimento").addClass('is-invalid');
        }
        if(!tempo_atendimento){
            msg += "\n- Tempo do Atendimento;";
            $("#tempo_atendimento").addClass('is-invalid');
        }
        if(!nr_hubpop){
            msg += "\n- HubPop;";
            $("select[name='nr_hubpop_ctrt']").addClass('is-invalid');
        }
        if(!valor_install){
            msg += "\n- Valor de Instalação;";
            $("#valor_install").addClass('is-invalid');
        }
        if(!dt_vencimento_nfse){
            msg += "\n- Previsão de pagamento da instalação;";
            $("#dt_vencimento_nfse").addClass('is-invalid');
        }
        if(!nm_parcelas){
            msg += "\n- Condição de pagamento da instalação;";
            $("#nm_parcelas").addClass('is-invalid');
        }
    } else {
        if(!valor_produto){
            msg += "\n- Valor do Produto;";
            $("#valor_produto").addClass('is-invalid');
        }
        if(!valor_servico){
            msg += "\n- Valor do Serviço;";
            $("#valor_servico").addClass('is-invalid');
        }
    }
    if(!valor_mensal){
        msg += "\n- Valor Mensal;";
        $("#valor_mensal").addClass('is-invalid');
    }
    if(!nr_ender){
        msg += "\n- Endereço;";
        $("#nr_ender").addClass('is-invalid');
    }
    
    if(subAction == "renovacao" && !motivo_renovacao){
        msg += "\n- Motivo da Renovacao;";
        $("#motivo_renovacao").addClass('is-invalid');
    }

    if(!msg){
        $("#type-contrato").val("contrato");
        $("#nr_ctrt_ctrt").val(nr_ctrt);
        $("#nr_pessoa_ctrt").val(nr_pessoa);
        $(".campo-obrigatorio").removeClass("is-invalid");

        $("#form-contrato").submit();

    } else {
        alert("Os seguintes campos precisam ser preenchidos: \n" + msg);
    }
}

function changeTipoProduto(unidade_negocio, nr_unidade_negocio_tipo = 0){
    if(unidade_negocio){
        $.post("../App_Pes/ControlCliente.php",
                {action: 'selecionar-tipo-produto',
                type: 'contrato',
                nr_unidade_negocio: unidade_negocio,
                nr_unidade_negocio_tipo: nr_unidade_negocio_tipo},
                    function(data){

                            resetField($("#nr_un_tipo"));
                            resetField($("#nr_un_item"));
                        
                        $("#nr_un_tipo").html(data);

                        if(unidade_negocio == 3){
                            $('.subitem-1').attr('style','display: block;');
                            $('.subitem-2').attr('style','display: none;');
                            $('.subitem-3').attr('style','display: block;');
                        }
                        else{
                            $('.subitem-1').attr('style','display: none;');
                            $('.subitem-2').attr('style','display: block;');
                            $('.subitem-3').attr('style','display: none;');
                        }
                    })
    } else {
        $(".subitem").attr('style','display: none;');
    }
}

function changeItemProduto(unidade_negocio_tipo, nr_unidade_negocio_item){
    if(unidade_negocio_tipo){

        if(unidade_negocio_tipo == 21){
            $("input[name='nr_ender[]']").attr('type', 'checkbox');
        } else {
            $("input[name='nr_ender[]']").attr('type', 'radio');
        }

        $.post("../App_Pes/ControlCliente.php",
                {action: 'selecionar-item-produto',
                type: 'contrato',
                nr_unidade_negocio_tipo: unidade_negocio_tipo,
                nr_unidade_negocio_item: nr_unidade_negocio_item},
                    function(data){

                            resetField($("#nr_un_item"));
                        ;

                        $("#nr_un_item").html(data);
                    })
    }
}

function visualizaContratoCancelado(nr_pessoa, nr_ctrt, nr_tpctrt){
    alteraCamposContrato(nr_tpctrt);

    
    $('#salvar-contrato').attr('onclick', '');
    $('#modalContrato input').attr('disabled', true);
    $('#modalContrato select').attr('disabled', true);
    $('#btn-renovacao').attr('disabled', true);


    $.post('../App_Pes/ControlCliente.php',
            {action: 'selecionar',
            type: 'contrato',
            nr_ctrt: nr_ctrt},
                function(data){
                    changeTipoProduto(data.nr_un, data.nr_un_tipo);
                    changeItemProduto(data.nr_un_tipo, data.nr_un_item);

                    $("#nm_ctrt").val(data.nm_ctrt);
                    $("#dt_prevista").val(data.dt_prevista);
                    $("#ds_tpctrt").val(data.ds_tpctrt);
                    $("#ds_status_contrato").val(data.ds_status_contrato);
                    $("#dt_ctrt").val(data.dt_ctrt);
                    $("#dt_vencto").val(data.dt_vencto);
                    $("#renovacao").val(data.renovacao);
                    $("#prazo_contrato").val(data.prazo_contrato);
                    $("#nr_potencial_fechamento").val(data.nr_potencial_fechamento);
                    $("#nr_diavencimento").val(data.nr_diavencimento);
                    $("#nr_empresa_ctrt").val(data.nr_empresa);
                    $("#nr_gerenteconta").val(data.nr_gerenteconta);
                    $("#nr_revendedor_ctrt").val(data.nr_revendedor);
                    $("#nr_un").val(data.nr_un);
                    $("#nr_sla").val(data.nr_sla);
                    $("#nr_tipo_atendimento").val(data.nr_tipo_atendimento);
                    $("#tempo_atendimento").val(data.tempo_atendimento);
                    $("select[name='nr_hubpop_ctrt']").val(data.nr_hubpop);
                    $("#valor_produto").val(data.valor_produto);
                    $("#valor_servico").val(data.valor_servico);
                    $("#valor_install").val(data.valor_install);
                    $("#dt_vencimento_nfse").val(data.dt_vencimento_nfse);
                    $("#nm_parcelas").val(data.nm_parcelas);
                    $("#valor_mensal").val(data.valor_mensal);
                    $("#qtd_ip").val(data.qtd_ip);
                    $("#qos").val(data.qos);
                    $("#qos_prioridade").val(data.qos_prioridade);
                    $("#projeto_especial").val(data.projeto_especial);
                    $("#precisa_repetidora").val(data.precisa_repetidora);

                    if(data.nr_un == 3){
                        $('.subitem-1').attr('style','display: block;');
                        $('.subitem-2').attr('style','display: none;');
                        $('.subitem-3').attr('style','display: block;');
                    }
                    else{
                        $('.subitem-1').attr('style','display: none;');
                        $('.subitem-2').attr('style','display: block;');
                        $('.subitem-3').attr('style','display: none;');
                    }

                    var nr_ender = data.nr_ender.split(";");

                    $.each(nr_ender, function(index, value){
                        $("#nr_ender_" + value).attr("checked", true);
                    });
                }, 'json');
}

function alteraCamposContrato(nr_tpctrt, nr_status_contrato = null){
    resetFields();

    $("#nm_ctrt").attr("disabled", true);
    $(".contrato-renovacao").hide();
    $("#renovacao").attr("disabled", true);
    $("#prazo_contrato").attr("disabled", true);
    $("#nr_diavencimento").attr("disabled", true);
    $("#dt_vencto").attr("disabled", true);

    if(nr_tpctrt == 6 || nr_status_contrato == 6){
        $(".contrato-alteracao").show();
        $(".contrato-ativo").hide();
        $(".contrato-info-adicional").show();
        $("#ds_status_contrato").attr("disabled", true);

        $("#sub-action").val("prevenda");

        if(nr_tpctrt == 6){
            $(".contrato-prevenda").show();
        } else {
            $("#nav-contrato").show();
            $(".contrato-implantacao").show();
        }
    } else {
        $("#nr_un").attr('disabled', true);
        $("#nr_un_tipo").attr('disabled', true);
        $("#nr_un_item").attr('disabled', true);
        $("#nr_sla").attr('disabled', true);
        $("#nr_tipo_atendimento").attr('disabled', true);
        $("#tempo_atendimento").attr('disabled', true);
        $("select[name='nr_hubpop_ctrt']").attr('disabled', true);
        $("#valor_produto").attr('disabled', true);
        $("#valor_servico").attr('disabled', true);
        $("#valor_mensal").attr('disabled', true);
        $("#valor_install").attr('disabled', true);
        $("#nm_parcelas").attr('disabled', true);
        $("#dt_prevista").attr('disabled', true);

        $(".contrato-info-adicional").hide();
        $(".contrato-ativo").show();
        $("#nav-contrato").show();
        $("#sub-action").val("ativo");
    }
}

function renovarContrato(){
    $("#nr_un").attr('disabled', false);
    $("#nr_un_tipo").attr('disabled', false);
    $("#nr_un_item").attr('disabled', false);
    $("#nr_sla").attr('disabled', false);
    $("#nr_tipo_atendimento").attr('disabled', false);
    $("#tempo_atendimento").attr('disabled', false);
    $("select[name='nr_hubpop_ctrt']").attr('disabled', false);
    $("#valor_produto").attr('disabled', false);
    $("#valor_servico").attr('disabled', false);
    $("#valor_mensal").attr('disabled', false);
    $("#valor_install").attr('disabled', false);
    $("#nm_parcelas").attr('disabled', false);
    $("#dt_prevista").attr('disabled', false);
    $("#nm_ctrt").attr("disabled", false);
    $("#renovacao").attr("disabled", false);
    $("#prazo_contrato").attr("disabled", false);
    $("#nr_diavencimento").attr("disabled", false);

    $(".contrato-renovacao").show();
    $(".ctrt-ativo").hide();
    $("#sub-action").val("renovacao");
}

function processoCancelaContrato(nr_ctrt){
    var confirm = window.confirm("Deseja realmente iniciar o processo de cancelamento deste contrato?");

    if(confirm){
        $.post('../App_Pes/ControlCliente.php', 
            {action:'processo_cancelamento', 
            type:'contrato', 
            nr_ctrt: nr_ctrt}, 
                function(data){
                    alert(data);
                    location.reload();
                });
    }
}

function reabilitaContrato(nr_ctrt){
    $("input[name=reabilitar_nr_ctrt]").val(nr_ctrt);
}

function cancelaContrato(nr_ctrt){
    $("input[name=cancelar_nr_ctrt]").val(nr_ctrt);
}

function resetField(field){
    field.empty();
    field.html("<option value=''>SELECIONE</option>");
}

function alteraUndefined(campo){
    if(typeof campo == "undefined"){
        campo = "";
    }
        
    return campo;
}

function resetFields(){
    $('#modalContrato input').attr('disabled', false);
    $('#modalContrato select').attr('disabled', false);
    $(".contrato-prevenda").hide();
    $(".contrato-implantacao").hide();
}

function actionNavContrato(){
    $("#form-lista-material").hide();
    $("#nav-contrato").hide();

    $("#nav-contrato .nav-link").click(function(){
        var id = $(this).attr("href");
        id = id.replace("#", "");
        $("#nav-contrato .nav-link").removeClass("active");
        $(this).addClass("active");
        
        
        actionFooterContrato(id);

        $("#" + id).show();
        if(id == "form-contrato"){
            $("#form-lista-material").hide();
        } else {
            $("#form-contrato").hide();
        }
    });
}

function actionFooterContrato(id){
    $(".footer-modal-contrato").hide();
    $("#footer-" + id).show();
}