$(function(){
    visualizarTodosArquivos();
    downloadZip();
});

function listaArquivos(nr_ctrt){
    $("#baixar-arquivo").attr("value", nr_ctrt);
    $.post("../App_Pes/ControlCliente.php", 
        {type: "arquivo",
        action: "download",
        nr_ctrt: nr_ctrt},
            function(data){
                $("#tbodyModalDownload").html(data);
            });
}

function visualizarTodosArquivos(){
    $("#visualizar-arquivo").click(function(){
        
        $(".link-visualizar-arquivo").each(function(index){
            let href = $(this).attr('href');

            window.open(href, '_blank');
        });
    });
}

function excluirAnexo(nr_anexo, ds_arquivo){
    if(confirm("Realmente deseja excluir o anexo: '" + ds_arquivo + "' ?")){
        $.post("../App_Pes/ControlCliente.php",
            {type: 'arquivo',
            action: 'excluir-anexo',
            nr_anexo: nr_anexo},
                function(data){
                    alert(data.mensagem);
                    if(data.erro == 0){
                        $("#anexo_" + nr_anexo).remove();
                    }
                }, 'json');
    }
}

function downloadZip(){
    $("#baixar-arquivo").click(function(){
        var val = $(this).attr("value");

        $("#download-all-files input[name='nr_ctrt']").val(val);
        $("#download-all-files").submit();
    });
}