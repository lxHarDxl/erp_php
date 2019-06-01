<?
require_once("../config.php");
require_once("../conexao.php");
require_once("../funcoes.php");
require_once("../evm_mailer.php");
require_once("../includes/fckeditor/fckeditor.php");
require_once("../upload_class.php");

$action = strtolower($_POST['action']);
$type   = strtolower($_POST['type']);

// echo "<br/>Action: $action";
// echo "<br/>Type: $type";

$chars = array("-", "_", "/", ".", ",", "+", "=", "?", ":", ";", "(", ")", " ");

if($type == "cliente"){

    $nr_cnpjcpf     = str_replace($chars, "", $_POST['nr_cnpjcpf']);
    $nr_ierg        = str_replace($chars, "", $_POST['nr_ierg']);
    $nm_pessoa      = utf8_decode($_POST['nm_pessoa']);
    $nr_empresa     = $_POST['nr_empresa'];
    $nr_colaborador = $_POST['nr_colaborador'];
    $nr_revendedor  = empty($_POST['nr_revendedor']) ? "default" : $_POST['nr_revendedor'];
    $nr_hubpop      = $_POST['nr_hubpop'];

    $tp_pessoa = strlen($nr_cnpjcpf) > 11 ? "J" : "F";

    if($action == "insere"){
        $nErro = 0;
        $mensagem;

        $where_svc = !empty($nr_ierg) ? "OR nr_ierg = '$nr_ierg'" : "";
        $select_verifica_cadastro = executar("SELECT nr_pessoa FROM pessoa WHERE nr_cnpjcpf = '$nr_cnpjcpf' $where_svc");

        if($select_verifica_cadastro) {
            $nr_pessoa = $select_verifica_cadastro[0][0];
            $tipo_documento = $tp_pessoa == "J" ? "CNPJ/I.E." : "CPF/RG";

            $mensagem = utf8_encode("Nao foi possivel finalizar o cadastro! \nMotivo: $tipo_documento ja cadastrado no sistema \n \nDeseja visualizar o cadastro?");
            $nErro = 1;

        } else {
            executar("BEGIN");

            $sql_pessoa = "INSERT INTO pessoa (nm_pessoa, nm_fantasia, nr_cnpjcpf, nr_ierg, tp_pessoa, reter_pis, reter_cofins, reter_inss, reter_ir, reter_csll) VALUES ('$nm_pessoa', '$nm_pessoa', '$nr_cnpjcpf', '$nr_ierg', '$tp_pessoa', 'N', 'N', 'N', 'N', 'N')";
            $insert_pessoa = executar($sql_pessoa);
    
            if($insert_pessoa){
                $nr_pessoa = executar("SELECT last_insert_id() FROM pessoa");
                $nr_pessoa = $nr_pessoa[0][0];
    
                $sql_cliente = "INSERT INTO cliente (nr_pessoa, nr_tipo_cliente, ds_status, dt_cadastro, nr_colaborador, nr_hubpop, nr_empresa, nr_revendedor) VALUES ($nr_pessoa, 1, 'A', now(), $nr_colaborador, $nr_hubpop, $nr_empresa, $nr_revendedor)";
                $insert_cliente = executar($sql_cliente);
    
                if(!$insert_cliente){
                    $mensagem .= utf8_decode("Não foi possível inserir o cliente! \n Erro na tabela: pessoa \n Verifique as informações: Nome do Cliente, CPF/CNPJ e I.E./RG.");
                    $nErro = 2;
                } else {
                    $nr_cliente = executar("SELECT last_insert_id() FROM cliente");
                    $nr_cliente = $nr_cliente[0][0];

                    $insert_gerenteconta = executar("INSERT INTO gerenteconta (nr_func, nr_cliente) VALUES ( $nr_colaborador, $nr_cliente)");
                }
    
            } else {
                $mensagem .= utf8_decode("Não foi possível inserir o cliente! \n Erro na tabela: cliente \n Verifique as informações: Colaborador, Revendedor, HubPop e Regional.");
                $nErro = 2;
            }
        }

        if($nErro == 0){
            executar("COMMIT");
            $mensagem = "Cliente inserido com sucesso!";
        } else if($nErro == 2) {
            executar("ROLLBACK");
        }

        $erro = $nErro;
        $dados = array('mensagem' => $mensagem,
                        'erro' => $erro,
                        'nr_pessoa' => $nr_pessoa);  

        echo json_encode($dados);
    }
    
    if($action == "atualiza"){
        $nr_pessoa = $_POST['nr_pessoa'];
        $nErro = 0;
        $mensagem;

        executar("BEGIN");

        $sql_pessoa = "UPDATE pessoa SET nm_pessoa = '$nm_pessoa', nr_cnpjcpf = '$nr_cnpjcpf', nr_ierg = '$nr_ierg', tp_pessoa = '$tp_pessoa' WHERE nr_pessoa = $nr_pessoa";
        $update_pessoa = executar($sql_pessoa);

        if($update_pessoa){
            $sql_cliente = "UPDATE cliente SET nr_colaborador = $nr_colaborador, nr_hubpop = $nr_hubpop, nr_empresa = $nr_empresa, nr_revendedor = $nr_revendedor WHERE nr_pessoa = $nr_pessoa";
            $update_cliente = executar($sql_cliente);

            if(!$update_cliente){
                $mensagem .= utf8_decode("Não foi possível alterar o cliente! \n Erro na tabela: pessoa \n Verifique as informações: Nome do Cliente, CPF/CNPJ e I.E./RG.");
                $nErro = 2;
            } else {
                $update_ctrt = executar("UPDATE ctrt SET nr_empresa = $nr_empresa WHERE nr_pessoa = $nr_pessoa");
            }

        } else {
            $mensagem .= utf8_decode("Não foi possível alterar o cliente! \n Erro na tabela: cliente \n Verifique as informações: Colaborador, Revendedor, HubPop e Regional.");
            $nErro = 2;
        }


        if($nErro == 0){
            executar("COMMIT");
            $mensagem = "Cliente atualizado com sucesso!";
        } else if($nErro == 2) {
            executar("ROLLBACK");
        }

        $dados = array('mensagem' => $mensagem,
                        'erro' => $nErro,
                        'nr_pessoa' => $nr_pessoa);  

        echo json_encode($dados);
    }
}

if($type == "endereco"){
    $nr_cep         = str_replace($chars, "", $_POST['nr_cep']);
    $tp_logradouro  = $_POST['tp_logradouro'];
    $nm_logradouro  = utf8_decode($_POST['nm_logradouro']);
    $nr_logradouro  = $_POST['nr_logradouro'];
    $ds_complemento = utf8_decode($_POST['ds_complemento']);
    $nm_bairro      = utf8_decode($_POST['nm_bairro']);
    $nm_cidade      = utf8_decode($_POST['nm_cidade']);
    $nm_estado      = utf8_decode($_POST['nm_estado']);
    $ds_logradouro  = utf8_decode($_POST['ds_logradouro']);

    if($action == "insere"){
        $nr_pessoa = $_POST['nr_pessoa'];
        $nErro = 0;
        $mensagem;

        executar("BEGIN");

        $sql_ender  = "INSERT INTO ender (nr_pessoa, tp_logradouro, nm_logradouro, nr_logradouro, ds_complemento, nr_cep, nm_bairro, nm_cidade, nm_estado, ds_logradouro) VALUES ($nr_pessoa, '$tp_logradouro', '$nm_logradouro', '$nr_logradouro', '$ds_complemento', '$nr_cep', '$nm_bairro', '$nm_cidade', '$nm_estado', '$ds_logradouro')";
        $insert_ender = executar($sql_ender);

        if(!$insert_ender){
            executar("ROLLBACK");

            $nErro += 1;
            $mensagem = utf8_encode("Nao foi possivel inserir o endereco! Entre em contato com o suporte.");
        } else {
            executar("COMMIT");

            $mensagem = utf8_encode("Endereco inserido com sucesso!");
        }

        $dados = array('mensagem' => $mensagem,
                        'erro' => $nErro,
                        'nr_pessoa' => $nr_pessoa);  

        echo json_encode($dados);
    }

    if($action == "atualiza"){
        $nr_pessoa = $_POST['nr_pessoa'];
        $nr_ender  = $_POST['nr_ender'];
        $nErro = 0;
        $mensagem;

        executar("BEGIN");

        $sql_ender  = "UPDATE ender SET tp_logradouro = '$tp_logradouro', nm_logradouro = '$nm_logradouro', nr_logradouro = '$nr_logradouro', ds_complemento = '$ds_complemento', nr_cep = '$nr_cep', nm_bairro = '$nm_bairro', nm_cidade = '$nm_cidade', nm_estado = '$nm_estado', ds_logradouro = '$ds_logradouro' WHERE nr_ender = $nr_ender";
        $update_ender = executar($sql_ender);

        if(!$update_ender){
            executar("ROLLBACK");

            $nErro += 1;
            $mensagem = utf8_encode("Nao foi possivel atualizar o endereco! Entre em contato com o suporte.");
        } else {
            executar("COMMIT");

            $mensagem = utf8_encode("Endereco atualizado com sucesso!");
        }

        $dados = array('mensagem' => $mensagem,
                        'erro' => $nErro,
                        'nr_pessoa' => $nr_pessoa,
                        'sql_mensagem' => utf8_decode($sql_ender));  

        echo json_encode($dados);
    }

    if($action == "selecionar"){
        $nr_ender = $_POST['nr_ender'];

        $sql = "SELECT * FROM ender WHERE nr_ender = $nr_ender";
        $select_ender = executar($sql);

        if($select_ender){
            $ender = array();

            $ender['nr_cep']         = mascara($select_ender[0]['nr_cep'], 'cep');
            $ender['tp_logradouro']  = utf8_encode($select_ender[0]['tp_logradouro']);
            $ender['nm_logradouro']  = utf8_encode($select_ender[0]['nm_logradouro']);
            $ender['nr_logradouro']  = utf8_encode($select_ender[0]['nr_logradouro']);
            $ender['ds_complemento'] = utf8_encode($select_ender[0]['ds_complemento']);
            $ender['nm_bairro']      = utf8_encode($select_ender[0]['nm_bairro']);
            $ender['nm_cidade']      = utf8_encode($select_ender[0]['nm_cidade']);
            $ender['nm_estado']      = utf8_encode($select_ender[0]['nm_estado']);
            $ender['ds_logradouro']  = utf8_encode($select_ender[0]['ds_logradouro']);
    
            echo json_encode($ender);
        }else{
            echo json_encode(array('sql' => $sql));
        }  
    }
    
}

