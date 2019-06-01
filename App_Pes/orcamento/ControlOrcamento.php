<?
require_once('../config.php');
require_once('../conexao.php');
require_once('../funcoes.php');

$type   = strtolower(addslashes($_POST['type']));
$action = strtolower(addslashes($_POST['action']));

if($type == 'centro_custo'){
    if($action == 'gravar'){
        $chars = array("%", " ", ".");
        $nr_diretor      = $_POST['nr_diretor'];
        $nr_centro_custo = $_POST['nr_centro_custo'];
        $valor_orcamento = str_replace(",", ".", str_replace($chars, "", $_POST['valor_orcamento']));

        $sql = "UPDATE centro_custo SET perc_orcamento = $valor_orcamento, nr_diretor = $nr_diretor WHERE nr_centro_custo = $nr_centro_custo";
        $update_centro_custo = executar($sql);
        if($update_centro_custo){
            $mensagem = utf8_decode("Alteração concluída com sucesso.");
        } else {
            $mensagem = utf8_decode("Não foi possível fazer a alteração.");
        }

        echo $mensagem;
    }
}
?>