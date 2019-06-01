$(function(){
    var btngravaSite = $("#btn-grava-site");
    $("input[name='valor_servico_lista_material']").mask('#.##0,00', {reverse: true});
    
    actionNavListaMaterial();
    gravaSite();
    gravaItem();
    liberaInserir();
    selecionaSite();
    selecionaItem();
    selecionaMateriais();
    selecionaFornecedor();
    selecionaLista();
})


// SELECIONA
// SELECIONA - TAG: OPTION

// Iniciado ao abrir o modal
// Busca no Banco de Dados os sites relacionados ao contrato selecionado
function selecionaSite(){
    // Inicia event trigger que escuta os clicks na barra de navegação
    $("#nav-lista-material").click(function(){
        // Reseta a lista para não haver duplicidades
        resetListaSite();
        // Garante que campo só vai ser acessível quando um serviço for selecionado
        $("input[name='valor_servico_lista_material']").attr("disabled", true);
        // Garante que o botão só vai ser acessível quando um site for selecionado
        $("#btn-insere-item-lista-material").attr("disabled", true);
        // Atribui numero do contrato relacionado
        var nr_ctrt = $("#nr_ctrt_ctrt").val();

        // Inicia Ajax para buscar os sites referente à este contrato
        $.post("../App_Pes/ControlCliente.php",
            {type: 'lista-material',
            action: 'seleciona-site',
            nr_ctrt: nr_ctrt},
                function(data){
                    // Cria loop para acessar todas as informações do JSON
                    for (var index in data) {
                        // Cria nova tag "option" para cada item recebido e acrescenta ao corpo do html
                        acrescentaSite(index, data[index]);
                    }
                }, 'json');
    });
}

function selecionaFornecedor(){
    $("#nr_servico").change(function(){
        resetListaFornecedor();
        var value = $(this).val();

        $.post("../App_Pes/ControlCliente.php",
            {type: 'lista-material',
            action: 'seleciona-fornecedor',
            servico: value},
                function(data){
                    // Cria loop para acessar todas as informações do JSON
                    for (var index in data) {
                        // Cria nova tag "option" para cada item recebido e acrescenta ao corpo do html
                        acrescentaFornecedor(index, data[index]);
                    }
                }, 'json');
    });
}

// SELECIONA - TABELA
function selecionaMateriais(){
    $("#material").on('input', function(){
        var nome = $(this).val();

        if(typeof nome != 'undefined' && nome != ""){
            $.post("../App_Pes/ControlCliente.php",
                {type: 'lista-material',
                action: 'seleciona-material',
                nome: nome},
                    function(data){
                        resetTableItens();
                        $("#itens-lista-material tbody").html(data);
                    });
        } else {
            resetTableItens();
        }
    });
}

function selecionaItem(){
    $("#adicionar-item-lista-material").click(function(){
        var value = $(this).attr("value");
        var flag = validaItem(value);

        if(flag){
            infoItem(value);
            $('#modalPesquisarItensListaMaterialContrato').modal('hide');
        }
    });
}

function selecionaLista(){
    $("#select-lista-material-servico").change(function(){
        var value = $(this).val();

        if(value){
            $.post("../App_Pes/ControlCliente.php",
                {type: "lista-material",
                action: "seleciona-lista",
                nr_custo: value},
                    function(data){
                        $("#modalTabelaListaMaterialContrato").modal('show');
                        $("#show-lista-material-servico").html(data.html);
                        $("#show-nome-lista-material-servico").html(data.html_title);

                        $("#select-lista-material-servico").val("");
                    }, 'json');
        }
    });
}

// INFORMAÇÔES
// Identifica quais informações serão levantadas de acordo com a operação
function infoItem(operacao){
    $("#btn-insere-item-lista-material").attr("value", operacao);

    switch(operacao){
        case "servico":
            infoServico();
            break;
        case "material":
            infoMaterial();
            break;
    }
}

function infoServico(){
    $("input[name='valor_servico_lista_material']").attr("disabled", false);
    var nr_servico    = $("#nr_servico").val();
    var nr_fornecedor = $("#fornecedor-servico").val();

    $.post("../App_Pes/ControlCliente.php",
        {type: 'lista-material',
        action: 'info-servico',
        nr_servico: nr_servico},
            function(data){
                $("#nm_material_servico").text(data.descricao);
                $("input[name='valor_servico_lista_material']").val(data.valor);
            }, 'json');

    $("#nr_material_servico").val(nr_servico);
    $("#nr_fornecedor2").val(nr_fornecedor);
}

function infoMaterial(){
    $("input[name='valor_servico_lista_material']").attr("disabled", true);
    var nr_material = $("input[name='nr_material']:checked").val();
    var nm_material = $("#nr_material_" + nr_material).text();

    $("#nr_material_servico").val(nr_material);
    $("#nm_material_servico").text(nm_material);

    $("#nr_fornecedor2").val("");
    $("input[name='valor_servico_lista_material']").val("");
}


