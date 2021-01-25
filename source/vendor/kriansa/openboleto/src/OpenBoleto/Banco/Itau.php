<?php
/*
 * OpenBoleto - Gera��o de boletos banc�rios em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2013 Estrada Virtual
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace OpenBoleto\Banco;

use OpenBoleto\BoletoAbstract;
use OpenBoleto\Exception;

/**
 * Classe boleto Ita� S/A
 *
 * @package    OpenBoleto
 * @author     Daniel Garajau <http://github.com/kriansa>
 * @copyright  Copyright (c) 2013 Estrada Virtual (http://www.estradavirtual.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Itau extends BoletoAbstract
{
    /**
     * C�digo do banco
     * @var string
     */
    protected $codigoBanco = '341';

    /**
     * Localiza��o do logotipo do banco, referente ao diret�rio de imagens
     * @var string
     */
    protected $logoBanco = 'itau.jpg';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'Pag�vel em qualquer banco at� o vencimento. Ap�s o vencimento o boleto deve ser atualizado no site do banco.';

    /**
     * Define as carteiras dispon�veis para este banco
     * @var array
     */
    protected $carteiras = array(
        '148', '149', '153', '108', '180', '121', '150', '109', '191', '116', '117', '119',
        '134', '135', '136', '104', '188', '147', '112', '115', '177', '172', '107', '204',
        '205', '206', '173', '196', '103', '102', '174', '198', '167', '202', '203', '175',
        '157',
    );

    /**
     * Campo obrigat�rio para emiss�o de boletos com carteira 198 fornecido pelo Banco com 5 d�gitos
     * @var int
     */
    protected $codigoCliente;

    /**
     * D�gito verificador da carteira/nosso n�mero para impress�o no boleto
     * @var int
     */
    protected $carteiraDv;

    /**
     * Cache do campo livre para evitar processamento desnecess�rio.
     *
     * @var string
     */
    protected $campoLivre;

    
    protected $linha_digitavel;
    
    protected $codigo_barras;
    
    
    /**
     * Define o c�digo do cliente
     *
     * @param int $codigoCliente
     * @return $this
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;
        return $this;
    }

    /**
     * Retorna o c�digo do cliente
     *
     * @return int
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }

    /**
     * Gera o Nosso N�mero.
     *
     * @return string
     */
    protected function gerarNossoNumero()
    {
        $this->getCampoLivre(); // For�a o calculo do DV.
        //$numero = self::zeroFill($this->getCarteira(), 3) . '/' . self::zeroFill($this->getSequencial(), 8);
        $numero = $this->getSequencial(); //alterado 18/12/2017
        $numero .= '-' . $this->carteiraDv;

        return $numero;
    }

    /**
     * M�todo para gerar o c�digo da posi��o de 20 a 44
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        if ($this->campoLivre) {
            return $this->campoLivre;
        }

        $sequencial = self::zeroFill($this->getSequencial(), 8);
        $carteira = self::zeroFill($this->getCarteira(), 3);
        $agencia = self::zeroFill($this->getAgencia(), 4);
        $conta = self::zeroFill($this->getConta(), 5);

        // Carteira 198 - (Nosso N�mero com 15 posi��es) - Anexo 5 do manual
        if (in_array($this->getCarteira(), array('107', '122', '142', '143', '196', '198'))) {
            $codigo = $carteira . $sequencial .
                self::zeroFill($this->getNumeroDocumento(), 7) .
                self::zeroFill($this->getCodigoCliente(), 5);

            // Define o DV da carteira para a view
            $this->carteiraDv = $modulo = static::modulo10($codigo);

            return $this->campoLivre = $codigo . $modulo . '0';
        }

        // Gera��o do DAC - Anexo 4 do manual
        // Adicionei a carteira 112 aqui, porque esta no SGL tamb�m, se n�o tiver, da pau boleto
        if (!in_array($this->getCarteira(), array('112','126', '131', '146', '150', '168'))) {
            // Define o DV da carteira para a view
            $this->carteiraDv = $dvAgContaCarteira = static::modulo10($agencia . $conta . $carteira . $sequencial);

        } else {
            // Define o DV da carteira para a view
            $this->carteiraDv = $dvAgContaCarteira = static::modulo10($carteira . $sequencial);
        }

        // M�dulo 10 Ag�ncia/Conta
        $dvAgConta = static::modulo10($agencia . $conta);
        return $this->campoLivre = $carteira . $sequencial . $dvAgContaCarteira . $agencia . $conta . $dvAgConta . '000';
    }

    /**
     * Define nomes de campos espec�ficos do boleto do Ita�
     *
     * @return array
     */
    public function getViewVars()
    {
        return array(
        //   'carteira' => null, // Campo n�o utilizado pelo Ita�
        );
    }
    
    
    
    //metodo customizado aqui para boletos do centris
    public function setlinha_digitavel($linha_digitavel)
    {
        $this->linha_digitavel = $linha_digitavel;
        return $this;
    }
    
    //metodo customizado aqui para boletos do centris
    public function getLinhaDigitavel(){
        
         $ld = $this->linha_digitavel;
         
         $part1 = substr($ld,0,5).'.'; 
         $part2 = substr($ld,5,5).' ';
         $part3 = substr($ld,10,5).'.';
         $part4 = substr($ld,15,6).' ';
         $part5 = substr($ld,21,5).'.';
         $part6 = substr($ld,26,6).' ';
         $cd = substr($ld,32,1).' '; 
         $part7 = substr($ld,33,14);
         
         return $part1.$part2.$part3.$part4.$part5.$part6.$cd.$part7;
         
    }
    
    //metodo customizado aqui para boletos do centris    
    public function setcodigo_barras($codigo_barras)
    {
        $this->codigo_barras = $codigo_barras;
        return $this;
    }
    
    //metodo customizado aqui para boletos do centris
    public function getNumeroFebraban()
    {
        //return self::zeroFill($this->getCodigoBanco(), 3) . $this->getMoeda() . $this->getDigitoVerificador() . $this->getFatorVencimento() . $this->getValorZeroFill() . $this->getCampoLivre();
        return $this->codigo_barras;
        
    }
    
    
    
    
}
