<?
require_once("../config.php");
require_once("../conexao.php");
require_once("../funcoes.php");
require_once("../evm_mailer.php");
$action = strtolower($_POST['action']);
$type   = strtolower($_POST['type']);



if($action == 'busca-funil-leads'){
    if($type != 'geral'){
        $where_un = " AND leads.unidade_negocio = $type";
    }else{
        $where_un = "";
    }
    $sql = "SELECT count(leads.lead_ID) AS 'total_leads', lead_etapa_nome FROM leads INNER JOIN lead_etapa ON leads.lead_etapa_ID = lead_etapa.lead_etapa_ID WHERE leads.lead_etapa_ID <> 0 $where_un GROUP BY leads.lead_etapa_ID";
    $res_etapas = executar($sql);
    if($res_etapas){
        $leads = array();

        for($i = 0; $i < count($res_etapas); $i++){
            $leads[$i]['name']  = utf8_encode($res_etapas[$i]['lead_etapa_nome']);
            $leads[$i]['value'] = (int)$res_etapas[$i]['total_leads'];
        }

        echo json_encode($leads);
    }
}

if($action == "busca-meta-leads"){
    $regional = $_POST['regional'];
    $tempo    = $_POST['tempo'];

    $where = "";
    if(!empty($regional) && $regional <> "geral"){
        $where = " WHERE nr_empresa IN ($regional)";
    }

    $select_meta_diaria = executar("SELECT SUM(comercial_ligacoes_diarias) FROM empresa $where");

    if(!empty($tempo)){
        $dados = array();
        $meta = array();
        $realizado = array();

        if($tempo == "personalizado"){
            $data1 = convertDateToBd($_POST['data1']);
            $data2 = convertDateToBd($_POST['data2']);

            if(!empty($data1) && !empty($data2)){
                if(strtotime($data1) < strtotime($data2)){
                    $mes1 = date("Y-m", strtotime($data1));
                    $mes2 = date("Y-m", strtotime($data2));
                }else{
                    $mes1 = date("Y-m", strtotime($data2));
                    $mes2 = date("Y-m", strtotime($data1));
                }

                $dif = strtotime($mes1) - strtotime($mes2);
                $meses = floor($dif / (60 * 60 * 24 * 30)) *-1;
                
                for($i = 0; $i < $meses; $i++){

                    $ano = date("Y", strtotime("+$i month", strtotime($mes1)));
                    $mes = date("m", strtotime("+$i month", strtotime($mes1)));
                    
                    if($i == 0 && $mes1 == $mes2){
                        $data_inicial = $data1;
                        $data_final   = $data2;
                    }else if($i == 0 && $mes1 != $mes2){
                        $data_inicial = $data1;
                        $data_final   = "$ano-$mes-31";
                    }else if($i != 0 && $mes1 == $mes2){
                        $data_inicial = "$ano-$mes-01";
                        $data_final   = $data2;
                    }else{
                        $data_inicial = "$ano-$mes-01";
                        $data_final   = "$ano-$mes-31";
                    }

                    $diasUteis = diasUteis($mes, $ano);

                    $metaMensal = $select_meta_diaria[0][0] * $diasUteis;

                    $meta[$i]['date']  = "$ano-$mes-01";
                    $meta[$i]['value'] = (int)$metaMensal;

                    $sql = "SELECT count(lc.id) FROM lead_contatos lc WHERE lc.lead_contato_data BETWEEN '$data_inicial' AND '$data_final'";
                    $contatos = executar($sql);
                    
                    $realizado[$i]['date']  = "$ano-$mes-01";
                    $realizado[$i]['value'] = $contatos[0][0];
                } 
            }

        }else{
            $z = 0;
            switch($tempo){
                case '12-meses':
                case '6-meses':
                    $tempo = strstr($tempo, "-", true);

                    for($i = ($tempo - 1); $i >= 0; $i--){
                        $ano = date( "Y", strtotime( "-$i month" ) );
                        $mes = date( "m", strtotime( "-$i month" ) );

                        $diasUteis = diasUteis($mes, $ano);

                        $metaMensal = $select_meta_diaria[0][0] * $diasUteis;

                        $meta[$z]['date']  = "$ano-$mes-01";
                        $meta[$z]['value'] = (int)$metaMensal;

                        $sql = "SELECT count(id) FROM lead_contatos WHERE lead_contato_data BETWEEN '$ano-$mes-01' AND '$ano-$mes-31'";
                        $contatos = executar($sql);
                        
                        $realizado[$z]['date']  = "$ano-$mes-01";
                        $realizado[$z]['value'] = $contatos[0][0];

                        $z += 1;
                    }
                    break;
                case '2018-ano':
                case '2019-ano':
                    $ano = strstr($tempo, "-", true);

                    for($i = 0; $i < 12; $i++){
                        $mes = $i + 1;

                        $diasUteis = diasUteis($mes, $ano);

                        $metaMensal = $select_meta_diaria[0][0] * $diasUteis;

                        $meta[$i]['date']  = "$ano-$mes-01";
                        $meta[$i]['value'] = (int)$metaMensal;

                        $sql = "SELECT count(id) FROM lead_contatos WHERE lead_contato_data BETWEEN '$ano-$mes-01' AND '$ano-$mes-31'";
                        $contatos = executar($sql);
                        
                        $realizado[$i]['date']  = "$ano-$mes-01";
                        $realizado[$i]['value'] = $contatos[0][0];
                    }
                    break;
            }
        }

        $dados['meta'] = $meta;
        $dados['realizado'] = $realizado;
    }
    echo json_encode($dados);
}

if($action == "busca-realizado-leads"){
    $regional = $_POST['regional'];
    $tempo    = $_POST['tempo'];

    $where = "";
    if(!empty($regional) && $regional <> "geral"){
        $where = " WHERE comercial_ligacoes_diarias IN ($regional)";
    }

    $select_meta_diaria = executar("SELECT SUM(comercial_ligacoes_diarias) FROM empresa $where");

    if(!empty($tempo)){
        $realizado = array();
        if($tempo == "personalizado"){
            
        }else{
            $sql = "";
            switch($tempo){
                case '12-meses':
                case '6-meses':
                $tempo = strstr($tempo, "-", true);
                $z = 0;
                for($i = ($tempo - 1); $i >= 0; $i--){
                    $ano = date( "Y", strtotime( "-$i month" ) );
                    $mes = date( "m", strtotime( "-$i month" ) );
                    
                    $sql = "SELECT count(id) FROM lead_contatos WHERE lead_contato_data BETWEEN '$ano-$mes-01' AND '$ano-$mes-31'";
                    $contatos = executar($sql);
                    
                    $realizado[$z]['date']  = "$ano-$mes-01";
                    $realizado[$z]['value'] = $contatos[0][0];

                    $z += 1;
                }
                break;
                case '2018':
                case '2019':
                $ano = $tempo;
                break;
            }
        }
        
        echo json_encode($dados);
    }
}