// GRAVAR
// Grava o novo site inserido
function gravaSite(){
    $("#btn-grava-site").click(function(){
        var estacao = $("input[name='estacao']").val();
        var nr_ctrt = $("#nr_ctrt_ctrt").val();

        $.post("../App_Pes/ControlCliente.php",
            {type: 'lista-material',
            action: 'grava-site',
            nr_ctrt: nr_ctrt,
            estacao: estacao},
                function(data){
                    if(data.erro == 0){
                        acrescentaSite(data.nr_custo, data.estacao);
                        $("input[name='estacao']").val("");
                    } 

                    alert(data.mensagem);
                }, 'json');
    });
}

// Grava novo item no site selecionado
function gravaItem(){
    $("#btn-insere-item-lista-material").click(function(){
        var operacao = $("#btn-insere-item-lista-material").attr("value");
        var flag = validaGravaItem(operacao);

        if(flag){
            var qtdade              = $("#qtdade").val();
            var nr_fornecedor2      = $("#nr_fornecedor2").val();
            var nr_custo            = $("#select_nr_custo").val();
            var nr_material_servico = $("#nr_material_servico").val();
            var valor_servico       = $("#valor_servico_lista_material").val();

            $.post("../App_Pes/ControlCliente.php",
                {type: "lista-material",
                action: "grava-" + operacao,
                nr_custo: nr_custo,
                nr_material_servico: nr_material_servico,
                nr_fornecedor2: nr_fornecedor2,
                nr_qtdade: qtdade,
                nr_valor_servico: valor_servico},
                    function(data){
                        alert(data);
                    })
        }
    });
}

function gravaStatusLista(status, nr_custo){
    status = status.val();
    if(status != ""){
        $.post("../App_Pes/ControlCliente.php",
            {type: "lista-material",
            action: "altera-status-lista",
            status: status,
            nr_custo: nr_custo},
                function(data){
                    alert(data);
                });
    }
}


// EXCLUIR
// Exclui Lista
function excluirLista(nr_custo){
    var confirm = window.confirm("Deseja excluir essa lista?");

    if(nr_custo && confirm){

        $.post("../App_Pes/ControlCliente.php",
            {type: "lista-material",
            action: "excluir-lista",
            nr_custo: nr_custo},
                function(data){
                    alert(data);
                    $("#modalTabelaListaMaterialContrato").modal('hide');
                });

    } else {
        if(!nr_custo) alert("Nao e possivel realizar operacao! \n \nNao foi possivel encontrar codigo: nr_custo");
    }
}

// Exclui Material
function excluirMaterial(nr_ctrt_material, total, nr_custo){
    var confirm = window.confirm("Deseja excluir esse material?");

    if(nr_ctrt_material && confirm){

        $.post("../App_Pes/ControlCliente.php",
            {type: "lista-material",
            action: "excluir-material",
            nr_ctrt_material: nr_ctrt_material,
            total: total,
            nr_custo: nr_custo},
                function(data){
                    alert(data);
                    $("#excluir_material_" + nr_ctrt_material).remove();
                });

    } else {
        if(!nr_ctrt_material) alert("Nao e possivel realizar operacao! \n \nNao foi possivel encontrar codigo: nr_ctrt_material");
    }
}

// Exclui Serviço
function excluirServico(nr_ctrt_servico, total, nr_custo){
    var confirm = window.confirm("Deseja excluir esse servico?");

    if(nr_ctrt_servico && confirm){

        $.post("../App_Pes/ControlCliente.php",
            {type: "lista-material",
            action: "excluir-servico",
            nr_ctrt_servico: nr_ctrt_servico,
            total: total,
            nr_custo: nr_custo},
                function(data){
                    alert(data);
                    $("#excluir_servico_" + nr_ctrt_servico).remove();
                });

    } else {
        if(!nr_ctrt_servico) alert("Nao e possivel realizar operacao! \n \nNao foi possivel encontrar codigo: nr_ctrt_servico");
    }
}


// VALIDAR
// Função responsável por identificar a ação que deve ser realizada ao validar a operação
function validaItem(operacao){
    var ret;
    switch(operacao){
        case "servico":
            ret = validaServico();
            break;
        case "material":
            ret = validaMaterial();
            break;
    }

    return ret;
}

// Função responsável por identificar a ação que deve ser realizada ao validar a operação de adicionar um item na lista
function validaGravaItem(operacao){
    var ret;
    switch(operacao){
        case "servico":
            ret = validaGravaServico();
            break;
        case "material":
            ret = validaGravaMaterial();
            break;
    }

    return ret;
}

