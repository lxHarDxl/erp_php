class ConfiguracaoOrcamentoModel {
    constructor() {
        
    }

    adiciona(nr_centro_custo, nr_diretor, perc_orcamento){

        this._id = nr_centro_custo;
        this._diretor = nr_diretor;
        this._orcamento = perc_orcamento;
    }

    grava(){
        $.post("../App_Pes/ControlOrcamento.php"
        ,{
                type: 'centro_custo',
                action: 'gravar',
                nr_centro_custo: this._id,
                nr_diretor: this._diretor,
                valor_orcamento: this._orcamento
            }
            ,function(data){
                alert(data);
            });
    }
}