if($action == 'busca' && $type == 'tipo-logradouro'){
    $logradouro = strtoupper(trim($_POST['logradouro']));

    $tipo = executar("SELECT codigo FROM tipo_logradouro WHERE descricao LIKE '%$logradouro%' LIMIT 1");
    echo $tipo[0][0];
}

if($action == 'exclui' && $type == 'lead'){
    $lead_ID = $_POST['id'];

    $delete_lead = executar("DELETE FROM leads WHERE lead_ID = $lead_ID");

    if($delete_lead){
        $mensagem = "Lead excluida com sucesso!";
    }else{
        $mensagem = "Não foi possível excluir a lead! Entre em contato com o administrador";
    }

    echo $mensagem;
}


if($type == 'leads'){
    // Variáveis referente à tabela "leads"
    if(isset($_POST['lead_nome']) && !empty($_POST['lead_nome'])){
        $lead_nome        = anti_injection(utf8_decode($_POST['lead_nome']));
    }
    
    $lead_cnpj        = limpaChar($_POST['lead_cnpj']);
    $lead_site        = utf8_decode($_POST['lead_site']);
    $lead_hubpop_ID   = validaSelect($_POST['lead_hubpop_ID']);
    $lead_etapa_ID    = validaSelect($_POST['lead_etapa_ID']);
    $lead_status_ID   = validaSelect($_POST['lead_status_ID']);
    $lead_anotacao    = utf8_decode($_POST['lead_anotacao']);
    $lead_colaborador = validaSelect($_POST['lead_colaborador']);
    $unidade_negocio  = validaSelect($_POST['unidade_negocio']);
    $lead_agendamento_recisao = convertDateToBd($_POST['lead_agendamento_recisao']);
    $lead_produto     = $_POST['lead_produto'];
    
    // Variáveis referente à tabela "lead_endereco"
    $lead_endereco_cep         = limpaChar(utf8_decode($_POST['lead_endereco_cep']));
    $lead_endereco_tipo        = utf8_decode($_POST['lead_endereco_tipo']);
    $lead_endereco_logradouro  = utf8_decode(addslashes($_POST['lead_endereco_logradouro']));
    $lead_endereco_numero      = utf8_decode($_POST['lead_endereco_numero']);
    $lead_endereco_complemento = utf8_decode($_POST['lead_endereco_complemento']);
    $lead_endereco_bairro      = utf8_decode($_POST['lead_endereco_bairro']);
    $lead_endereco_cidade      = utf8_decode($_POST['lead_endereco_cidade']);
    $lead_endereco_estado      = utf8_decode($_POST['lead_endereco_estado']);
    
    if($action == "alterar"){
        // Variaveis de controle de erros
        $msg_erro = "";
        $nErro   = 0;
        
        $lead_ID  = $_POST['lead_ID'];
        
        $sql_lead = "UPDATE leads SET unidade_negocio = $unidade_negocio, lead_agendamento_recisao = '$lead_agendamento_recisao', lead_colaborador = '$lead_colaborador', lead_nome  = '$lead_nome', lead_cnpj = '$lead_cnpj', lead_email = '$lead_email', lead_site = '$lead_site', lead_hubpop_ID = '$lead_hubpop_ID' , lead_etapa_ID = '$lead_etapa_ID', lead_status_ID = '$lead_status_ID', lead_anotacao = '$lead_anotacao', lead_produto = '$lead_produto' WHERE lead_ID = $lead_ID";
        
        $nr_lead_endereco = executar("SELECT lead_endereco_ID FROM lead_endereco WHERE lead_ID = $lead_ID");
        if($nr_lead_endereco && !empty($nr_lead_endereco[0][0])){
            $sql_lend = "UPDATE lead_endereco SET lead_endereco_cep ='$lead_endereco_cep', lead_endereco_tipo = '$lead_endereco_tipo', lead_endereco_logradouro = '$lead_endereco_logradouro', lead_endereco_numero = '$lead_endereco_numero', lead_endereco_complemento = '$lead_endereco_complemento', lead_endereco_bairro = '$lead_endereco_bairro', lead_endereco_cidade = '$lead_endereco_cidade', lead_endereco_estado = '$lead_endereco_estado' WHERE lead_ID = $lead_ID";
        } else {
            $sql_lend = "INSERT INTO lead_endereco (lead_endereco_cep, lead_endereco_tipo, lead_endereco_logradouro, lead_endereco_numero, lead_endereco_complemento, lead_endereco_bairro, lead_endereco_cidade, lead_endereco_estado, lead_ID ) VALUES ('$lead_endereco_cep', '$lead_endereco_tipo', '$lead_endereco_logradouro', '$lead_endereco_numero', '$lead_endereco_complemento', '$lead_endereco_bairro', '$lead_endereco_cidade', '$lead_endereco_estado', $lead_ID)";
        }
        
        // Inicia Transações
        executar("BEGIN");
        $up_lead = executar($sql_lead);
        if($up_lead){
            $up_lend = executar($sql_lend);
            
            if(!$up_lend){
                
                $nErro += 1;
                $msg_erro = utf8_decode("Não foi possivel atualizar as informações pertinentes ao endereço.");
            }
        }else{
            $nErro += 1;
            $msg_erro = utf8_decode("Não foi possivel atualizar as informações pertinentes à lead.");
        }

        if($nErro == 0){
            executar("COMMIT");
            $mensagem = utf8_decode("Atualizado com sucesso!");
            //$mensagem = utf8_decode($lead_produto);
        }else{
            executar("ROLLBACK");
            $mensagem = $msg_erro;
            
        }

        $retorno = array("mensagem" => $mensagem,
                        "lead_ID" => $lead_ID);

        echo json_encode($retorno);
    }

    if($action == "gravar"){
        // Variaveis de controle de erros
        $msg_erro = "";
        $n_erro   = 0;

        if(empty($lead_cnpj)){
            $select_cnpj = false;
        }else{
            $select_cnpj = verificaCNPJ($lead_cnpj);
        }

        if($select_cnpj){
            $nErro += 1;
            $msg_erro = utf8_decode("Já existe uma lead com esse CNPJ, não é possível gravar. Nome: {$select_cnpj[0]['lead_nome']}");
            
        }else{
            $sql_lead = "INSERT INTO leads (unidade_negocio, lead_nome, lead_cnpj, lead_colaborador, lead_email, lead_site, lead_hubpop_ID, lead_etapa_ID, lead_status_ID, lead_anotacao, lead_agendamento_recisao, lead_produto) VALUES ($unidade_negocio, '$lead_nome', '$lead_cnpj', $lead_colaborador, '$lead_email', '$lead_site', $lead_hubpop_ID , $lead_etapa_ID, $lead_status_ID, '$lead_anotacao', '$lead_agendamento_recisao', '$lead_produto')";
            
            // Inicia Transações
            executar("BEGIN");
            $ins_lead = executar($sql_lead);
            
            if($ins_lead){
                
                $sql = executar("SELECT last_insert_id() FROM leads LIMIT 1");
                $lead_ID = $sql[0][0];
                
                $sql_lend = "INSERT INTO lead_endereco (lead_ID, lead_endereco_cep, lead_endereco_tipo, lead_endereco_logradouro, lead_endereco_numero, lead_endereco_complemento, lead_endereco_bairro, lead_endereco_cidade, lead_endereco_estado) VALUES ($lead_ID, '$lead_endereco_cep', '$lead_endereco_tipo', '$lead_endereco_logradouro', '$lead_endereco_numero', '$lead_endereco_complemento', '$lead_endereco_bairro', '$lead_endereco_cidade', '$lead_endereco_estado')";
                $ins_lend = executar($sql_lend);
                
                if(!$ins_lend){
                    $nErro += 1;
                    $msg_erro = utf8_decode("Não foi possivel gravar as informações pertinentes ao endereço.");
                }
                
            }else{
                
                $nErro += 1;
                $msg_erro = utf8_decode("Não foi possivel gravar as informações pertinentes à lead.");
            }
        }

        if($nErro == 0){
            executar("COMMIT");
            $mensagem = utf8_decode("Lead inserida com sucesso!");
        }else{
            executar("ROLLBACK");
            $mensagem = $msg_erro;
        }

        $retorno = array("mensagem" => $mensagem,
                        "lead_ID" => $lead_ID);
        echo json_encode($retorno);
    }

    if($action == "selecionar"){
        $lead_ID  = $_POST['lead_ID'];

        $retorno = array();

        $sql = "SELECT l.lead_nome, l.lead_cnpj, le.lead_endereco_tipo, le.lead_endereco_logradouro, le.lead_endereco_numero, le.lead_endereco_cep, le.lead_endereco_bairro, le.lead_endereco_cidade, le.lead_endereco_estado, f.nm_filial, h.nm_hubpop FROM leads l INNER JOIN lead_endereco le ON l.lead_ID = le.lead_ID INNER JOIN empresa ON l.unidade_negocio = empresa.nr_empresa INNER JOIN filial f ON empresa.nr_filial = f.nr_filial INNER JOIN hubpop h ON l.lead_hubpop_ID = h.nr_hubpop WHERE l.lead_ID = $lead_ID";
        $lead = executar($sql);

        if($lead){
            $retorno['lead_nome']                 = empty($lead[0]['lead_nome']) ? "" : utf8_encode($lead[0]['lead_nome']);
            $retorno['lead_cnpj']                 = empty($lead[0]['lead_cnpj']) ? "" : mascara($lead[0]['lead_cnpj'], 'cnpj');
            $retorno['lead_endereco_tipo']        = empty($lead[0]['lead_endereco_tipo']) ? "" : utf8_decode(verifiTipoEndereco($lead[0]['lead_endereco_tipo']));
            $retorno['lead_endereco_logradouro']  = empty($lead[0]['lead_endereco_logradouro']) ? "" : utf8_decode($lead[0]['lead_endereco_logradouro']);
            $retorno['lead_endereco_numero']      = empty($lead[0]['lead_endereco_numero']) ? "" : utf8_decode($lead[0]['lead_endereco_numero']);
            $retorno['lead_endereco_cep']         = empty($lead[0]['lead_endereco_cep']) ? "" : utf8_decode($lead[0]['lead_endereco_cep']);
            $retorno['lead_endereco_bairro']      = empty($lead[0]['lead_endereco_bairro']) ? "" : utf8_decode($lead[0]['lead_endereco_bairro']);
            $retorno['lead_endereco_cidade']      = empty($lead[0]['lead_endereco_cidade']) ? "" : utf8_encode($lead[0]['lead_endereco_cidade']);
            $retorno['lead_endereco_estado']      = empty($lead[0]['lead_endereco_estado']) ? "" : utf8_decode($lead[0]['lead_endereco_estado']);
            $retorno['regional']                  = empty($lead[0]['nm_filial']) ? "" : utf8_encode($lead[0]['nm_filial']);
            $retorno['hubpop']                    = empty($lead[0]['nm_hubpop']) ? "" : utf8_encode($lead[0]['nm_hubpop']);
        }else{
            $retorno['erro'] = utf8_decode("Não foi possível encontrar nenhuma lead.");
        }

        echo json_encode($retorno);
    }
}

