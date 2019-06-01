$(function(){
    novaMensagem();
    encaminharOS();
    fechaOS();
    reabilitaOS();
    postUtilitarios();
});

function listaOrdemServico(nr_ctrt, ativo = true, ctrt = true){
    var text = ctrt ? "O.S. - Contrato: " + nr_ctrt : "O.S. - Lead: " + nr_ctrt;

    $("#modalListaOrdemServicoTitle").text(text);
    $("#btn-nova-os").attr("onclick", "criaOS(" + nr_ctrt + ");")

    $.post("../App_Pes/ControlCliente.php",
        {type: 'ordem-servico',
        action: 'selecionar-lista',
        nr_ctrt: nr_ctrt,
        ctrt: ctrt},
            function(data){
                console.log(data);
                $("#modalListaOrdemServico tbody").html(data);
            });

    if(!ativo) {
        $("#btn-nova-os").hide();
    } else {
        $("#btn-nova-os").show();
    }
}

function criaOS(nr_ctrt){
    $("#nr_ctrt_os").val(nr_ctrt);

    $("#btnOSSalva").attr("onclick", "postOS('criar');");
    $("#os_titulo").attr("disabled", false);
    $("#os_dt_abertura").val(dataAtualFormatada());
    $("#os_encaminhar").attr("name", "os_encaminhar");
    $("#os_depto_atual").attr("name", "os_depto_atual");
    $(".field-editar-os").hide();
    $(".criar-os-2").show();
}

function editaOS(nr_os, status){
    $(".field-editar-os").show();
    $(".criar-os-2").hide();
    $("#os_titulo").attr("disabled", true);

    $("#os_status").val(status);
    
    $.post( "../App_Pes/ControlLeads.php", 
    {action: 'selecionar', type: 'ordem-servico', nr_os: nr_os},
        function(data_os){
            if(!data_os.erro){

                alteraBotoesOS(status, data_os.func);

                $("#os_dt_abertura").val(data_os.dt_abertura);
                $("#os_aberto_por").val(data_os.func);
                $("#os_encaminhar").val(data_os.depto);
                $("#os_depto_atual").val(data_os.depto_destino);
                $("#os_titulo").val(data_os.titulo);
                $("#os_nr_depto").val(data_os.nr_depto);
                $("#nr_os").val(data_os.nr_os);

                $("#table-historico-os").html(data_os.historico);
            }else{
                alert(data_os.erro);
            }
        }, 'json');
}

function postOS(action){

    if(action == 'criar'){
        var os_encaminhar  = $("select[name='os_encaminhar_cria']").val();
    }else{
        var os_encaminhar  = $("select[name='os_encaminhar']").val();
    }

    let lead_ID        = $("input[name='lead_ID']").val();
    let os_nome        = $("input[name='os_nome']").val();
    let os_depto_atual = $("select[name='os_depto_atual']").val();
    let os_titulo      = $("input[name='os_titulo']").val();
    let os_mensagem    = $("textarea[name='os_mensagem']").val();
    let os_aberto_por  = $("#os_aberto_por").val();
    let nr_os          = $("#editar-nr-os").attr("value");
    let nr_ctrt        = $("#nr_ctrt_os").val();

    $.post( "../App_Pes/ControlLeads.php",
        {action: action, type: 'ordem-servico', nr_ctrt: nr_ctrt, nr_os: nr_os, lead_ID: lead_ID, os_lead_aberto_por: os_aberto_por, lead_nome: os_nome, nr_depto_atual: os_depto_atual, nr_depto: os_encaminhar, titulo: os_titulo, mensagem: os_mensagem},
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

function fechaOS(){
    $("#btn-fechar-os").click(function(){
        var action = $("#btn-fechar-os").attr("value");
        $("#salva-mensagem").attr("value", action);

        if(action == "fechar-os"){
            $(".field-encaminhar").hide();
            $('#modalMensagem').modal('show');
        }else{
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

function postUtilitarios(){
    $("#salva-mensagem").click(function(){
        var action = $("#salva-mensagem").attr("value");

        var os_encaminhar = $("#os_encaminhar_mensagem").val();
        var os_mensagem   = $("#os_nova_mensagem").val();
        var nr_os         = $("#nr_os").val();
        var nr_depto      = $("#os_nr_depto").val();
        var status        = $("#os_status").val();
        
        $.post("../App_Pes/ControlLeads.php",
            {action: action, type: 'ordem-servico', os_encaminhar: os_encaminhar, mensagem: os_mensagem, nr_os: nr_os, nr_depto: nr_depto, status: status},
                function(data){
                    alert(data);
                    location.reload();
                });
    });
}

function mudaDonoOS(){
    $("#btn-mudar-dono").click(function(){
        postOS("mudar-dono");
    });
}

function dataAtualFormatada(){
    var data = new Date(),
        dia  = data.getDate().toString(),
        diaF = (dia.length == 1) ? '0' + dia : dia,
        mes  = (data.getMonth()+1).toString(), //+1 pois no getMonth Janeiro começa com zero.
        mesF = (mes.length == 1) ? '0' + mes : mes,
        anoF = data.getFullYear();
    return diaF+"/"+mesF+"/"+anoF;
}

function alteraBotoesOS(status, dono){
    dono = verficaDonoOS(dono);
    $("#btn-mudar-dono").attr("disabled", !dono);

    if(status == 'F'){
        $("#os-ativa").hide();
        if(dono){
            $("#os-fechada").show();
        } else {
            $("#os-fechada").hide();
        }
    } else {
        $("#os-ativa").show();
        $("#os-fechada").hide();
        if(dono){
            $("#btn-fechar-os").attr("value", "fechar-os");
            $("#btn-fechar-os").text("Fechar OS");
        } else {
            $("#btn-fechar-os").attr("value", "pedido-fechamento");
            $("#btn-fechar-os").text("Solicitar Fechamento");
        }
    }
}

function verficaDonoOS(dono){
    let session_login = $("#login_nr_func").val();

    return session_login == dono;
}