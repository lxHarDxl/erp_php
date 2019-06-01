$(function(){
    $('.cep').mask('00000-000');    
});

function novoEndereco(nr_pessoa){
    $('#salvar-endereco').attr('onclick', 'gravaEndereco(' + nr_pessoa + ');');
}

function alteraEndereco(nr_pessoa, nr_ender){
    $('#salvar-endereco').attr('onclick', 'gravaEndereco(' + nr_pessoa + ', ' + nr_ender + ');');
    console.log("alteraEndereco");
    console.log(nr_pessoa);
    console.log(nr_ender);
    $.post('../App_Pes/ControlCliente.php',
            {action: 'selecionar',
            type: 'endereco',
            nr_ender: nr_ender},
                function(data){
                    console.log(data);

                    $("#nr_cep").val(data.nr_cep);
                    $("#tp_logradouro").val(data.tp_logradouro);
                    $("#nm_logradouro").val(data.nm_logradouro);
                    $("#nr_logradouro").val(data.nr_logradouro);
                    $("#ds_complemento").val(data.ds_complemento);
                    $("#nm_bairro").val(data.nm_bairro);
                    $("#nm_cidade").val(data.nm_cidade);
                    $("#nm_estado").val(data.nm_estado);
                    $("#ds_logradouro").val(data.ds_logradouro);

                }, 'json');
}

function gravaEndereco(nr_pessoa, nr_ender = null){
    var nr_cep         = $("#nr_cep").val();
    var tp_logradouro  = $("#tp_logradouro").val();
    var nm_logradouro  = $("#nm_logradouro").val();
    var nr_logradouro  = $("#nr_logradouro").val();
    var ds_complemento = $("#ds_complemento").val();
    var nm_bairro      = $("#nm_bairro").val();
    var nm_cidade      = $("#nm_cidade").val();
    var nm_estado      = $("#nm_estado").val();
    var ds_logradouro  = $("#ds_logradouro").val();

    var msg;
    if(!tp_logradouro){
        msg += "\n- Logradouro;";
        $("#tp_logradouro").addClass("is-invalid");
    }
    if(!nm_logradouro){
        msg += "\n- Logradouro;";
        $("#nm_logradouro").addClass("is-invalid");
    }
    if(!nr_logradouro){
        msg += "\n- Numero;";
        $("#nr_logradouro").addClass("is-invalid");
    }
    if(!nm_bairro){
        msg += "\n- Bairro;";
        $("#nm_bairro").addClass("is-invalid");
    }
    if(!nm_cidade){
        msg += "\n- Cidade;";
        $("#nm_cidade").addClass("is-invalid");
    }
    if(!nm_estado){
        msg += "\n- Estado;";
        $("#nm_estado").addClass("is-invalid");
    }
    if(!ds_logradouro){
        msg += "\n- Tipo;";
        $("#ds_logradouro").addClass("is-invalid");
    }

    var action;

    if(nr_ender){
        action = "atualiza";
    } else if (nr_pessoa) {
        action = "insere";
    } else {
        msg += "nr_ender e nr_pessoa não encontrado! Entrar em contato com o suporte.";
    }
    
    if(!msg){
        $(".campo-obrigatorio").removeClass("is-invalid");
        
        $.post( "../App_Pes/ControlCliente.php", 
                {type: "endereco",
                action: action,
                nr_pessoa: nr_pessoa,
                nr_ender: nr_ender,
                nr_cep: nr_cep,
                tp_logradouro: tp_logradouro,
                nm_logradouro: nm_logradouro,
                nr_logradouro: nr_logradouro,
                ds_complemento: ds_complemento,
                nm_bairro: nm_bairro,
                nm_cidade: nm_cidade,
                nm_estado: nm_estado,
                ds_logradouro: ds_logradouro},
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


function pesquisaCEP(){
    //Nova variável "cep" somente com dígitos.
    var cep = $("#nr_cep").val().replace(/\D/g, '');

    //Verifica se campo cep possui valor informado.
    if (cep != "") {

        //Expressão regular para validar o CEP.
        var validacep = new RegExp(/^[0-9]{8}$/);

        //Valida o formato do CEP.
        if(validacep.test(cep)) {

            //Consulta o webservice viacep.com.br/
            $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                if (!("erro" in dados)) {
                    //Atualiza os campos com os valores da consulta.
                    var n = dados.logradouro.indexOf(" ") + 1;
                    var logradouro = dados.logradouro.substr(n);
                    var tipoLogradouro = dados.logradouro.substr(0, n)

                    buscaTipoLogradouro(tipoLogradouro);
                    
                    $("#nm_logradouro").val(logradouro);
                    $("#nm_bairro").val(dados.bairro);
                    $("#nm_cidade").val(dados.localidade);
                    $("#nm_estado").val(dados.uf);
                } //end if.
                else {
                    //CEP pesquisado não foi encontrado.
                    alert("CEP não encontrado.");
                }
            });
        } //end if.
        else {
            //cep é inválido.
            alert("Formato de CEP inválido.");
        }
    } //end if.
    else {
        alert("Campo do CEP vazio!");
    }
}

function buscaTipoLogradouro(logradouro){
    $.post("../App_Pes/ControlCliente.php", {action: "busca", type: "tipo-logradouro", logradouro: logradouro}, function(data){
        $("#tp_logradouro").val(data);
    });
}