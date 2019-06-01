<!-- MODAIS -->
<!-- Modal Contatos -->
<div class="modal in fade" id="modalContato" role="dialog" aria-labelledby="TituloModalCentralizado" aria-hidden="true">
    <div class="modal-dialog text-left" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="TituloModalCentralizado">Contato</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form name="form-contatos" action="../App_Pes/ControlCliente.php" method="POST">
            <div class="modal-body form-row pb-4">
            <div class="form-group col-md-12">
                Nome
                <input type="text" class="form-control form-control-sm" id="nm_ctt" name="nm_ctt" placeholder="">
            </div>
            <div class="form-group col-md-6">
                <?=utf8_decode("Gênero")?>
                <select class="form-control form-control-sm w-100" id="ctt_genero" name="ctt_genero" required>

                    <option>SELECIONE</option>
                    <option value="F">Feminino</option>
                    <option value="M">Masculino</option>

                </select>
            </div>
            <div class="form-group col-md-8">
                Ramal
                <input type="text" class="form-control form-control-sm w-25" id="nr_ramal" name="nr_ramal" placeholder="">
            </div>
            <div class="form-group col-md-6">
                Telefone
                <input type="text" class="form-control form-control-sm w-100 phone" id="nr_telefone" name="nr_telefone" placeholder="">
            </div>
            <div class="form-group col-md-6">
                Telefone (2)
                <input type="text" class="form-control form-control-sm w-100 phone" id="nr_celular" name="nr_celular" placeholder="">
            </div>
            <div class="form-group col-md-12">
                Email
                <textarea class="form-control" id="ds_email" name="ds_email" rows="2" placeholder=""></textarea>
            </div>
            <div class="form-group col-md-6">
                Site
                <input type="text" class="form-control form-control-sm w-100" id="ds_site" name="ds_site" placeholder="">
            </div>
            <div class="form-group col-md-6">
                Skype
                <input type="text" class="form-control form-control-sm w-100" id="ctt_skype" name="ctt_skype" placeholder="">
            </div>
            <div class="form-group col-md-6">
                <?=utf8_decode("Função")?>
                <input type="text" class="form-control form-control-sm w-100" id="ctt_funcao" name="ctt_funcao" placeholder="">
            </div>
            <div class="form-group col-md-6">
                Tipo
                <select class="form-control form-control-sm w-100" id="ds_telefone" name="ds_telefone" required>

                    <option>SELECIONE</option>

                    <?
                    $tipo = arrayDsTp();
                    foreach($tipo as $tipo ){

                        if(!empty($lead_ID)){
                            $verifOP = $tipo['nr_func'] == $lead['lead_colaborador'];
                            $selecao = $verifOP ? "selected='selected'" : "";
                        }else{
                            $verifOP = $tipo['nr_func'] == $_SESSION['login']['nr_func'];
                            $selecao = $verifOP ? "selected='selected'" : "";
                        }

                        ?>
                        <option id="<?=$tipo?>" <?=$selecao?> value="<?=$tipo?>"><?=$tipo?></option>
                        <?
                    }
                    ?>

                </select>
            </div>
            <div class="modal-footer">
                <input type="hidden"  name="lead_contato_id" id="lead_contato_id" value="" />
                <input type="hidden"  name="action-contato" id="action-contato" value="" />
                <button type="button" id="fechar-contato" class="btn btn-secondary" data-dismiss="modal"><?=utf8_decode("Fechar")?></button>
                <button type="button" id="salvar-contato" <?=$onclickSalvaContatos?> data-dismiss="modal" class="btn btn-primary"><?=utf8_decode("Salvar mudanças")?></button>
            </div>
        </form>
        </div>
    </div>
</div>
</div>
<!-- FIM Modal Contatos -->

<!-- Modal Enderecos -->
<div class="modal in fade" id="modalEndereco" role="dialog" aria-labelledby="TituloModalEndereco" aria-hidden="true">
    <div class="modal-dialog modal-lg text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="TituloModalEndereco"><?=utf8_decode("Endereço")?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="form-endereco" action="../App_Pes/ControlCliente.php" method="POST">
                <div class="modal-body form-row pb-4">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="nr_cep" class="col-sm-2 col-form-label">CEP:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control form-control-sm cep" name="nr_cep" id="nr_cep" />
                            </div>
                            <div class="col-sm-7">
                                <button type="button" class="btn btn-sm btn-outline-secondary campo-obrigatorio" id="busca-cep" onclick="pesquisaCEP();">Buscar CEP</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row"> 
                            <label for="tp_logradouro" class="col-sm-2 col-form-label"> Logradouro: </label>
                            <div class="col-sm-2">
                                <select id="tp_logradouro" name="tp_logradouro" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option>SELECIONE</option>
                                    <?
                                    $tipo_logradouro = executar("SELECT codigo, descricao FROM tipo_logradouro ORDER BY descricao");
            
                                    foreach($tipo_logradouro as $value){
                                        ?>
                                        <option id="<?=$value['codigo']?>" value="<?=$value['codigo']?>"><?=$value['descricao']?></option>
                                        <?
                                    }
                                    
                                    ?>
            
                                </select>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm campo-obrigatorio" id="nm_logradouro" name="nm_logradouro" placeholder="">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="nr_logradouro" class="col-sm-2 col-form-label"><?=utf8_decode("Número:")?></label>
                            <div class="col-sm-2">
                                <input tipe="text" class="form-control form-control-sm campo-obrigatorio" name="nr_logradouro" id="nr_logradouro" />
                            </div>
                            <label for="ds_complemento" class="col-sm-2 col-form-label"><?=utf8_decode("Complemento:")?></label>
                            <div class="col-sm-6">
                                <input tipe="text" class="form-control form-control-sm" name="ds_complemento" id="ds_complemento" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="nm_bairro" class="col-sm-2 col-form-label"><?=utf8_decode("Bairro:")?></label>
                            <div class="col-sm-3">
                                <input tipe="text" class="form-control form-control-sm campo-obrigatorio" name="nm_bairro" id="nm_bairro" />
                            </div>
                            <label for="nm_cidade" class="col-sm-2 col-form-label campo-obrigatorio"><?=utf8_decode("Cidade:")?></label>
                            <div class="col-sm-3">
                                <input tipe="text" class="form-control form-control-sm campo-obrigatorio" name="nm_cidade" id="nm_cidade" />
                            </div>
                            <label for="nm_estado" class="col-sm-1 col-form-label"><?=utf8_decode("UF:")?></label>
                            <div class="col-sm-1">
                                <input tipe="text" class="form-control form-control-sm campo-obrigatorio" name="nm_estado" id="nm_estado" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="ds_logradouro" class="col-sm-2 col-form-label"><?=utf8_decode("Tipo:")?></label>
                            <div class="col-sm-3">
                                <select class="form-control form-control-sm w-100 campo-obrigatorio" id="ds_logradouro" name="ds_logradouro" required>
            
                                    <option>SELECIONE</option>
            
                                    <?
                                    $tipo = arrayDsTp();
                                    foreach($tipo as $tipo ){
                                        ?>
                                        <option id="<?=$tipo?>" <?=$selecao?> value="<?=$tipo?>"><?=$tipo?></option>
                                        <?
                                    }
                                    ?>
            
                                </select>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="fechar-endereco" class="btn btn-secondary" data-dismiss="modal"><?=utf8_decode("Fechar")?></button>
                    <button type="button" id="salvar-endereco" class="btn btn-primary" onclick=""><?=utf8_decode("Salvar mudanças")?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- FIM Modal Enderecos -->

