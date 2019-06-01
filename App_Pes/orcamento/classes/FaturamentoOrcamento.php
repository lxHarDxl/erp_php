<?
require_once("../funcoes.php");

class FaturamentoOrcamento {
    /**
     * Variáveis de controle da classe
     */
    private $ano;
    private $tabela;
    private $taxaLimiteGasto;
    private $taxaReservaCaixa;

    private $taxaInadimplenciaMensal = array();
    private $limiteGastoMensal = array();
    private $reservaCaixaMensal = array();

    private $totalFaturamentoPrevisto;
    private $totalFaturamentoRealizado;
    private $totalTaxaInadimplencia;
    private $totalLimiteGasto;
    private $totalReservaCaixa;




    /**
     * Cria a classe FaturamentoOrcamento e recebe o ano em referencia ao Faturamento
     */
    function __construct($ano, $limite_gasto = 90){
        $this->ano = $ano;
        $this->taxaLimiteGasto = $limite_gasto;
        $this->taxaReservaCaixa = 100 - $this->taxaLimiteGasto;
        $this->totalFaturamentoPrevisto = 0;
        $this->totalFaturamentoRealizado = 0;

    }

    /**
     * Getters e Setters
     */

    /**
     * Executada quando o objeto é destruido
     */
    function __destruct(){

    }

    /**
     * Get the value of ano
     */ 
    public function getAno()
    {
        return $this->ano;
    }

    /**
     * Set the value of ano
     *
     * @return  self
     */ 
    public function setAno($ano)
    {
        $this->ano = $ano;

        return $this;
    }

    /**
     * Get the value of tabela
     */ 
    public function getTabela()
    {
        return $this->tabela;
    }

    /**
     * Get the value of taxaLimiteGasto
     */ 
    public function getTaxaLimiteGasto()
    {
        return $this->taxaLimiteGasto;
    }

    /**
     * Get the value of taxaReservaCaixa
     */ 
    public function getTaxaReservaCaixa()
    {
        return $this->taxaReservaCaixa;
    }

        /**
     * Get the value of totalFaturamentoPrevisto
     */ 
    public function getTotalFaturamentoPrevisto()
    {
        return "R$".number_format($this->totalFaturamentoPrevisto, 2, ",", ".");
    }

    /**
     * Get the value of totalFaturamentoRealizado
     */ 
    public function getTotalFaturamentoRealizado()
    {
        return "R$".number_format($this->totalFaturamentoRealizado, 2, ",", ".");
    }

    /**
     * Get the value of totalTaxaInadimplencia
     */ 
    public function getTotalTaxaInadimplencia()
    {
        return number_format($this->totalTaxaInadimplencia, 2, ",", ".")."%";
    }

    /**
     * Get the value of totalLimiteGasto
     */ 
    public function getTotalLimiteGasto()
    {
        return "R$".number_format($this->totalLimiteGasto, 2, ",", ".");
    }

    /**
     * Get the value of totalReservaCaixa
     */ 
    public function getTotalReservaCaixa()
    {
        return "R$".number_format($this->totalReservaCaixa, 2, ",", ".");
    }


    /**
     * 
     * FUNÇÕES DA CLASSE
     * 
     */

    /**
     * Função responsável por montar a tabela, call todas as funções 
     */
    public function montaTabela(){
        $html = "";
        $cabecalho = self::getCabecalho();
        $indices   = self::getIndices();
        $faturamento_realizado = self::infoFaturamentoMensalRealizado();
        $faturamento_previsto  = self::infoFaturamentoMensalPrevisto();
        $this->limiteGastoMensal = self::calculaLimiteGasto($faturamento_realizado, $faturamento_previsto);
        $this->reservaCaixaMensal = self::calculaReservaCaixa($faturamento_realizado, $faturamento_previsto);
        $this->taxaInadimplenciaMensal = self::calculaTaxaInadimplenciaMensal($faturamento_realizado, $faturamento_previsto);
        self::calculaTaxaInadimplenciaTotal();

        $html .= "<table class='table table-sm table-hover table-bordered border border-primary mr-3'>
                    <thead class='bg-primary text-light'>
                        $cabecalho
                    </thead>
                    <tbody>";

        foreach($indices as $key=>$value){
            $flag = 0;
            $info = "";
            switch($key){
                case 0:
                    $info = $faturamento_realizado;
                    $total = self::getTotalFaturamentoRealizado();
                    $flag = 1;
                    break;
                case 1:
                    $info = $this->taxaInadimplenciaMensal;
                    $total = self::getTotalTaxaInadimplencia();
                    $flag = 2;
                    break;
                case 2:
                    $info = $faturamento_previsto;
                    $total = self::getTotalFaturamentoPrevisto();
                    $flag = 1;
                    break;
                case 3:
                    $info = $this->limiteGastoMensal;
                    $total = self::getTotalLimiteGasto();
                    break;
                case 4:
                    $info = $this->reservaCaixaMensal;
                    $total = self::getTotalReservaCaixa();
                    break;
            }

            $html .= "<tr style='font-size: 10px !mportant;'>
                        <td class='bg-primary text-light'>
                            $value
                        </td>";

            for($i = 0; $i < 12; $i++){
                if($flag == 2){
                    $color = $info[$i] <= 0 ? "text-danger" : "text-success";
                } else {
                    $color = "";
                }

                $info[$i] = $flag == 2 ? number_format($info[$i], 2, ",", ".")."%" : $info[$i];
                $info[$i] = $flag == 1 ? "R$".number_format($info[$i], 2, ",", ".") : $info[$i]; 
                $html .= "<td class='bg-light $color'>{$info[$i]}</td>";
            }

            $html .= "  <td class='bg-light'>
                            $total
                        </td>
                    </tr>";
        }
        $html .= "  </tbody>
                </table>";

        $this->tabela = $html;
    }