if($type == 'contato'){
    // Variáveis referente às informações do contato realizado
    $now = date("Y-m-d");
    $lead_ID = $_POST['lead_ID'];
    $lead_contato_nome        = utf8_decode($_POST['lead_contato_nome']);
    $lead_contato_telefone    = utf8_decode(limpaChar($_POST['lead_contato_telefone']));
    $lead_contato_telefone2   = utf8_decode(limpaChar($_POST['lead_contato_telefone2']));
    $lead_contato_email       = utf8_decode($_POST['lead_contato_email']);
    $lead_contato_vendedor_ID = utf8_decode($_POST['lead_contato_vendedor_ID']);
    $lead_contato_data        = empty($_POST['lead_contato_data']) ? "" : utf8_decode(convertDateToBd($_POST['lead_contato_data']));
    $lead_contato_agendamento = empty($_POST['lead_contato_agendamento']) ? "" : utf8_decode(convertDateToBd($_POST['lead_contato_agendamento']));
    $lead_contato_anotacoes   = utf8_decode($_POST['lead_contato_anotacoes']);


    if($action == 'gravar'){
         // Variaveis de controle de erros
         $msg_erro = "";
         $n_erro   = 0;
 
         $sql_lead_contato = "INSERT INTO lead_contatos (lead_ID, lead_contato_nome, lead_contato_telefone, lead_contato_telefone2, lead_contato_vendedor_ID, lead_contato_anotacoes, lead_contato_data, lead_contato_agendamento, data_insercao, nr_func_insercao, lead_contato_email) VALUES ($lead_ID, '$lead_contato_nome', '$lead_contato_telefone', '$lead_contato_telefone2', $lead_contato_vendedor_ID, '$lead_contato_anotacoes', '$lead_contato_data', '$lead_contato_agendamento', '$now', {$_SESSION['login']['nr_func']}, '$lead_contato_email')";

         // Inicia Transações
         executar("BEGIN");
         $ins_lead = executar($sql_lead_contato);

        if(!$ins_lead){

            $nErro += 1;
            $msg_erro = utf8_decode("Não foi possivel gravar as informações pertinentes à lead.");
        }
 
         if($nErro == 0){
             executar("COMMIT");
             $mensagem = utf8_decode("Contato inserido com sucesso!");
         }else{
             executar("ROLLBACK");
             $mensagem = $msg_erro;
         }
 
         $retorno = array("mensagem" => $mensagem,
                         "lead_ID" => $lead_ID);
 
         echo json_encode($retorno);
    }

    if($action == 'alterar'){
        // Variaveis de controle de erros
        $msg_erro = "";
        $n_erro   = 0;
        $id_ctt   = $_POST['id'];

        $sql_lead_contato = "UPDATE lead_contatos SET lead_contato_email = '$lead_contato_email', lead_ID = $lead_ID, lead_contato_nome = '$lead_contato_nome', lead_contato_telefone = '$lead_contato_telefone', lead_contato_telefone2 = '$lead_contato_telefone2', lead_contato_vendedor_ID = $lead_contato_vendedor_ID, lead_contato_anotacoes = '$lead_contato_anotacoes', lead_contato_data = '$lead_contato_data', lead_contato_agendamento = '$lead_contato_agendamento', data_alteracao = '$now', nr_func_alteracao = {$_SESSION['login']['nr_func']} WHERE id = $id_ctt";

        // Inicia Transações
        executar("BEGIN");
        $ins_lead = executar($sql_lead_contato);

       if(!$ins_lead){
           
           $nErro += 1;
           $msg_erro = utf8_decode("Não foi possivel alterar as informações pertinentes à lead.");
       }

        if($nErro == 0){
            executar("COMMIT");
            $mensagem = utf8_decode("Contato alterado com sucesso!");
        }else{
            executar("ROLLBACK");
            $mensagem = $msg_erro;
        }

        $retorno = array("mensagem" => $mensagem,
                        "lead_ID" => $lead_ID);

        echo json_encode($retorno);
   }

   if($action == "selecionar"){
        $id = $_POST['id'];

        $sql = "SELECT * FROM lead_contatos WHERE id = $id";
        $select_contato = executar($sql);

        if($select_contato){
            $contato = array();

            if(!empty($select_contato[0]['lead_contato_data']) && $select_contato[0]['lead_contato_data'] != "0000-00-00"){
                $contato['lead_contato_data'] = convertDateToBr($select_contato[0]['lead_contato_data']);
            }else{
                $contato['lead_contato_data'] = "";
            }

            if(!empty($select_contato[0]['lead_contato_agendamento']) && $select_contato[0]['lead_contato_agendamento'] != "0000-00-00"){
                $contato['lead_contato_agendamento'] = convertDateToBr($select_contato[0]['lead_contato_agendamento']);
            }else{
                $contato['lead_contato_agendamento'] = "";
            }

            if(substr($select_contato[0]['lead_contato_telefone'], 0, 4) == '0800' || substr($select_contato[0]['lead_contato_telefone'], 0, 4) == '0300'){
                $contato['lead_contato_telefone'] = mascara($select_contato[0]['lead_contato_telefone'], 'telefone-0800');
            }else{
                $contato['lead_contato_telefone']    = strlen($select_contato[0]['lead_contato_telefone']) <= 9 ? "11".$select_contato[0]['lead_contato_telefone'] : $select_contato[0]['lead_contato_telefone'];
                $contato['lead_contato_telefone']    = strlen($contato['lead_contato_telefone']) > 10 ? mascara($contato['lead_contato_telefone'], 'celular') : mascara($contato['lead_contato_telefone'], 'telefone');
            }
    
            if(substr($select_contato[0]['lead_contato_telefone2'], 0, 4) == '0800' || substr($select_contato[0]['lead_contato_telefone'], 0, 4) == '0300'){
                $contato['lead_contato_telefone2'] = mascara($select_contato[0]['lead_contato_telefone2'], 'telefone-0800');
            }else{
                $contato['lead_contato_telefone2']    = strlen($select_contato[0]['lead_contato_telefone2']) <= 9 ? "11".$select_contato[0]['lead_contato_telefone2'] : $select_contato[0]['lead_contato_telefone2'];
                $contato['lead_contato_telefone2']    = strlen($contato['lead_contato_telefone2']) > 10 ? mascara($contato['lead_contato_telefone2'], 'celular') : mascara($contato['lead_contato_telefone2'], 'telefone');
            }
    
            $contato['lead_contato_nome']        = utf8_encode($select_contato[0]['lead_contato_nome']);
            $contato['lead_contato_vendedor_ID'] = $select_contato[0]['lead_contato_vendedor_ID'];
            $contato['lead_contato_anotacoes']   = utf8_encode($select_contato[0]['lead_contato_anotacoes']);
            $contato['lead_contato_email']       = utf8_encode($select_contato[0]['lead_contato_email']);
    
            echo json_encode($contato);
        }else{
            echo json_encode(array('sql' => $sql));
        }  
   }
}