// FUNCIONALIDADES REFERENTE AOS CONTATOS
if($type == "contato"){
    $nr_telefone = str_replace($chars, '', $_POST['nr_telefone']);
    $nr_celular  = str_replace($chars, '', $_POST['nr_celular']);
    $nm_ctt      = utf8_decode($_POST['nm_ctt']);
    $nr_ramal    = utf8_decode($_POST['nr_ramal']);
    $ds_email    = utf8_decode($_POST['ds_email']);
    $ds_site     = utf8_decode($_POST['ds_site']);
    $ctt_skype   = utf8_decode($_POST['ctt_skype']);
    $ctt_genero  = $_POST['ctt_genero'];
    $ctt_funcao  = $_POST['ctt_funcao'];
    $ds_telefone = $_POST['ds_telefone'];

    if($action == "insere"){
        $nr_pessoa = $_POST['nr_pessoa'];
        $nErro = 0;
        $mensagem;
        $mensagem_sql = "";

        executar("BEGIN");

        $sql_ctt = "INSERT INTO ctt (nr_pessoa, nm_ctt, ds_email, ctt_funcao, ctt_skype, ctt_genero, ds_site) VALUES ($nr_pessoa, '$nm_ctt', '$ds_email', '$ctt_funcao', '$ctt_skype', '$ctt_genero', '$ds_site')";
        $insert_ctt = executar($sql_ctt);

        if($insert_ctt){
            $nr_ctt = executar("SELECT last_insert_id() FROM ctt");
            $nr_ctt = $nr_ctt[0][0];

            $nr_ddd = !empty($nr_telefone) ? substr($nr_telefone, 0, 2) : "";
            $nr_tel = !empty($nr_telefone) ? substr($nr_telefone, 2) : "";
            $nr_dddcel = !empty($nr_celular) ? substr($nr_celular, 0, 2) : "";
            $nr_telcel = !empty($nr_celular) ? substr($nr_celular, 2) : "";

            $sql_tel  = "INSERT INTO tel (nr_pessoa, nr_ctt, nr_telefone, nr_ddd, nr_dddcel, nr_celular, ds_telefone, nr_ramal) VALUES ($nr_pessoa, $nr_ctt, '$nr_tel', '$nr_ddd', '$nr_dddcel', '$nr_telcel', '$ds_telefone', '$nr_ramal')";
            $insert_tel = executar($sql_tel);

            if(!$insert_tel){
                $nErro += 1;
                $mensagem = utf8_encode("Nao foi possivel inserir os telefones! Entre em contato com o suporte.");
                $mensagem_sql = utf8_decode($sql_tel);
            }

        } else {
            $nErro += 1;
            $mensagem .= utf8_decode("Não foi possível inserir o contato! \n Erro na tabela: ctt \n Verifique as informações: Nome, Email, Função, Skype e Gênero.");
            $mensagem_sql = utf8_decode($sql_ctt);
        }

        if($nErro == 0){
            executar("COMMIT");
            $mensagem = utf8_decode("Contato inserido com sucesso!");
        } else {
            executar("ROLLBACK");
        }

        $dados = array('mensagem' => $mensagem,
                        'erro' => $nErro,
                        'nr_pessoa' => $nr_pessoa,
                        'mensagem_sql' => $mensagem_sql);  

        echo json_encode($dados);
    }

    if($action == "atualiza"){
        $nr_pessoa = $_POST['nr_pessoa'];
        $nr_ctt    = $_POST['nr_ctt'];
        $nErro = 0;
        $mensagem;
        $mensagem_sql = "";

        executar("BEGIN");

        $sql_ctt = "UPDATE ctt SET nm_ctt = '$nm_ctt', ds_email = '$ds_email', ctt_funcao = '$ctt_funcao', ctt_skype = '$ctt_skype', ctt_genero = '$ctt_genero', ds_site = '$ds_site' WHERE nr_ctt = $nr_ctt";
        $update_ctt = executar($sql_ctt);

        if($update_ctt){

            $nr_ddd = !empty($nr_telefone) ? substr($nr_telefone, 0, 2) : "";
            $nr_tel = !empty($nr_telefone) ? substr($nr_telefone, 2) : "";
            $nr_dddcel = !empty($nr_celular) ? substr($nr_celular, 0, 2) : "";
            $nr_telcel = !empty($nr_celular) ? substr($nr_celular, 2) : "";

            $ramal = !empty($nr_ramal) ? ", nr_ramal = $nr_ramal" : " , nr_ramal = ''";

            $sql_tel  = "UPDATE tel SET nr_telefone = '$nr_tel', nr_ddd = '$nr_ddd', nr_dddcel = '$nr_dddcel', nr_celular = '$nr_telcel', ds_telefone = '$ds_telefone' $ramal WHERE nr_ctt = $nr_ctt";
            $update_tel = executar($sql_tel);

            if(!$update_tel){
                $nErro += 1;
                $mensagem = utf8_encode("Nao foi possivel atualizar os telefones! Entre em contato com o suporte.");
                $mensagem_sql = utf8_decode($sql_tel);
            }

        } else {
            $nErro += 1;
            $mensagem .= utf8_decode("Não foi possível atualizar o contato! \n Erro na tabela: ctt \n Verifique as informações: Nome, Email, Função, Skype e Gênero.");
            $mensagem_sql = utf8_decode($sql_ctt);
        }

        if($nErro == 0){
            executar("COMMIT");
            $mensagem = utf8_decode("Contato atualizado com sucesso!");
        } else {
            executar("ROLLBACK");
        }

        $dados = array('mensagem' => $mensagem,
                        'erro' => $nErro,
                        'nr_pessoa' => $nr_pessoa,
                        'mensagem_sql' => $mensagem_sql);  

        echo json_encode($dados);
    }


    if($action == "selecionar"){
        $nr_ctt = $_POST['nr_ctt'];

        $sql = "SELECT * FROM ctt INNER JOIN tel ON ctt.nr_ctt = tel.nr_ctt WHERE ctt.nr_ctt = $nr_ctt";
        $select_ctt = executar($sql);

        if($select_ctt){
            $ctt = array();

            $ctt['nm_ctt']      = utf8_decode($select_ctt[0]['nm_ctt']);
            $ctt['ds_email']    = utf8_decode($select_ctt[0]['ds_email']);
            $ctt['ctt_skype']   = utf8_decode($select_ctt[0]['ctt_skype']);
            $ctt['ctt_funcao']  = utf8_decode($select_ctt[0]['ctt_funcao']);
            $ctt['ctt_genero']  = utf8_decode($select_ctt[0]['ctt_genero']);
            $ctt['ds_telefone'] = utf8_decode($select_ctt[0]['ds_telefone']);
            $ctt['nr_ramal']    = utf8_decode($select_ctt[0]['nr_ramal']);
            $ctt['ds_site']     = utf8_decode($select_ctt[0]['ds_site']);

            if(!empty($select_ctt[0]['nr_telefone'])){
                $telefone = $select_ctt[0]['nr_ddd'].$select_ctt[0]['nr_telefone'];

                if(strlen($telefone) <= 10){
                    $ctt['nr_telefone'] = mascara($telefone, 'telefone');
                } else {
                    $ctt['nr_telefone'] = substr($telefone, 0, 4) == "0800" ? mascara($telefone, 'telefone-0800') : mascara($telefone, 'celular');
                }
            }else{
                $ctt['nr_telefone'] = "";
            }

            if(!empty($select_ctt[0]['nr_celular'])){
                $celular = $select_ctt[0]['nr_dddcel'].$select_ctt[0]['nr_celular'];

                if(strlen($celular) <= 10){
                    $ctt['nr_celular'] = mascara($celular, 'telefone');
                } else {
                    $ctt['nr_celular'] = substr($celular, 0, 4) == "0800" ? mascara($celular, 'telefone-0800') : mascara($celular, 'celular');
                }
            }else{
                $ctt['nr_celular'] = "";
            }
    
            echo json_encode($ctt);
        }else{
            echo json_encode(array('sql' => $sql));
        }  
    }
}

