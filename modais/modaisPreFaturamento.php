<!-- MODAL - Pré-Faturamento -->
<div class="modal in fade" id="modalPreFaturamento" role="dialog" aria-labelledby="TituloModalPreFaturamento" aria-hidden="true">
    <div class="modal-dialog modal-lg text-left" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h3 class="modal-title" id="TituloModalPreFaturamento">Faturamento</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="form-pre-faturamento" action="../App_Pes/ControlPreFaturamento.php" method="POST">
                <div class="modal-body form-row pb-4">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="nr_payment_fornecedor" class="col-sm-2 col-form-label">Cliente:</label>
                            <div class="col-sm-8">
                                <select class="custom-select custom-select-sm" name="nr_payment_fornecedor" id="nr_payment_fornecedor">
                                    <option value="">SELECIONE</option> 
                                    <?
                                        $clientes = executar("SELECT nr_payment_fornecedor, Empresa FROM payment_fornecedor WHERE status = 'A'");
                                        foreach($clientes as $v){ 
                                    ?>
                                            <option value="<?=$v['nr_payment_fornecedor']?>"><?=$v['Empresa']?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nr_empresa" class="col-sm-2 col-form-label"><?=utf8_decode("Unidade de Negócio:")?></label>
                            <div class="col-sm-8">
                                <select class="custom-select custom-select-sm" name="nr_empresa" id="nr_empresa">
                                    <option value="">SELECIONE</option> 
                                    <?
                                        $empresa = executar("SELECT nr_empresa, nm_fantasia FROM empresa");
                                        foreach($empresa as $v){ 
                                    ?>
                                            <option value="<?=$v['nr_empresa']?>"><?=$v['nm_fantasia']?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nr_conta" class="col-sm-2 col-form-label"><?=utf8_decode("Conta:")?></label>
                            <div class="col-sm-4">
                                <select class="custom-select custom-select-sm" name="nr_conta" id="nr_conta">
                                    <option value="">SELECIONE</option> 
                                    <?
                                        $conta = executar("SELECT nr_empresa_conta, CONCAT(nm_banco,' (',nr_agencia,' - ',nr_conta_corrente,')') as ds FROM empresa_conta JOIN banco USING(nr_banco) WHERE empresa_conta.nr_empresa = {$_SESSION['login']['nr_empresa']} AND exibir = 1 ORDER BY nm_banco");
                                        foreach($conta as $v){ 
                                    ?>
                                            <option value="<?=$v['nr_empresa_conta']?>"><?=$v['ds']?></option>
                                    <? } ?>
                                </select>
                            </div>

                            <label for="nr_centro_custo" class="col-sm-2 col-form-label"><?=utf8_decode("Centro de Custo:")?></label>
                            <div class="col-sm-4">
                                <select class="custom-select custom-select-sm" name="nr_centro_custo" id="nr_centro_custo">
                                    <option value="">SELECIONE</option> 
                                    <?
                                        $res_centro   = executar('SELECT nr_centro_custo, nm_centro FROM centro_custo WHERE situacao = "A" ORDER BY nm_centro');
                                        foreach($res_centro as $v){ 
                                    ?>
                                            <option value="<?=$v['nr_centro_custo']?>"><?=$v['nm_centro']?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nr_tppgto" class="col-sm-2 col-form-label"><?=utf8_decode("Forma de Pagamento:")?></label>
                            <div class="col-sm-4">
                                <select class="custom-select custom-select-sm" name="nr_tppgto" id="nr_tppgto">
                                    <option value="">SELECIONE</option> 
                                    <?
                                        $tppgto = executar("SELECT nr_tppgto, ds_tppgto FROM tppgto WHERE status_2 = 'A'");
                                        foreach($tppgto as $v){ 
                                            $selected = $v['nr_tppgto'] == 1 ? "selected" : "";
                                    ?>
                                            <option value="<?=$v['nr_tppgto']?>" <?=$selected?>><?=$v['ds_tppgto']?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>


                        <? $plano_conta = executar('SELECT nr_item, CONCAT(nr_principal,".",frmtd) as ds, ds_item FROM plano_conta_itens WHERE ds_item <> "INUTILIZADO" ORDER BY nr_principal,frmtd ASC') ?> 

                        <div class="form-group row">
                            <label for="plano_conta_1" class="col-sm-2 col-form-label"><?=utf8_decode("Plano de conta 1:")?></label>
                            <div class="col-sm-4">
                                <select class="custom-select custom-select-sm" name="plano_conta_1" id="plano_conta_1">
                                    <option value="">SELECIONE</option> 
                                    <? foreach($plano_conta as $v){ ?>
                                            <option value="<?=$v['nr_item']?>"><?=$v['ds'].' - '.$v['ds_item']?></option>
                                    <? } ?>
                                </select>
                            </div>

                            <label for="plano_conta_2" class="col-sm-2 col-form-label"><?=utf8_decode("Plano de conta 2:")?></label>
                            <div class="col-sm-4">
                                <select class="custom-select custom-select-sm" name="plano_conta_2" id="plano_conta_2" >
                                    <option value="">SELECIONE</option> 
                                    <? foreach($plano_conta as $v){ ?>
                                            <option value="<?=$v['nr_item']?>"><?=$v['ds'].' - '.$v['ds_item']?></option>
                                    <? } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="porcentagem_plano_conta_1" class="col-sm-4 col-form-label"></label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control form-control-sm"  name="porcentagem_plano_conta_1" id="porcentagem_plano_conta_1"  />
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="font-size: 10px !important;">%</div>
                                </div>
                            </div>

                            <label for="porcentagem_plano_conta_2" class="col-sm-4 col-form-label"></label>
                            <div class="input-group col-sm-2">
                                <input type="text" class="form-control form-control-sm"  name="porcentagem_plano_conta_2" id="porcentagem_plano_conta_2" />
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="font-size: 10px !important;">%</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="valor_1" class="col-sm-2 col-form-label" title="<?=utf8_decode("Valor Referente às parcelas do plano de conta 1")?>">Valor (R$):</label>
                            <div class="input-group col-sm-4"  title="<?=utf8_decode("Valor Referente às parcelas do plano de conta 1")?>">
                                <input type="text" class="form-control form-control-sm moeda"  name="valor_1" id="valor_1"  disabled/>
                            </div>

                            <label for="valor_2" class="col-sm-2 col-form-label"  title="<?=utf8_decode("Valor Referente às parcelas do plano de conta 2")?>">Valor 2 (R$):</label>
                            <div class="input-group col-sm-4" title="<?=utf8_decode("Valor Referente às parcelas do plano de conta 2")?>">
                                <input type="text" class="form-control form-control-sm moeda"  name="valor_2" id="valor_2" disabled/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="qtde_parcelas" class="col-sm-4 col-form-label"><?=utf8_decode("Número de Parcelas:")?></label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control form-control-sm"  name="qtde_parcelas" id="qtde_parcelas"  />
                            </div>

                            <label for="dt_vencto" class="col-sm-2 col-form-label"><?=utf8_decode("Data de Vencimento:")?></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm"  name="dt_vencto" id="dt_vencto" />
                            </div>
                        </div>

                    </div>

                    <input type="hidden" name="valor_original" id="valor_original" value="" />
                    <input type="hidden" name="nr_ctrt" id="nr_ctrt" value="" />
                </div>
                <div class="modal-footer row justify-content-center">
                    <button type="button" id="fechar-faturamento" class="btn btn-outline-secondary" data-dismiss="modal"><?=utf8_decode("Fechar")?></button>
                    <button type="button" id="pre-visualizar-parcelas" class="btn btn-success"><?=utf8_decode("Pré-Visualizar Parcelas")?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- FIM - MODAL - Pré-Faturamento -->


<!-- MODAL - CONTEUDO VISUALIZAÇÃO Pré-Faturamento -->
<div class="modal in fade" id="modalPreVisualizarFaturamento" tabindex="-2"role="dialog" aria-labelledby="TituloModalPreVisualizarFaturamento" aria-hidden="true">
    <div class="modal-dialog modal-xl text-left" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h3 class="modal-title" id="TituloModalPreVisualizarFaturamento"><?=utf8_decode("Pré-Visualizar Faturamento")?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="form-faturamento" id="form-faturamento" action="../App_Pes/ControlPreFaturamento.php" method="POST">
                <div class="form-group row justify-content-center">
                    <label for="dt_inicio_faturamento" class="col-sm-4 col-form-label"><?=utf8_decode("Data de Ativação:")?></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control form-control-sm"  name="dt_inicio_faturamento" id="dt_inicio_faturamento" />
                    </div>
                </div>
                <div class="modal-body form-row pb-4" id="conteudo-previsualizar-faturamento" style="overflow: auto; height: 65vh; margin-top: -20px;">
                    
                </div>

                <input type="hidden" name="type" value="parcelas" />
                <input type="hidden" name="action" value="faturar" />
                <input type="hidden" name="nr_ctrt_prefaturamento" value="" />
                <input type="hidden" name="tipo_faturamento" value="" />
                <div class="modal-footer row justify-content-center">
                    <button type="button" id="cancelar-faturamento" class="btn btn-outline-secondary" data-dismiss="modal"><?=utf8_decode("Cancelar")?></button>
                    <button type="button" id="faturar" class="btn btn-success">
                        <span id="spin" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Faturar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- FIM - MODAL - CONTEUDO VISUALIZAÇÃO Pré-Faturamento -->