<!-- Modal Contratos -->
<div class="modal in fade" id="modalContrato" role="dialog" aria-labelledby="TituloModalContrato" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="TituloModalContrato"><?=utf8_decode("Contrato")?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="nav-contrato">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="nav-link-contrato"  href="#form-contrato">Dados do Contrato</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="nav-lista-material" href="#form-lista-material">Lista de material</a>
                    </li>
                </ul>
            </div>
            <!-- Dados do Contrato -->
            <form name="form-contrato" id="form-contrato" action="../App_Pes/ControlCliente.php" method="POST">
                <div class="modal-body form-row pb-4" style="overflow: auto; height: 65vh;">
                    
                    <!-- Dados básicos do Contrato -->                    
                    <div class="col-md-12 border-bottom border-info">
                        <h6>Dados do contrato</h6>
                    </div>
                    <div class="col-md-12 pt-2">
                        <div class="form-group row">
                            <label for="nm_ctrt" class="col-sm-2 col-form-label">Nome:</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control form-control-sm" name="nm_ctrt" id="nm_ctrt" />
                            </div>
                            <label for="dt_prevista" class="col-sm-2 col-form-label contrato-info-adicional">Prev. Fechamento:</label>
                            <div class="col-sm-3 contrato-info-adicional">
                                <input type="text" class="form-control form-control-sm " name="dt_prevista" id="dt_prevista" />
                            </div>
                        </div>
                        <div class="form-group row contrato-alteracao">
                            <label for="ds_tpctrt" class="col-sm-2 col-form-label">Tipo:</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control form-control-sm" name="ds_tpctrt" id="ds_tpctrt" disabled/>
                            </div>
                            <label for="dt_ctrt" class="col-sm-1 col-form-label"><?=utf8_decode("Emissão:")?></label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control form-control-sm" name="dt_ctrt" id="dt_ctrt" disabled/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 pt-2 contrato-prevenda">
                        <div class="form-group row">
                            <label for="ds_status_contrato" class="col-sm-2 col-form-label">Status:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm" name="ds_status_contrato" id="ds_status_contrato"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 pt-2 contrato-implantacao">
                        <div class="form-group row">
                            <label for="nr_status_contrato" class="col-sm-2 col-form-label">Status:</label>
                            <div class="col-sm-10">
                                <select id="nr_status_contrato" name="nr_status_contrato" class="custom-select custom-select-sm campo-obrigatorio w-50">
                                    <?
                                    $res_status_contrato = executar('SELECT nr_status_contrato, ds_status_contrato FROM status_contrato WHERE nr_status_contrato IN(1,3,6,9) ORDER BY ds_status_contrato ASC');
                                    foreach($res_status_contrato as $value){
                                        ?>
                                        <option id="<?=$value['nr_status_contrato']?>" value="<?=$value['nr_status_contrato']?>" ><?=$value['ds_status_contrato']?></option>
                                        
                                    <? } ?>
            
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 pt-2 contrato-ativo">
                        <div class="form-group row">
                            <label for="renovacao" class="col-sm-2 col-form-label"><?=utf8_decode("Renovação")?>:</label>
                            <div class="col-sm-5">
                                <select name="renovacao" id="renovacao" class="custom-select custom-select-sm campo-obrigatorio" disabled>
                                    <option value="aditivo" >Aditivo</option>
                                    <option value="automatica" ><?=utf8_decode("Automática")?></option>
                                </select>
                            </div>
                            <label for="prazo_contrato" class="col-sm-2 col-form-label">Prazo do Contrato:</label>
                            <div class="input-group col-sm-3">
                                <select id="prazo_contrato" name="prazo_contrato" class="custom-select custom-select-sm campo-obrigatorio" disabled>
                                    <option value="">Selecione</option>
                                    <? for($x=60; $x>=12; $x = $x - 12){?>
                                    <option value="<?=$x?>" ><?=$x?></option>
                                    <? } ?>
            
                                </select>
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="font-size: 10px !important;">Meses</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 pt-2 contrato-ativo">
                        <div class="form-group row">
                            <label for="dt_vencto" class="col-sm-2 col-form-label ctrt-ativo">Vencimento:</label>
                            <div class="col-sm-3 ctrt-ativo">
                                <input type="text" class="form-control form-control-sm" name="dt_vencto" id="dt_vencto"  disabled/>
                            </div>
                            
                            <label for="dt_ativacao" class="col-sm-2 col-form-label contrato-renovacao"><?=utf8_decode("Data de Ativação")?>:</label>
                            <div class="col-sm-3 contrato-renovacao">
                                <input type="text" class="form-control form-control-sm " name="dt_ativacao" id="dt_ativacao" />
                            </div>

                            <label for="nr_diavencimento" class="col-sm-2 col-form-label">Dia da fatura:</label>
                            <div class="col-sm-3">
                                <select id="nr_diavencimento" name="nr_diavencimento" class="custom-select custom-select-sm campo-obrigatorio" disabled>
                                    <option value="">Selecione</option>
                                    <? 
                                    $array_dia = array(5,10,15,20,25,30);
                                    foreach($array_dia as $x){?>
                                    <option value="<?=$x?>" ><?=$x?></option>
                                    <? } ?>
            
                                </select>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-info" name="btn-renovacao" id="btn-renovacao" onclick="renovarContrato();"><?=utf8_decode("Renovação")?></button>
                        </div>
                    </div>

                    <div class="col-md-12 contrato-alteracao">
                        <div class="form-group row">
                            <label for="nr_potencial_fechamento" class="col-sm-2 col-form-label">Potencial de Fechamento:</label>
                            <div class="col-sm-4">
                                <select id="nr_potencial_fechamento" name="nr_potencial_fechamento" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option>SELECIONE</option>
                                    <?
                                    $res_potencial_fechamento = executar('SELECT nr_potencial_fechamento, descricao_potencial_fechamento FROM status_potencial_fechamento ORDER BY descricao_potencial_fechamento');
            
                                    foreach($res_potencial_fechamento as $value){
                                        ?>
                                        <option id="<?=$value['nr_potencial_fechamento']?>" value="<?=$value['nr_potencial_fechamento']?>" ><?=$value['descricao_potencial_fechamento']?></option>
                                        <?
                                    }
                                    
                                    ?>
            
                                </select>
                            </div>
                            <label for="nr_empresa_ctrt" class="col-sm-2 col-form-label">Empresa:</label>
                            <div class="col-sm-4">
                                <select id="nr_empresa_ctrt" name="nr_empresa" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option>SELECIONE</option>
                                    <?
                                    $res_empresa = executar('SELECT nr_empresa, nm_fantasia FROM empresa ORDER BY nm_fantasia');
            
                                    foreach($res_empresa as $value){
                                        ?>
                                        <option id="<?=$value['nr_empresa']?>" value="<?=$value['nr_empresa']?>" ><?=$value['nm_fantasia']?></option>
                                        <?
                                    }
                                    
                                    ?>
            
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="nr_gerenteconta" class="col-sm-2 col-form-label">Gerente de Conta:</label>
                            <div class="col-sm-5">
                                <select id="nr_gerenteconta" name="nr_gerenteconta" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option>SELECIONE</option>
                                    <?
                                    $res_ger = executar('SELECT nr_func, UPPER(nm_pessoa) as nm_pessoa FROM func JOIN pessoa USING(nr_pessoa) WHERE gerente_conta = "S" ORDER BY nm_pessoa');
            
                                    foreach($res_ger as $value){
                                        $selected = $res_ger == $cliente[0]['nr_colaborador'] ? "selected" : "";
                                        ?>
                                        <option id="<?=$value['nr_func']?>" value="<?=$value['nr_func']?>" <?=$selected?>><?=$value['nm_pessoa']?></option>
                                        <?
                                    }
                                    
                                    ?>
            
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="nr_revendedor_ctrt" class="col-sm-2 col-form-label">Revendedor:</label>
                            <div class="col-sm-5">
                                <select id="nr_revendedor_ctrt" name="nr_revendedor" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option>SELECIONE</option>
                                    <?
                                    $res_revendedor = executar('SELECT nr_revendedor, revendedor_nome FROM revendedores ORDER BY revendedor_nome');
            
                                    foreach($res_revendedor as $value){
                                        $selected = $res_revendedor == $cliente[0]['nr_revendedor'] ? "selected" : "";
                                        ?>
                                        <option id="<?=$value['nr_revendedor']?>" value="<?=$value['nr_revendedor']?>" <?=$selected?>><?=$value['revendedor_nome']?></option>
                                        <?
                                    }
                                    
                                    ?>
            
                                </select>
                            </div>
                        </div>
                    </div> 

                    <!-- Motivo da renovação -->
                    <div class="col-md-12 border-bottom border-info pt-3 contrato-renovacao">
                        <h6><?=utf8_decode("Motivo da Renovação")?></h6>
                    </div>
                    <div class="col-md-12 pt-2 contrato-renovacao">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <select id="motivo_renovacao" name="motivo_renovacao" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option value="">Selecione</option>
                                    <option value="1">UPGRADE</option>
                                    <option value="2"><?=utf8_decode("RENOVAÇÃO POR TEMPO")?></option>
                                    <option value="3">REAJUSTE DE VALORES</option>
                                    <option value="4">DOWNGRADE E REAJUSTE</option>
                                    <option value="5">UPGRADE E REAJUSTE</option>
                                    <option value="6"><?=utf8_decode("NOVOS SERVIÇOS VINCULADO")?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Seleção de produtos -->
                    <div class="col-md-12 border-bottom border-info pt-3">
                        <h6>Produtos</h6>
                    </div>
                    <div class="col-md-12 pt-2">
                        <div class="form-group row">
                            <label for="nr_un" class="col-sm-2 col-form-label"><?=utf8_decode("Unidade de Negócio:")?></label>
                            <div class="col-sm-10">
                                <select id="nr_un" name="nr_un" class="custom-select custom-select-sm campo-obrigatorio w-50" onchange="changeTipoProduto($(this).val());">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_un = executar('SELECT nr_unidade_negocio, nm_unidade_negocio FROM unidade_negocio WHERE status_2 = "A"');
    
                                    foreach($res_un as $value){
                                        ?>
                                        <option id="<?=$value['nr_unidade_negocio']?>" value="<?=$value['nr_unidade_negocio']?>"><?=$value['nm_unidade_negocio']?></option>
    
                                        <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="nr_un_tipo" class="col-sm-2 col-form-label">Tipo:</label>
                            <div class="col-sm-10">
                                <select id="nr_un_tipo" name="nr_un_tipo" class="custom-select custom-select-sm campo-obrigatorio w-50" onchange="changeItemProduto($(this).val());">
                                    <option value="">SELECIONE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="nr_un_item" class="col-sm-2 col-form-label">Item:</label>
                            <div class="col-sm-10">
                                <select id="nr_un_item" name="nr_un_item" class="custom-select custom-select-sm campo-obrigatorio w-50">
                                    <option value="">SELECIONE</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 subitem subitem-1 mt-2">
                        <div class="form-group row">
                            <label for="nr_sla" class="col-sm-2 col-form-label" title="<?=utf8_decode("Tempo para a solução da ocorrência.")?>">SLA:</label>
                            <div class="col-sm-4">
                                <select id="nr_sla" name="nr_sla" class="custom-select custom-select-sm campo-obrigatorio w-50" title="<?=utf8_decode("Tempo para a solução da ocorrência.")?>">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_sla = executar('SELECT nr_sla, ds_sla FROM sla');
                                    
                                    foreach($res_sla as $value){
                                    ?>
                                        <option value="<?=$value['nr_sla']?>"><?=$value['ds_sla']?></option>

                                    <? } ?>
                                </select>
                            </div>
                            <label for="nr_tipo_atendimento" class="col-sm-1 col-form-label" title="<?=utf8_decode("Tempo de suporte.")?>">Tipo:</label>
                            <div class="col-sm-4" title="<?=utf8_decode("Tempo de suporte.")?>">
                                <select id="nr_tipo_atendimento" name="nr_tipo_atendimento" class="custom-select custom-select-sm campo-obrigatorio w-50">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_tipo = executar('SELECT nr_tipo_atendimento, ds_tipo_atendimento FROM tipo_atendimento ORDER BY ds_tipo_atendimento ASC');
                                    
                                    foreach($res_tipo as $value){
                                    ?>
                                        <option value="<?=$value['nr_tipo_atendimento']?>"><?=$value['ds_tipo_atendimento']?></option>

                                    <? } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tempo_atendimento" class="col-sm-2 col-form-label" title="<?=utf8_decode("Tempo de para chegar ao local da ocorrência.")?>">Tempo:</label>
                            <div class="input-group col-sm-4" title="<?=utf8_decode("Tempo de para chegar ao local da ocorrência.")?>">
                                <input type="text" class="form-control form-control-sm" name="tempo_atendimento" id="tempo_atendimento" /> 
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="font-size: 10px !important;">Hora(s)</div>
                                </div>
                            </div>
                            <label for="nr_hubpop_ctrt" class="col-sm-1 col-form-label">HubPop:</label>
                            <div class="col-sm-4">

                                <select id="nr_hubpop_ctrt" name="nr_hubpop_ctrt" class="custom-select custom-select-sm campo-obrigatorio w-75">
                                    
                                <option value="">SELECIONE</option>
                                    <?
                                    $res_hub = executar('SELECT nr_hubpop, nm_hubpop FROM hubpop WHERE status_hubpop = "S" ORDER BY nm_hubpop');
                                    
                                    foreach($res_hub as $value){
                                    ?>
                                        <option value="<?=$value['nr_hubpop']?>"><?=$value['nm_hubpop']?></option>

                                    <? } ?>

                                </select>

                            </div>
                        </div>
                    </div>


                    <!-- Endereços -->
                    <div class="col-md-12 border-bottom border-info pt-3">
                        <h6><?=utf8_decode("Endereços")?></h6>
                    </div>
                    <div class="col-md-12 mt-2 overflow-info-cliente-2">
                        <table class="table table-sm table-hover bg-white">
                            <?
                            $dados_endereco = executar('SELECT * FROM ender WHERE nr_pessoa = '.$nr_pessoa);
                            if($dados_endereco){
                                foreach($dados_endereco as $ender){
                                    $logradouro = $ender['tp_logradouro'].' '.$ender['nm_logradouro'].', '.$ender['nr_logradouro'];
                                    ?>
                                    <tr colspan="5">
                                        <td colspan="1"><input type="radio" name="nr_ender[]" value="<?=$ender['nr_ender']?>" id="nr_ender_<?=$ender['nr_ender']?>" /></td>
                                        <td colspan="1"><?=$logradouro?></td>
                                        <td colspan="1"><?=$ender['nm_bairro']?></td>
                                        <td colspan="1"><?=$ender['nm_cidade']?></td>
                                        <td colspan="1"><?=$ender['nm_estado']?></td>
                                    </tr>

                                    <?
                                }
                            } else {
                                ?>
                                <tr colspan="5">
                                    <td colspan="5" class="text-danger text-center"><b><?=utf8_decode("Não foi possível encontrar nenhum endereço cadastrado.")?></b></td>
                                </tr>
                                <?
                            }
                            ?>
                        </table>
                    </div>


                    <!-- Valores do Projeto -->
                    <div class="subitem-2 subitem col-md-12 border-bottom border-info pt-3">
                        <h6>Valores do Projeto</h6>
                    </div>
                    <div class="subitem-2 subitem col-md-12 pt-2">
                        <div class="form-group row">
                            <label for="valor_produto" class="col-sm-2 col-form-label">Produto:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm w-50 moeda" name="valor_produto" id="valor_produto" /> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="valor_servico" class="col-sm-2 col-form-label"><?=utf8_decode("Serviço")?>:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm w-50 moeda" name="valor_servico" id="valor_servico" /> 
                            </div>
                        </div>
                    </div>
                    

                    <!-- Valores Recorrentes -->
                    <div class="subitem-3 subitem col-md-12 border-bottom border-info pt-3">
                        <h6>Valor Recorrente</h6>
                    </div>
                    <div class="col-md-12 subitem-3 subitem mt-2">
                        <div class="form-group row">
                            <label for="valor_install" class="col-sm-2 col-form-label"><?=utf8_decode("Instalação")?>:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm w-75 moeda" name="valor_install" id="valor_install" /> 
                            </div>
                            <label for="dt_vencimento_nfse" class="col-sm-2 col-form-label"><?=utf8_decode("Prev. Pagamento Instalação:")?></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm" name="dt_vencimento_nfse" id="dt_vencimento_nfse" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nm_parcelas" class="col-sm-2 col-form-label"><?=utf8_decode("Cond. Pagamento Instalação:")?></label>
                            <div class="col-sm-3">
                                <select id="nm_parcelas" name="nm_parcelas" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_condPgto = executar('SELECT nm_parcelas, nm_cond FROM cond_pgto ORDER BY nm_parcelas');
                                    
                                    foreach($res_condPgto as $value){
                                    ?>
                                        <option value="<?=$value['nm_parcelas']?>"><?=$value['nm_cond']?></option>

                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="valor_mensal" class="col-sm-2 col-form-label">Mensal:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm w-50 moeda" name="valor_mensal" id="valor_mensal" /> 
                            </div>
                        </div>
                    </div>

                    <!-- Quantidade de IPs -->
                    <div class="col-md-12 border-bottom border-info pt-3 contrato-info-adicional">
                        <h6>Quantidade de IPs</h6>
                    </div>
                    <div class="col-md-12 mt-2 contrato-info-adicional">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <select id="qtd_ip" name="qtd_ip" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_ip = array(array('0   Ips /Projeto sem IP fixo', '0   Ips /Projeto sem IP fixo'), array('3   Ips - Mascara (255.255.255.248/29)', '3   Ips - Mascara (255.255.255.248/29)'), array('11 Ips - Mascara (255.255.255.240/28)', '11 Ips - Mascara (255.255.255.240/28)'), array('27 Ips - Mascara (255.255.255.224/27)', '27 Ips - Mascara (255.255.255.224/27)'), array('59 Ips - Mascara (255.255.255.224/26)', '59 Ips - Mascara (255.255.255.224/26)'), array('123   Ips - Mascara (255.255.255.128/25)', '123   Ips - Mascara (255.255.255.128/25)'), array('251 Ips - Mascara (255.255.255.0/24)', '251 Ips - Mascara (255.255.255.0/24)'));
                                    
                                    foreach($res_ip as $value){
                                    ?>
                                        <option value="<?=$value[0]?>"><?=$value[1]?></option>

                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- QoS -->
                    <div class="col-md-12 border-bottom border-info pt-3 contrato-info-adicional">
                        <h6>QoS</h6>
                    </div>
                    <div class="col-md-12 mt-2 contrato-info-adicional">
                        <div class="form-group row">
                            <label for="qos" class="col-sm-2 col-form-label"><?=utf8_decode("Possui:")?></label>
                            <div class="col-sm-3">
                                <select id="qos" name="qos" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_possui = array(array('SIM', 'SIM'), array('NAO', 'NAO'));
                                    
                                    foreach($res_possui as $value){
                                    ?>
                                        <option value="<?=$value[0]?>"><?=$value[1]?></option>

                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="qos_prioridade" class="col-sm-2 col-form-label"><?=utf8_decode("Prioridade:")?></label>
                            <div class="col-sm-3">
                                <select id="qos_prioridade" name="qos_prioridade" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_prioridade = array(array(1, 1), array(2, 2), array(3, 3), array(4, 4));
                                    
                                    foreach($res_prioridade as $value){
                                    ?>
                                        <option value="<?=$value[0]?>"><?=$value[1]?></option>

                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>


                    <!-- Adicionais -->
                    <div class="col-md-12 border-bottom border-info pt-3 contrato-info-adicional">
                        <h6>Adicionais</h6>
                    </div>
                    <div class="col-md-12 mt-2 contrato-info-adicional">
                        <div class="form-group row">
                            <label for="projeto_especial" class="col-sm-2 col-form-label"><?=utf8_decode("Projeto Especial?")?></label>
                            <div class="col-sm-3">
                                <select id="projeto_especial" name="projeto_especial" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $projeto_especial = array(array('SIM', 'SIM'), array('NAO', 'NAO'));
                                    
                                    foreach($projeto_especial as $value){
                                    ?>
                                        <option value="<?=$value[0]?>"><?=$value[1]?></option>

                                    <? } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="precisa_repetidora" class="col-sm-2 col-form-label"><?=utf8_decode("Precisa Repetidora:")?></label>
                            <div class="col-sm-3">  
                                <select id="precisa_repetidora" name="precisa_repetidora" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $precisa_repetidora = array(array('SIM', 'SIM'), array('NAO', 'NAO'));
                                    
                                    foreach($precisa_repetidora as $value){
                                    ?>
                                        <option value="<?=$value[0]?>"><?=$value[1]?></option>

                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="sub-action" id="sub-action"  value="" />
                <input type="hidden" name="action" id="action-contrato" value="" />
                <input type="hidden" name="type"   id="type-contrato"   value="contrato" />
                <input type="hidden" name="nr_ctrt"   id="nr_ctrt_ctrt"   value="" />
                <input type="hidden" name="nr_pessoa" id="nr_pessoa_ctrt" value="" />
            </form>
            <div class="modal-footer footer-modal-contrato" id="footer-form-contrato">
                <button type="button" id="fechar-contrato" class="btn btn-secondary" data-dismiss="modal"><?=utf8_decode("Fechar")?></button>
                <button type="button" id="salvar-contrato" class="btn btn-primary" onclick=""><?=utf8_decode("Salvar mudanças")?></button>
            </div>
            <!-- FIM - Dados do Contrato -->

            <!-- Lista de Materiais -->
            <form name="form-lista-material" id="form-lista-material" action="../App_Pes/ControlCliente.php" method="POST">
                <!--div class="modal-body form-row pb-4" style="overflow: auto; height: 65vh;"-->
                <div class="modal-body form-row">
                    <!-- Gravar Site -->                    
                    <div class="col-md-12 mt-2">
                        <div class="form-group row">
                            <label for="estacao" class="col-sm-2 col-form-label"><?=utf8_decode("Gravar Site:")?></label>
                            <div class="col-sm-6">
                                <input name="estacao" id="estacao" class="form-control form-control-sm"/>
                            </div>
                            <div class="col-sm-4">
                                <button type="button" id="btn-grava-site" class="btn btn-sm btn-outline-dark">Gravar</button>
                            </div>
                        </div>
                    </div>


                    <!-- Materiais e Serviços -->                    
                    <div class="col-md-12 border-top border-info pt-3">
                        <h6><?=utf8_decode("Inserir Materiais e Serviços")?></h6>
                    </div>

                    <!-- Gravar Materiais -->                    
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="select_nr_custo" class="col-sm-2 col-form-label"><?=utf8_decode("Site:")?></label>
                            <div class="col-sm-6">
                                <select id="select_nr_custo" name="select_nr_custo" class="custom-select custom-select-sm campo-obrigatorio"></select>
                            </div>
                            <div class="col-sm-4">
                                <button type="button" data-toggle="modal" data-target="#modalPesquisarItensListaMaterialContrato" class="btn btn-sm btn-outline-dark"><?=utf8_decode("Pesquisar Material/Serviço")?></button>
                            </div>
                            <label for="nm_material_servico" class="col-sm-2 col-form-label"><?=utf8_decode("Material/Serviço:")?></label>
                            <div class="col-sm-10 my-3">
                                <p id="nm_material_servico"></p>
                                <input type="hidden" id="nr_material_servico" name="nr_material_servico" />
                            </div>
                            <label for="valor_servico_lista_material" class="col-sm-2 col-form-label"><?=utf8_decode("Valor:")?></label>
                            <div class="col-sm-4" title="<?=utf8_decode("Campo exclusivo para Serviços.")?>" >
                                <input type="text" class="form-control form-control-sm" id="valor_servico_lista_material" name="valor_servico_lista_material" />
                                <input type="hidden" id="nr_fornecedor2" name="nr_fornecedor2" />
                            </div>
                            <label for="qtdade" class="col-sm-2 col-form-label"><?=utf8_decode("Quantidade:")?></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm" id="qtdade" name="qtdade" disabled />
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <button type="button" id="btn-insere-item-lista-material" class="btn btn-sm btn-outline-dark">
                                <?=utf8_decode("Inserir")?>
                            </button>
                        </div>
                    </div>

                    <!-- Lista de Materiais/Serviços -->                
                    <div class="col-md-12 border-top border-info mt-4 pt-2"></div>
                    <div class="col-md-12 border rounded border-primary bg-primary py-2">
                        <div class="row justify-content-center">
                            <label for="select-lista-material-servico" class="col-sm-2 col-form-label text-white" style="font-size: 13px;"><?=utf8_decode("Selecionar Lista:")?></label>
                            <div class="col-sm-6">
                                <select id="select-lista-material-servico"  style="font-size: 12px;" name="select-lista-material-servico" class="custom-select custom-select-sm campo-obrigatorio"></select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- FIM - Lista de Materiais -->

        </div>
    </div>
</div>
<!-- FIM Modal Contratos -->


<!-- Tabela - Materiais e Serviços -->
<div class="modal fade mt-5" id="modalTabelaListaMaterialContrato" tabindex="-2" role="dialog" aria-labelledby="TituloModalTabelaListaMaterialContrato" aria-hidden="true">
    <div class="modal-dialog modal-xl text-left" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h3 class="modal-title text-white" id="TituloModalTabelaListaMaterialContrato"><?=utf8_decode("Lista de Materiais")?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="col-md-12 pl-5 bg-primary text-white mx-auto" style="margin-top: -2px;" id="show-nome-lista-material-servico"></div>

            <form name="form-tabela-contrato" id="form-tabela-contrato" action="../App_Pes/ControlCliente.php" method="POST">
                <div class="modal-body form-row pb-4" style="font-size: 12px;">
                    <div id="show-lista-material-servico" class="w-100"></div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- FIM Tabela - Materiais e Serviços -->


<!-- Modal Pesquisar Material/Serviço Contratos -->
<div class="modal fade mt-5" id="modalPesquisarItensListaMaterialContrato" tabindex="-2" role="dialog" aria-labelledby="TituloModalPesquisarItensListaMaterialContrato" aria-hidden="true">
    <div class="modal-dialog text-left" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h3 class="modal-title text-white" id="TituloModalPesquisarItensListaMaterialContrato"><?=utf8_decode("Itens - Lista de Materiais")?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="form-contrato" id="form-contrato" action="../App_Pes/ControlCliente.php" method="POST">
                <div class="modal-body form-row pb-4" style="font-size: 12px;">

                    <div id="nav-pesquisa-lista-material">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#lista-material-material">Material</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="nav-lista-material" href="#lista-material-servico"><?=utf8_decode("Serviço")?></a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Informações dos materiais e serviços a serem inseridos -->
                    <div class="col-md-12 mt-2"  id="lista-material-material">
                        <div class="form-group row">
                            <label for="material" class="col-sm-2 col-form-label"><?=utf8_decode("Material:")?></label>
                            <div class="col-sm-8">
                                <input name="material" id="material" class="form-control form-control-sm"/>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-2" id="lista-material-servico">
                        <div class="form-group row">
                            <label for="nr_servico" class="col-sm-2 col-form-label"><?=utf8_decode("Serviço:")?></label>
                            <div class="col-sm-10">
                                <select name="nr_servico" id="nr_servico" class="custom-select custom-select-sm">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $arrServico = executar('SELECT  nr_servico,  ds_servico FROM servico WHERE servico_status = "A" ORDER BY ds_servico');
                                    foreach($arrServico as $v){ ?>
                                        <option value="<?=$v['nr_servico']?>"><?=$v['ds_servico']?></option>
                                    <? } ?>
                                </select>
                            </div>
                            <label for="fornecedor-servico" class="col-sm-2 col-form-label"><?=utf8_decode("Fornecedor:")?></label>
                            <div class="col-sm-10">
                                <select name="fornecedor-servico" id="fornecedor-servico" class="custom-select custom-select-sm">
                                    <option value="">SELECIONE</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabela de materiais encontrados -->
                    <div id="itens-lista-material" style="overflow: auto; height: 180px; width: 100%;">
                        <table class="table table-sm table-hover w-100">
                            <tbody class="pl-3">

                            </tbody>
                        </table>
                    </div>

                </div>
            </form>
                <div class="modal-footer">
                    <button type="button" id="adicionar-item-lista-material" class="btn btn-primary">
                        <span class="" id="charge-btn" role="status" aria-hidden="true"></span>
                        <?=utf8_decode("Adicionar Item")?>
                    </button>
                </div>
        </div>
    </div>
</div>
<!-- FIM Modal Pesquisar Material/Serviço Contratos -->

<!-- Modal Ativar Contratos -->
<div class="modal in fade" id="modalAtivarContrato" role="dialog" aria-labelledby="TituloModalAtivarContrato" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="TituloModalAtivarContrato"><?=utf8_decode("Ativar Contrato")?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="form-contrato" id="form-contrato" action="../App_Pes/ControlCliente.php" method="POST">
                <div class="modal-body form-row pb-4" style="overflow: auto; height: 70vh;">
                    
                    <div class="col-md-12 pt-2">
                        <div class="form-group row">
                            <label for="nm_ctrt" class="col-sm-2 col-form-label">Nome:</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm" name="nm_ctrt" id="ativa-ctrt-nm_ctrt" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="prazo_contrato" class="col-sm-2 col-form-label">Prazo do Contrato:</label>
                            <div class="input-group col-sm-4">
                                <select id="ativa-ctrt-prazo_contrato" name="prazo_contrato" class="custom-select custom-select-sm campo-obrigatorio" >
                                    <option value="">Selecione</option>
                                    <? for($x=60; $x>=12; $x = $x - 12){?>
                                    <option value="<?=$x?>" ><?=$x?></option>
                                    <? } ?>
            
                                </select>
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="font-size: 10px !important;">Meses</div>
                                </div>
                            </div>
                            <label for="nr_diavencimento" class="col-sm-2 col-form-label">Dia da Fatura:</label>
                            <div class="input-group col-sm-4">
                                <select id="ativa-ctrt-nr_diavencimento" name="nr_diavencimento" class="custom-select custom-select-sm campo-obrigatorio" >
                                    <option value="">Selecione</option>
                                    <? for($y=5; $y<=30; $y += 5){?>
                                    <option value="<?=$y?>" ><?=$y?></option>
                                    <? } ?>
            
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nr_day_finish" class="col-sm-2 col-form-label"><?=utf8_decode("Dias para Ativação")?></label>
                            <div class="col-sm-2">
                                <input type="number" class="form-control form-control-sm" name="nr_day_finish" id="ativa-ctrt-nr_day_finish"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="notafiscal" class="col-sm-2 col-form-label"><?=utf8_decode("Nota Fiscal")?></label>
                            <div class="col-sm-4">
                                <select name="notafiscal" id="ativa-ctrt-notafiscal" class="custom-select custom-select-sm campo-obrigatorio" >
                                    <option value="N" ><?=utf8_decode("Não")?></option>
                                    <option value="S" >Sim</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Valores Recorrentes -->
                    <div class="subitem-11 subitem col-md-12 border-bottom border-info pt-3">
                        <h6>Atendimento</h6>
                    </div>
                    <div class="col-md-12 subitem subitem-11 mt-2">
                        <div class="form-group row">
                            <label for="nr_sla" class="col-sm-2 col-form-label" title="<?=utf8_decode("Tempo para a solução da ocorrência.")?>">SLA:</label>
                            <div class="col-sm-4">
                                <select id="ativa-ctrt-nr_sla" name="nr_sla" class="custom-select custom-select-sm campo-obrigatorio w-50" title="<?=utf8_decode("Tempo para a solução da ocorrência.")?>">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_sla = executar('SELECT nr_sla, ds_sla FROM sla');
                                    
                                    foreach($res_sla as $value){
                                    ?>
                                        <option value="<?=$value['nr_sla']?>"><?=$value['ds_sla']?></option>

                                    <? } ?>
                                </select>
                            </div>
                            <label for="nr_tipo_atendimento" class="col-sm-1 col-form-label" title="<?=utf8_decode("Tempo de suporte.")?>">Tipo:</label>
                            <div class="col-sm-4" title="<?=utf8_decode("Tempo de suporte.")?>">
                                <select id="ativa-ctrt-nr_tipo_atendimento" name="nr_tipo_atendimento" class="custom-select custom-select-sm campo-obrigatorio w-50">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_tipo = executar('SELECT nr_tipo_atendimento, ds_tipo_atendimento FROM tipo_atendimento ORDER BY ds_tipo_atendimento ASC');
                                    
                                    foreach($res_tipo as $value){
                                    ?>
                                        <option value="<?=$value['nr_tipo_atendimento']?>"><?=$value['ds_tipo_atendimento']?></option>

                                    <? } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tempo_atendimento" class="col-sm-2 col-form-label" title="<?=utf8_decode("Tempo de para chegar ao local da ocorrência.")?>">Tempo:</label>
                            <div class="input-group col-sm-4" title="<?=utf8_decode("Tempo de para chegar ao local da ocorrência.")?>">
                                <input type="text" class="form-control form-control-sm" name="tempo_atendimento" id="ativa-ctrt-tempo_atendimento" /> 
                                <div class="input-group-prepend">
                                    <div class="input-group-text" style="font-size: 10px !important;">Hora(s)</div>
                                </div>
                            </div>
                            <label for="nr_hubpop_ctrt" class="col-sm-1 col-form-label">HubPop:</label>
                            <div class="col-sm-4">

                                <select id="ativa-ctrt-nr_hubpop_ctrt" name="nr_hubpop_ctrt" class="custom-select custom-select-sm campo-obrigatorio w-75">
                                    
                                <option value="">SELECIONE</option>
                                    <?
                                    $res_hub = executar('SELECT nr_hubpop, nm_hubpop FROM hubpop WHERE status_hubpop = "S" ORDER BY nm_hubpop');
                                    
                                    foreach($res_hub as $value){
                                    ?>
                                        <option value="<?=$value['nr_hubpop']?>"><?=$value['nm_hubpop']?></option>

                                    <? } ?>

                                </select>

                            </div>
                        </div>
                    </div>

                    <!-- Valores Recorrentes -->
                    <div class="subitem-22 subitem col-md-12 border-bottom border-info pt-3">
                        <h6>Valores do Projeto</h6>
                    </div>
                    <div class="subitem-22 subitem col-md-12 pt-2">
                        <div class="form-group row">
                            <label for="valor_produto" class="col-sm-2 col-form-label">Produto:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm w-50 moeda" name="valor_produto" id="ativa-ctrt-valor_produto" /> 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="valor_servico" class="col-sm-2 col-form-label"><?=utf8_decode("Serviço")?>:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm w-50 moeda" name="valor_servico" id="ativa-ctrt-valor_servico" /> 
                            </div>
                        </div>
                    </div>

                    <!-- Valores Recorrentes -->
                    <div class="subitem-33 subitem col-md-12 border-bottom border-info pt-3">
                        <h6>Valor Recorrente</h6>
                    </div>
                    <div class="col-md-12 subitem-33 subitem mt-2">
                        <div class="form-group row">
                            <label for="valor_install" class="col-sm-2 col-form-label"><?=utf8_decode("Instalação")?>:</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm w-75 moeda" name="valor_install" id="ativa-ctrt-valor_install" /> 
                            </div>
                            <label for="dt_vencimento_nfse" class="col-sm-2 col-form-label"><?=utf8_decode("Prev. Pagamento Instalação:")?></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control form-control-sm" name="dt_vencimento_nfse" id="ativa-ctrt-dt_vencimento_nfse" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nm_parcelas" class="col-sm-2 col-form-label"><?=utf8_decode("Cond. Pagamento Instalação:")?></label>
                            <div class="col-sm-3">
                                <select id="ativa-ctrt-nm_parcelas" name="nm_parcelas" class="custom-select custom-select-sm campo-obrigatorio">
                                    <option value="">SELECIONE</option>
                                    <?
                                    $res_condPgto = executar('SELECT nm_parcelas, nm_cond FROM cond_pgto ORDER BY nm_parcelas');
                                    
                                    foreach($res_condPgto as $value){
                                    ?>
                                        <option value="<?=$value['nm_parcelas']?>"><?=$value['nm_cond']?></option>

                                    <? } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="valor_mensal" class="col-sm-2 col-form-label">Mensal:</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control form-control-sm w-50 moeda" name="valor_mensal" id="ativa-ctrt-valor_mensal" /> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="ativa-ctrt-fechar-contrato" class="btn btn-secondary" data-dismiss="modal"><?=utf8_decode("Fechar")?></button>
                    <button type="button" id="ativa-ctrt-salvar-contrato" class="btn btn-primary" onclick="">
                        <span class="" id="charge-btn" role="status" aria-hidden="true"></span>
                        <?=utf8_decode("Salvar mudanças")?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- FIM Modal Ativar Contratos -->


<!-- Modal Reabilitar Contratos -->
<div class="modal fade" id="modalReabilitarContrato" role="dialog" aria-labelledby="TituloModalReabilitarContrato" aria-hidden="true">
    <div class="modal-dialog text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="TituloModalReabilitarContrato"><?=utf8_decode("Reabilitar Contrato")?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="form-contrato" id="form-contrato" action="../App_Pes/ControlCliente.php" method="POST">
                <div class="modal-body form-row pb-4" >
                    
                    <div class="col-md-12 pt-2">
                        <label class="col-sm-2 col-form-label">Motivo:</label>
                        <p id="total-caracteres" title="Minimo de caracteres" class="" style="margin-bottom: 0px; padding-left: 350px"><span id="contador-caracteres">0</span><span>/15 caracteres</span></p>
                        <div class="col-sm-12">
                            <textarea type="text" class="form-control form-control-sm" name="mensagem" id="ativa-ctrt-mensagem" ></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="reabilitar_nr_ctrt" value="" />
                </div>
            </form>
                <div class="modal-footer">
                    <button type="button" id="reabilita-ctrt-fechar-contrato" class="btn btn-secondary" data-dismiss="modal"><?=utf8_decode("Fechar")?></button>
                    <button type="button" id="reabilita-ctrt-salvar-contrato" class="btn btn-primary" disabled>
                        <span class="" id="charge-btn" role="status" aria-hidden="true"></span>
                        <?=utf8_decode("Salvar mudanças")?>
                    </button>
                </div>
        </div>
    </div>
</div>
<!-- FIM Modal Reabilitar Contratos -->


<!-- Modal Cancelar Contratos -->
<div class="modal fade" id="modalCancelarContrato" role="dialog" aria-labelledby="TituloModalCancelarContrato" aria-hidden="true">
    <div class="modal-dialog text-left" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="TituloModalCancelarContrato"><?=utf8_decode("Cancelar Contrato")?></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="form-contrato" id="form-contrato" action="../App_Pes/ControlCliente.php" method="POST">
                <div class="modal-body form-row pb-4" >
                    
                    <div class="col-md-12 pt-2">
                        <label class="col-sm-2 col-form-label">Motivo:</label>

                        <div class="col-sm-8">
                            <select name="cancelar_nr_tipo" class="custom-select custom-select-sm">
                                <option value="">SELECIONE</option>
                                <? $res_tipo_cancelado = executar('SELECT nr_tipo_cancelado, ds_tipo_cancelado FROM tipo_cancelado');
                                foreach($res_tipo_cancelado as $value){ ?>

                                    <option value="<?=$value['nr_tipo_cancelado']?>"><?=$value['ds_tipo_cancelado']?></option>

                                <? } ?>
                            </select>
                        </div>
                        
                        <p id="cancelar_total-caracteres" title="Minimo de caracteres" class="" style="margin-bottom: 0px; padding-left: 350px"><span id="cancelar_contador-caracteres">0</span><span>/15 caracteres</span></p>
                        <div class="col-sm-12">
                            <textarea type="text" class="form-control form-control-sm" name="cancelar_mensagem" id="ativa-ctrt-mensagem" ></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="cancelar_nr_ctrt" value="" />
                </div>
            </form>
                <div class="modal-footer">
                    <button type="button" id="cancela-ctrt-fechar-contrato" class="btn btn-secondary" data-dismiss="modal"><?=utf8_decode("Fechar")?></button>
                    <button type="button" id="cancela-ctrt-salvar-contrato" class="btn btn-primary" disabled>
                        <span class="" id="charge-btn" role="status" aria-hidden="true"></span>
                        <?=utf8_decode("Salvar mudanças")?>
                    </button>
                </div>
        </div>
    </div>
</div>
<!-- FIM Modal Cancelar Contratos -->


<!-- Modal LISTA Ordem de Serviço -->
<div class="modal fade" id="modalListaOrdemServico" tabindex="-1" role="dialog" aria-labelledby="modalListaOrdemServicoTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalListaOrdemServicoTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body form-row" id="bodyListaOrdemServico">
                <!-- INFORMAÇÕES DA LEAD -->
                <div class="col-sm-12">
                    <table class="table table-sm table-hover ">
                        <thead>
                            <tr class="table-light text-primary border" colspan="8">
                                <td scope="col" colspan="1"><?=utf8_decode("Código")?></td>
                                <td scope="col" colspan="1"><?=utf8_decode("Título")?></td>
                                <td scope="col" colspan="1">Abertura</td>
                                <td scope="col" colspan="1">Fechamento</td>
                                <td scope="col" colspan="1">Para</td>
                                <td scope="col" colspan="1">Encaminhado</td>
                                <td scope="col" colspan="1">Aberto Por</td>
                                <td scope="col" colspan="1"></td>
                            </tr>
                        </thead>
                        <tbody style="font-size: 12px;"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer row justify-content-center">
                <button type="button" data-toggle="modal" data-target="#modalOrdemServico" title="<?=utf8_decode("Adicionar nova Ordem de Serviço")?>" class="btn btn-sm btn-primary" id="btn-nova-os">
                    <?=utf8_decode("Nova O.S.")?>
                </button>
                <button type="button" class="btn btn-sm btn-outline-dark" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIM Modal LISTA Ordem de Serviço -->



<!-- Modal Ordem de Serviço -->
<div class="modal fade" id="modalOrdemServico" tabindex="-1" role="dialog" aria-labelledby="modalOrdemServicoTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalOrdemServicoTitle"><?=utf8_decode("Ordem de Serviço")?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body form-row" id="bodyOrdemServico" style="overflow: auto; height: 70vh;">
                
                <!-- INFORMAÇÕES DO CHAMADO -->
                <div id="contatos" class="col-md-12">
                    <p class="h5 text-muted mb-4" id="modalOSInfoLead"><?=utf8_decode("Chamado")?></p>
                </div>
                <div class="col-md-12">
                    <div class="form-group row">
                        <label for="os_dt_abertura" class="col-sm-2 col-form-label"><?=utf8_decode("Data de Abertura:")?></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control form-control-sm" id="os_dt_abertura" name="os_dt_abertura" value="" placeholder="" disabled>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group row">
                        <label for="os_aberto_por" class="col-sm-2 col-form-label"><?=utf8_decode("Aberto Por:")?></label>
                        <div class="col-sm-4">
                            <select class="custom-select custom-select-sm" id="os_aberto_por" name="os_aberto_por" required>
                                <option value="">SELECIONE</option>
                            <?
                                $array_func = executar('SELECT nr_func, nm_pessoa FROM func INNER JOIN pessoa ON func.nr_pessoa = pessoa.nr_pessoa ORDER BY nm_pessoa');
                                foreach($array_func as $func ){            
                                    ?>
                                    <option id="<?=$func['nr_func']?>" value="<?=$func['nr_func']?>"><?=$func['nm_pessoa']?></option>
                                    <?
                                }
                            ?>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary col-sm-2 field-editar-os" id="btn-mudar-dono" <?=$dono_os?>>Mudar dono</button>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group row">
                        <label for="os_depto_atual" class="col-sm-2 col-form-label"><?=utf8_decode("Departamento Atual:")?></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control form-control-sm" id="os_depto_atual" name="os_depto_atual" value="" placeholder="" disabled>
                        </div>

                        <label for="os_encaminhar" class="col-sm-1 col-form-label field-editar-os"><?=utf8_decode("Enviado Para:")?></label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control form-control-sm field-editar-os" id="os_encaminhar" name="os_encaminhar" value="" placeholder="" disabled>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 criar-os-2">
                    <div class="form-group row">
                        <label for="os_encaminhar_cria" class="col-sm-2 col-form-label"><?=utf8_decode("Encaminhar Para:")?></label>
                        <div class="col-sm-4">
                            <select class="custom-select custom-select-sm" id="os_encaminhar_cria" name="os_encaminhar_cria" required>
                                <option value="">SELECIONE</option>
                            <?
                                $array_depto = executar('SELECT nr_depto, nm_depto FROM depto ORDER BY nm_depto');
                                foreach($array_depto as $depto ){            
                                    ?>
                                    <option id="<?=$depto['nr_depto']?>" value="<?=$depto['nr_depto']?>"><?=$depto['nm_depto']?></option>
                                    <?
                                }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group row">
                        <label for="os_titulo" class="col-sm-2 col-form-label"><?=utf8_decode("Título:")?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" id="os_titulo" name="os_titulo" value="" placeholder="" required>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 criar-os-2">
                    <div class="form-group row">
                        <label for="os_mensagem" class="col-sm-2 col-form-label"><?=utf8_decode("Mensagem:")?></label>
                        <div class="col-sm-10">
                            <textarea class="form-control form-control-sm w-100" rows="4" id="os_mensagem" name="os_mensagem" value="" placeholder="" required> </textarea>
                        </div>
                    </div>
                </div>

                <div id="mensagens" class="col-md-12 field-editar-os ">
                    <p class="h5 text-muted mt-4"><?=utf8_decode("Histórico de Mensagens")?></p>
                </div>
                <div class="col-md-12 field-editar-os border border-info">
                    <table class="table table-sm" id="table-historico-os">

                    </table>
                </div>

                <!-- VALORES DE CONTROLE - INPUT HIDDEN -->

                <input type="hidden" name="login_nr_func" id="login_nr_func" value="<?=$_SESSION['login']['nr_func']?>"/>
                <input type="hidden" name="nr_ctrt_os" id="nr_ctrt_os" value=""/>
                <input type="hidden" name="nr_os" id="nr_os" value=""/>
                <input type="hidden" name="os_nr_depto" id="os_nr_depto" value=""/>
                <input type="hidden" name="os_status" id="os_status" value=""/>

                <!-- FIM - VALORES DE CONTROLE - INPUT HIDDEN -->
            </div>

            <div class="modal-footer field-editar-os row justify-content-center" id="footer-editar-os">
                <div id="os-ativa">
                    <button type="button" title="" class="btn btn-sm btn-danger" id="btn-fechar-os"></button>
    
                    <button type="button" data-toggle="modal" data-target="#modalMensagem" title="<?=utf8_decode("Encaminhar a Ordem de Serviço para um novo departamento")?>" class="btn btn-sm btn-outline-primary ml-5" id="btn-encaminhar-os">
                        <?=utf8_decode("Encaminhar")?>
                    </button>
    
                    <button type="button" data-toggle="modal" data-target="#modalMensagem" title="<?=utf8_decode("Adicionar nova mensagem na Ordem de Serviço")?>" class="btn btn-sm btn-outline-primary" id="btn-nova-mensagem">
                        <?=utf8_decode("Nova Mensagem")?>
                    </button>
                </div>
                
                <div id="os-fechada">
                    <button type="button" title="<?=utf8_decode("Reabilitar Ordem de Serviço")?>" class="btn btn-sm btn-outline-secondary" id="btn-reabilitar-os">
                        <?=utf8_decode("Reabilitar Ordem de Serviço")?>
                    </button>
                </div>

            </div>
            <div class="modal-footer criar-os-2 row justify-content-center">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="btnOSSalva" value=""><?=utf8_decode("Salvar")?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnOSCancela">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIM Modal Ordem de Serviço -->

<!-- Modal Mensagem/Encaminhar/Fechar -->
<div class="modal fade border border-info" id="modalMensagem" tabindex="-2" role="dialog" aria-labelledby="modalMensagemTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal bg-light" role="document">
        <div class="modal-content">
            <div class="modal-body form-row" id="bodyMensagem">
                <div class="form-group col-md-12 field-encaminhar">
                    <?=utf8_decode("Encaminhar Para:")?>
                    <select class="form-control form-control-sm w-100" id="os_encaminhar_mensagem" name="os_encaminhar_mensagem" required>

                    <option value="">SELECIONE</option>

                    <?
                    $array_depto = executar('SELECT nr_depto, nm_depto FROM depto ORDER BY nm_depto');
                    foreach($array_depto as $depto ){

                        
                        ?>
                        <option id="<?=$depto['nr_depto']?>" value="<?=$depto['nr_depto']?>"><?=$depto['nm_depto']?></option>
                        <?
                    }
                    ?>

                    </select>
                </div>
                <div class="form-group col-md-12">
                    <?=utf8_decode("Mensagem")?>
                    <textarea class="form-control form-control-sm w-100" rows="4" id="os_nova_mensagem" name="os_nova_mensagem" value="" placeholder="" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="salva-mensagem" value=""><?=utf8_decode("Salvar")?></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancela-mensagem">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- FIM Modal Mensagem/Encaminhar/Fechar -->



<!-- Modal UPLOAD arquivos -->
<div class="modal fade" id="modalUploadArquivo" tabindex="-1" role="dialog" aria-labelledby="modalUploadArquivoTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="modalUploadArquivoTitle"><?=utf8_decode("Importar Arquivos")?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body form-row pt-3" id="bodyUploadArquivo">
                <form id="formulario-upload" class="form col-md-12" enctype="multipart/form-data">
                    <div class="col-md-12 mt-3">
                        <div class="form-group row mb-2">
                            <label for="ds_arquivo" class="col-sm-2 pt-2" style="font-size: 12px;"><?=utf8_decode("Descrição:")?></label>
                            <div class="col-sm-10 w-100">
                                <input type="text" style="font-size: 12px;" class="form-control form-control-sm w-75" id="ds_arquivo" name="ds_arquivo">
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <div class="custom-file col-sm-10 mt-4 ml-4" style="font-size: 12px;">
                                <input type="file" class="custom-file-input" name="nm_arquivo" id="nm_arquivo" lang="pt">
                                <label class="custom-file-label" for="nm_arquivo" id="label-importa">Selecionar Arquivo</label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="action"  id="action-upload"  value="upload" />
                    <input type="hidden" name="type"    id="type-upload"    value="arquivo" />
                    <input type="hidden" name="nr_ctrt" id="nr_ctrt-upload" value="" />
                </form>
                <div title="Progress-bar: Upload File" class="progress w-100" style="height: 1px;">
                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="importar-arquivo" value=""><?=utf8_decode("Importar")?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelar-arquivo">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIM Modal UPLOAD arquivos -->


<!-- Modal DOWNLOAD arquivos -->
<div class="modal fade" id="modalDownloadArquivo" tabindex="-1" role="dialog" aria-labelledby="modalDownloadArquivoTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="modalDownloadArquivoTitle"><?=utf8_decode("Baixar Arquivos")?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body form-row pt-3" id="bodyDownloadArquivo">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col"><?=utf8_decode("Descrição")?></th>
                            <th scope="col" style="width: 15%"><?=utf8_decode("Ações")?></th>
                        </tr>
                    </thead>
                    <tbody id="tbodyModalDownload" style="font-size: 12px;">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer row justify-content-center">
                <!--button type="button" class="btn btn-primary" id="visualizar-arquivo" value=""><?=utf8_decode("Visualizar Todos")?></button-->
                
                <form id="download-all-files" action="../App_Pes/ControlCliente.php" method="POST">
                    <input type="hidden" name="action"  value="download-zip" />
                    <input type="hidden" name="type"    value="arquivo" />
                    <input type="hidden" name="nr_ctrt" value="" />
                    <button type="button" class="btn btn-primary" id="baixar-arquivo" value=""><?=utf8_decode("Baixar Todos")?></button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- FIM Modal DOWNLOAD arquivos -->


<!-- Modal Parcelas Financeiro -->
<div class="modal fade" id="modalParcelasFinanceiro" tabindex="-1" role="dialog" aria-labelledby="modalParcelasFinanceiroTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="modalParcelasFinanceiroTitle"><?=utf8_decode("Posição Financeira")?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body form-row pt-3" id="bodyDownloadArquivo">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th scope="col"><?=utf8_decode("Título")?></th>
                            <th scope="col"><?=utf8_decode("Parcela")?></th>
                            <th scope="col"><?=utf8_decode("Valor")?></th>
                            <th scope="col"><?=utf8_decode("Vencimento")?></th>
                            <th scope="col"><?=utf8_decode("Situação")?></th>
                        </tr>
                    </thead>
                    <tbody id="tbodyModalParcelasFinanceiro" style="font-size: 12px;">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer row justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!-- FIM Modal Parcelas Financeiro -->