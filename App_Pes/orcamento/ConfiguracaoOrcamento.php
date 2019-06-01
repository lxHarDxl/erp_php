<?
require_once('../config.php');
require_once('../conexao.php');
require_once('../funcoes.php');
require_once('../toolbarOrcamento.php');

if($_SESSION['login']['nr_func'] != 211){
    a(utf8_decode("Seu usuário não possui permissão para acessar esse formulário!"));
    redireciona("../Principal.php");
}

$title = utf8_decode("Configuração - Orçamento");
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
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

    <style>
        table thead {
            font-size: 12px !important;
        }
        table tbody {
            font-size: 10px !important;
        }
        table tbody select {
            font-size: 10px !important;
        }
    </style>
</head>
<body >
    <div id="cabecalho">
	        <?php require_once("../topo.php"); ?>
	</div>
		<?=toolBar($opcoes, $empresa)?>
	<div id="corpo" class="pt-4">
        <div class="container-fluid">
            <div class="container mx-auto">
                <div class="border border-primary">
                    <div class="text-light border border-primary rounded bg-primary pl-4 pt-2">
                        <h4><b><?=$title?></b></h4>
                    </div>
                    <div class="container table-responsible">
                        <table class="table table-sm table-hover mt-4 mb-4 w-75 mx-auto">
                            <thead class="bg-primary text-light">
                                <tr>
                                    <th scope="col">Departamento</th>
                                    <th scope="col"><?=utf8_decode("Colaborador Responsável")?></th>
                                    <th scope="col">Budget %</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                $orcamentos = executar("SELECT nr_centro_custo, nm_centro, nr_diretor, perc_orcamento FROM centro_custo WHERE situacao = 'A' ORDER BY nm_centro");
                                if($orcamentos){
                                    foreach($orcamentos as $v){
                                        $v['perc_orcamento'] = number_format($v['perc_orcamento'], 2, ",", ".");
                                ?>
                                    <tr id="nr_centro_custo_<?=$v['nr_centro_custo']?>">
                                        <td scope="row" class="pt-2 nm_depto"><?=$v['nm_centro']?></td>
                                        <td scope="row">
                                            <select name="nr_diretor" class="custom-select custom-select-sm nr_diretor">
                                                <option value=""></option>
                                                <?
                                                $info = executar("SELECT nr_func, nm_pessoa FROM func INNER JOIN pessoa ON func.nr_pessoa = pessoa.nr_pessoa INNER JOIN login l ON pessoa.nr_pessoa = l.nr_pessoa WHERE l.ativo = 'S' ");
                                                foreach($info as $value){
                                                    $selecao = $value['nr_func'] == $v['nr_diretor'] ? "selected" : "";
                                                ?>
                                                    <option value="<?=$value['nr_func']?>" <?=$selecao?>><?=$value['nm_pessoa']?></option>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td scope="row" class="pt-2">
                                            <input name="perc_orcamento" class="valor-orcamento form-group form-group-sm" value="<?=$v['perc_orcamento']?>" />
                                        </td>
                                        <td scope="row">
                                            <button type="button" class="btn btn-sm btn-success" onclick="configuracaoOrcamentoController.adiciona(<?=$v['nr_centro_custo']?>);">Gravar</button>
                                        </td>
                                    </tr>
                                <?

                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="last_id" value="<?=$id?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../App_Pes/js/ConfiguracaoOrcamento/controllers/ConfiguracaoOrcamentoController.js"></script>
    <script src="../App_Pes/js/ConfiguracaoOrcamento/models/ConfiguracaoOrcamentoModel.js"></script>
    <script>
        let configuracaoOrcamentoController = new ConfiguracaoOrcamentoController();
    </script>
    <script>
        $(function(){
            $('.valor-orcamento').mask('00,00%', {reverse: true});
        });
    </script>
</body>
</html>