if($type == 'cliente'){
    $nErro = 0;
    $mensagem = "";
    $mensagem_sql = "";
    $nr_pessoa = "";

    $lead_ID = $_POST['lead_ID'];

    if($action == 'cadastrar'){

        $lead_cnpj                 = limpaChar($_POST['lead_cnpj']);
        $lead_nome                 = utf8_decode($_POST['lead_nome']);
        $lead_endereco_cep         = utf8_decode(limpaChar($_POST['lead_endereco_cep']));
        $lead_endereco_tipo        = utf8_decode($_POST['lead_endereco_tipo']);
        $lead_endereco_logradouro  = utf8_decode(limpaChar($_POST['lead_endereco_logradouro']));
        $lead_endereco_numero      = utf8_decode($_POST['lead_endereco_numero']);
        $lead_endereco_complemento = utf8_decode($_POST['lead_endereco_complemento']);
        $lead_endereco_bairro      = utf8_decode($_POST['lead_endereco_bairro']);
        $lead_endereco_cidade      = utf8_decode($_POST['lead_endereco_cidade']);
        $lead_endereco_estado      = utf8_decode($_POST['lead_endereco_estado']);

        $nr_empresa = $_POST['nr_empresa'];
        $nr_hubpop  = $_POST['nr_hubpop'];

        executar("BEGIN");
        $sql_pessoa = "INSERT INTO pessoa (nm_pessoa, nm_fantasia, nr_cnpjcpf, tp_pessoa, reter_pis, reter_cofins, reter_inss, reter_ir, reter_csll) VALUES ('$lead_nome', '$lead_nome', '$lead_cnpj', 'J', 'N', 'N', 'N', 'N', 'N')";
        $insert_pessoa = executar($sql_pessoa);
    
        if($insert_pessoa){
            $nr_pessoa = executar("SELECT last_insert_id() FROM pessoa");
            $nr_pessoa = $nr_pessoa[0][0];
    
            $sql_ender  = "INSERT INTO ender (nr_pessoa, tp_logradouro, nm_logradouro, nr_logradouro, ds_complemento, nr_cep, nm_bairro, nm_cidade, nm_estado) VALUES ($nr_pessoa, '$lead_endereco_tipo', '$lead_endereco_logradouro', '$lead_endereco_numero', '$lead_endereco_complemento', '$lead_endereco_cep', '$lead_endereco_bairro', '$lead_endereco_cidade', '$lead_endereco_estado')";
            $insert_ender = executar($sql_ender);
    
            if(!$insert_ender){
                $nErro += 1;
                $mensagem = utf8_encode("Nao foi possivel inserir o endereco! Entre em contato com o suporte.");
                $mensagem_sql = $sql_ender;
            }else{
                $sql_cliente = "INSERT INTO cliente (nr_pessoa, nr_tipo_cliente, ds_status, dt_cadastro, nr_colaborador, nr_hubpop, nr_empresa) VALUES ($nr_pessoa, 1, 'A', now(), {$_SESSION['login']['nr_func']}, $nr_hubpop, $nr_empresa)";
                $insert_cliente = executar($sql_cliente);

                if(!$insert_cliente){
                    $nErro += 1;
                    $mensagem = utf8_encode("Nao foi possivel inserir o cliente! Entre em contato com o suporte.");
                    $mensagem_sql = $sql_cliente;
                }else{
                    $nr_cliente = executar("SELECT last_insert_id() FROM cliente");
                    $nr_cliente = $nr_cliente[0][0];

                    $sql_ordem_servico = "UPDATE ordem_servico SET nr_pessoa = $nr_pessoa WHERE lead_ID = $lead_ID";
                    $update_ordem_servico = executar($sql_ordem_servico);

                    if(!$update_ordem_servico){
                        $nErro += 1;
                        $mensagem = utf8_encode("Nao foi possivel atualizar Ordens de Serviços! Entre em contato com o suporte.");
                        $mensagem_sql = $sql_ordem_servico;
                    }

                    $sql_lead = "UPDATE leads SET nr_cliente = $nr_cliente WHERE lead_ID = $lead_ID";
                    $update_lead = executar($sql_lead);

                    if(!$update_lead){
                        $nErro += 1;
                        $mensagem = utf8_encode("Nao foi possivel atualizar a Lead com o novo código do cliente! Entre em contato com o suporte.");
                        $mensagem_sql = $sql_lead;
                    }
                }

                $sql_contatos = "SELECT id, lead_ID, lead_contato_nome, lead_contato_telefone, lead_contato_telefone2, lead_contato_vendedor_ID, lead_contato_anotacoes, lead_contato_data, lead_contato_agendamento, data_insercao, nr_func_insercao, data_alteracao, nr_func_alteracao, lead_contato_email FROM lead_contatos WHERE lead_ID = $lead_ID ORDER BY id DESC LIMIT 1";
                $select_contatos = executar($sql_contatos);

                if($select_contatos && !empty($sql_contatos)){
                    $sql_ctt = "INSERT INTO ctt (nr_pessoa, nm_ctt, ds_email, ctt_funcao, ctt_skype, ctt_genero) VALUES ($nr_pessoa, '{$select_contatos[0]['lead_contato_nome']}', '{$select_contatos[0]['lead_contato_email']}', 'T.I.', '', '')";
                    $insert_ctt = executar($sql_ctt);

                    if($insert_ctt){

                        $nr_ctt = executar("SELECT last_insert_id() FROM ctt");
                        $nr_ctt = $nr_ctt[0][0];

                        $length_tel1 = strlen($select_contatos[0]['lead_contato_telefone']);
                        $length_tel2 = strlen($select_contatos[0]['lead_contato_telefone2']);

                        if($length_tel1 > 9){
                            $nr_ddd = substr($select_contatos[0]['lead_contato_telefone'], 0, 2);
                            $nr_tel = substr($select_contatos[0]['lead_contato_telefone'], 2);
                        } else {
                            $nr_ddd = "11";
                            $nr_tel = !empty($select_contatos[0]['lead_contato_telefone']) ? $select_contatos[0]['lead_contato_telefone'] : "";
                        }

                        if($length_tel2 > 9){
                            $nr_dddcel = substr($select_contatos[0]['lead_contato_telefone2'], 0, 2);
                            $nr_telcel = substr($select_contatos[0]['lead_contato_telefone2'], 2);
                        } else {
                            $nr_dddcel = "11";
                            $nr_telcel = !empty($select_contatos[0]['lead_contato_telefone2']) ? $select_contatos[0]['lead_contato_telefone2'] : "";
                        }
                
                        $sql_tel  = "INSERT INTO tel (nr_pessoa, nr_ctt, nr_telefone, nr_ddd, nr_dddcel, nr_celular, ds_telefone) VALUES ($nr_pessoa, $nr_ctt, '$nr_tel', '$nr_ddd', '$nr_dddcel', '$nr_telcel', 'T.I.')";
                        $insert_tel = executar($sql_tel);

                        if(!$insert_tel){
                            $nErro += 1;
                            $mensagem = utf8_encode("Nao foi possivel inserir os telefones! Entre em contato com o suporte.");
                            $mensagem_sql = $sql_tel;
                        }

                    }else{
                        $nErro += 1;
                        $mensagem = utf8_encode("Nao foi possivel inserir o contato! Entre em contato com o suporte.");
                        $mensagem_sql = $sql_ctt;
                    }
                }
            }
        }else{
            $nErro += 1;
            $mensagem = utf8_encode("Não foi possível inserir o cliente! Entre em contato com o suporte.");
            $mensagem_sql = $sql_pessoa;
        }
    
        if($nErro == 0){
            $flag = atualizaEtapaLead($lead_ID);
            executar("COMMIT");
            $mensagem = utf8_encode("Cliente inserido com sucesso!");
        }else{
            executar("ROLLBACK");
            $nr_pessoa = 0;
        }
    
        $return = array("mensagem" => $mensagem,
                        "mensagem1SQL" => utf8_encode($mensagem_sql),
                        "nr_pessoa" => $nr_pessoa);
    
        echo json_encode($return);
    }

    if($action == 'atualiza-etapa'){
        $flag = atualizaEtapaLead($lead_ID);
    }
}

