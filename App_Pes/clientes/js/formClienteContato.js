$(function(){
    var SPMaskBehavior = function (val) {
        var newVal;
        var flag = false;
        if(val.replace(/\D/g, '').length >= 4 && val.replace(/\D/g, '').substr(0, 4) === '0800'){
            newVal = '0000 000 0000';
        }else if(val.replace(/\D/g, '').length === 11 && !flag){
            newVal = '(00) 00000-0000';
        }else{
            newVal = '(00) 0000-00009'; 
        }
        return newVal;
    },
    spOptions = {
        onKeyPress: function(val, e, field, options) {
            field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    };
    
    $('.phone').mask(SPMaskBehavior, spOptions);
});

function novoContato(nr_pessoa){
    $('#salvar-contato').attr('onclick', 'gravaContato(' + nr_pessoa + ');');
}

function alteraContato(nr_pessoa, nr_ctt){
    $('#salvar-contato').attr('onclick', 'gravaContato(' + nr_pessoa + ', ' + nr_ctt + ');');
    $.post('../App_Pes/ControlCliente.php',
            {action: 'selecionar',
            type: 'contato',
            nr_ctt: nr_ctt},
                function(data){

                    $("#nm_ctt").val(data.nm_ctt);
                    $("#ctt_genero").val(data.ctt_genero);
                    $("#nr_ramal").val(data.nr_ramal);
                    $("#nr_telefone").val(data.nr_telefone);
                    $("#nr_celular").val(data.nr_celular);
                    $("#ds_email").val(data.ds_email);
                    $("#ds_site").val(data.ds_site);
                    $("#ctt_skype").val(data.ctt_skype);
                    $("#ctt_funcao").val(data.ctt_funcao);
                    $("#ds_telefone").val(data.ds_telefone);

                }, 'json');
}

function gravaContato(nr_pessoa, nr_ctt = null){
    var nm_ctt      = $("#nm_ctt").val();
    var ctt_genero  = $("#ctt_genero").val();
    var nr_ramal    = $("#nr_ramal").val();
    var nr_telefone = $("#nr_telefone").val();
    var nr_celular  = $("#nr_celular").val();
    var ds_email    = $("#ds_email").val();
    var ds_site     = $("#ds_site").val();
    var ctt_skype   = $("#ctt_skype").val();
    var ctt_funcao  = $("#ctt_funcao").val();
    var ds_telefone = $("#ds_telefone").val();

    var msg;
    if(!nm_ctt){
        msg += "\n- Nome;";
        $("#nm_ctt").addClass('is-invalid');
    }
    if(!nr_telefone){
        msg += "\n- Telefone;";
        $("#nr_telefone").addClass('is-invalid');
    }
    if(!ds_telefone){
        msg += "\n- Tipo;";
        $("#ds_telefone").addClass('is-invalid');
    }

    var action;

    if(nr_ctt){
        action = "atualiza";
    } else if (nr_pessoa) {
        action = "insere";
    } else {
        msg += "nr_ctt e nr_pessoa não encontrado! Entrar em contato com o suporte.";
    }

    if(!msg){
        $(".campo-obrigatorio").removeClass("is-invalid");
        
        $.post( "../App_Pes/ControlCliente.php", 
                {type: "contato",
                action: action,
                nr_pessoa: nr_pessoa,
                nr_ctt: nr_ctt,
                nm_ctt: nm_ctt,
                ctt_genero: ctt_genero,
                nr_ramal: nr_ramal,
                nr_telefone: nr_telefone,
                nr_celular: nr_celular,
                ds_email: ds_email,
                ds_site: ds_site,
                ctt_skype: ctt_skype,
                ctt_funcao: ctt_funcao,
                ds_telefone: ds_telefone},
                    function(data){
                        alert(data.mensagem);
                        
                        if(data.erro == 0){
                            location.href = "../App_Pes/Cliente.php?nr_pessoa=" + data.nr_pessoa;
                        }

                    }, 'json');

    } else {
        alert("Os seguintes campos precisam ser preenchidos: \n" + msg);
    }
}