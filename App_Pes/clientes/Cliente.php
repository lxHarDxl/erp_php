<?
require_once("../config.php");
require_once("../conexao.php");
require_once("../funcoes.php");
require_once("../toolbarPrincipal.php");

$nr_pessoa = $_GET['nr_pessoa'];

if(!empty($nr_pessoa)){
    $sql = "SELECT p.*, c.* FROM pessoa p INNER JOIN cliente c ON p.nr_pessoa = c.nr_pessoa WHERE p.nr_pessoa = $nr_pessoa";

    $cliente = executar($sql);

    $cliente[0]['nr_cnpjcpf'] = strlen($cliente[0]['nr_cnpjcpf']) > 11 ? mascara($cliente[0]['nr_cnpjcpf'], 'cnpj') : mascara($cliente[0]['nr_cnpjcpf'], 'cpf');
    $cliente[0]['nr_ierg'] = strlen($cliente[0]['nr_ierg']) > 10 ? mascara($cliente[0]['nr_ierg'], 'ie') : mascara($cliente[0]['nr_ierg'], 'rg');
} else {
    $disable_button = "title='".utf8_decode("Botão será desbloqueado ao salvar o formulário de clientes.")."' disabled";

    $nr_pessoa = false;
}
//var_dump($_SERVER);
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=$titulo.' '.$versao?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="Content-Language" content="pt-BR">
	<meta http-equiv="Cache-control" content="no-cache, must-revalidate">
	<meta http-equiv="expires" content="-1">
	<link HREF="<?=$css?>" REL="stylesheet" TYPE="text/css">
	<link HREF="<?=$caminho?>/menu.css" REL="stylesheet" TYPE="text/css">
	<link rel="stylesheet" href="<?=$caminho?>/jquery.tabs.css" type="text/css" media="print, projection, screen">
	<script language="JavaScript"  src="<?=$jscript?>"></script>
    <script language="JavaScript"  src="<?=$jsfuncao?>"></script>
    
    <link href="<?=$caminho?>/obj_js/jquery-ui.css" rel="stylesheet">
    <script language="JavaScript" src="<?=$caminho?>/obj_js/jquery-ui.js"></script>
	<script type="text/javascript" src="<?=$caminho?>/obj_js/jquery.tabs.pack.js"></script>
	<script language="JavaScript" src="<?=$caminho?>/obj_js/jquery.dimensions.js"></script>
	
    <script type="text/javascript" src="<?=$caminho?>/jquery/jquery-3.3.1.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.11/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.11/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script type="text/javascript" src="http://code.jquery.com/qunit/qunit-1.11.0.js"></script>
  
    <script type="text/javascript" src="../obj_js/jQuery-Mask-Plugin-master/test/sinon-1.10.3.js"></script>
    <script type="text/javascript" src="../obj_js/jQuery-Mask-Plugin-master/test/sinon-qunit-1.0.0.js"></script>
    <script type="text/javascript" src="../obj_js/jQuery-Mask-Plugin-master/src/jquery.mask.js"></script>

    <script type="text/javascript" src="js/Cliente/formCliente.js"></script>
    <script type="text/javascript" src="js/Cliente/formClienteEndereco.js"></script>
    <script type="text/javascript" src="js/Cliente/formClienteContato.js"></script>
    <script type="text/javascript" src="js/Cliente/formClienteContrato.js"></script>
    <script type="text/javascript" src="js/Cliente/formClienteAtivaContrato.js"></script>
    <script type="text/javascript" src="js/Cliente/formClienteOS.js"></script>
    <script type="text/javascript" src="js/Cliente/formClienteUpload.js"></script>
    <script type="text/javascript" src="js/Cliente/formClienteDownload.js"></script>
    <script type="text/javascript" src="js/Cliente/formClienteListaMaterial.js"></script>
    <script type="text/javascript" src="js/Cliente/formClienteFinanceiro.js"></script>

    <style>
        #botoes {
            padding-top: -20px;
        }

        #info-cliente{
            font-size: 10px !important;
            margin-bottom: 4px;
        }
        #info-cliente input{
            height: -50% !important;
        }

        #modalContato {
            font-size: 10px !important;
            margin-bottom: 4px;
        }
        #modalContrato {
            font-size: 10px !important;
            margin-bottom: 4px;
        }
        #modalAtivarContrato {
            font-size: 10px !important;
            margin-bottom: 4px;
        }
        #modalEndereco {
            font-size: 10px !important;
            margin-bottom: 4px;
        }
        #modalOrdemServico{
            font-size: 10px !important;
            margin-bottom: 4px;
        }
        
        .form-group select{
            font-size: 10px !important;
            height: -50% !important;
        }
        .overflow-info-cliente{
            height: 100px;
            overflow: auto;
        }
        .overflow-info-cliente-2{
            height: 150px;
            overflow: auto;
        }
        .overflow-info-cliente table, .overflow-info-cliente-2 table{
            font-size: 10px;
        }
        .riscado {
            text-decoration: line-through;
        }
    </style>
