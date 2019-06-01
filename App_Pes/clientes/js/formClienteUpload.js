$(function(){
    alteraNomeInput();
    formularioSubmit();

    var form;
});

function alteraNomeInput(){
    var arquivo = $("#nm_arquivo");
    
    arquivo.change(function(event){
        var nomeDoArquivo = arquivo.val();
        var nm = nomeDoArquivo.split("\\");
        var ult = nm.length - 1;
        $("#label-importa").text(nm[ult]);
    });
}

function importaArquivos(nr_ctrt){
    $("#nr_ctrt-upload").val(nr_ctrt);
    resetForm();
}

function formularioSubmit(){
    $("#importar-arquivo").click(function() {
        $.ajax({
            url: "../App_Pes/ControlCliente.php",
            type: 'POST',
            data: new FormData($("#formulario-upload")[0]),
            success: function(data) {
                alert(data);
            },
            cache: false,
            contentType: false,
            processData: false,

            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    // Responsável pela atualização da barra de progresso
                    myXhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            // Calcula a porcentagem do progresso
                            let progresso = (e.loaded / e.total)*100;
                            progresso.toFixed(0);

                            // Atualiza barra de progresso
                            $('.progress-bar').attr({
                                style: "width: " + progresso + "%",
                                'aria-valuenow': progresso,
                            });
                        }
                    }, false);
                }
                return myXhr;
            }
        });
    });
}

function resetForm(){
    $("#ds_arquivo").val("");
    $("#nm_arquivo").val("");
    $("#label-importa").text("");
    $(".progress-bar").attr({style: "width: 0%", 'aria-valuenow': 0});
}