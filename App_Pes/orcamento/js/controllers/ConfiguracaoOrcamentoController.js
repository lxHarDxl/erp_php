class ConfiguracaoOrcamentoController {

    constructor(){
        this._configuracaoOrcamentoModel = new ConfiguracaoOrcamentoModel();
    }

    adiciona(id){
        this._tr = document.querySelector("#nr_centro_custo_" + id);
        this._nrDiretor = this._tr.children[1].children[0].value;
        this._valorOrcamento = this._tr.children[2].children[0].value;
        
        this._configuracaoOrcamentoModel.adiciona(id, this._nrDiretor, this._valorOrcamento);
        this._configuracaoOrcamentoModel.grava();
    }

}