if($type == "contrato"){
    $dt_prevista             = convertDateToBd($_POST['dt_prevista']);
    $dt_vencimento_nfse      = !empty($_POST['dt_vencimento_nfse']) ? convertDateToBd($_POST['dt_vencimento_nfse']) : "";
    $valor_servico           = empty($_POST['valor_servico']) ? "0.00" : str_replace(",", ".", str_replace(".", "", $_POST['valor_servico']));
    $valor_install           = empty($_POST['valor_install']) ? "0.00" : str_replace(",", ".", str_replace(".", "", $_POST['valor_install']));
    $valor_produto           = empty($_POST['valor_produto']) ? "0.00" : str_replace(",", ".", str_replace(".", "", $_POST['valor_produto']));
    $valor_mensal            = empty($_POST['valor_mensal'])  ? "0.00" : str_replace(",", ".", str_replace(".", "", $_POST['valor_mensal']));
    $nr_revendedor           = empty($_POST['nr_revendedor']) ? 0 : $_POST['nr_revendedor'];
    $nr_sla                  = $nr_un == 3 ? "44"  : $_POST['nr_sla'];
    $nr_tipo_atendimento     = $nr_un == 3 ? "9"   : $_POST['nr_tipo_atendimento'];
    $nr_hubpop               = $nr_un == 3 ? "605" : $_POST['nr_hubpop_ctrt'];
    $tempo_atendimento       = $nr_un == 3 ? "0"   : $_POST['tempo_atendimento'];
    $nr_gerenteconta         = $_POST['nr_gerenteconta'];
    $nr_ender                = !empty($_POST['nr_ender']) ? implode(";", $_POST['nr_ender']) : "";
    $nr_un                   = $_POST['nr_un'];
    $nr_un_tipo              = $_POST['nr_un_tipo'];
    $nr_un_item              = $_POST['nr_un_item'];
    $nr_potencial_fechamento = $_POST['nr_potencial_fechamento'];
    $nr_empresa              = $_POST['nr_empresa'];
    $nr_status_contrato      = $_POST['nr_status_contrato'];
    $nm_ctrt                 = $_POST['nm_ctrt'];
    $cond_pgto_nfse          = $_POST['nm_parcelas'];
    $qtd_ip                  = $_POST['qtd_ip'];
    $qos                     = $_POST['qos'];
    $qos_prioridade          = $_POST['qos_prioridade'];
    $projeto_especial        = $_POST['projeto_especial'];
    $precisa_repetidora      = $_POST['precisa_repetidora'];

    $fields = '';
    $insrts = '';

    if(!empty($dt_vencimento_nfse)){
        $fields .= ', dt_vencimento_nfse';
        $insrts .= ', "'.$dt_vencimento_nfse.'"';
    }
    if(!empty($cond_pgto_nfse)){
        $fields .= ', cond_pgto';
        $insrts .= ', '.$cond_pgto_nfse;
    }
    if(empty($nr_sla)){ $nr_sla = 44; }
    if(!empty($nr_sla)){ 
        $fields .= ', nr_sla';
        $insrts .= ', '.$nr_sla;
    }
    if(!empty($nr_hubpop)){ 
        $fields .= ', nr_hubpop';
        $insrts .= ', '.$nr_hubpop;
    }
    if(empty($nr_hubpop)){ $nr_hubpop = 605; }
    if(empty($nr_tipo_atendimento)){ $nr_tipo_atendimento = 9; }
    if(empty($tempo_atendimento)){ $tempo_atendimento = 0; }


    if($action == "ativar"){
        $nr_ctrt          = $_POST['nr_ctrt'];
        $prazo_contrato   = !empty($_POST['prazo_contrato']) ? $_POST['prazo_contrato'] : 0;
        $notafiscal       = $_POST['notafiscal'];
        $nr_diavencimento = $_POST['nr_diavencimento'];
        $nr_day_finish    = $_POST['nr_day_finish'];

        $mensagem = "";
        $nErro = 0;

        $dt_final = date('Y-m-d', mktime(0, 0, 0, date('m') + $prazo_contrato, date('d'), date('Y')));

        $data_vcto = new DateTime();
        $data_vcto->add(new DateInterval('P'.$nr_day_finish.'D'));
        $dt_ativacao = $data_vcto->format('Y-m-d');

        $field_nfse = !empty($dt_vencimento_nfse) ? ", dt_vencimento_nfse = '$dt_vencimento_nfse'" : "";

        executar("BEGIN");

        $sql_update_ctrt = "UPDATE ctrt SET
                                nm_ctrt = '$nm_ctrt',
                                nr_status_contrato = 6,
                                nr_tpctrt = 7,
                                prazo_contrato = '$prazo_contrato',
                                notafiscal = '$notafiscal',
                                nr_diavencimento = $nr_diavencimento,
                                nr_sla = $nr_sla,
                                nr_tipo_atendimento = $nr_tipo_atendimento,
                                tempo_atendimento = '$tempo_atendimento',
                                nr_hubpop = $nr_hubpop,
                                dt_ativacao = '$dt_ativacao',
                                dt_vencto = '$dt_final',
                                cond_pgto = '$cond_pgto_nfse' $field_nfse
                            WHERE
                                nr_ctrt = $nr_ctrt";

        if($update_ctrt = executar($sql_update_ctrt)){

            $sql_update_ctrt_valor = "UPDATE ctrt_valor SET
                                        produto_valor = $valor_produto,
                                        servico_valor = $valor_servico,
                                        install_valor = $valor_install,
                                        mensal_valor = $valor_mensal
                                    WHERE
                                        nr_ctrt = $nr_ctrt";
            if(!executar($sql_update_ctrt_valor)){
                $nErro += 1;
                $mensagem .= utf8_decode("Não foi possível atualizar os valores.");
            }
        } else {
            $nErro += 1;
            $mensagem .= utf8_decode("Não foi possível atualizar o contrato.");
        }

        if($nErro == 0){
            #FECHA O CHAMADO
            $select_os= executar('SELECT nr_os FROM ordem_servico WHERE nr_ctrt ='.$nr_ctrt.' LIMIT 1');
            $select_os= $select_os[0]['nr_os'];
            $msg	 = utf8_decode('ESTE CONTRATO FOI FECHADO PARA À ATIVAÇÃO DE UM NOVO CONTRATO');

            if(executar('UPDATE ordem_servico SET status = "F", dt_fechamento = NOW() WHERE nr_ctrt = '.$nr_ctrt.' LIMIT 1')){
                $sql_update_os_mensagem = 'INSERT INTO ordem_servico_msg (nr_os_msg, nr_os, nr_func, mensagem, dt_mensagem) VALUES (default, "'.$select_os.'", "'.$_SESSION['login']['nr_func'].'", "'.$msg.'", NOW() )';

                if(executar($sql_update_os_mensagem)){
                    
                    $select_pessoa = executar('SELECT pessoa.nr_pessoa, pessoa.nm_pessoa, ctrt.nr_cliente FROM pessoa JOIN ctrt USING(nr_pessoa) WHERE ctrt.nr_ctrt = '.$nr_ctrt.' LIMIT 1');
                    $nr_pessoa = $select_pessoa[0]['nr_pessoa'];
                    
                    #CRIA UMA OS PARA O CONTRATO NOVO, PARA PROJETOS.
                    $titulo     = 'CONTRATO ATIVADO';
                    $mensagemOS = utf8_decode('Ordem de Serviço criada.');
                    $sql = 'INSERT INTO ordem_servico (nr_ctrt, nr_func, nr_depto, titulo, dt_abertura, status, nr_tempo_sla) VALUES('.$nr_ctrt.','.$_SESSION['login']['nr_func'].', 2 , '.'"'.$titulo.'",now(),"A","0")';
                    if(executar($sql)){

                        $res = executar('SELECT LAST_INSERT_ID() as nr_os FROM ordem_servico');
                        $nr_os = $res[0]['nr_os'];

                        $sql = 'INSERT INTO ordem_servico_msg (nr_func,nr_os,mensagem,dt_mensagem) VALUES ('.$_SESSION['login']['nr_func'].','.$nr_os.',"'.$mensagemOS.'",now())';
                        if(!executar($sql)){
                            $nErro += 1;
                            $mensagem .= utf8_decode("Não foi possível fechar OS.");
                        }
                    } else {
                        $nErro += 1;
                        $mensagem .= utf8_decode("Não foi possível fechar OS.");
                    }
                } else {
                    $nErro += 1;
                    $mensagem .= utf8_decode("Não foi possível inserir mensagem ao fechar OS.");
                }
            } else {
                $nErro += 1;
                $mensagem .= utf8_decode("Não foi possível fechar OS.");
            }
        }

        if($nErro == 0){
            $cliente_financeiro = verificaCliente($nr_pessoa, $nr_ctrt);
            //echo $cliente_financeiro['sql'];

            $nErro += $cliente_financeiro['erro'];
            $mensagem .= $cliente_financeiro['mensagem'];
        }

        if($nErro == 0){
            $contrato_pre_faturamento = criaContratoPreVenda($nr_pessoa, $nr_ctrt);
            //echo $cliente_financeiro['sql'];

            $nErro += $contrato_pre_faturamento['erro'];
            $mensagem .= $contrato_pre_faturamento['mensagem'];
        }

        if($nErro == 0){
            enviaEmailOS($mensagemOS, $select_pessoa[0]['nm_pessoa'], $nr_os, $titulo, $mensagemOS);
            enviaEmailFinanceiro($cliente_financeiro['nr_payment_fornecedor'], $nr_ctrt, $contrato_pre_faturamento['nr_ctrt_prefaturamento']);

            executar("COMMIT");
            $mensagem = utf8_decode("Contrato ativado com sucesso!");
        } else {
            executar("ROLLBACK");
        }

        $dados = array('mensagem' => $mensagem,
                        'erro' => $nErro,
                        'nr_pessoa' => $nr_pessoa);

        echo json_encode($dados);
    }
       

    if($action == "atualiza"){
        $nr_ctrt           = $_POST['nr_ctrt'];
        $nr_pessoa         = $_POST['nr_pessoa'];
        $renovacao         = $_POST['renovacao'];
        $prazo_contrato    = $_POST['prazo_contrato'];
        $nr_diavencimento  = $_POST['nr_diavencimento'];
        $ctrt_renovacao_ID = $_POST['motivo_renovacao'];
        $dt_ativacao       = convertDateToBd($_POST['dt_ativacao']);
        $subAction         = strtolower($_POST['sub-action']);
        $nErro     = 0;
        $mensagem  = "";
        if(!empty($dt_vencimento_nfse)){
            $dt_nfse = ", dt_vencimento_nfse = '$dt_vencimento_nfse' ";
        } else {
            $dt_nfse = "";
        }

        executar("BEGIN");

        if($subAction == "prevenda"){
            $sql_ctrt = "UPDATE 
                            ctrt 
                        SET
                            dt_prevista = '$dt_prevista',
                            nr_sla = $nr_sla,
                            nr_ender = '$nr_ender',
                            nr_hubpop = $nr_hubpop,
                            nr_empresa = $nr_empresa,
                            nr_revendedor = $nr_revendedor,
                            nr_status_contrato = $nr_status_contrato,
                            nr_tipo_atendimento = $nr_tipo_atendimento,
                            nr_unidade_negocio_item = $nr_un_item,
                            nr_potencial_fechamento = $nr_potencial_fechamento,
                            qos = '$qos',
                            qtd_ip = '$qtd_ip',
                            cond_pgto = '$cond_pgto_nfse',
                            qos_prioridade = '$qos_prioridade',
                            projeto_especial = '$projeto_especial',
                            tempo_atendimento = '$tempo_atendimento',
                            precisa_repetidora = '$precisa_repetidora'
                            $dt_nfse
                        WHERE 
                            nr_ctrt = $nr_ctrt";

        } else if($subAction == "ativo") {
            $sql_ctrt = "UPDATE 
                            ctrt 
                        SET
                            dt_vencimento_nfse = '$dt_vencimento_nfse',
                            nr_ender = '$nr_ender',
                            nr_empresa = $nr_empresa,
                            nr_revendedor = $nr_revendedor
                        WHERE 
                            nr_ctrt = $nr_ctrt";
        } else if($subAction == "renovacao"){
            $dt_vencto = date('Y-m-d', mktime(0, 0, 0, date('m', strtotime($dt_ativacao)) + $prazo_contrato, date('d', strtotime($dt_ativacao)), date('Y', strtotime($dt_ativacao))));
            
            executar('INSERT INTO ctrt_log VALUES(default, "'.$nr_ctrt.'", "'.$_SESSION['login']['nr_func'].'", NOW(), "'.$dt_ativacao.'", "'.$dt_vencto.'",  "'.$nm_ctrt .'", "'.$nr_gerenteconta.'", "'.$renovacao.'", "'.$prazo_contrato.'", "'.$ctrt_renovacao_ID.'", "'.$nr_diavencimento.'", "'.$nr_un_item.'", "'.$nr_sla.'", "'.$nr_tipo_atendimento.'", "'.$nr_hubpop.'", "'.str_replace(array('.', ','), array('', '.'), $nr_produto).'", "'.str_replace(array('.', ','), array('', '.'), $nr_servico).'", "'.str_replace(array('.', ','), array('', '.'), $nr_install).'", "'.str_replace(array('.', ','), array('', '.'), $nr_mensal).'"); ');

            $sql_ctrt = "UPDATE 
                            ctrt 
                        SET
                            nm_ctrt = '$nm_ctrt'
                            nr_sla = $nr_sla,
                            nr_ender = '$nr_ender',
                            nr_hubpop = $nr_hubpop,
                            nr_empresa = $nr_empresa,
                            nr_revendedor = $nr_revendedor,
                            nr_diavencimento = '$nr_diavencimento',
                            nr_tipo_atendimento = $nr_tipo_atendimento,
                            nr_unidade_negocio_item = $nr_un_item,
                            nr_potencial_fechamento = $nr_potencial_fechamento,
                            renovacao = '$renovacao',
                            cond_pgto = '$cond_pgto_nfse',
                            prazo_contrato = '$prazo_contrato',
                            tempo_atendimento = '$tempo_atendimento',
                            dt_ativacao = '$dt_ativacao',
                            dt_vencto = '$dt_vencto'
                            $dt_nfse
                        WHERE 
                            nr_ctrt = $nr_ctrt";

            $contrato_pre_faturamento = criaContratoPreVenda($nr_pessoa, $nr_ctrt);

            $nErro += $contrato_pre_faturamento['erro'];
            $mensagem .= $contrato_pre_faturamento['mensagem'];
        }

        $update_ctrt = executar($sql_ctrt);

        if($update_ctrt){
            if($subAction != "ativo"){
                $sql_ctrt_valor = "UPDATE 
                                        ctrt_valor 
                                    SET
                                        mensal_valor = $valor_mensal,
                                        produto_valor = $valor_produto,
                                        servico_valor = $valor_servico,
                                        install_valor = $valor_install
                                    WHERE 
                                        nr_ctrt = $nr_ctrt";
                $update_ctrt_valor = executar($sql_ctrt_valor);
                if(!$update_ctrt_valor) {
                    $nErro += 1;
                    $mensagem .= utf8_decode("Não foi possível altualizar valores do contrato.");
                }
            }

            $select_gerenteconta = executar("SELECT nr_func FROM gerenteconta WHERE nr_ctrt = $nr_ctrt");

            if(!empty($select_gerenteconta)){

                $sql_gerenteconta = "UPDATE gerenteconta SET nr_func = $nr_gerenteconta WHERE nr_ctrt = $nr_ctrt";
            } else {

                $nr_cliente = executar("SELECT cliente.nr_cliente FROM pessoa INNER JOIN cliente ON pessoa.nr_pessoa = cliente.nr_pessoa WHERE pessoa.nr_pessoa = $nr_pessoa");
                $sql_gerenteconta = 'INSERT INTO gerenteconta 
                                            (nr_gerenteconta,
                                            nr_func,
                                            nr_cliente,
                                            nr_ctrt) 
                                        VALUES 
                                            (default,
                                            '.$nr_gerenteconta.',
                                            '.$nr_cliente[0][0].',
                                            '.$nr_ctrt.');';
            }

            $update_gerenteconta = executar($sql_gerenteconta);

            if(!$update_gerenteconta){
                $nErro += 1;
                $mensagem .= utf8_decode("Não foi possível altualizar o gerente do contrato.");
            }
        } else {
            $nErro += 1;
            $mensagem .= utf8_decode("Não foi possível altualizar informações do contrato.");
        }

        if($nErro == 0){
            executar("COMMIT");
            $mensagem = utf8_decode("Atualização concluida com sucesso!");
        } else {
            executar("ROLLBACK");
        }

        a($mensagem);
        redireciona("../App_Pes/Cliente.php?nr_pessoa=$nr_pessoa");        
    }

    if($action == "insere"){
        $nr_pessoa = $_POST['nr_pessoa'];
        $nErro     = 0;
        $mensagem  = "";

        $info_empresa = executar("SELECT pessoa.*, cliente.nr_cliente, empresa.nr_filial, empresa.nr_empresa FROM pessoa INNER JOIN cliente ON pessoa.nr_pessoa = cliente.nr_pessoa INNER JOIN empresa ON cliente.nr_empresa = empresa.nr_empresa WHERE pessoa.nr_pessoa = $nr_pessoa");
        
        $nr_cliente         = $info_empresa[0]['nr_cliente'];
        $nr_filial	        = $info_empresa[0]['nr_filial'];
        $nr_tpctrt	        = 6;//CONTRATO DE PREVENDA
        $nr_prod	        = 98;//PRODUTO PADRAO DE PREVENDA - OUTROS
        $nr_depto	        = 42;//DEPARTAMENTO DE PRE-VENDAS
        $nr_status_contrato = 9;//EM ANDAMENTO


        $titulo      = "OS: PREVENDA - CLIENTE: {$info_empresa[0]['nm_pessoa']}";
        $mensagem_os = utf8_decode("Criada às ").date("H:i:s d/m/Y");
        $today       = date("Y-m-d H:i:s"); 

        $sql_ctrt = 'INSERT INTO ctrt 
                        (nr_ctrt,
                        nr_cliente,
                        nr_status_contrato,
                        nr_pessoa,
                        nr_filial,
                        nr_tpctrt,
                        nr_ender,
                        nr_prod,
                        nr_unidade_negocio_item,
                        nm_ctrt,
                        dt_ctrt,
                        dt_prevista,
                        nr_valor,
                        nr_desconto,
                        notafiscal,
                        nr_diavencimento,
                        fatmanual,
                        nr_conta,
                        nr_tipo_atendimento,
                        tempo_atendimento,
                        progresso,
                        ctrt_novo,
                        qtd_ip,
                        qos,
                        ctrt.nr_revendedor,
                        qos_prioridade,
                        projeto_especial,
                        precisa_repetidora,
                        nr_empresa '.$fields.') 
                    VALUES
                        (default,
                        '.$nr_cliente.',
                        '.$nr_status_contrato.',
                        '.$nr_pessoa.',
                        '.$nr_filial.',
                        '.$nr_tpctrt.',
                        "'.$nr_ender.'",
                        '.$nr_prod.',
                        '.$nr_un_item.',
                        "'.$nm_ctrt.'",
                        NOW(),
                        "'.$dt_prevista.'",
                        0,
                        0,
                        "N",
                        0,
                        "N",
                        0,
                        '.$nr_tipo_atendimento.',
                        "'.$tempo_atendimento.'",
                        0,
                        0,
                        "'.$qtd_ip.'",
                        "'.$qos.'",
                        "'.$nr_revendedor.'",
                        "'.$qos_prioridade.'",
                        "'.$projeto_especial.'",
                        "'.$precisa_repetidora.'",
                        '.$info_empresa[0]['nr_empresa'].' '.$insrts.')';
        executar("BEGIN");
        $insert_ctrt = executar($sql_ctrt);

        if($insert_ctrt){
            $res = executar('SELECT LAST_INSERT_ID() as nr_ctrt FROM ctrt');
            $nr_ctrt = $res[0]['nr_ctrt'];
		
            #VALOR DO CONTRATO
            $sql_ctrt_valor = 'INSERT INTO ctrt_valor 
                                    (nr_ctrt_valor,
                                    nr_ctrt,
                                    produto_valor,
                                    servico_valor,
                                    install_valor,
                                    mensal_valor) 
                                VALUES 
                                    (default,
                                    '.$nr_ctrt.',
                                    "'.$valor_produto.'",
                                    "'.$valor_servico.'",
                                    "'.$valor_install.'",
                                    "'.$valor_mensal.'");';

            $res_ctrt_valor = executar($sql_ctrt_valor);
            if(!$res_ctrt_valor){
                $nErro += 1;
                $mensagem = utf8_decode("Não foi possível inserir os valores do contrato!");
                $mensagem_sql = $sql_ctrt_valor;
            }
            
            if(!empty($nr_gerenteconta)){
                $sql_gerente = 'INSERT INTO gerenteconta 
                                    (nr_gerenteconta,
                                    nr_func,
                                    nr_cliente,
                                    nr_ctrt) 
                                VALUES 
                                    (default,
                                    '.$nr_gerenteconta.',
                                    '.$nr_cliente.',
                                    '.$nr_ctrt.');';

                $res_gerente = executar($sql_gerente);

                if(!$res_gerente){
                    $nErro += 1;
                    $mensagem = utf8_decode("Não foi possível inserir o gerente do contrato!");
                    $mensagem_sql = $sql_gerente;
                }
            }

            $sql_ordem_servico = 'INSERT INTO ordem_servico 
                                        (nr_ctrt,
                                        nr_func,
                                        nr_depto,
                                        titulo,
                                        dt_abertura,
                                        status,
                                        nr_tempo_sla) 
                                    VALUES 
                                        ('.$nr_ctrt.',
                                        '.$nr_gerenteconta.',
                                            42 ,
                                            '.'"'.$titulo.'",
                                        now(),
                                        "A",
                                        "0")';

            $insert_ordem_servico = executar($sql_ordem_servico);
            if($insert_ordem_servico){
                $res = executar('SELECT LAST_INSERT_ID() as nr_os FROM ordem_servico');
                $nr_os = $res[0]['nr_os'];

                $sql_ordem_servico_mensagem   = 'INSERT INTO ordem_servico_msg 
                                                    (nr_func,
                                                    nr_os,
                                                    mensagem,
                                                    dt_mensagem) 
                                                VALUES 
                                                    ('.$_SESSION['login']['nr_func'].',
                                                    '.$nr_os.',
                                                    "'.$mensagem_os.'",
                                                    now())';

                $insert_ordem_servico_mensagem = executar($sql);
                if(!$insert_ordem_servico_mensagem){

                    $nErro += 1;
                    $mensagem = utf8_decode("Não foi possível inserir mensagem na O.S. referente ao contrato!");
                    $mensagem_sql = $insert_ordem_servico_mensagem;
                }

            } else {
                $nErro += 1;
                $mensagem = utf8_decode("Não foi possível inserir a nova O.S. referente ao contrato!");
                $mensagem_sql = $sql_ordem_servico;
            }
        } else {
            $nErro += 1;
            $mensagem = utf8_decode("Não foi possível inserir o contrato!");
            $mensagem_sql = $sql_ctrt;
        }

        if($nErro == 0){
            executar("COMMIT");
            $mensagem = utf8_decode("Contrato inserido com sucesso!");

            $mailDepto = executar('SELECT ds_email FROM depto WHERE nr_depto = '.$nr_depto);
            $mensagem_os = str_replace(chr(13),'<br />',$mensagem_os);
            $html = layoutChamado($cliente[0]['cliente'], $nr_os, $titulo, $mensagem_os, null);

            $mailer = new EVM_Mailer();
            $mailer->set_sender('Erp Raicom','no-reply@gruporedes.global');
            $mailer->set_subject('[erp_raicom] Ordem de Serviços '.$nr_os);
            $mailer->set_message_type('html');
            $mailer->set_message($html);
            $mailer->add_recipient('DEPTO', $mailDepto[0]['ds_email']);
            $mailer->add_CC($_SESSION['login']['nm_pessoa'], str_replace('raicom.com.br', 'gruporedes.global', $_SESSION['login']['nm_user']));

            $mailer->send();
        } else {
            executar("ROLLBACK");
        }

        $dados = array('mensagem' => $mensagem,
                        'erro' => $nErro,
                        'nr_pessoa' => $nr_pessoa,
                        'mensagem_sql' => $mensagem_sql);  

        //echo json_encode($dados);
        a($mensagem);
        redireciona("../App_Pes/Cliente.php?nr_pessoa=$nr_pessoa");
    }

    if($action == "selecionar"){
        $nr_ctrt = $_POST['nr_ctrt'];

        $dados = array();
        $select_ctrt = executar("SELECT 
                                    ctrt.nr_ctrt,
                                    ctrt.nr_pessoa,
                                    ctrt.nr_cliente,
                                    ctrt.nr_status_contrato,
                                    ctrt.nr_filial,
                                    ctrt.nr_tpctrt,
                                    ctrt.nr_sla,
                                    ctrt.nr_ender,
                                    ctrt.nr_prod,
                                    ctrt.nr_hubpop,
                                    ctrt.nm_ctrt,
                                    ctrt.dt_ctrt,
                                    ctrt.dt_vencto,
                                    ctrt.dt_prevista,
                                    ctrt.dt_reuniao,
                                    ctrt.nr_day_finish,
                                    ctrt.dt_cancelamento,
                                    ctrt.nr_valor,
                                    ctrt.nr_desconto,
                                    ctrt.status_2,
                                    ctrt.nr_diavencimento,
                                    ctrt.nr_tipo_atendimento,
                                    ctrt.tempo_atendimento,
                                    ctrt.dt_ativacao,
                                    ctrt.prazo_contrato,
                                    ctrt.renovacao,
                                    ctrt.qtd_ip,
                                    ctrt.qos,
                                    ctrt.nr_revendedor,
                                    ctrt.qos_prioridade,
                                    ctrt.precisa_repetidora,
                                    ctrt.projeto_especial,
                                    ctrt.dt_vencimento_nfse,
                                    ctrt.cond_pgto,
                                    ctrt.nr_empresa,
                                    ctrt.nr_potencial_fechamento,
                                    ctrt_valor.produto_valor,
                                    ctrt_valor.servico_valor,
                                    ctrt_valor.install_valor,
                                    ctrt_valor.mensal_valor,
                                    gerenteconta.nr_func,
                                    uni.nr_unidade_negocio_item,
                                    uni.nr_unidade_negocio_tipo,
                                    unt.nr_unidade_negocio,
                                    tpc.ds_tpctrt,
                                    ds_status_contrato
                                FROM
                                    ctrt
                                        INNER JOIN
                                    ctrt_valor ON ctrt.nr_ctrt = ctrt_valor.nr_ctrt
                                        INNER JOIN
                                    gerenteconta ON gerenteconta.nr_ctrt = ctrt.nr_ctrt
                                        INNER JOIN
                                    unidade_negocio_item uni ON ctrt.nr_unidade_negocio_item = uni.nr_unidade_negocio_item
                                        INNER JOIN
                                    unidade_negocio_tipo unt ON uni.nr_unidade_negocio_tipo = unt.nr_unidade_negocio_tipo
                                        INNER JOIN
                                    tpctrt tpc ON ctrt.nr_tpctrt = tpc.nr_tpctrt
                                        INNER JOIN
                                    status_contrato ON status_contrato.nr_status_contrato = ctrt.nr_status_contrato
                                WHERE
                                    ctrt.nr_ctrt = $nr_ctrt");

        if($select_ctrt){
            $dados['dt_ctrt']                 = convertDateToBr($select_ctrt[0]['dt_ctrt']);
            $dados['dt_vencto']               = empty($select_ctrt[0]['dt_vencto']) ? "" : convertDateToBr($select_ctrt[0]['dt_vencto']);
            $dados['dt_prevista']             = $select_ctrt[0]['dt_prevista'] == "0000-00-00" ? "" : convertDateToBr($select_ctrt[0]['dt_prevista']);
            $dados['dt_vencimento_nfse']      = empty($select_ctrt[0]['dt_vencimento_nfse']) ? "" : convertDateToBr($select_ctrt[0]['dt_vencimento_nfse']);
            $dados['valor_mensal']            = empty($select_ctrt[0]['mensal_valor']) ? "" : number_format($select_ctrt[0]['mensal_valor'], 2, ",", ".");
            $dados['valor_produto']           = empty($select_ctrt[0]['produto_valor']) ? "" : number_format($select_ctrt[0]['produto_valor'], 2, ",", ".");
            $dados['valor_servico']           = empty($select_ctrt[0]['servico_valor']) ? "" : number_format($select_ctrt[0]['servico_valor'], 2, ",", ".");
            $dados['valor_install']           = empty($select_ctrt[0]['install_valor']) ? "" : number_format($select_ctrt[0]['install_valor'], 2, ",", ".");
            $dados['nr_un']                   = $select_ctrt[0]['nr_unidade_negocio'];
            $dados['nr_sla']                  = $select_ctrt[0]['nr_sla'];
            $dados['nr_ctrt']                 = $select_ctrt[0]['nr_ctrt'];
            $dados['nr_ender']                = $select_ctrt[0]['nr_ender'];
            $dados['nr_hubpop']               = $select_ctrt[0]['nr_hubpop'];
            $dados['nr_empresa']              = $select_ctrt[0]['nr_empresa'];
            $dados['nr_un_tipo']              = $select_ctrt[0]['nr_unidade_negocio_tipo'];
            $dados['nr_un_item']              = $select_ctrt[0]['nr_unidade_negocio_item'];
            $dados['nr_revendedor']           = $select_ctrt[0]['nr_revendedor'];
            $dados['nr_gerenteconta']         = $select_ctrt[0]['nr_func'];
            $dados['nr_diavencimento']        = $select_ctrt[0]['nr_diavencimento'];
            $dados['nr_tipo_atendimento']     = $select_ctrt[0]['nr_tipo_atendimento'];
            $dados['nr_potencial_fechamento'] = $select_ctrt[0]['nr_potencial_fechamento'];
            $dados['qos']                     = utf8_decode($select_ctrt[0]['qos']);
            $dados['qtd_ip']                  = $select_ctrt[0]['qtd_ip'];
            $dados['nm_ctrt']                 = utf8_encode($select_ctrt[0]['nm_ctrt']);
            $dados['ds_tpctrt']               = utf8_decode($select_ctrt[0]['ds_tpctrt']);
            $dados['renovacao']               = utf8_decode($select_ctrt[0]['renovacao']);
            $dados['nm_parcelas']             = utf8_decode($select_ctrt[0]['cond_pgto']);
            $dados['qos_prioridade']          = utf8_decode($select_ctrt[0]['qos_prioridade']);
            $dados['prazo_contrato']          = utf8_decode($select_ctrt[0]['prazo_contrato']);
            $dados['projeto_especial']        = utf8_decode($select_ctrt[0]['projeto_especial']);
            $dados['tempo_atendimento']       = utf8_decode($select_ctrt[0]['tempo_atendimento']);
            $dados['ds_status_contrato']      = utf8_decode($select_ctrt[0]['ds_status_contrato']);
            $dados['nr_status_contrato']      = utf8_decode($select_ctrt[0]['nr_status_contrato']);
            $dados['precisa_repetidora']      = utf8_decode($select_ctrt[0]['precisa_repetidora']);
        }

        echo json_encode($dados);
    }

    if($action == "selecionar-lista"){
        $nr_pessoa = $_POST['nr_pessoa'];
        $html = "";
        
        if($_POST['nr_status_contrato'] != "leads"){
            $nr_status_contrato = !empty($_POST['nr_status_contrato']) ? " AND ctrt.nr_status_contrato = ".$_POST['nr_status_contrato'] : "";

            $dados_ctrt = executar("SELECT 
                                    ctrt.nr_ctrt,
                                    nm_ctrt,
                                    ds_tpctrt,
                                    nr_tpctrt,
                                    DATE_FORMAT(dt_vencto, '%d/%m/%Y') AS dt_vencto,
                                    DATE_FORMAT(dt_ctrt, '%d/%m/%Y') AS dt_ctrt,
                                    CONCAT(ds_unidade_negocio_tipo, ' ', nm_item) AS nm_prod,
                                    ds_status_contrato,
                                    ctrt.nr_status_contrato,
                                    ctrt.nr_potencial_fechamento,
                                    unidade_negocio.nr_unidade_negocio,
                                    mensal_valor,
                                    install_valor
                                FROM
                                    ctrt
                                        JOIN
                                    tpctrt USING (nr_tpctrt)
                                        INNER JOIN
                                    ctrt_valor ON ctrt_valor.nr_ctrt = ctrt.nr_ctrt
                                        JOIN
                                    unidade_negocio_item ON unidade_negocio_item.nr_unidade_negocio_item = ctrt.nr_unidade_negocio_item
                                        JOIN
                                    unidade_negocio_tipo USING (nr_unidade_negocio_tipo)
                                        JOIN
                                    unidade_negocio ON unidade_negocio.nr_unidade_negocio = unidade_negocio_tipo.nr_unidade_negocio
                                        JOIN
                                    status_contrato ON status_contrato.nr_status_contrato = ctrt.nr_status_contrato
                                WHERE 
                                    ctrt.nr_pessoa = $nr_pessoa AND ctrt.ctrt_novo = 0 $nr_status_contrato
                                ORDER BY ctrt.nr_ctrt DESC");
    
    
            
            
            if($dados_ctrt && count($dados_ctrt) > 0){
                foreach($dados_ctrt as $ctrt){
                    $valor_mensal = !empty($ctrt['mensal_valor']) ? number_format($ctrt['mensal_valor'], 2, ',', '.') : "0,00";
                    $valor_instalacao = !empty($ctrt['install_valor']) ? number_format($ctrt['install_valor'], 2, ',', '.') : "0,00";
                    
                    $listaOS = $ctrt['nr_status_contrato'] == 3 ? ", false" : "";
                    $onclick = $ctrt['nr_status_contrato'] == 3  || $ctrt['nr_status_contrato'] == 12 ? "visualizaContratoCancelado" : "alteraContrato";
                    $status_cancelado = $ctrt['nr_status_contrato'] == 3 ? "text-danger riscado" : "";
                    $status_cancelado = $ctrt['nr_status_contrato'] == 12 ? "text-danger" : $status_cancelado;
    
                    $ativarContrato = '';
                    if($ctrt['nr_status_contrato'] == 9 && $ctrt['nr_potencial_fechamento'] == 1){
                        $ativarContrato .= '<img src="../imagens/icones/check_rounded.png" style="width: 20px; height: 20px;" class="ativar-contato" data-toggle="modal" data-target="#modalAtivarContrato" title="Ativar Contrato" onclick="preAtivarContrato('.$nr_pessoa.', '.$ctrt['nr_ctrt'].', '.$ctrt['nr_unidade_negocio'].');" alt="Ativar Contrato" />';
                    } 
                    if($ctrt['nr_status_contrato'] == 12){
                        $ativarContrato .= '<img src="../imagens/icones/cancel_rounded.png" style="width: 20px; height: 20px;" class="cancelar-contato" data-toggle="modal" data-target="#modalCancelarContrato" title="Cancelar Contrato" onclick="cancelaContrato('.$ctrt['nr_ctrt'].');" alt="Cancelar Contrato" />';
                        $ativarContrato .= '<img src="../imagens/icones/undo_2_rounded.png" style="width: 20px; height: 20px;" class="reabilitar-contato" data-toggle="modal" data-target="#modalReabilitarContrato" title="Reabilitar Contrato" onclick="reabilitaContrato('.$ctrt['nr_ctrt'].');" alt="Reabilitar Contrato" />';
                    } else if($ctrt['nr_status_contrato'] != 3){
                        $ativarContrato .= '<img src="../imagens/icones/cancel_rounded.png" style="width: 20px; height: 20px;" class="processo-cancelamento-contato" title="Iniciar processo de cancelamento do contrato Contrato" onclick="processoCancelaContrato('.$ctrt['nr_ctrt'].');" alt="Iniciar processo de cancelamento do contrato" />';
                    }
    
                    $html .= '<tr colspan="11" class="'.$status_cancelado.'">
                        <td colspan="1">'.$ctrt['nr_ctrt'].'</td>
                        <td colspan="2">'.utf8_decode($ctrt['nm_ctrt']).'</td>
                        <td colspan="1">'.$ctrt['nm_prod'].'</td>
                        <td colspan="1">'.$ctrt['ds_status_contrato'].'</td>
                        <td colspan="1">'.$valor_mensal.'</td>
                        <td colspan="1">'.$valor_instalacao.'</td>
                        <td colspan="1">'.$ctrt['dt_ctrt'].'</td>
                        <td colspan="1">'.$ctrt['dt_vencto'].'</td>
                        <td colspan="1" class="align-right">
                            <img src="../imagens/icones/edit_rounded.ico" class="editar-contato" data-toggle="modal" data-target="#modalContrato" title="Editar Contrato" onclick="'.$onclick.'('.$nr_pessoa.', '.$ctrt['nr_ctrt'].', '.$ctrt['nr_tpctrt'].', '.$ctrt['nr_status_contrato'].');" alt="Edição de contratos" />
                            <img src="../imagens/icones/ordem_servico_rounded.png" data-toggle="modal" data-target="#modalListaOrdemServico" style="width: 20px; height: 20px;" title="'.utf8_decode("Lista de Ordens de Serviços").'" onclick="listaOrdemServico('.$ctrt['nr_ctrt'].$listaOS.');" alt="'.utf8_decode("Lista de Ordens de Serviços").'" />
                            <img src="../imagens/icones/parcelas_financeiro.png" data-toggle="modal" data-target="#modalParcelasFinanceiro" style="width: 20px; height: 20px;" title="'.utf8_decode("Parcelas do Financeiro").'" onclick="buscaFinanceiro('.$ctrt['nr_ctrt'].');" alt="'.utf8_decode("Parcelas do Financeiro").'" />
                        </td>
                        <td colspan="1" class="align-right">
                            <img src="../imagens/icones/download_rounded.png" data-toggle="modal" data-target="#modalDownloadArquivo" style="width: 20px; height: 20px;" title="Baixar Arquivos" onclick="listaArquivos('.$ctrt['nr_ctrt'].');" alt="Baixar arquivos" />
                            <img src="../imagens/icones/upload_rounded.png" data-toggle="modal" data-target="#modalUploadArquivo" style="width: 20px; height: 20px;" title="Importar Arquivos" onclick="importaArquivos('.$ctrt['nr_ctrt'].');" alt="Importar arquivos" />
                            '.$ativarContrato.'
                        </td>
                    </tr>';
                }
            }
        } 

        if($_POST['nr_status_contrato'] == "leads" || empty($_POST['nr_status_contrato'])){
            $sql_leads = "SELECT DISTINCT l.lead_ID, l.lead_nome, l.lead_cnpj, l.lead_data_create, le.lead_etapa_nome, ls.lead_status_nome FROM leads l INNER JOIN lead_etapa le ON l.lead_etapa_ID = le.lead_etapa_ID INNER JOIN lead_status ls ON ls.lead_status_ID = l.lead_status_ID WHERE nr_cliente = (SELECT nr_cliente FROM cliente WHERE nr_pessoa = $nr_pessoa ) ORDER BY lead_nome ";
            $leads = executar($sql_leads);

            if($leads && !empty($leads[0])){
                foreach($leads as $v){
                    $lead_cnpj = mascara($v['lead_cnpj'], 'cnpj');
                    $html .= '<tr colspan="11" class="'.$status_cancelado.'">
                        <td colspan="1">'.$v['lead_ID'].'</td>
                        <td colspan="3">'.$v['lead_nome'].'</td>
                        <td colspan="2">'.$lead_cnpj.'</td>
                        <td colspan="2">'.$v['lead_etapa_nome'].'</td>
                        <td colspan="2">'.$v['lead_status_nome'].'</td>
                        <td colspan="1" class="align-right">
                            <a href="../App_Pes/Leads.php?lead_ID='.$v['lead_ID'].'" target="_blank"><img src="../imagens/icones/edit_rounded.ico" title="Editar Lead" alt="Edição de leads" /></a>
                            <img src="../imagens/icones/ordem_servico_rounded.png" data-toggle="modal" data-target="#modalListaOrdemServico" style="width: 20px; height: 20px;" title="'.utf8_decode("Lista de Ordens de Serviços").'" onclick="listaOrdemServico('.$v['lead_ID'].', false, false);" alt="'.utf8_decode("Lista de Ordens de Serviços").'" />
                        </td>
                    </tr>';
                }
            }
        }
        
        if(!($dados_ctrt && count($dados_ctrt) > 0) && !($leads && !empty($leads[0]))){
            $html .= '<tr colspan="4">
                <td colspan="4" class="text-danger text-center"><b>'.utf8_decode("Não foi possível encontrar nenhum contrato cadastrado.").'</b></td>
            </tr>';
        }


        echo $html;

    }

    if($action == "selecionar-tipo-produto"){
        $nr_unidade_negocio      = $_POST['nr_unidade_negocio'];
        $nr_unidade_negocio_tipo = $_POST['nr_unidade_negocio_tipo'];

        $sql_unidade_negocio_tipo    = "SELECT nr_unidade_negocio_tipo, ds_unidade_negocio_tipo FROM unidade_negocio_tipo WHERE nr_unidade_negocio = $nr_unidade_negocio ORDER BY ds_unidade_negocio_tipo";
        $select_unidade_negocio_tipo = executar($sql_unidade_negocio_tipo);

        $html = "<option value=''>SELECIONE</option>";
        foreach($select_unidade_negocio_tipo as $value){
            $selected = $nr_unidade_negocio_tipo == $value['nr_unidade_negocio_tipo'] ? "selected" : "";
            $html .= "<option value='{$value['nr_unidade_negocio_tipo']}' $selected>{$value['ds_unidade_negocio_tipo']}</option>";
        }

        echo $html;
    }

    if($action == "selecionar-item-produto"){
        $nr_unidade_negocio_tipo = $_POST['nr_unidade_negocio_tipo'];
        $nr_unidade_negocio_item = $_POST['nr_unidade_negocio_item'];

        $sql_unidade_negocio_item    = "SELECT nr_unidade_negocio_item, nm_item FROM unidade_negocio_item WHERE nr_unidade_negocio_tipo = $nr_unidade_negocio_tipo ORDER BY nm_item";
        $select_unidade_negocio_item = executar($sql_unidade_negocio_item);

        $html = "<option value=''>SELECIONE</option>";
        foreach($select_unidade_negocio_item as $value){
            $selected = $nr_unidade_negocio_item == $value['nr_unidade_negocio_item'] ? "selected" : "";
            $html .= "<option value='{$value['nr_unidade_negocio_item']}' $selected>{$value['nm_item']}</option>";
        }

        echo $html;
    }

    if($action == "processo_cancelamento"){
        $nr_ctrt = $_POST['nr_ctrt'];

        executar('BEGIN');
	   $sql = 'UPDATE ctrt SET nr_status_contrato = "12" WHERE nr_ctrt = '.$nr_ctrt;
	   $res_ctrt = executar($sql);
	
	   #SELECIONA A PESSOA DO CONTRATO
	   $res_pessoa = executar('SELECT nr_pessoa, nr_cliente FROM ctrt WHERE nr_ctrt = '.$nr_ctrt.' LIMIT 1');
	   $nr_cliente = $res_pessoa[0]['nr_cliente'];

	   #SE EXISTIR UMA OS
	   $res_os = executar('SELECT nr_os FROM ordem_servico WHERE nr_ctrt = '.$nr_ctrt);
	   if(!empty($res_os)){
	   	#FECHA O CHAMADO E INSERE UMA MENSAGEM DE FECHAMENTO
	   	$res_os = $res_os[0]['nr_os'];

	   	$res_os_msg = executar('INSERT INTO ordem_servico_msg (nr_os_msg, nr_os, nr_func, mensagem, dt_mensagem) VALUES (default, '.$res_os.', '.$_SESSION['login']['nr_func'].', "Cancelamento em andamento.", NOW())');
	   }

	   if($res_ctrt){
		executar('COMMIT');
		echo 'Processo de cancelamento iniciado com sucesso!';
	   }
	   else{
	   	executar('ROLLBACK');
		echo 'Erro ao iniciar processo de cancelamento do contrato';
	   }
    }

    if($action == "reabilitar"){
        $nr_ctrt  = $_POST['nr_ctrt'];
        $mensagem = utf8_decode($_POST['mensagem']);

        executar('BEGIN');
        $sql = 'UPDATE ctrt SET nr_status_contrato = "1" WHERE nr_ctrt = '.$nr_ctrt;
        $res_ctrt = executar($sql);
        
        #SELECIONA A PESSOA DO CONTRATO
        $res_pessoa = executar('SELECT nr_pessoa, nr_cliente FROM ctrt WHERE nr_ctrt = '.$nr_ctrt.' LIMIT 1');
        $nr_cliente = $res_pessoa[0]['nr_cliente'];

        #SE EXISTIR UMA OS
        $res_os = executar('SELECT nr_os FROM ordem_servico WHERE nr_ctrt = '.$nr_ctrt);
        if(!empty($res_os)){
            #RE-ABRE O CHAMADO E INSERE UMA MENSAGEM DE FECHAMENTO
            $res_os = $res_os[0]['nr_os'];

            $res_os_msg = executar('INSERT INTO ordem_servico_msg (nr_os_msg, nr_os, nr_func, mensagem, dt_mensagem) VALUES (default, '.$res_os.', '.$_SESSION['login']['nr_func'].', "Contrato Reabilitado: '.$mensagem.'", NOW())');
        }
        
        if($res_ctrt){
            executar('COMMIT');
            echo 'Contrato reabilitado com sucesso!';
            //redireciona('../App_Pes/ListaContratos.php?id='.$nr_cliente.'#Produto');
        }
        else{
            executar('ROLLBACK');
            echo 'Erro ao reabilitar contrato';
            //redireciona('../App_Pes/ListaContratos.php?id='.$nr_cliente.'#Produto');
        }
    }

    if($action == "cancelar"){
        $mensagem = utf8_decode($_REQUEST['mensagem']);
        $nr_tipo  = utf8_decode($_REQUEST['nr_tipo']);
        $nr_ctrt  = $_REQUEST['nr_ctrt'];

	
	/*	ALTERA O STATUS PARA CANCELADO DO CONTRATO FECHA O CHAMADO SE ESTIVER ABERTO	*/
	/*	ADICIONA O MOTIVO DO FECHAMENTO		*/
	   executar('BEGIN');
	   
	   $res_ctrt = executar('UPDATE ctrt SET nr_status_contrato = "3", dt_cancelamento = NOW() WHERE nr_ctrt = '.$nr_ctrt);
	   $res_canc = executar('INSERT INTO ctrt_cancelado (nr_ctrt_cancelado, nr_ctrt, nr_tipo_cancelado, motivo) VALUES (default, '.$nr_ctrt.', "'.$nr_tipo.'", "'.$mensagem.'")');
	
	   #SELECIONA A PESSOA DO CONTRATO
	   $res_pessoa = executar('SELECT nr_pessoa, nr_cliente FROM ctrt WHERE nr_ctrt = '.$nr_ctrt.' LIMIT 1');
	   $nr_cliente = $res_pessoa[0]['nr_cliente'];

	   #SE EXISTIR UMA OS
	   $res_os = executar('SELECT nr_os FROM ordem_servico WHERE nr_ctrt = '.$nr_ctrt);
	   if(!empty($res_os)){
	   	#FECHA O CHAMADO E INSERE UMA MENSAGEM DE FECHAMENTO
	   	$res_os = $res_os[0]['nr_os'];

	   	$res_os_msg = executar('INSERT INTO ordem_servico_msg (nr_os_msg, nr_os, nr_func, mensagem, dt_mensagem) VALUES (default, '.$res_os.', '.$_SESSION['login']['nr_func'].', "'.$mensagem.'", NOW())');

	   	$res_os_up = executar('UPDATE ordem_servico SET status = "F" WHERE nr_ctrt = '.$nr_ctrt.' LIMIT 1');
	   }

	
	   if($res_ctrt && $res_canc){
		executar('COMMIT');
		echo 'Contrato cancelado com sucesso';
	   } else {
	   	executar('ROLLBACK');
		echo 'Erro ao cancelar o contrato';
	   }
	}
}

if($type == "ordem-servico"){
    if($action == "selecionar-lista"){
        // Recebe número do contrato
        $nr_ctrt = $_POST['nr_ctrt'];
        $flag    = $_POST['ctrt'];

        $where = $flag === true ? "ctrt.nr_ctrt = $nr_ctrt" : "ordem_servico.lead_ID = $nr_ctrt";

        // SQL base onde é selecionado informações básicas referente às O.S.
        $sql_ordem_servico = "SELECT 
                    ordem_servico.nr_os,
                    ordem_servico.titulo,
                    (SELECT nm_depto FROM depto WHERE depto.nr_depto = ordem_servico.nr_depto_destino ) as deptoEncaminhado,
                    depto.nm_depto,
                    DATE_FORMAT(ordem_servico.dt_abertura,'%d/%m/%Y %H:%i:%s') as dt_abertura,
                    ordem_servico.dt_fechamento,
                    UPPER(nm_pessoa) as nm_pessoa,
                    depto.nr_depto,
                    ordem_servico.nr_func,
                    ordem_servico.status
                FROM
                    ordem_servico
                        INNER JOIN
                    depto ON depto.nr_depto = ordem_servico.nr_depto
                        LEFT JOIN
                    ctrt ON ctrt.nr_ctrt = ordem_servico.nr_ctrt
                        LEFT JOIN
                    sla ON ctrt.nr_sla = sla.nr_sla
                        INNER JOIN
                    func ON ordem_servico.nr_func = func.nr_func
                        INNER JOIN
                    pessoa ON func.nr_pessoa = pessoa.nr_pessoa
                WHERE
                    $where";
        $select_ordem_servico = executar($sql_ordem_servico);

        // Inicia variável que receberá o HTML que será incorporado na tag <tbody>
        $html = "";

        // Valida se o select trouxe algum resultado
        if($select_ordem_servico && !empty($select_ordem_servico[0][0])){

            // Caso possua algum valor, inicia loop para montar corpo da table
            foreach($select_ordem_servico as $value){
                // OPERAÇÕES PARA OS CANCELADA/FECHADA
                // Caso a O.S. esteja fechada deixa o resgistro com caracteres vermelhos
                $os_fechada = $value['status'] == "F" ? "class='bg-danger' title='".utf8_decode("Ordem de Serviço Fechada")."'" : "";
                $value['status'] = "'".$value['status']."'";
                $editar_os  = '<img src="../imagens/icones/edit_rounded.ico" id="editar-os" data-toggle="modal" data-target="#modalOrdemServico" onclick="editaOS('.$value['nr_os'].', '.$value['status'].');" title="'.utf8_decode("Editar Ordem de Serviço").'" alt="Editar de OS" />';

                $html .= '<tr colspan="8" '.$os_fechada.'>
                                <td scope="row" colspan="1">'.$value['nr_os'].'</td>
                                <td colspan="1">'.$value['titulo'].'</td>
                                <td colspan="1">'.$value['dt_abertura'].'</td>
                                <td colspan="1">'.convertDateToBr($value['dt_fechamento']).'</td>
                                <td colspan="1">'.$value['nm_depto'].'</td>
                                <td colspan="1">'.$value['deptoEncaminhado'].'</td>
                                <td colspan="1">'.$value['nm_pessoa'].'</td>
                                <td colspan="1">'.$editar_os.'</td>
                            </tr>';
            }

        } else {

            // Caso não tenha nenhuma O.S. envia mensagem alertando
            $html .= '<tr colspan="8">
                        <td colspan="8" class="text-danger text-center"><b><'.utf8_decode("Não foi possível localizar nenhuma Ordem de Serviço.").'</b></td>
                    </tr>';

        }
        echo $html;
    }
}

if($type == "arquivo"){
    $nr_contrato = $_POST['nr_ctrt'];

    if($action == "seleciona-extensao-permitida"){
        $ext = getTiposPermitidos();
        $ext = implode(", ", $ext);

        echo utf8_decode($ext);
    }

    if($action == "upload"){
        $info = implode("\n - ", $_POST);

        if (empty($_FILES['nm_arquivo']) || $_FILES['nm_arquivo']['error'] !== UPLOAD_ERR_OK) {
            echo utf8_decode("Erro ao importar arquivo: ".$_FILES['nm_arquivo']['error']."\n".$info);

        } else if(file_exists($_SERVER['DOCUMENT_ROOT'].'/arquivos/'.$_FILES['nm_arquivo']['name'])) {
            echo 'Erro: Nome do arquivo ja existe. Renomeie o arquivo.\n'.$info;

        } else {
            
            $ds_arquivo = $_POST['ds_arquivo'];
            $k = new Upload($_FILES['nm_arquivo']);
            $upMax = $k->getUploadMax();
            $k->setDestino($_SERVER['DOCUMENT_ROOT'].'/arquivos/');

            $sql = 'INSERT INTO anexo
                        (nr_ctrt,
                        tipo,
                        nm_arquivo,
                        tamanho,
                        ds_arquivo,
                        dt_anexo,
                        status_anexo)
                    VALUES 
                        ('.$nr_contrato.','
                        .'"'.$k->getTipo().'",'
                        .'"'.$k->getArquivo().'",'
                        .'"'.$k->getTamanho().'",'
                        .'"'.$ds_arquivo.'",'
                        .'now(),'
                        .'"A");';
        
            executar('BEGIN');
            $res = executar($sql);
        
            if($k->moveUpload() && $res) {
                executar('COMMIT');
                echo utf8_decode("Upload efetuado com sucesso!");
            } else {
                executar('ROLLBACK');
                echo utf8_decode($k->erroMsg);
            }
        }
    }

    if($action == "download"){
        $html = "";
        $sql_anexo = "SELECT * FROM anexo WHERE nr_ctrt = $nr_contrato";

        $anexo = executar($sql_anexo);
        if($anexo){
            foreach($anexo as $a){
                $descricao = '"'.$a['ds_arquivo'].'"';
                $html .= "<tr class='w-100' id='anexo_{$a['nr_anexo']}'>
                                        <td>{$a['ds_arquivo']}</td>
                                        <td style='width: 15%'>
                                            <a href='$caminho/arquivos/{$a['nm_arquivo']}' class='link-visualizar-arquivo' target='_blank' title='Visualizar/Download arquivo: {$a['nm_arquivo']}'>
                                                <img src='$icones/download_rounded.png' width='20px' height='20px' />
                                            </a>
                                            <img src='../imagens/icones/cancel_rounded.png' style='width: 20px; height: 20px;' class='excluir-anexo' title='Excluir Anexo' onclick='excluirAnexo({$a['nr_anexo']}, $descricao);' alt='Excluir Anexo' />
                                        </td>
                                    </tr>";
            }
        } else {
            $html .= utf8_decode("<tr class='w-100 text-danger text-center'><td class='mx-auto'><b>Não Foi possível encontrar nenhum anexo referente à este contrato</b></td></tr>");
        }

        echo $html;
    }

    if($action == "download-zip"){
        $arquivos = executar("SELECT nm_arquivo FROM anexo WHERE nr_ctrt = $nr_contrato");

        // Criando Zip Temporário
        $cwd = $_SERVER['DOCUMENT_ROOT'].'/arquivos/';
        $nameZip = "arquivos_".(uniqid()).".zip";
        $pathZip = $cwd.$nameZip;
        $zip = new ZipArchive();
        $status_zip = $zip->open($pathZip, ZIPARCHIVE::CREATE);
        if ( $status_zip === true) {
            // Criando um diretorio chamado "teste" dentro do pacote
            //$z->addEmptyDir('teste');
            foreach($arquivos as $arq){
                $newFile = $cwd.$arq['nm_arquivo'];
                $zip->addFile($newFile);
            }
            $zip->close();

            // Download ZipFile PDF
            header('Content-Length: ' . filesize($pathZip));
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$nameZip.'"');

            readfile($pathZip);
    
            // Removendo Zip Temporário
            unlink($pathZip);
        } else {
            echo "Nao criou arquivo: $status_zip";
        }
    }

    if($action == "excluir-anexo"){
        $path     = $_SERVER['DOCUMENT_ROOT'].'/arquivos/';
        $nr_anexo = $_POST['nr_anexo'];
        $mensagem = "";
        $nErro    = 0;

        $nm_arquivo = executar("SELECT nm_arquivo FROM anexo WHERE nr_anexo = $nr_anexo");
        $nm_arquivo = $nm_arquivo[0][0];

        $arquivo = $path.$nm_arquivo;
        
        $file_exists = file_exists($arquivo);
        if($file_exists) unlink($arquivo);
        $sql_delete_anexo = "DELETE FROM anexo WHERE nr_anexo = $nr_anexo";

        executar("BEGIN");
        $delete_anexo = executar($sql_delete_anexo);

        if($delete_anexo && $file_exists){
            executar("COMMIT");
            $mensagem = utf8_encode("Anexo excluido com sucesso!");
        } else {
            executar("ROLLBACK");
            $nErro += 1;
            $mensagem = utf8_encode("Não foi possível escluir o anexo. Entre em contato com o suporte!");
        }

        $return = array('mensagem' => $mensagem,
                        'erro' => $nErro);
        echo json_encode($return);
    }
}

if($type == "lista-material"){
    if($action == "grava-servico"){
        $nErro = 0;
        $mensagem = "";

        $nr_custo         = $_POST['nr_custo'];
        $nr_servico       = $_POST['nr_material_servico'];
        $nr_fornecedor2   = $_POST['nr_fornecedor2'];
        $nr_qtdade        = $_POST['nr_qtdade'];
        $nr_valor_servico = str_replace(",", ".", str_replace(".", "", $_POST['nr_valor_servico']));

        executar('BEGIN'); 
        $sql = 'INSERT INTO ctrt_servico (nr_custo,nr_servico,nr_fornecedor,nr_qtdade,nr_valor) VALUES ('.$nr_custo.','.$nr_servico.','.$nr_fornecedor2.','.$nr_qtdade.','.$nr_valor_servico.')';
        $res2 = executar($sql); 

        if(!$res2){
            $nErro += 1;
            $mensagem .= utf8_decode("Não foi possível inserir o serviço!");

        } else {
            $sql = 'UPDATE ctrt_custo SET nr_valor_total = (nr_valor_total + ('.$nr_valor_servico*$nr_qtdade.')) WHERE nr_custo = '.$nr_custo;
            $res = executar($sql);
            if(!$res){
                $nErro += 1;
                $mensagem .= utf8_decode("Ocorreu um erro ao atualizar o valor da lista!");
            }
        }

        if($nErro == 0) {
            executar('COMMIT');
            echo utf8_decode("Serviço inserido com sucesso!");
        } else { 
            executar('ROLLBACK');
            echo $mensagem;
        }
    }

    if($action == "grava-material"){
        $nErro = 0;
        $mensagem = "";

        $nr_custo         = $_POST['nr_custo'];
        $nr_material      = $_POST['nr_material_servico'];
        $nr_fornecedor2   = $_POST['nr_fornecedor2'];
        $nr_qtdade        = $_POST['nr_qtdade'];
        $nr_valor_servico = str_replace(",", ".", str_replace(".", "", $_POST['nr_valor_servico']));

        $total_material = executar('SELECT COUNT(*) as total FROM ctrt_material WHERE nr_material = '.$nr_material.' AND nr_custo = '.$nr_custo);
        if($total_material[0]['total'] == 1) {
            $sql = 'UPDATE ctrt_material SET nr_qtdade = nr_qtdade + '.$nr_qtdade.', status_2 = "A" WHERE nr_material = '.$nr_material;
        } else {
            $sql = 'INSERT INTO ctrt_material (nr_material,nr_custo,nr_qtdade,status_2) VALUES ('.$nr_material.','.$nr_custo.','.$nr_qtdade.',"A")';
        }

        executar("BEGIN");
        $res = executar($sql);
        if($res){
            $sql = 'UPDATE ctrt_custo SET nr_valor_total = (nr_valor_total + (SELECT (vl_custo*'.$nr_qtdade.') FROM material WHERE nr_material = '.$nr_material.')) WHERE nr_custo = '.$nr_custo;
            $res = executar($sql);

            if(!$res){
                $nErro += 1;
                $mensagem .= utf8_decode("Ocorreu um erro ao atualizar o valor da lista!");
            }     
        } else {
            $nErro += 1;
            $mensagem .= utf8_decode("Não foi pssível acrescentar o material a lista.");
        }
        
        if($nErro == 0) {
            executar('COMMIT');
            echo utf8_decode("Material inserido com sucesso!");
        } else { 
            executar('ROLLBACK');
            echo $mensagem;
        }
    }


    if($action == "grava-site"){
        $nr_ctrt = $_POST['nr_ctrt'];
        $estacao = utf8_decode($_POST['estacao']);

        executar("BEGIN");

        $res = executar('INSERT INTO ctrt_custo (nr_ctrt,nm_estacao,nr_funcionario) VALUES ('.$nr_ctrt.',"'.$estacao.'",'.$_SESSION['login']['nr_func'].')');
        if($res){
            $erro = 0;
            $mensagem = utf8_encode("Site inserido com sucesso!");

            $nr_custo = executar("SELECT last_insert_id() FROM ctrt_custo LIMIT 1");
            executar("COMMIT");
        } else {
            $erro = 1;
            $mensagem = utf8_decode("Não foi possível inserir o site!");
            executar("ROLLBACK");
        }

        $return = array('nr_custo' => $nr_ctrt,
                        'estacao' => $estacao,
                        'erro' => $erro,
                        'mensagem' => $mensagem);
        echo json_encode($return);
    }

    if($action == "excluir-lista"){
        $nr_custo = $_POST['nr_custo'];

        executar("BEGIN");
        $sql = 'DELETE FROM ctrt_custo WHERE nr_custo = '.$nr_custo;
        $res = executar($sql);

        if($res){
            executar("COMMIT");
            echo utf8_decode("Exclusão realizada com sucesso!");
        } else {
            executar("ROLLBACK");
            echo utf8_decode("Erro ao tentar excluir.\n\nEntre em contato com o suporte.");
        }
    }

    if($action == "excluir-material"){
        $nr_ctrt_material = $_POST['nr_ctrt_material'];
        $nr_custo = $_POST['nr_custo'];
        $total = $_POST['total'];

        executar("BEGIN");
        $sql = 'DELETE FROM ctrt_material WHERE nr_ctrt_material = '.$nr_ctrt_material;
        $res = executar($sql);

        $sql = 'UPDATE ctrt_custo SET nr_valor_total = nr_valor_total - '.$total.' WHERE nr_custo = '.$nr_custo;
        $res2 = executar($sql);

        if($res && $res2){
            executar("COMMIT");
            echo utf8_decode("Exclusão realizada com sucesso!");
        } else {
            executar("ROLLBACK");
            echo utf8_decode("Erro ao tentar excluir.\n\nEntre em contato com o suporte.");
        }
    }

    if($action == "excluir-servico"){
        $nr_ctrt_servico = $_POST['nr_ctrt_servico'];
        $nr_custo = $_POST['nr_custo'];
        $total = $_POST['total'];

        executar("BEGIN");
        $sql = 'DELETE FROM ctrt_servico WHERE nr_ctrt_servico = '.$nr_ctrt_servico;
        $res = executar($sql);

        $sql = 'UPDATE ctrt_custo SET nr_valor_total = nr_valor_total - '.$total.' WHERE nr_custo = '.$nr_custo;
        $res2 = executar($sql);

        if($res && $res2){
            executar("COMMIT");
            echo utf8_decode("Exclusão realizada com sucesso!");
        } else {
            executar("ROLLBACK");
            echo utf8_decode("Erro ao tentar excluir.\n\nEntre em contato com o suporte.");
        }
    }

    if($action == "altera-status-lista"){
        $status_lista = $_POST['status'];
        $nr_custo = $_POST['nr_custo'];

        executar("BEGIN");
        $sql = 'UPDATE ctrt_custo SET '
						.'ds_status = "'.$status_lista.'",'
						.'nr_funcionario = '.$_SESSION['login']['nr_func'].','
						.'dt_aprovacao = now() '
                    .'WHERE nr_custo = '.$nr_custo;
        $res = executar($sql);

        if($res){
            executar("COMMIT");
            echo utf8_encode("Alteracao realizada com sucesso!");
        } else {
            executar("ROLLBACK");
            echo utf8_encode("Erro ao tentar alterar o status.\n\nEntre em contato com o suporte.");
        }
    }

    if($action == "seleciona-site"){
        $nr_ctrt = $_POST['nr_ctrt'];
        $ctrt_custo = executar("SELECT nr_custo, nm_estacao FROM ctrt_custo WHERE nr_ctrt = $nr_ctrt");

        $return = array();

        foreach($ctrt_custo as $val){
            $return["{$val['nr_custo']}"] = utf8_decode($val['nm_estacao']);
        }

        echo json_encode($return);
    }

    if($action == "seleciona-material"){
        $nome = $_POST['nome'];
        $res_material	= executar('SELECT nr_material, nm_material FROM material WHERE nm_material like "%'.$nome.'%" AND status_material = "A"  ORDER BY nm_material LIMIT 10');

        $html = "";
        foreach($res_material as $v){
            $html .= '<tr style="font-size: 12px; width: 100%;" class="ml-3">
                        <td><input type="radio" name="nr_material" value="'.$v['nr_material'].'"></td>
                        <td id="nr_material_'.$v['nr_material'].'">'.$v['nm_material'].'</td>
                    </tr>';
        }
        echo $html;
    }

    if($action == "seleciona-fornecedor"){
        $nr_servico = $_POST['servico'];
        $sql = executar("SELECT 
                    servico_fornecedor.nr_fornecedor,
                    nm_fantasia 
                FROM pessoa 
                    JOIN fornecedor USING(nr_pessoa) 
                    JOIN servico_fornecedor USING(nr_fornecedor) 
                WHERE servico_fornecedor.nr_servico = $nr_servico");

        $return = array();
        foreach($sql as $v){
            $return["{$v['nr_fornecedor']}"] = utf8_encode($v['nm_fantasia']);
        }

        echo json_encode($return);
    }

    // Monta tabela com as listas e seus materiasi/serviços já inseridos
    if($action == "seleciona-lista"){
        $nr_custo = $_POST['nr_custo'];
        $site = executar("SELECT * FROM ctrt_custo WHERE nr_custo = $nr_custo");

        // MATERIAIS
        // SELECIONA informações base dos materiais da lista
        $sql = "SELECT 
                    nr_ctrt_material,
                    nr_custo,
                    ds_classe_material AS descricao_classe,
                    nm_material AS descricao_material,
                    ctrt_material.nr_qtdade AS quantidade,
                    UPPER(cd_medida) AS medida,
                    (ctrt_material.nr_qtdade * vl_custo) AS total_material,
                    vl_custo
                FROM
                    classe_material
                        JOIN
                    material ON classe_material.nr_classe_material = material.nr_classe_material
                        JOIN
                    ctrt_material ON material.nr_material = ctrt_material.nr_material
                WHERE
                    nr_custo = $nr_custo";
        $listaMaterial = executar($sql);

        // SELECIONA valores de custo dos materiais da lista
        $sql = "SELECT 
                    SUM(ctrt_material.nr_qtdade * vl_custo) AS total_lista
                FROM
                    ctrt_material
                        JOIN
                    material USING (nr_material)
                WHERE
                    nr_custo =  $nr_custo";
        $total_lista_material = executar($sql);

        // SERVIÇOS
        // SELECIONA informações base dos serviços da lista
        $sql = "SELECT 
                    nr_ctrt_servico,
                    nm_pessoa,
                    ds_servico,
                    UPPER(servico.cd_medida) AS cd_medida,
                    nr_qtdade,
                    (nr_qtdade * nr_valor) AS valor_total,
                    nr_valor AS valor,
                    vl_referencia
                FROM
                    ctrt_servico
                        JOIN
                    servico ON servico.nr_servico = ctrt_servico.nr_servico
                        JOIN
                    fornecedor ON fornecedor.nr_fornecedor = ctrt_servico.nr_fornecedor
                        JOIN
                    pessoa ON pessoa.nr_pessoa = fornecedor.nr_pessoa
                WHERE
                    nr_custo = $nr_custo";
        $listaServico = executar($sql);	
        
        // SELECIONA valores de custo dos serviços da lista
        $sql = "SELECT 
                    SUM(nr_qtdade*nr_valor) as total_lista 
                FROM 
                    ctrt_servico 
                WHERE 
                    nr_custo = $nr_custo";
        $total_lista_servico = executar($sql);
        $option .= "<option value=''>SELECIONE</option>";
        $arr = array('A' => 'APROVADA','R' => 'REPROVADA');
        foreach($arr as $k => $v) {
            $sel = ($site[0]['ds_status']  == "$k") ? 'selected' : '';
            $option .= '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
        }

        $html_title = "";
        $html_title = "{$site[0]['nm_estacao']} - Total Geral: R$ ".mascaraMoeda($site[0]['nr_valor_total'],'tela');
        $html_title .= "<select class='custom-select custom-select-sm ml-5 w-25' id='status-lista' onchange='gravaStatusLista($(this), $nr_custo);' style='font-size: 12px;'>$option</select>";

        if($site[0]['ds_status'] != "A") {
            $html_title .= "<img src='../imagens/icones/cancel_rounded.png' style='width: 20px; height: 20px;' id='excluir_lista' class='excluir-lista ml-3' onclick='excluirLista($nr_custo);' title='Excluir Lista'alt='Excluir Lista' />";
        }

        $html = "";
        $html .= '
            <table class="table table-sm" width="100%">';
            if(!empty($listaMaterial)) {
                $html .= '<tr>
                            <th style="background-color:#B9BCFB;color:#000;font-size: 14px;" colspan="8">					
                                Lista de Material - Total Lista: R$ '.mascaraMoeda($total_lista_material[0]['total_lista'],'tela').'
                            </th>
                        </tr>
                        <tr>
                            <table border="0" id="listagem" width="100%">
                                <tr id="header">
                                    <td colspan="3">Material</td>
                                    <td colspan="1">Quantidade</td>
                                    <td colspan="1">Medida</td>
                                    <td colspan="1">Valor</td>
                                    <td colspan="1">Total</td>
                                    <td colspan="1"></td>
                                </tr>';
                foreach($listaMaterial as $k=>$v) {
                    $html .= '
                                <tr style="font-size: 15px;" id="excluir_material_'.$v['nr_ctrt_material'].'">
                                    <td colspan="3">'.$v['descricao_material'].'</td>
                                    <td colspan="1">'.$v['quantidade'].'</td>
                                    <td colspan="1">'.$v['medida'].'</td>
                                    <td colspan="1">'.mascaraMoeda($v['vl_custo'],'tela').'</td>
                                    <td colspan="1">'.mascaraMoeda($v['total_material'],'tela').'</td>';

                                if($site[0]['ds_status'] != 'A') {
                                    $html .= "
                                    <td colspan='1' class='text-right'>
                                        <img src='../imagens/icones/cancel_rounded.png' onclick='excluirMaterial({$v['nr_ctrt_material']}, {$v['total_material']}, $nr_custo);' style='width: 20px; height: 20px;' class='excluir-material' value='{$v['nr_ctrt_material']}' title='Excluir Material'alt='Excluir Material' />
                                    </td>";
                                }
                        $html .= '</tr>';
                }
                $html .= '</table>
                </tr>';
            }
        
            if(!empty($listaServico)) {
                $html .= '<table class="table table-sm mt-3">
                            <tr style="margin-top: 5px;">
                                <th style="background-color:#B9BCFB;color:#000;font-size: 14px;" colspan="8">
                                    Lista de Servi&ccedil;os - Total Lista: R$ '.mascaraMoeda($total_lista_servico[0]['total_lista'],'tela').'
                                </th>
                            </tr>
                        </table>
                        <tr>
                            <table border="0" id="listagem" width="100%">
                                <tr id="header">
                                    <td>Servi&ccedil;o</td>
                                    <td>Fornecedor</td>
                                    <td>Quantidade</td>
                                    <td>Medida</td>
                                    <td>Referencia</td>
                                    <td>Valor</td>
                                    <td>Total</td>
                                    <td></td>
                                </tr>';
                    foreach($listaServico as $k=>$v) {
                    $html .= '
                                <tr style="font-size: 15px;" id="excluir_servico_'.$v['nr_ctrt_servico'].'">
                                    <td>'.$v['ds_servico'].'</td>
                                    <td>'.$v['nm_pessoa'].'</td>
                                    <td>'.$v['nr_qtdade'].'</td>
                                    <td>'.$v['cd_medida'].'</td>
                                    <td>'.mascaraMoeda($v['vl_referencia'],'tela').'</td>
                                    <td>'.mascaraMoeda($v['valor'],'tela').'</td>
                                    <td>'.mascaraMoeda($v['valor_total'],'tela').'</td>';

                                if($site[0]['ds_status'] != "A") {
                                    $html .= "
                                    <td class='text-right'>
                                        <img src='../imagens/icones/cancel_rounded.png' onclick='excluirServico({$v['nr_ctrt_servico']}, {$v['valor_total']}, $nr_custo);' style='width: 20px; height: 20px;' class='excluir-servico' value='{$v['nr_ctrt_servico']}' title='Excluir Serviço'alt='Excluir Serviço' />
                                    </td>";
                                }
                        $html .= '</tr>';
                    }

                    $html .= '</table>
                        </tr>
                    </table>';

                }

            $html .= '
            </table>
            </td>
            </div>
        </tr>
        </table>';
            
        $ret = array('html' => utf8_encode($html),
                    'html_title' => utf8_encode($html_title));
        echo json_encode($ret);
    }

    if($action == "info-servico"){
        $nr_servico = $_POST['nr_servico'];

        $select_servico = executar("SELECT ds_servico, vl_referencia FROM servico WHERE nr_servico = $nr_servico");

        $data = array('descricao' => utf8_encode($select_servico[0]['ds_servico']),
                    'valor' => number_format($select_servico[0]['vl_referencia'], 2, ",", "."));

        echo json_encode($data);
    }
}

if($type == "financeiro"){
    if($action == "selecionar"){
        $nr_ctrt = addslashes($_POST['nr_ctrt']);
        $html = "";

        $sql = "SELECT DISTINCT boletos_separados, unif_svascm FROM payment_fornecedor pf INNER JOIN payment p ON pf.nr_payment_fornecedor = p.nr_payment_fornecedor WHERE p.nr_ctrt = $nr_ctrt";
        $info_cliente = executar($sql);
        if($info_cliente){
            $parcelas = $info_cliente[0]['unif_svascm'] == 'S' ? unifica($nr_ctrt) : naoUnifica($nr_ctrt, $info_cliente[0]['boletos_separados']) ;

            foreach($parcelas as $v){
                $situacao_color = "";
                switch($v['situacao']){
                    case 1:
                        $hoje = strtotime(date("Y-m-d"));
                        $vencimento = strtotime($v['dt_vencto']);

                        $situacao_color = $hoje > $vencimento ? "text-danger" : "text-success";
                        $v['situacao']  = $hoje > $vencimento ? utf8_encode("Vencido") : "À vencer";
                        break;
                    case 2:
                        $v['situacao']  = "Pago";
                        break;
                }
                $v['nr_valor']  = number_format($v['nr_valor'], 2, ",", ".");
                $v['dt_vencto'] = convertDateToBr($v['dt_vencto']);

                $html .= "<tr>
                            <td scope='row'>{$v['nr_payment']}</td>
                            <td scope='row'>{$v['parcela']}</td>
                            <td scope='row'>{$v['nr_valor']}</td>
                            <td scope='row'>{$v['dt_vencto']}</td>
                            <td scope='row' class='$situacao_color'>{$v['situacao']}</td>
                        </tr>";

            }

        } else {
            $html = utf8_decode("<tr class='text-danger w-100 text-center'><b>Não foi possível encontrar nenhuma parcela referente à este contrato.</b></tr>");
        }

        echo utf8_decode($html);
    }
}

if($action == 'busca' && $type == 'tipo-logradouro'){
    $logradouro = strtoupper(trim($_POST['logradouro']));

    $tipo = executar("SELECT codigo FROM tipo_logradouro WHERE descricao LIKE '%$logradouro%' LIMIT 1");
    echo $tipo[0][0];
}

if($type == "info"){
    if($action == "chart"){
        $nr_empresa = $_POST['nr_empresa'];
        $nr_empresa = $nr_empresa == "geral" ? "" : " AND (ctrt.nr_empresa = $nr_empresa OR cliente.nr_empresa = $nr_empresa)";

        $mes = date("n");
        $ano = date("Y");

        $retorno = array();
        $z = 0;
        for($i = 11; $i >= 0; $i--){
            $mes_temp = $mes - $i;
            if($mes_temp <= 0){
                $mes_temp = 12 + $mes_temp;
                $ano_temp = $ano - 1;
            } else {
                $ano_temp = $ano;
            }
            $mes_temp = strlen($mes_temp) == 1 ? "0$mes_temp" : $mes_temp;

            $mes_recebimento   = date("$ano_temp-$mes_temp");

            $sql = "SELECT 
                        (SUM(mensal_valor) + SUM(install_valor) + SUM(produto_valor) + SUM(servico_valor)) AS valor
                    FROM
                        ctrt
                            INNER JOIN
                        ctrt_valor ON ctrt.nr_ctrt = ctrt_valor.nr_ctrt
                            INNER JOIN
                        pessoa ON ctrt.nr_pessoa = pessoa.nr_pessoa
                            INNER JOIN
                        cliente ON pessoa.nr_pessoa = cliente.nr_pessoa
                    WHERE
                        dt_ativacao <= '$mes_recebimento-31'
                            AND dt_vencto > '$mes_recebimento-31'
                            AND (dt_cancelamento > '$mes_recebimento-31' OR ctrt.nr_status_contrato = 1) $nr_empresa";

            $valor_pago = executar($sql);
            $retorno[$z]['date']  = "$mes_recebimento-28";
            $retorno[$z]['value'] = $valor_pago[0]['valor'];
            $z += 1;
        }

        echo json_encode($retorno);
    }

    if($action == "contratocliente"){
        $nr_empresa = $_POST['nr_empresa'];
        $nr_empresa = $nr_empresa == "geral" ? "" : " AND ctrt.nr_empresa = $nr_empresa ";

        $contratos = executar("SELECT COUNT(nr_ctrt) FROM ctrt WHERE nr_status_contrato = 1 $nr_empresa");

        $nr_empresa = $_POST['nr_empresa'] == "geral" ? "" : " AND (ctrt.nr_empresa = {$_POST['nr_empresa']} OR cliente.nr_empresa = {$_POST['nr_empresa']})";
        $clientes  = executar("SELECT 
                                    COUNT(nr_cliente) AS numClientes
                                FROM
                                    cliente
                                WHERE
                                    nr_cliente IN (SELECT DISTINCT
                                            cliente.nr_cliente
                                        FROM
                                            cliente
                                                INNER JOIN
                                            pessoa ON cliente.nr_pessoa = pessoa.nr_pessoa
                                                INNER JOIN
                                            ctrt ON pessoa.nr_pessoa = ctrt.nr_pessoa
                                        WHERE
                                            nr_status_contrato = 1 $nr_empresa)");

        $retorno = array();
        $retorno['contrato'] = $contratos[0][0];
        $retorno['cliente']  = $clientes[0][0];

        echo json_encode($retorno);
    }
}

###################################################################
#                           FUNÇÕES
###################################################################

function verificaCliente($nr_pessoa, $nr_ctrt){
    $return = array("erro" => 0, "mensagem" => "");

    $pessoa = executar("SELECT nm_pessoa, nr_cnpjcpf, nr_ierg FROM pessoa WHERE nr_pessoa = $nr_pessoa");
    $nr_cnpjcpf = $pessoa[0]['nr_cnpjcpf'];

    $select_pay_for = executar("SELECT nr_payment_fornecedor FROM payment_fornecedor WHERE CGCCPF LIKE '%$nr_cnpjcpf%'");
    if($select_pay_for){
        $nr_payment_fornecedor = $select_pay_for[0][0];

        $sql_update_payment_fornecedor = "UPDATE payment_fornecedor SET nr_pessoa = $nr_pessoa WHERE nr_payment_fornecedor = $nr_payment_fornecedor";
        $return['sql'] = $sql_update_payment_fornecedor;

        $update_payment_fornecedor = executar($sql_update_payment_fornecedor);

        if(!$update_payment_fornecedor){
            $return['erro'] += 1;
            $return['mensagem'] .= utf8_decode("Não foi possível atualizar o cliente do financeiro com o código do cliente no comercial!");
        } else {
            $select_cliente = executar("SELECT nr_payment_fornecedor FROM payment_fornecedor WHERE CGCCPF LIKE '%$nr_cnpjcpf%'");
            $return['nr_payment_fornecedor'] = $select_cliente[0][0];
            $return['acao'] = 'update';
        }

    } else {
        $nr_ender = executar("SELECT nr_ender FROM ctrt WHERE nr_ctrt = $nr_ctrt");
        $nr_ender = strpos($nr_ender[0][0], ';') ? strstr($nr_ender[0][0], ';', true) : $nr_ender[0][0];

        $ender = executar("SELECT CONCAT(tp_logradouro, ' ',nm_logradouro, ' - ', nr_logradouro, ', ', ds_complemento) AS Endereco, nr_cep AS CEP, nm_bairro AS Bairro, nm_cidade AS Cidade, nm_estado AS Estado FROM ender WHERE nr_ender = $nr_ender");

        $sql_payment_fornecedor = "INSERT INTO payment_fornecedor (nr_pessoa, Empresa, RazaoSocial, CGCCPF, INSCRG, Endereco, Bairro, Cidade, Estado, CEP, TipoPessoa, tipo_2, status) VALUES ($nr_pessoa, '{$pessoa[0]['nm_pessoa']}', '{$pessoa[0]['nm_pessoa']}', '{$pessoa[0]['nr_cnpjcpf']}', '{$pessoa[0]['nr_ierg']}', '{$ender[0]['Endereco']}', '{$ender[0]['Bairro']}', '{$ender[0]['Cidade']}', '{$ender[0]['Estado']}', '{$ender[0]['CEP']}', 'J', 'C', 'A')";
        $return['sql'] = $sql_payment_fornecedor;

        $insert_payment_fornecedor = executar($sql_payment_fornecedor);

        if(!$insert_payment_fornecedor){
            $return['erro'] += 1;
            $return['mensagem'] .= utf8_decode("Não foi possível inserir o cliente no financeiro.");
        } else {
            $select_cliente = executar("SELECT last_insert_id() FROM payment_fornecedor LIMIT 1");
            $return['nr_payment_fornecedor'] = $select_cliente[0][0];
            $return['acao'] = 'insert';
        }
    }

    return $return;
}

function criaContratoPreVenda($nr_pessoa, $nr_ctrt){
    $return = array("erro" => 0, "mensagem" => "");

    $sql_ctrt_prefaturamento = "INSERT INTO ctrt_prefaturamento (nr_ctrt, nr_pessoa, dt_ativacao, nr_func_ativacao, status) VALUES ($nr_ctrt, $nr_pessoa, NOW(), {$_SESSION['login']['nr_func']}, 'A')";
    $insert_ctrt_prefaturamento = executar($sql_ctrt_prefaturamento);

    if(!$insert_ctrt_prefaturamento){
        $return['erro'] += 1;
        $return['mensagem'] .= utf8_decode("Não foi possível inserir o contrato na rotina de Pré-Faturamento.");
    } else {
        $select_ctrt_prefaturamento = executar("SELECT last_insert_id() FROM ctrt_prefaturamento LIMIT 1");
        $return['nr_ctrt_prefaturamento'] = $select_ctrt_prefaturamento[0][0];
        $return['acao'] = 'insert';
    }

    return $return;
}

function enviaEmailOS($mensagemOS, $nr_cliente, $nr_os, $titulo, $mensagem){

    // Carrega informações para envio do e-mail
    $mailDepto = executar('SELECT ds_email FROM depto WHERE nr_depto = 6');
    $mensagemOS = str_replace(chr(13),'<br />',$mensagem);
    $html = layoutChamado($nr_cliente, $nr_os, $titulo, $mensagemOS, null);
    
    #ENVIA O E-MAIL
    $mailer = new EVM_Mailer();
    $mailer->set_sender('Erp Raicom','no-reply@gruporedes.global');
    $mailer->set_subject('[erp_raicom] Ordem de Serviços '.$nr_os);
    $mailer->set_message_type('html');
    $mailer->set_message($html);
    $mailer->add_recipient('DEPTO', "luis.novais@gruporedes.global");
    #$mailer->add_recipient('DEPTO', $mailDepto[0]['ds_email']);
    $mailer->add_CC($_SESSION['login']['nm_pessoa'], str_replace('raicom.com.br', 'gruporedes.global', $_SESSION['login']['nm_user']));

    $mailer->send();
}

function enviaEmailFinanceiro($cliente, $nr_ctrt, $nr_ctrt_prefaturamento){
    $select_pf = executar("SELECT Empresa, CGCCPF, INSCRG FROM payment_fornecedor WHERE nr_payment_fornecedor = $cliente");
    $select_ctrt = executar("SELECT nm_ctrt FROM ctrt WHERE nr_ctrt = $nr_ctrt");
    $html = layoutClienteFinanceiro($select_pf[0]['Empresa'], $select_pf[0]['CGCCPF'], $select_pf[0]['INSCRG'], $select_ctrt[0]['nm_ctrt'], $cliente, $nr_ctrt_prefaturamento);
    
    #ENVIA O E-MAIL
    $mailer = new EVM_Mailer();
    $mailer->set_sender('Erp Raicom','no-reply@gruporedes.global');
    $mailer->set_subject('[erp_raicom] Contrato Ativado - '.$nr_ctrt);
    $mailer->set_message_type('html');
    $mailer->set_message($html);
    $mailer->add_recipient('DEPTO', "luis.novais@gruporedes.global");
    #$mailer->add_recipient('DEPTO', 'financeiro@gruporedes.global');
    $mailer->add_CC($_SESSION['login']['nm_pessoa'], str_replace('raicom.com.br', 'gruporedes.global', $_SESSION['login']['nm_user']));

    $mailer->send();
}
  

function layoutClienteFinanceiro($cliente, $cpfcnpj, $ierg, $nome_contrato, $nr_pf, $nr_ctrt_prefaturamento){
    $cpfcnpj = strlen($cpfcnpj) > 11 ? mascara($cpfcnpj, 'cnpj') : mascara($cpfcnpj, 'cpf');
    $ierg    = $ierg == "ISENTO" ? $ierg : strlen($ierg) > 9 ? mascara($ierg, 'ie') : mascara($ierg, 'rg');

    $html = '
    <html>
    <head>
    </head>
    <body>
        <table border="0" cellpadding="2" width="100%" style="font:12px arial;">
            <tr>
                <td colspan="2">
                    <img src="http://erp.gruporedes.global/imagens/logo_grupo-redes.png" height="150px" /><br />
                    <strong style="font-size:14px;color:#2A176F;">Grupo Redes</strong>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" style="font:12px arial;">O contrato <strong>'.$nome_contrato.'</strong> foi ativado! <a href="'.$_SERVER['SERVER_NAME'].'/App_Pes/PreFaturamento.php?id='.$nr_ctrt_prefaturamento.'">Clique aqui</a> para gerar o Contas a Receber. </td>
            </tr>
            <tr>
                <td colspan="2">Com a ativação um cliente foi alterado no financeiro:</td>
            </tr>
            <tr>
                <td width="10%"><strong>Cliente:<strong></td>
                <td colspan="1" style="font:12px arial;">'.$cliente.'</td>
            </tr>
            <tr>
                <td width="10%"><strong>CNPJ/CPF:<strong></td>
                <td colspan="1" style="font:12px arial;">'.$cpfcnpj.'</td>
            </tr>
            <tr>
                <td width="10%"><strong>I.E./RG:<strong></td>
                <td colspan="1" style="font:12px arial;">'.$ierg.'</td>
            </tr>
            <tr>
                <td colspan="2" style="font:12px arial;">Para atualizar o cadastro do cliente, <a href="'.$_SERVER['SERVER_NAME'].'/App_Pes/AddFornClieFinanc.php?id='.$nr_pf.'">Clique aqui</a>.</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">Grupo Redes<br /><a href="http://gruporedes.global/" title="Grupo Redes">http://gruporedes.global/</a></td>
            </tr>
        </table>
    </body>
    </html>';

    return utf8_decode($html);
}

function naoUnifica($contrato, $bs){

    $pc_list = $bs == 'S' ? "" : " AND p.nr_item NOT IN (323, 174, 467) ";

    $sql = "SELECT 
                p.nr_payment AS nr_payment
                ,p.nr_item AS item
                ,pd.nr_payment_detalhe AS nr_payment_detalhe
                ,pd.nr_payment_parcela AS parcela
                ,pd.nr_valor AS nr_valor
                ,pd.dt_vencto AS dt_vencto
                ,pd.nr_situacao_pagamento AS situacao
            FROM
                payment_detalhe pd
                    INNER JOIN
                payment p ON pd.nr_payment = p.nr_payment
                    INNER JOIN
                conta_receber cr ON p.nr_payment = cr.nr_payment
            WHERE p.nr_ctrt = ".$contrato." $pc_list  ORDER BY pd.dt_vencto, p.nr_payment, pd.nr_payment_parcela";
    $parcelas = executar($sql);
    
    if($bs == 'S'){
        $reg = $parcelas;
    } else if($parcelas) {
        $list_parc = 0;
        $zx = 0;
        $flagSVA = false;
        $cparcela = count($parcelas);

        while($zx < $cparcela){
            if($parcelas[$zx]['item'] == 324){
                
                $flagSVA = $cparcela > 1 ? true : false;
                $parc_temp = $parcelas[$zx]['parcela'];
                $sql = "SELECT 
                        pd.nr_payment_detalhe AS nr_payment_detalhe
                        ,pd.nr_valor AS nr_valor
                    FROM
                        payment_detalhe pd
                            INNER JOIN
                        payment p ON pd.nr_payment = p.nr_payment
                            INNER JOIN
                        conta_receber cr ON p.nr_payment = cr.nr_payment
                    WHERE p.nr_ctrt = ".$contrato." AND p.nr_item IN (323, 467) AND pd.dt_vencto = '".$parcelas[$zx]['dt_vencto']."' AND (pd.nr_payment_parcela = '{$parcelas[$zx]['parcela']}'  OR p.nr_item = 174 ) ORDER BY pd.dt_vencto, p.nr_payment, pd.nr_payment_parcela";
                $parcelas_2 = executar($sql);
    
                if($parcelas_2){
                    foreach($parcelas_2 as $v){
                        $parcelas[$zx]['nr_valor'] += $v['nr_valor'];
            
                        $list_parc = $list_parc == 0 ? $v['nr_payment_detalhe'] : $list_parc.", ".$v['nr_payment_detalhe'];
                    }
                }
            }
            $zx += 1;
        }

        if(!$flagSVA){
            $sql = "SELECT 
                        p.nr_payment AS nr_payment
                        ,pd.nr_payment_detalhe AS nr_payment_detalhe
                        ,pd.nr_payment_parcela AS parcela
                        ,pd.nr_valor AS nr_valor
                        ,pd.dt_vencto AS dt_vencto
                        ,pd.nr_situacao_pagamento as situacao
                    FROM
                        payment_detalhe pd
                            INNER JOIN
                        payment p ON pd.nr_payment = p.nr_payment
                            INNER JOIN
                        conta_receber cr ON p.nr_payment = cr.nr_payment
                    WHERE p.nr_item IN (323, 174, 467) AND nr_payment_detalhe NOT IN (".$list_parc.") AND p.nr_ctrt = ".$contrato." ORDER BY pd.dt_vencto, p.nr_payment, pd.nr_payment_parcela";
                $parcelas_2 = executar($sql);
    
                $reg = array_merge($parcelas, $parcelas_2);
        }else{
            $reg = $parcelas;
        }
    }

    return $reg;
}

function unifica($contrato){
    $count = 0;
    $sql = "SELECT 
                p.nr_payment AS nr_payment
                ,pd.nr_payment_detalhe AS nr_payment_detalhe
                ,pd.nr_payment_parcela AS parcela
                ,pd.nr_valor AS nr_valor
                ,pd.dt_vencto AS dt_vencto
                ,pd.nr_situacao_pagamento as situacao
            FROM
                payment_detalhe pd
                    INNER JOIN
                payment p ON pd.nr_payment = p.nr_payment
                    INNER JOIN
                conta_receber cr ON p.nr_payment = cr.nr_payment
            WHERE p.nr_ctrt = ".$contrato." AND p.nr_item <> 188";
    $parcelas = executar($sql);
    $arr_reg = array();

    if(!empty($parcelas)){
        for($zx = 0; $zx < count($parcelas); $zx++){
            if($arr_reg[$count]['dt_vencto'] == $parcelas[$zx]['dt_vencto'] ){
                $arr_reg[$count]['nr_valor'] += $parcelas[$zx]['nr_valor']; 
            }else{
                $arr_reg[$count]['nr_payment_fornecedor'] = $parcelas[$zx]['nr_payment_fornecedor'];
                $arr_reg[$count]['nr_payment_detalhe']    = $parcelas[$zx]['nr_payment_detalhe'];
                $arr_reg[$count]['parcela']               = $parcelas[$zx]['parcela'];
                $arr_reg[$count]['nr_valor']              = $parcelas[$zx]['nr_valor'];
                $arr_reg[$count]['dt_vencto']             = $parcelas[$zx]['dt_vencto'];
                $arr_reg[$count]['nr_ctrt']               = $parcelas[$zx]['nr_ctrt'];
                $arr_reg[$count]['situacao']              = $parcelas[$zx]['situacao'];

                $count = $zx == 0 ? $count : $count + 1;
            }
        }
    }

    $sql = "SELECT 
                p.nr_payment AS nr_payment
                ,pd.nr_payment_detalhe AS nr_payment_detalhe
                ,pd.nr_payment_parcela AS parcela
                ,pd.nr_valor AS nr_valor
                ,pd.dt_vencto AS dt_vencto
                ,pd.nr_situacao_pagamento as situacao
            FROM
                payment_detalhe pd
                    INNER JOIN
                payment p ON pd.nr_payment = p.nr_payment
                    INNER JOIN
                conta_receber cr ON p.nr_payment = cr.nr_payment
            WHERE p.nr_ctrt = ".$contrato." AND p.nr_item = 188";
    $parcelas = executar($sql);

    if(!empty($parcelas)){
        for($zx = 0; $zx < count($parcelas); $zx++){
            $count += 1;

            $arr_reg[$count]['nr_payment_fornecedor'] = $parcelas[$zx]['nr_payment_fornecedor'];
            $arr_reg[$count]['nr_payment_detalhe']    = $parcelas[$zx]['nr_payment_detalhe'];
            $arr_reg[$count]['parcela']               = $parcelas[$zx]['parcela'];
            $arr_reg[$count]['nr_valor']              = $parcelas[$zx]['nr_valor'];
            $arr_reg[$count]['dt_vencto']             = $parcelas[$zx]['dt_vencto'];
            $arr_reg[$count]['nr_ctrt']               = $parcelas[$zx]['nr_ctrt'];
            $arr_reg[$count]['situacao']              = $parcelas[$zx]['situacao'];
        }
    }
    return $arr_reg;
}



?>