    /**
     * Retorna cabeçalho da tabela do faturamento mensal
     */
    public function getCabecalho(){
        return "<tr>
                    <th></th>
                    <th>Jan</th>
                    <th>Fev</th>
                    <th>Mar</th>
                    <th>Abr</th>
                    <th>Mai</th>
                    <th>Jun</th>
                    <th>Jul</th>
                    <th>Ago</th>
                    <th>Set</th>
                    <th>Out</th>
                    <th>Nov</th>
                    <th>Dez</th>
                    <th>Total</th>
                </tr>";
    }

    /**
     * Retorna informações principais da tabela de faturamento
     */
    public function getIndices(){
        $limite_gasto = self::getTaxaLimiteGasto();
        $reserva_caixa = self::getTaxaReservaCaixa();
        $info = array("Faturamento", utf8_decode("Taxa Inadimplência"), "Fat. Previsto", "Limite de gastos $limite_gasto%", "Reserva de Caixa $reserva_caixa%");

        return $info;
    }

    /**
     * Retorna informações do faturamento realizado no ano selecionado
     */
    public function infoFaturamentoMensalRealizado(){
        $ano_selecionado = $this->ano;
        $totalRealizado = $this->totalFaturamentoRealizado;
        $retorno = array();

        for($i = 1; $i <= 12; $i++){
            $z = $i - 1;
            $faturamento_mensal_realizado = executar("SELECT SUM(nr_valor_pago) as Valor FROM payment_detalhe WHERE dt_pagamento BETWEEN '$ano_selecionado-$i-01' AND '$ano_selecionado-$i-31' ");
            $retorno[$z] = empty($faturamento_mensal_realizado[0][0]) ? 0.00 : $faturamento_mensal_realizado[0][0];
            $totalRealizado += $faturamento_mensal_realizado[0][0];
        }
        $this->totalFaturamentoRealizado = $totalRealizado;

        return $retorno;
    }

    /**
     * Retorna informações do faturamento previsto no ano selecionado
     */
    public function infoFaturamentoMensalPrevisto(){
        $ano_selecionado = $this->ano;
        $totalPrevisto = $this->totalFaturamentoPrevisto;
        $retorno = array();

        for($i = 1; $i <= 12; $i++){
            $z = $i - 1;
            $faturamento_mensal_previsto = executar("SELECT SUM(nr_valor) as Valor FROM payment_detalhe WHERE dt_vencto BETWEEN '$ano_selecionado-$i-01' AND '$ano_selecionado-$i-31' ");
            $retorno[$z] = empty($faturamento_mensal_previsto[0][0]) ? 0.00 : $faturamento_mensal_previsto[0][0];
            $totalPrevisto += $faturamento_mensal_previsto[0][0];
        }
        $this->totalFaturamentoPrevisto = $totalPrevisto;

        return $retorno;
    }

    /**
     * Função que calcula a taxa de inadimplência mensal com base nas informações do faturamento mensal realizado e previsto
     * 
     * @param array $realizado [faturamento realizado]
     * @param array $previsto  [faturamento previsto]
     * @return array [taxa de inadimplencia mensal]
     */
    public function calculaTaxaInadimplenciaMensal($realizado, $previsto){
        $retorno = array();

        for($i = 0; $i < count($realizado); $i++){
            $retorno[$i] = ($realizado[$i] / $previsto[$i]) - 1;
        }

        return $retorno;
    }

    /**
     * Função que calcula a taxa de inadimplência total com base nas informações do faturamento total realizado e previsto
     */
    public function calculaTaxaInadimplenciaTotal(){
        $this->totalTaxaInadimplencia = ($this->totalFaturamentoRealizado / $this->totalFaturamentoPrevisto) - 1;
    }    

    /**
     * Função que calcula o limite de gastos mensal com base nas informações do faturamento mensal realizado e previsto
     * Calcula também o limite de gastos total
     * 
     * @param array $realizado [faturamento realizado]
     * @param array $previsto  [faturamento previsto]
     * @return array [limite de gastos mensal]
     */
    public function calculaLimiteGasto($realizado, $previsto){
        $taxa = ($this->taxaLimiteGasto / 100);
        $count = count($realizado) > count($previsto) ? count($realizado) : count($previsto);
        $total = $this->totalLimiteGasto;
        $retorno = array();

        for($i = 0; $i < $count; $i++){
            $valor = $realizado[$i] <= 0 || empty($realizado[$i]) ? $previsto[$i] : $realizado[$i];
            $retorno[$i] = "R$".number_format($valor * $taxa, 2, ",", ".");

            $total += ($valor * $taxa);
        }
        $this->totalLimiteGasto = $total;

        return $retorno;
    }

    /**
     * Função que calcula a reserva de caixa mensal com base nas informações do faturamento mensal realizado e previsto
     * Calcula também a reserva de caixa total
     * 
     * @param array $realizado [faturamento realizado]
     * @param array $previsto  [faturamento previsto]
     * @return array [reserva de caixa mensal]
     */
    public function calculaReservaCaixa($realizado, $previsto){
        $taxa = ($this->taxaReservaCaixa / 100);
        $count = count($realizado) > count($previsto) ? count($realizado) : count($previsto);
        $total = $this->totalReservaCaixa;
        $retorno = array();

        for($i = 0; $i < $count; $i++){
            $valor = $realizado[$i] <= 0 || empty($realizado[$i]) ? $previsto[$i] : $realizado[$i];
            $retorno[$i] = "R$".number_format($valor * $taxa, 2, ",", ".");

            $total += ($valor * $taxa);
        }
        $this->totalReservaCaixa = $total;

        return $retorno;
    }
}


?>