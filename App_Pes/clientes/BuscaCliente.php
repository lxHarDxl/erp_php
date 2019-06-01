<?php
require_once("../config.php");
require_once("../conexao.php");
require_once("../funcoes.php");
$hostname=gethostbyaddr($_SERVER['REMOTE_ADDR']);
$cor="#FFFCDE";

if(isset($_GET['keyword'])) {
    
    if($_GET['tipo'] == 'nome') {
        $keyword = anti_injection($_GET['keyword']);
		$where = 'WHERE nm_pessoa LIKE "%'.$keyword.'%" OR nm_fantasia LIKE "%'.$keyword.'%"';

    }else{
        $keyword = anti_injection($_GET['keyword']);
		$where = 'WHERE nr_cnpjcpf = "'.$keyword.'"';
    }
	
    $sql = 'SELECT nr_pessoa,nm_pessoa,nr_cnpjcpf,tp_pessoa FROM pessoa '.$where.' ORDER BY nm_pessoa';
    $data = executar($sql);

	if(!empty($data)) {
	
		$resultado = '
		<table width="100%" cellspacing="1" id="listagem" border="1">
		<tr>
			<th colspan="3"><img src="'.$iconetopo.'"/>&nbsp;Resultado da Pesquisa</th>
		</tr>
		<tr id="header">
   	    	<td>&nbsp;</td>
   	    	<td>CNPJ/CPF</td>
   	    	<td>Raz&atilde;o Social</td>
	    </tr>';

    foreach($data as $v) {
            $nr_cnpjcpf = ($v['tp_pessoa'] == 'F') ? mascara($v['nr_cnpjcpf'],'CPF') : mascara($v['nr_cnpjcpf'],'CNPJ');
			$resultado.= '
                <tr bgcolor="'.$cor.'">
				    <td align="center" width="5%">
					    <input type="radio" name="cliente" title="Inserir Cliente" onclick="pegaPessoa(\''.$v['nm_pessoa'].'\','.$v['nr_pessoa'].');" />
    				</td>
                    <td>'.$nr_cnpjcpf.'</td>
                    <td>'.$v['nm_pessoa'].'</td>
                </tr>';
			if($cor=="#FFFCDE"){$cor="#FFFFFF";}else{$cor="#FFFCDE";}
	}

    } else {
        $resultado.= '
        	<tr>
        		<td colspan="3">&nbsp;</td>
        	</tr>
            <tr>
                <td id="notfound" colspan="3">Nenhum registro encontrado</td>
            </tr>
			<tr>
        		<td colspan="3">&nbsp;</td>
        	</tr>';
    }
	$resultado.= '</table>';
}
?>

<html>
<head>
<title><?php echo $titulo . " " . $versao;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Content-Language" content="pt-BR">
<meta http-equiv="Cache-control" content="no-cache, must-revalidate">
<meta http-equiv="expires" content="-1">
<link HREF="<?php echo $css;?>" REL="stylesheet" TYPE="text/css">
<script language="JavaScript" src="<?php echo $jscript;?>"></script>
<script language="JavaScript" src="<?php echo $jsfuncao;?>"></script>
</head>
<body onLoad="document.formulario.keyword.focus();">
<form name="formulario" method="get">
<table cellpadding="2" cellspacing="0" width="100%" id="formconsulta">
<tr>
	<th colspan="3"><img src="<?php echo $iconetopo;?>"/>&nbsp;Pesquisa de Pessoa</th>
</tr>
<tr>
	<td id="label_form">Palavra Chave</td>
	<td id="label_form">Tipo:</td>
	<td id="label_form">&nbsp;</td>
</tr>
<tr>
	<td><input type="text" name="keyword" size="30" value="<?=$keyword?>" obrigatorio="true" label="Palavra Chave"></td>
	<td>
		<select name="tipo">
            <option value="nome">RAZ&Atilde;O SOCIAL</option>
            <option value="cpf">CPF/CNPJ</option>
        </select>
	</td>
	<td><input type="submit" name="pesquisa" value="Pesquisar" class="botao"></td>
</tr>
<tr>
	<td colspan="3">&nbsp;</td>
</tr>
</table>
</form>

<table>
<tr>
	<td colspan="3">
		<div style="overflow:auto;height:225px;width:496px;">
		<?=$resultado;?>
		</div>
	</td>
</tr>
</table>
<script language="JavaScript" src="<?=$jsgatilho;?>"></script>
</body>
</html>
