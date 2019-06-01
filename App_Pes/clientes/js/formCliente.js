$(function(){

    var CPFCNPJmask = function (val) {
        var newVal;
        var flag = false;
        if(val.replace(/\D/g, '').length <= 11){
            newVal = '000.000.000-009';
        }else{
            newVal = '00.000.000/0000-00'; 
        }
        return newVal;
    },
    cpfcnpjOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(CPFCNPJmask.apply({}, arguments), options);
        }
    };

     var IERGmask = function (val) {
        var newVal;
        var flag = false;
        if(val.replace(/\D/g, '').length <= 9){
            newVal = '00.000.000-09';
        }else{
            newVal = '000.000.000.000'; 
        }
        return newVal;
    },
    iergOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(IERGmask.apply({}, arguments), options);
        }
    };

    $('#nr_cnpjcpf').mask(CPFCNPJmask, cpfcnpjOptions);
    $('#nr_ierg').mask(IERGmask, iergOptions);

});


function gravaCliente(nr_pessoa){
    var nm_pessoa      = $("input[name=nm_pessoa]").val();
    var nr_cnpjcpf     = $("input[name=nr_cnpjcpf]").val();
    var nr_ierg        = $("input[name=nr_ierg]").val();
    var nr_empresa     = $("select[name=nr_empresa]").val();
    var nr_colaborador = $("select[name=nr_colaborador]").val();
    var nr_revendedor  = $("select[name=nr_revendedor]").val();
    var nr_hubpop      = $("select[name=nr_hubpop]").val();

    var msg;
    if(!nm_pessoa){
        msg += "\n- Cliente;";
        $("input[name=nm_pessoa]").addClass("is-invalid");
    }
    if(!nr_cnpjcpf){
        msg += "\n- CPF/CNPJ;";
        $("input[name=nr_cnpjcpf]").addClass("is-invalid");
    }
    if(!nr_empresa){
        msg += "\n- Regional;";
        $("select[name=nr_empresa]").addClass("is-invalid");
    }
    if(!nr_colaborador){
        msg += "\n- Colaborador;";
        $("select[name=nr_colaborador]").addClass("is-invalid");
    }
    if(!nr_hubpop){
        msg += "\n- HubPop;";
        $("select[name=nr_hubpop]").addClass("is-invalid");
    }

    if(!msg){
        var action;

        if(nr_pessoa){
            action = "atualiza";
        } else {
            action = "insere";
        }

        $(".campo-obrigatorio").removeClass("is-invalid");
        
        $.post( "../App_Pes/ControlCliente.php", 
                {type: "cliente",
                action: action,
                nr_pessoa: nr_pessoa,
                nm_pessoa: nm_pessoa,
                nr_cnpjcpf: nr_cnpjcpf,
                nr_ierg: nr_ierg,
                nr_empresa: nr_empresa,
                nr_colaborador: nr_colaborador,
                nr_revendedor: nr_revendedor,
                nr_hubpop: nr_hubpop},
                    function(data){
                        var c = confirm(data.mensagem);
                        
                        if(data.erro == 0){
                            location.href = "../App_Pes/Cliente.php?nr_pessoa=" + data.nr_pessoa;
                        } else if (data.erro == 1 && c){
                            window.open("../App_Pes/Cliente.php?nr_pessoa=" + data.nr_pessoa, '_blank');
                        }
                    }, 'json');
    } else {
        alert("Os seguintes campos precisam ser preenchidos: \n" + msg);
    }

}