<?php

/*
 * Idéia da classe foi retirada do Framework CodeIgniter
 * http://www.codeigniter.com.br/manual/libraries/validation.html
 * 
 * Classe adaptada por Túlio Spuri, para ser usado sem o Framework.
 * 
 * Contato: tulios@comp.ufla.br
 */

class Validacao {

    private $regras = array();
    private $campos = array();
    private $mensagensErros = array();
    private $mensagemErroFinal = '';
    private $totalErros = 0;
    private $errosCompactados = TRUE;
    

    # Metodos Validacao
    # Se SATISFAZ o método retorna TRUE. Ao contratrio FALSE.
    
    private function obrigatorio($string) {
    
        $this->setMensagemErro('obrigatorio','O campo %s deve ser preenchido');
        $this->setMensagemErro('obrigatorioPlural', 'Os campos %s devem ser preenchidos');
    
        if (trim($string) == '') {
            return FALSE;
        }       
        return TRUE;
       
    }
    
    
    private function numerico($string) {
    
        $this->setMensagemErro('numerico', 'O campo %s deve conter um n&uacute;mero');
        $this->setMensagemErro('numericoPlural', 'Os campos %s devem conter um n&uacute;mero');
        
        if (! is_numeric($string)) {
            return FALSE;
        }
        return TRUE;
    
    }
    
    
    private function minCaracter ($string, $parametro) {
    
        $this->setMensagemErro('minCaracter', 'O campo %s deve conter %s caracteres ou mais');
        $this->setMensagemErro('minCaracterPlural', 'Os campos %s devem conter %s caracteres ou mais');
        
        if (strlen($string) < $parametro) {
            return FALSE;
        }
        return TRUE;
    
    }
    