// Função responsável por validar se os campos dos itens de serviço foram preenchidos devidamente
function validaServico(){
    var nr_servico = $("#nr_servico").val();
    var fornecedor_servico = $("#fornecedor-servico").val();
    var msg;

    if(!nr_servico){
        msg += "\n- Servico;";
    }
    if(!fornecedor_servico){
        msg += "\n- Fornecedor;";
    }

    if(!msg){
        return true;
    } else {
        alert("Os campos deve ser preenchidos: \n" + msg);
        return false;
    }
}

// Função responsável por validar se os campos dos itens de serviço foram preenchidos devidamente
function validaGravaServico(){
    var valor = $("input[name='valor_servico_lista_material']").val();
    var quantidade = $("#qtdade").val();
    var msg;

    if(!valor){
        msg += "\n- Valor;";
    }
    if(!quantidade){
        msg += "\n- Quantidade;";
    }

    if(!msg){
        return true;
    } else {
        alert("Os campos deve ser preenchidos: \n" + msg);
        return false;
    }
}

// Função responsável por validar se os campos dos itens de serviço foram preenchidos devidamente
function validaMaterial(){
    var nr_material = $("input[name='nr_material']:checked").val();
    var msg;

    if(!nr_material){
        msg += "Um material deve ser selecionado!";
    }

    if(!msg){
        return true;
    } else {
        alert(msg);
        return false;
    }
}

// Função responsável por validar se os campos dos itens de serviço foram preenchidos devidamente
function validaGravaMaterial(){
    var quantidade = $("#qtdade").val();
    var msg;

    if(!quantidade){
        msg += "\n- Quantidade;";
    }

    if(!msg){
        return true;
    } else {
        alert("Os campos deve ser preenchidos: \n" + msg);
        return false;
    }
}

// LIBERAR

// Escuta o campo de select dos sites
// Quando selecionado algum site, libera botão para inserir
function liberaInserir(){
    $("#select_nr_custo").change(function(){
        if($(this).val() != ""){
            $("#btn-insere-item-lista-material").attr("disabled", false);
        } else {
            $("#btn-insere-item-lista-material").attr("disabled", true);
        }
    });
}



// RESET

// Reseta a tabela que contém os materiais e serviços para inserção
function resetTableItens(){
    $("#itens-lista-material tbody").html("");
}

// Reseta os campos referente à busca de materiais e serviços
function resetFieldItens(){
    $("#material").val("");
    $("#nr_servico").val("");
    $("#fornecedor-servico").val("");
}

// Limpa a tag "select" dos sites
function resetListaSite(){
    document.getElementById("select_nr_custo").innerHTML = "";
    document.getElementById("select-lista-material-servico").innerHTML = "";
    acrescentaSite("", "SELECIONE");
}

// Limpa a tag "select" dos fornecedores
function resetListaFornecedor(){
    document.getElementById("fornecedor-servico").innerHTML = "";
    acrescentaFornecedor("", "SELECIONE");
}


// ACRESCENTA


// Para não ter que recarregar a pagina, quando um novo site é inserido,
// é criado automaticamente uma nova tag "option" com as informações do site
function acrescentaSite(nr_custo, estacao){
    acrescentaOption(nr_custo, estacao, "select_nr_custo");

    estacao = estacao == "SELECIONE" ? "" : estacao;
    acrescentaOption(nr_custo, estacao, "select-lista-material-servico");
}

// Para não ter que recarregar a pagina, quando um novo serviço é selecionado,
// é criado automaticamente uma nova tag "option" com as informações dos forncedores referente à ele
function acrescentaFornecedor(nr_fornecedor, nm_fantasia){
    acrescentaOption(nr_fornecedor, nm_fantasia, "fornecedor-servico");
}

// Para não ter que recarregar a pagina, quando um novo site é inserido,
// é criado automaticamente uma nova tag "option" com as informações passada
function acrescentaOption(value, text, field_id){

    var option = document.createElement('option');
    var id = !value ? "" : field_id + "_" + value;
    
    option.textContent = text;
    option.setAttribute("id", id);
    option.value = value;
    
    document.getElementById(field_id).appendChild(option);
}



// NAVBAR

// Escuta o nav-bar para apresentar informações adequandamente de acordo com o item selecionado
function actionNavListaMaterial(){
    exibeModalItens('lista-material-material');

    $("#nav-pesquisa-lista-material .nav-link").click(function(){
        var id = $(this).attr("href");
        id = id.replace("#", "");
        exibeModalItens(id);
        
        $("#nav-pesquisa-lista-material .nav-link").removeClass("active");
        $(this).addClass("active");
        
        resetTableItens();
        resetFieldItens();
    });
}
function exibeModalItens(id){
    if(id == "lista-material-servico"){
        $("#adicionar-item-lista-material").attr("value", "servico");
        $("#lista-material-servico").show();
        $("#lista-material-material").hide();
        $("#itens-lista-material").hide();
    } else {
        $("#adicionar-item-lista-material").attr("value", "material");
        $("#lista-material-material").show();
        $("#lista-material-servico").hide();
        $("#itens-lista-material").show();
    }
}