if($type == 'ordem-servico'){
    $lead_nome          = $_POST['lead_nome'];
    $nr_depto           = $_POST['nr_depto'];
    $nr_depto_atual     = $_POST['nr_depto_atual'];
    $os_lead_aberto_por = $_POST['os_lead_aberto_por'];
    $titulo             = utf8_decode($_POST['titulo']);
    $lead_ID            = $_POST['lead_ID'];
    $mensagem           = utf8_decode($_POST['mensagem']);

    $tempo_atendimento = "";
    $nr_funcionario = $_SESSION['login']['nr_func'];

    if($action == "criar"){

        $campos_os  = "nr_func, nr_depto, titulo, dt_abertura, dt_fechamento, status";
        $valores_os = "$nr_funcionario, $nr_depto, '$titulo', now(), DATE_ADD(now(), INTERVAL 3 DAY), 'A'";

        if(isset($_POST['nr_ctrt']) && !empty($_POST['nr_ctrt'])){
            $campos_os .= ", nr_ctrt";
            $valores_os .= ", {$_POST['nr_ctrt']}";
        } else {
            $campos_os .= ", lead_ID";
            $valores_os .= ", $lead_ID";
        }
    
        executar('BEGIN');
        $sql = "INSERT INTO ordem_servico ($campos_os) VALUES ($valores_os)";
        $res = executar($sql);
        $res = executar('SELECT LAST_INSERT_ID() as nr_os FROM ordem_servico');
        $nr_os = $res[0]['nr_os'];
    
        $sql = 'INSERT INTO ordem_servico_msg (nr_func,nr_os,mensagem,dt_mensagem) VALUES ('.$nr_funcionario.','.$nr_os.',"'.$mensagem.'",now())';
        $res = executar($sql);
    
        $mailDepto = executar('SELECT ds_email FROM depto WHERE nr_depto = '.$nr_depto);
        $mensagem = str_replace(chr(13),'<br />',$mensagem);
        $html = layoutChamado($lead_nome,$nr_os,$titulo,$mensagem,null);

        if($res){
            $mailer = new EVM_Mailer();
            $mailer->set_sender('Erp Raicom','no-reply@gruporedes.global');
            $mailer->set_subject('[erp_raicom] Ordem de Servicos '.$nr_os);
            $mailer->set_message_type('html');
            $mailer->set_message($html);
            $mailer->add_recipient('DEPTO',$mailDepto[0]['ds_email']);
            $mailer->add_CC($_SESSION['login']['nm_pessoa'],$_SESSION['login']['nm_user']);
            $mailer->send();


            executar("COMMIT");
            echo "OS Gerada com sucesso!";
        }else{
            executar("ROLLBACK");
            echo "Erro ao gerar a OS. Entre em contato com o Administrador";
        }
    }

    if($action == "selecionar"){
        $nr_os = $_POST['nr_os'];
        
        $retorno = array();
        $sql = "SELECT ordem_servico.nr_func, depto.nr_depto as id_depto, UPPER(depto.nm_depto) as nr_depto, UPPER(d2.nm_depto) as nr_depto_destino, titulo, DATE_FORMAT(dt_abertura,'%d/%m/%Y %H:%i:%s') as dt_abertura, nr_os FROM ordem_servico INNER JOIN depto ON ordem_servico.nr_depto = depto.nr_depto LEFT JOIN depto d2 ON ordem_servico.nr_depto_destino = d2.nr_depto WHERE nr_os = $nr_os";
        $sel_os = executar($sql);

        if($sel_os){
            $retorno['nr_os']         = $sel_os[0]['nr_os'];
            $retorno['func']          = $sel_os[0]['nr_func'];
            $retorno['depto']         = utf8_encode($sel_os[0]['nr_depto']);
            $retorno['nr_depto']      = $sel_os[0]['id_depto'];
            $retorno['depto_destino'] = empty($sel_os[0]['nr_depto_destino']) ? "" : utf8_encode($sel_os[0]['nr_depto_destino']);
            $retorno['titulo']        = utf8_encode($sel_os[0]['titulo']);
            $retorno['dt_abertura']   = $sel_os[0]['dt_abertura'];

            $selecr_os_msg = executar("SELECT CONCAT(UPPER(p.nm_pessoa), ' escreveu as ', DATE_FORMAT(dt_mensagem,'%d/%m/%Y %H:%i:%s') ) as info, mensagem FROM ordem_servico_msg osm INNER JOIN func f ON osm.nr_func = f.nr_func INNER JOIN pessoa p ON f.nr_pessoa = p.nr_pessoa WHERE osm.nr_os = $nr_os ORDER BY dt_mensagem DESC");
            $html = "";
            foreach($selecr_os_msg as $value_msg){

                $html .= '<tr colspan="10">
                    <th colspan="10" class="text-left text-light bg-primary">'.utf8_encode($value_msg['info']).'</th>
                </tr>
                <tr colspan="10">
                    <td colspan="10" class="text-left">'.utf8_encode($value_msg['mensagem']).'</td>
                </tr>';
            }

            $retorno['historico'] = $html;

        }else{
            #echo "\n deu false";
            $retorno['erro'] = utf8_decode("Não foi possível recuperar informações da OS");
        }

        echo json_encode($retorno);
    }

    if($action == "salva-mensagem"){
        $nr_os = $_POST['nr_os'];

        executar('BEGIN');
			
        $sql = 'INSERT INTO ordem_servico_msg (nr_os,nr_func,mensagem,dt_mensagem) VALUES ('.$nr_os.','.$nr_funcionario.',"'.$mensagem.'",now())';
        $res = executar($sql);

        $mailDepto = executar('SELECT ds_email FROM depto WHERE nr_depto = '.$nr_depto);
        $mailFuncionario = executar('SELECT pessoa.nr_pessoa, nm_pessoa as nomeFuncionario,nm_user as mailFuncionario FROM pessoa JOIN func ON pessoa.nr_pessoa = func.nr_pessoa JOIN login ON pessoa.nr_pessoa = login.nr_pessoa JOIN ordem_servico ON func.nr_func = ordem_servico.nr_func WHERE nr_os = '.$nr_os);
        $sqlHistorico = 'SELECT nm_pessoa as funcionario,mensagem,date_format(dt_mensagem,"%d/%m/%Y %H:%m:%i") as dataMsg FROM ordem_servico_msg JOIN func ON func.nr_func = ordem_servico_msg.nr_func JOIN pessoa ON pessoa.nr_pessoa = func.nr_pessoa WHERE nr_os = '.$nr_os.' ORDER BY nr_os_msg DESC;';

        $historico = executar($sqlHistorico);
        $mensagem = str_replace(chr(13),'<br />',$mensagem);
        $html = layoutChamado($lead_nome,$nr_os, utf8_decode($titulo) ,$mensagem,$historico);

        $mailer = new EVM_Mailer();
        $mailer->set_sender('Erp Raicom','no-reply@gruporedes.global');
        $mailer->set_subject('[erp_raicom] Ordem de Servico '.$nr_os);
        $mailer->set_message_type('html');
        $mailer->set_message($html);
        $mailer->add_recipient('DEPTO ORIGINAL',$mailDepto[0]['ds_email']);
        #$mailer->add_recipient('DEPTO ORIGINAL', "luis.novais@gruporedes.global");

    
        # SE A OS FOR DE PROJETOS(6), ENVIAR UMA CÓPIA PARA COMERCIAL(2)
        if($nr_depto == 6){
            $mailer->add_recipient('DEPTO Comercial', 'comercial@gruporedes.global');
        }

        #VERIFICA SE A OS FOI ENCAMINHADA
        $encaminhouDepto = executar('SELECT nr_depto_destino, ds_email FROM ordem_servico JOIN depto ON depto.nr_depto = ordem_servico.nr_depto_destino WHERE nr_os = "'.$nr_os.'"');

        if(!empty($encaminhouDepto)){
             $mailer->add_recipient('DEPTO ENCAMINHADO', $encaminhouDepto[0]['ds_email']);
             #$mailer->add_recipient('DEPTO ENCAMINHADO', "luis.novais@gruporedes.global");
        }

        if($res) {
            $mailer->send();
            executar('COMMIT');
            echo utf8_decode("Mensagem enviada com sucesso!");
        } else { 
            executar('ROLLBACK');
            echo utf8_decode("Erro ao enviar mensagem.");
        }
    }

    if($action == "encaminha-os"){
        $nr_os = $_POST['nr_os'];
        $nr_depto_destino = $_POST['os_encaminhar'];

        executar('BEGIN');

        $res = executar('UPDATE ordem_servico SET nr_depto_destino = '.$nr_depto_destino.' WHERE nr_os = '.$nr_os);
        
        $array_depto = executar('SELECT nm_depto,ds_email FROM depto WHERE nr_depto = '.$nr_depto_destino);
        $nome_depto = $array_depto[0]['nm_depto'];
        $email_depto = $array_depto[0]['ds_email'];
        
        $sql = 'INSERT INTO ordem_servico_msg (nr_os,nr_func,mensagem,dt_mensagem) VALUES ('.$nr_os.','.$nr_funcionario.',"Chamado Encaminhado para '.$nome_depto."\n\n".$mensagem.'",now())';
        $res = executar($sql);

        $mailDepto = executar('SELECT ds_email FROM depto WHERE nr_depto = '.$nr_depto_destino);
        $mailFuncionario = executar('SELECT nm_pessoa as nomeFuncionario,nm_user as mailFuncionario FROM pessoa JOIN func ON pessoa.nr_pessoa = func.nr_pessoa JOIN login ON pessoa.nr_pessoa = login.nr_pessoa JOIN ordem_servico ON func.nr_func = ordem_servico.nr_func WHERE nr_os = '.$nr_os);
        $sqlHistorico = 'SELECT nm_pessoa as funcionario,mensagem,date_format(dt_mensagem,"%d/%m/%Y %H:%m:%i") as dataMsg FROM ordem_servico_msg JOIN func ON func.nr_func = ordem_servico_msg.nr_func JOIN pessoa ON pessoa.nr_pessoa = func.nr_pessoa WHERE nr_os = '.$nr_os.' ORDER BY nr_os_msg DESC;';

        $historico = executar($sqlHistorico);
        $html = layoutChamado($lead_nome,$nr_os,'Ordem de Servi&ccedil;o Encaminhada','Ordem de Servi&ccedil;o N&ordm; '.$nr_os.' foi encaminhada para o seu departamento',$historico);

        $mailer = new EVM_Mailer();
        $mailer->set_sender('Erp Raicom','no-reply@gruporedes.global');
        $mailer->set_subject('[erp_raicom] Ordem de Serviço '.$nr_os);
        $mailer->set_message_type('html');
        $mailer->set_message($html);
        $mailer->add_recipient('DEPTO',$email_depto);
        $mailer->add_recipient($mailFuncionario[0]['nomeFuncionario'], str_replace('raicom.com.br', 'gruporedes.global', $mailFuncionario[0]['mailFuncionario']));

        #VERIFICA SE A OS FOI ENCAMINHADA
        $encaminhouDepto = executar('SELECT nr_depto_destino, ds_email FROM ordem_servico JOIN depto ON depto.nr_depto = ordem_servico.nr_depto_destino WHERE nr_os = "'.$nr_os.'"');

        if(!empty($encaminhouDepto)){
            $mailer->add_recipient('DEPTO ENCAMINHADO', $encaminhouDepto[0]['ds_email']);
        }

        $mailer->send();
        if($res) {
            executar('COMMIT');
            echo utf8_decode("Ordem de Serviço encaminhada com sucesso!");
        } else {
            executar('ROLLBACK');
            echo utf8_decode("Não foi possível encaminhar a Ordem de Serviço");
        }
    }

    if($action == "pedido-fechamento"){
        $nr_os = $_POST['nr_os'];
        executar('BEGIN');
        $resChmd = executar('UPDATE ordem_servico SET status = "P", dt_fechamento = now() WHERE nr_os = '.$nr_os);
        $resChmdMsg = executar('INSERT INTO ordem_servico_msg (nr_func,nr_os,mensagem,dt_mensagem) VALUES ('.$nr_funcionario.','.$nr_os.',"PEDIDO DE FECHAMENTO",NOW())');

        //-- Envio de email
        $mailDepto = executar('SELECT ds_email FROM depto WHERE nr_depto = '.$nr_depto);

	    $mailFunc = executar('SELECT nm_pessoa as nomeFunc, nm_user as mailFunc FROM ordem_servico JOIN func USING(nr_func) JOIN pessoa ON pessoa.nr_pessoa = func.nr_pessoa JOIN login ON pessoa.nr_pessoa = login.nr_pessoa WHERE nr_os = '.$nr_os);

        $sqlHistorico = 'SELECT nm_pessoa as funcionario,mensagem,date_format(dt_mensagem,"%d/%m/%Y %H:%i:%s") as dataMsg FROM ordem_servico_msg JOIN func ON func.nr_func = ordem_servico_msg.nr_func JOIN pessoa ON pessoa.nr_pessoa = func.nr_pessoa WHERE nr_os = '.$nr_os.' ORDER BY nr_os_msg DESC;';
        $historico = executar($sqlHistorico);
        $html = layoutChamado($lead_nome,$nr_os,'Pedido de Fechamento','Foi solicitado o pedido de fechamento do chamado N&ordm; '.$nr_os,$historico);
        
        $mailer = new EVM_Mailer();
        $mailer->set_sender('Erp Raicom','no-reply@gruporedes.global');
        $mailer->set_subject('[erp_raicom] Ordem de Servi�os '.$nr_os);
        $mailer->set_message_type('html');
        $mailer->set_message($html);
        $mailer->add_recipient('DEPTO',$mailDepto[0]['ds_email']);
        $mailer->add_CC($mailFunc[0]['nomeFunc'], str_replace('raicom.com.br', 'gruporedes.global', $mailFunc[0]['mailFunc']));
        #$mailer->add_CC("Luís Felipe", "luis.novais@gruporedes.global");

        $mailer->send();
        if($resChmd && $resChmdMsg) {
            executar('COMMIT');
            echo utf8_decode("Solicitação de Fechamento feita com sucesso! Aguarde a análise do dono da OS");
        } else {
            executar('ROLLBACK');
            a('Erro ao encaminhar pedido de fechamento');
        }
    }

    if($action == "fecha-os"){
        $nr_os  = $_POST['nr_os'];
        $status = $_POST['status'];

        if($status != 'P') {
            $dt_fechamento = ',dt_fechamento = now()';
        }

        executar('BEGIN');
        $res = executar('UPDATE ordem_servico SET status = "F" '.$dt_fechamento.' WHERE nr_os = '.$nr_os);

        $sql = 'INSERT INTO ordem_servico_msg (nr_os,nr_func,mensagem,dt_mensagem) VALUES ('.$nr_os.','.$nr_funcionario.',"'.$mensagem.'",now())';
        $res = executar($sql);

        $mailDepto = executar('SELECT ds_email FROM depto WHERE nr_depto = '.$nr_depto);
        $mailFuncionario = executar('SELECT nm_pessoa as nomeFuncionario,nm_user as mailFuncionario FROM pessoa JOIN func ON pessoa.nr_pessoa = func.nr_pessoa JOIN login ON pessoa.nr_pessoa = login.nr_pessoa JOIN ordem_servico ON func.nr_func = ordem_servico.nr_func WHERE nr_os = '.$nr_os);
        $sqlHistorico = 'SELECT nm_pessoa as funcionario,mensagem,date_format(dt_mensagem,"%d/%m/%Y %H:%m:%i") as dataMsg FROM ordem_servico_msg JOIN func ON func.nr_func = ordem_servico_msg.nr_func JOIN pessoa ON pessoa.nr_pessoa = func.nr_pessoa WHERE nr_os = '.$nr_os.' ORDER BY nr_os_msg DESC;';

        $historico = executar($sqlHistorico);
        $mensagem = str_replace(chr(13),'<br />',$mensagem);
        $html = layoutChamado($lead_nome,$nr_os,$titulo_os.' - Ordem de Servi&ccedil;o Fechada',$mensagem,$historico);

        $mailer = new EVM_Mailer();
        $mailer->set_sender('Erp Raicom','no-reply@gruporedes.global');
        $mailer->set_subject('[erp_raicom] Ordem de Serviço '.$nr_os);
        $mailer->set_message_type('html');
        $mailer->set_message($html);
        $mailer->add_recipient('DEPTO',$mailDepto[0]['ds_email']);
        $mailer->add_recipient($mailFuncionario[0]['nomeFuncionario'], str_replace('raicom.com.br', 'gruporedes.global', $mailFuncionario[0]['mailFuncionario']));


        #VERIFICA SE A OS FOI ENCAMINHADA
        $encaminhouDepto = executar('SELECT nr_depto_destino, ds_email FROM ordem_servico JOIN depto ON depto.nr_depto = ordem_servico.nr_depto_destino WHERE nr_os = "'.$nr_os.'"');

        if(!empty($encaminhouDepto)){
             $mailer->add_recipient('DEPTO ENCAMINHADO', $encaminhouDepto[0]['ds_email']);
        }

        $mailer->send();        
        if($res) {
            executar('COMMIT');
            echo utf8_decode("Ordem de Serviço fechada com sucesso!");
        } else {
            executar('ROLLBACK');
            echo utf8_decode("Erro ao tentar fechar Ordem de Serviço");
        }
    }

    if($action == "reabilitar"){
        $nr_os  = $_POST['nr_os'];

        executar('BEGIN');
        $res = executar('UPDATE ordem_servico SET status = "A",dt_fechamento = NULL WHERE nr_os = '.$nr_os);
        
	    $mailDepto =executar('SELECT ds_email FROM ordem_servico JOIN depto USING(nr_depto) WHERE nr_os = '.$nr_os);
	    $mailFunc = executar('SELECT nm_pessoa as nomeFunc, nm_user as mailFunc FROM ordem_servico JOIN func USING(nr_func) JOIN pessoa ON pessoa.nr_pessoa = func.nr_pessoa JOIN login ON login.nr_pessoa = func.nr_pessoa WHERE nr_os ='.$nr_os);

	    $sqlHistorico = 'SELECT nm_pessoa as funcionario,mensagem,date_format(dt_mensagem,"%d/%m/%Y %H:%i:%s") as dataMsg FROM ordem_servico_msg JOIN func ON func.nr_func = ordem_servico_msg.nr_func JOIN pessoa ON pessoa.nr_pessoa = func.nr_pessoa WHERE nr_os = '.$nr_os.' ORDER BY nr_os_msg DESC';
        $historico = executar($sqlHistorico);
        $mensagem = str_replace(chr(13),'<br />',$mensagem);
        $html = layoutChamado($lead_nome,$nr_os,$titulo,$mensagem,$historico);

        $mailer = new EVM_Mailer();
        $mailer->set_sender('Erp Raicom','no-reply@raicom.com.br');
        $mailer->set_subject('[erp_raicom] Reabertura de Chamados');
        $mailer->set_message_type('html');
        $mailer->set_message($html);
        $mailer->add_recipient('DEPTO',$mailDepto[0]['ds_email']);
        $mailer->add_CC($mailFunc[0]['nomeFunc'], str_replace('raicom.com.br', 'gruporedes.global', $mailFunc[0]['mailFunc']));

        $mailer->send();
        if($res) {
            executar('COMMIT');
            echo utf8_decode("Ordem de Serviço reabilitada com sucesso!");
        } else {
            executar('ROLLBACK');
            echo utf8_decode("Erro ao tentar reabilitar Ordem de serviço.");
        }
    }

    if($action == "mudar-dono"){
        $nr_os = $_POST['nr_os'];
        #ATUALIZA
        executar("BEGIN");
        $sql = 'UPDATE ordem_servico SET nr_func = "'.$os_lead_aberto_por.'" WHERE nr_os = "'.$nr_os.'" LIMIT 1';
        $res = executar($sql);
            
        $sql_2 = 'INSERT INTO ordem_servico_msg (nr_func,nr_os,mensagem,dt_mensagem) VALUES ('.$_SESSION['login']['nr_func'].','.$nr_os.',"Mudou o Dono", now())';
        $res_2 = executar($sql_2); 

        if($res && $res_2){
            executar("COMMIT");
            echo utf8_decode("Dono atualizado com sucesso!");
        }else{
            echo utf8_decode("Erro ao tentar atualizar o dono da OS");
        }
    }
}