    private function maxCaracter ($string, $parametro) {
    
        $this->setMensagemErro('maxCaracter', 'O campo %s deve conter %s caracteres ou menos');
        $this->setMensagemErro('maxCaracterPlural', 'Os campos %s devem conter %s caracteres ou menos');
        
        if (strlen($string) > $parametro) {
            return FALSE;
        }
        return TRUE;
    
    }    
    
    
    private function email ($string) {

        $this->setMensagemErro('email', 'O campo %s deve conter um email v&aacute;lido');
        $this->setMensagemErro('emailPlural', 'Os campos %s devem conter um email v&aacute;lido');
    
        $padrao = utf8_decode("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix");
    
        return (! preg_match($padrao, $string)) ? FALSE : TRUE;
        
    }
    
    
    private function mac ($string) {
    
        $this->setMensagemErro('mac', 'O campo %s deve conter um MAC v&aacute;lido');
        $this->setMensagemErro('macPlural', 'Os campos %s devem conter um MAC v&aacute;lido');    
    
        return (! preg_match("/^(([0-9a-f]{2}):){5}([0-9a-f]{2})$/i", $string)) ? FALSE : TRUE;
        
    }
    
    
    private function ip ($string) {
    
        $this->setMensagemErro('ip', 'O campo %s deve conter um IP v&aacute;lido');
        $this->setMensagemErro('ipPlural', 'Os campos %s devem conter um IP v&aacute;lido');      
    
        return ( ! preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/',$string)) ? FALSE : TRUE;
    }    
    

    private function alfanumerico ($string) {
    
        $this->setMensagemErro('alfanumerico', 'O campo %s deve conter apenas letras e/ou n&uacute;meros');
        $this->setMensagemErro('alfanumericoPlural', 'Os campos %s devem conter apenas letras e/ou n&uacute;meros');    
    
        $padrao = '/^[0-9a-zA-Záàãâéèẽêíìĩîóòõôúùũûç\s]+$/i';
        $padrao = utf8_decode($padrao);

        return (! preg_match($padrao, utf8_decode($string))) ? FALSE : TRUE;
  
    }    
    
    
    private function alfa ($string) {
    
        $this->setMensagemErro('alfa', 'O campo %s deve conter apenas letras');
        $this->setMensagemErro('alfaPlural', 'Os campos %s devem conter apenas letras');    
    
        $padrao = '/^[a-zA-Záàãâéèẽêíìĩîóòõôúùũûç\s]+$/i';
        $padrao = utf8_decode($padrao);

        return (! preg_match($padrao, utf8_decode($string))) ? FALSE : TRUE;
  
    }      
    
    
    private function mascara ($string, $parametro) {

        $this->setMensagemErro('mascara', 'O campo %s deve seguir o seguinte padr&atilde;o: %s');
        $this->setMensagemErro('mascaraPlural', 'Os campos %s devem seguir o seguinte padr&atilde;o: %s');        
    
        if (strlen($string) != strlen($parametro)) {
                
            return FALSE;
        
        }
        
        
        for ($i = 0; $i <= strlen($parametro); $i++) {
                         
            # Parametro e NUMERO             
            if ($parametro[$i] == '9') {
            
                if (! is_numeric($string[$i])) {
                
                    return FALSE;
                
                }
            
            }
            # Parametro e LETRA
            else if ($parametro[$i] == 'A') {
            
                # Se string NAO tem um letra retorna FALSE
                $padrao = '[a-zA-Záàãâéèẽêíìĩîóòõôúùũûç]';
                $padrao = utf8_decode($padrao);
                
                if (! eregi($padrao,utf8_decode($string[$i]))) {
                
                    return FALSE;
                
                }                
            
            }
            # Se NAO e LETRA nem NUMERO entao os valores $parametro $string devem ser iguais
            else {
            
                if ($string[$i] != $parametro[$i]) {
                
                        return FALSE;
                    
                }
           
            }

        }

        return TRUE;
    
    }


    private function cpf ($str) {

        $this->setMensagemErro('cpf', 'O campo %s deve conter um CPF v&aacute;lido.');
        $this->setMensagemErro('cpfPlural', 'Os campos %s devem conter CPFs v&aacute;lidos.');  

        function achaVerificador ($l, $d) {
            $s = 0;
            for ($i=0; $i<=$l; $i++) {
                $s += ($d[$i] * (($l + 2) - $i));
            }

            $v = $s % 11;

            if ($v < 2) {
                $v = 0;
            }
            else {
                $v = 11 - $v;
            }

            return $v;
        }

        /*
         * Analisa se o CPF está formatado da seguinte forma
         * xxx.xxx.xxx-xx
         */
        if (preg_match("/(\d+){3}\.(\d+){3}\.(\d+){3}\-(\d+){2}/", $str,$s)) {

            $str = str_replace(array('.','-'),'', $s[0]);
            
        }

        /*
         * Invalidar CPFs sequenciais 0 até 9
         */
        for ($i=0; $i<=9; $i++){
        
            $cpfInvalido = str_repeat($i, 11);
        
            if ($str == $cpfInvalido){
                return FALSE;
            }
            
        }

        if (strlen($str) != 11 OR !is_numeric($str)) {
        
            return FALSE;
            
        }
        else {
            /*
             * Separando os 9 digitos
             * dos 2 verificadores
             */
            $noveDigitos = substr($str, 0, 9);
            $verificadores = substr($str, 9, 10);

       
            /*
             * Com base nos 9 digitos, achamos
             * o primeiro digito verificador
             */
            $verificador1 = achaVerificador(8,$noveDigitos);
            $dezDigitos = $noveDigitos . $verificador1;

            /*
             * Com base nos 10 digitos, achamos
             * o segundo digito verificador
             */
            $verificador2 = achaVerificador(9, $dezDigitos);
            $onzeDigitos = $dezDigitos . $verificador2;
            
            $verificadoresCorreto = $verificador1.$verificador2;

            if ($verificadores == $verificadoresCorreto) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
    }

    # FIM Metodos Validacao
    

    function setRegras($regras = array()) {
    
        $this->regras = $regras;
    
    }
    
    function setCampos($campos = array()) {
    
        $this->campos = $campos;
    
    }

    function setErrosCompactados ($booleano) {
    
        $this->errosCompactados = $booleano;
    
    }
    
    function setMensagemErro ($regra, $mensagem) {
    
        # Se a mensagem ja existe no array de erros, ela nao sera adicionada novamente
        if (! array_key_exists($regra, $this->mensagensErros)) {
        
            $this->mensagensErros[$regra] = $mensagem;

        }
    
    }
    
    
    function mostrarArray($array) {
    
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    
    }
    
    
    function getPost ($campo) {
    
        if (! isset($_POST[$campo])) {
        
            return;
        
        }
    
        $campo = $_POST[$campo];
            
		return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($campo));
    
    }
    
    
    function executar () {
    
        foreach ($this->regras as $campo => $regras) {

            # Se nao a regras para o campo entao nao processa o proximo campo.            
            if (empty($regras)) {

                continue;
            
            }
            
            $regras = explode('|', $regras);
            
            foreach ($regras as $regra) {
                
                if (preg_match("/(.*?)\[(.*?)\]/", $regra, $combinacao)) {
                
                    $regra = $combinacao[1];
                    $parametro = $combinacao[2];

                }
                
                # Se o metodo nao existe tenta uma funcao nativa do PHP
                if (! method_exists($this, $regra)) {
                
                    $_POST[$campo] = $regra($_POST[$campo]);
                
                }
                else {

                    if (($regra != 'obrigatorio') and ($_POST[$campo] == '')) {
                    
                        continue;
                    
                    }
                
                    $resultado = $this->$regra($_POST[$campo], $parametro);
                
                }

                    
                # Se as regras nao foram satisfeiras exibe os erros    
                if ($resultado === FALSE) {
                
                    $campo = (! empty($this->campos[$campo])) ? $this->campos[$campo] : ucwords($campo);
                
                    # Insere algumas informacoes no array para compactar mensagens de erros
                    if ($this->errosCompactados == TRUE) {
                    
                        $indiceArray = ($parametro != '') ? $regra . "[{$parametro}]" : $regra;
                        
                        $erros[$indiceArray][$campo] = '';
                    
                    }
                    else {
                    
                        $this->mensagemErroFinal .= sprintf($this->mensagensErros[$regra], $campo, $parametro);
                        $this->mensagemErroFinal .= "<br>\n";
                        
                    }
                 
                   $this->totalErros++;                 
                    
                }

            }
            
            # Resetando variaveis
            $parametro = '';
        
        }

        
        # Mensagens Compactadas        
        if ($this->errosCompactados == TRUE) {

            if (isset($erros)) {
            
                foreach ($erros as $regra => $campos) {

                    if (preg_match("/(.*?)\[(.*?)\]/", $regra, $combinacao)) {
                    
                        $regra = $combinacao[1];
                        $parametro = $combinacao[2];

                    }

                    $campos = array_keys($campos);
                    $numeroCampos = count($campos);
                    $ultimoCampo = array_pop($campos);
                    
                    
                    $inicioMsg = implode(', ', $campos);
                    $finalMsg = ($numeroCampos == 1) ? $ultimoCampo : " e $ultimoCampo";
                    
                    $camposFormatados = $inicioMsg . $finalMsg;
                    
                    $regra = ($numeroCampos == 1) ? $regra : $regra."Plural";
                    
                    $this->mensagemErroFinal .= sprintf($this->mensagensErros[$regra], $camposFormatados, $parametro);
                    $this->mensagemErroFinal .= "<br>\n";

                }
                
            }
            
        }
            
        if ($this->totalErros == 0) {
            return TRUE;
        }
        return FALSE;
    
    }
    
    function mostrarErros() {
    
        echo $this->mensagemErroFinal;
    
    }
    
    


}

?>