</head>
<body class="bg-white">
        <script>
            $(function(){
                
                preencheTabelaContratos(<?=$nr_pessoa == false ? 0 : $nr_pessoa?>, '');
            })
        </script>
    <div id="cabecalho">
	        <?php require_once("../topo.php"); ?>
	</div>
		<?=toolBar($opcoes, $empresa)?>
	<div id="corpo" class="">
    <br />

    <div class="container-fluid">

        <div id="form-body" class="px-3">
            <h3 class="pl-3 text-muted"><?=utf8_decode("Formulário de Clientes")?></h3>
            <form id="form-pessoa" action="../App_Pes/ControlCliente.php" method="POST" class=" p-3 shadow border border-info bg-light">
                <div id="info-cliente" class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
						    <label for="nm_pessoa" class="col-sm-2 col-form-label"> Cliente: </label>
						    <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm campo-obrigatorio" id="nm_pessoa" name="nm_pessoa" value="<?=$cliente[0]['nm_pessoa']?>" placeholder="">
						    </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group row">
						    <label for="nr_cnpjcpf" class="col-sm-2 col-form-label"> CPF/CNPJ: </label>
						    <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm campo-obrigatorio" id="nr_cnpjcpf" name="nr_cnpjcpf" value="<?=$cliente[0]['nr_cnpjcpf']?>" placeholder="">
						    </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group row">
						    <label for="nr_ierg" class="col-sm-2 col-form-label"> RG/I.E.: </label>
						    <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm" id="nr_ierg" name="nr_ierg" value="<?=$cliente[0]['nr_ierg']?>" placeholder="">
						    </div>
                        </div>
                    </div>
                    <div class="col-md-5 pt-2">
                        <h6><?=utf8_decode("Endereços")?></h6>
                        <div class="border border-info">
                            <div class="overflow-info-cliente p-2">
                                <table class="table table-sm table-hover bg-white">
                                    <?
                                    if($nr_pessoa != false){
                                        $dados_endereco = executar('SELECT * FROM ender WHERE nr_pessoa = '.$nr_pessoa);
                                    } else {
                                        $dados_endereco = false;
                                    }
                                    if($dados_endereco){
                                        foreach($dados_endereco as $ender){
                                            $logradouro = $ender['tp_logradouro'].' '.$ender['nm_logradouro'].', '.$ender['nr_logradouro'];
                                            ?>
                                            <tr colspan="5">
                                                <td colspan="1"><?=$logradouro?></td>
                                                <td colspan="1"><?=$ender['nm_bairro']?></td>
                                                <td colspan="1"><?=$ender['nm_cidade']?></td>
                                                <td colspan="1"><?=$ender['nm_estado']?></td>
                                                <td colspan="1"><img src="../imagens/icones/edit_rounded.ico" class="editar-endereco" onclick="alteraEndereco(<?=$nr_pessoa?>, <?=$ender['nr_ender']?>);" data-toggle="modal" data-target="#modalEndereco" title="<?=utf8_decode("Editar Endereço")?>" alt="Edição de contatos" /></td>
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
                            <div class="row justify-content-md-center mt-2">
                                <button type="button" class="mb-2 btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modalEndereco"  id="novo-endereco" onclick="novoEndereco(<?=$nr_pessoa?>);" <?=$disable_button?>>  <?=utf8_decode("Novo Endereço")?></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 pt-2">
                        <h6>Contatos</h6>
                        <div class="border border-info">
                            <div class="overflow-info-cliente p-2">
                                <table class="table table-sm table-hover bg-white">
                                    <?
                                    if(!empty($nr_pessoa)){
                                        $dados_telefone = executar('SELECT ctt.nr_ctt, nm_ctt, nr_ddd, nr_telefone, nr_dddcel, nr_celular, ds_telefone FROM tel JOIN ctt USING(nr_ctt) WHERE ctt.nr_pessoa = '.$nr_pessoa);
                                    } else {
                                        $dados_telefone = false;
                                    }
                                    if($dados_telefone){
                                        foreach($dados_telefone as $tel){
                                            //var_dump($tel);
                                            if(!empty($tel['nr_telefone'])){
                                                $telefone = $tel['nr_ddd'].$tel['nr_telefone'];

                                                if(strlen($telefone) <= 10){
                                                    $telefone = mascara($telefone, 'telefone');
                                                } else {
                                                    $telefone = substr($telefone, 0, 4) == "0800" ? mascara($telefone, 'telefone-0800') : mascara($telefone, 'celular');
                                                }
                                            }else{
                                                $telefone = "";
                                            }

                                            if(!empty($tel['nr_celular'])){
                                                $celular = $tel['nr_dddcel'].$tel['nr_celular'];

                                                if(strlen($celular) <= 10){
                                                    $celular = mascara($celular, 'telefone');
                                                } else {
                                                    $celular = substr($celular, 0, 4) == "0800" ? mascara($celular, 'telefone-0800') : mascara($celular, 'celular');
                                                }
                                            }else{
                                                $celular = "";
                                            }
                                            ?>
                                            <tr colspan="4">
                                                <td colspan="1"><?=$tel['nm_ctt']?></td>
                                                <td colspan="1"><?=$telefone?></td>
                                                <td colspan="1"><?=$celular?></td>
                                                <td colspan="1"><?=$tel['ds_telefone']?></td>
                                                <td colspan="1" class="align-right"><img src="../imagens/icones/edit_rounded.ico" class="editar-contato" data-toggle="modal" data-target="#modalContato" title="Editar Contato" onclick="alteraContato(<?=$nr_pessoa?>, <?=$tel['nr_ctt']?>);" alt="Edição de contatos" /></td>
                                            </tr>

                                            <?
                                        }
                                    } else {
                                        ?>
                                        <tr colspan="4">
                                            <td colspan="4" class="text-danger text-center"><b><?=utf8_decode("Não foi possível encontrar nenhum contato cadastrado.")?></b></td>
                                        </tr>
                                        <?
                                    }
                                    ?>
                                </table>
                            </div>
                            <div class="row justify-content-md-center mt-2">
                                <button type="button" class="mb-2 btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modalContato"  id="novo-contato" onclick="novoContato(<?=$nr_pessoa?>);" <?=$disable_button?>> Novo Contato </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 pt-4 ml">
                        <div class="form-group row">
                            <label for="nr_empresa" class="col-sm-2 col-form-label"> Regional: </label>
                            <div class="col-sm-10">
                                <select id="nr_empresa" name="nr_empresa" class="custom-select custom-select-sm w-75 campo-obrigatorio">
                                    <option value=""></option>
                                    <?
                                    $empresa = executar("SELECT nr_empresa, nm_fantasia FROM empresa ORDER BY nm_fantasia");
                                    foreach($empresa as $value){
                                        $selected = $value['nr_empresa'] == $cliente[0]['nr_empresa'] ? "selected" : "";
                                        ?>
                                        <option id="<?=$value['nr_empresa']?>" value="<?=$value['nr_empresa']?>" <?=$selected?>> <?=$value['nm_fantasia']?> </option>
                                        <?
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nr_colaborador" class="col-sm-2 col-form-label"> Consultor: </label>
                            <div class="col-sm-10">
                                <select id="nr_colaborador" name="nr_colaborador" class="custom-select custom-select-sm w-75 campo-obrigatorio">
                                    <option value=""></option>
                                    <?
                                    $colaborador = executar("SELECT nr_func, UPPER(nm_pessoa) as nm_pessoa, ativo FROM func JOIN pessoa USING(nr_pessoa)JOIN login USING(nr_pessoa) WHERE ativo='S' ORDER BY nm_pessoa");
                                    foreach($colaborador as $value){
                                        $selected = $value['nr_func'] == $cliente[0]['nr_colaborador'] ? "selected" : "";
                                        ?>
                                        <option id="<?=$value['nr_func']?>" value="<?=$value['nr_func']?>" <?=$selected?>> <?=$value['nm_pessoa']?> </option>
                                        <?
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nr_revendedor" class="col-sm-2 col-form-label"> Revenda: </label>
                            <div class="col-sm-10">
                                <select id="nr_revendedor" name="nr_revendedor" class="custom-select custom-select-sm w-75">
                                    <option value=""></option>
                                    <?
                                    $empresa = executar("SELECT nr_revendedor, revendedor_nome FROM revendedores ORDER BY revendedor_nome");
                                    foreach($empresa as $value){
                                        $selected = $value['nr_revendedor'] == $cliente[0]['nr_revendedor'] ? "selected" : "";
                                        ?>
                                        <option id="<?=$value['nr_revendedor']?>" value="<?=$value['nr_revendedor']?>" <?=$selected?>> <?=$value['revendedor_nome']?> </option>
                                        <?
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="nr_hubpop" class="col-sm-2 col-form-label"> HubPop: </label>
                            <div class="col-sm-10">
                                <select id="nr_hubpop" name="nr_hubpop" class="custom-select custom-select-sm w-75 campo-obrigatorio">
                                    <option value=""></option>
                                    <?
                                    $hubpop = executar("SELECT nr_hubpop, CONCAT(nm_hubpop, '(', nm_apelido, ')') as lead_hubpop_br FROM hubpop WHERE status_hubpop = 'S' ORDER BY nm_hubpop");
                                    foreach($hubpop as $value){
                                        $selected = $value['nr_hubpop'] == $cliente[0]['nr_hubpop'] ? "selected" : "";
                                        ?>
                                        <option id="<?=$value['nr_hubpop']?>" value="<?=$value['nr_hubpop']?>" <?=$selected?>> <?=$value['lead_hubpop_br']?> </option>
                                        <?
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 pt-2">
                        <h6 class="d-inline">Contratos/Propostas</h6> 
                        <select id="nr_status_contrato" name="nr_status_contrato" class="custom-select custom-select-sm w-25 ml-3 mb-2" onchange="preencheTabelaContratos(<?=$nr_pessoa?>, $(this).val());" <?=$disable_button?>>
                            <option value="">TODOS</option>
                            <?
                            $status_contrato = executar("SELECT nr_status_contrato, ds_status_contrato FROM status_contrato ORDER BY ds_status_contrato");
                            foreach($status_contrato as $value){
                                ?>
                                <option id="<?=$value['nr_status_contrato']?>" value="<?=$value['nr_status_contrato']?>"> <?=$value['ds_status_contrato']?> </option>
                                <?
                            }
                            ?>
                            <option value="leads">LEADS</option>
                        </select>
                        
                        <div class="border border-info">
                            <div class="overflow-info-cliente-2 p-2">
                                <table class="table table-sm table-hover bg-white">
                                    <tbody id="tbody-contratos">

                                    </tbody>
                                </table>
                            </div>
                            <div class="row justify-content-md-center mt-2">
                                <button type="button" class="mb-2 btn btn-sm btn-outline-info" data-toggle="modal" data-target="#modalContrato"  id="novo-contrato" onclick="novoContrato(<?=$nr_pessoa?>);" <?=$disable_button?>> Novo Contrato </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-md-center mt-2">
                    <button type="button" class="btn btn-sm btn-primary" id="gravar-cliente" onclick="gravaCliente(<?=$nr_pessoa?>);"> Gravar </button>
                    <button type="button" class="ml-3 btn btn-sm btn-secondary" id="cancelar-cliente" onclick="voltar(<?=$nr_pessoa?>);" > Cancelar </button>
                </div>
            </form>
            <? require_once("../modais/modaisCliente.php"); ?>
        </div>
    </div>
    <script>
        function voltar(nr_pessoa){
            if(nr_pessoa){
                window.location="../App_Pes/ListaCliente.php";
            } else {
                window.location="../App_Pes/CnsCliente.php";
            }
        }
    </script>
</body>