if($action == 'selecionar' && $type == 'cnpj'){
    $cnpj = limpaChar($_POST['cnpj']);

    $info_cnpj = verificaCNPJCliente($cnpj);
    if($info_cnpj){
        $info_cnpj = $info_cnpj[0];
    }else{
        $info_cnpj = "";
    }

    echo json_encode($info_cnpj);
}




############################################## FUNCTIONS ###########################################

function limpaChar($string){
    $chars = array("/", ":", ";", "'", '"', "(", ")", "-", "_", " ", ".", ",", "[", "]", "{", "}");

    return str_replace($chars, "", $string);
}


function verificaCNPJ($cnpj){
    $sel_verifica_cnpj = executar("SELECT lead_ID, lead_nome FROM leads WHERE lead_cnpj = '$cnpj'");

    return $sel_verifica_cnpj;
}

function verificaCNPJCliente($cnpj){
    $sel_verifica_cnpj = executar("SELECT nr_pessoa, nm_pessoa FROM pessoa WHERE nr_cnpjcpf = '$cnpj'");

    return $sel_verifica_cnpj;
}


function atualizaEtapaLead($id){
    $up_lead = executar("UPDATE leads SET lead_etapa_ID = 4 WHERE lead_ID = $id");

    return $up_lead;
}

function validaSelect($campo){
    if($campo == "SELECIONE"){
        $campo = "";
    }

    return $campo;
}

function verifiTipoEndereco($tipo_endereco){
    $logradouro = executar("SELECT descricao FROM tipo_logradouro WHERE codigo = '$tipo_enredeco'");

    return $logradouro[0][0];
}

function diasUteis($mes,$ano){
  
    $uteis = 0;

    $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano); 
  
    for($dia = 1; $dia <= $dias_no_mes; $dia++){
  
      $timestamp = mktime(0, 0, 0, $mes, $dia, $ano);
      $semana    = date("N", $timestamp);
  
      if($semana < 6) $uteis++;
  
    }
  
    return $uteis;
}

?>