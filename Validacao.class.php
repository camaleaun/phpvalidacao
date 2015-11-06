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
    private $estilosErros = array('prefixo' => '', 'sufixo' => '');
    private $errosCompactados = TRUE;

    /*
     * Como os erros serao exibidos
     * 1 - texto puro
     * 2 - alert javascript
     */
    private $interfaceErros   = 1;

    /*
     * Enquanto um input nao tiver suas regras satisfeitas,
     * as mensagens de erros dos outros nao serao exibidas.
     */
    private $umPorUm = FALSE;



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

        return (! filter_var($string, FILTER_VALIDATE_INT)) ? FALSE : TRUE;

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

        return (filter_var($string, FILTER_VALIDATE_EMAIL) === FALSE) ? FALSE : TRUE;

    }

    private function mac ($string) {

        $this->setMensagemErro('mac', 'O campo %s deve conter um MAC v&aacute;lido');
        $this->setMensagemErro('macPlural', 'Os campos %s devem conter um MAC v&aacute;lido');

        return (! preg_match("/^(([0-9a-f]{2}):){5}([0-9a-f]{2})$/i", $string)) ? FALSE : TRUE;

    }

    private function ipv4 ($string) {

        $this->setMensagemErro('ipv4', 'O campo %s deve conter um IP vers&atilde;o 4 v&aacute;lido');
        $this->setMensagemErro('ipv4Plural', 'Os campos %s devem conter um IP vers&atilde;o 4 v&aacute;lido');

        return (filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE) ? FALSE : TRUE;

    }

    private function ipv6 ($string) {

        $this->setMensagemErro('ipv6', 'O campo %s deve conter um IP vers&atilde;o 6 v&aacute;lido');
        $this->setMensagemErro('ipv6Plural', 'Os campos %s devem conter um IP vers&atilde;o 6 v&aacute;lido');

        return (filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE) ? FALSE : TRUE;

    }

    private function ip_fora_faixa_privada ($string) {

        $this->setMensagemErro('ip_fora_faixa_privada', 'O campo %s deve conter um IP fora da faixa privada.');
        $this->setMensagemErro('ip_fora_faixa_privadaPlural', 'Os campos %s devem conter um IP fora da faixa privada');

        return (filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === FALSE) ? FALSE : TRUE;

    }

    private function ip_fora_faixa_reservada ($string) {

        $this->setMensagemErro('ip_fora_faixa_reservada', 'O campo %s deve conter um IP fora da faixa reservada.');
        $this->setMensagemErro('ip_fora_faixa_reservadaPlural', 'Os campos %s devem conter um IP fora da faixa reservada');

        return (filter_var($string, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) === FALSE) ? FALSE : TRUE;

    }

    private function url ($string) {

        $this->setMensagemErro('url','O campo %s deve conter uma URL v&aacute;lida');
        $this->setMensagemErro('urlPlural','O campo %s deve conter uma URL v&aacute;lida');

        return (filter_var($string, FILTER_VALIDATE_URL) === FALSE) ? FALSE : TRUE;

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

    private function igualA ($string, $parametro) {

        $this->setMensagemErro('igualA', 'O campo %s deve ser igual ao campo %s');
        $this->setMensagemErro('igualAPlural', 'Os campos %s devem ser igual ao campo %s');

        if ($string != $_POST[$parametro]) {
            return FALSE;
        }
        return TRUE;

    }

    private function mascara ($string, $parametro) {

        $this->setMensagemErro('mascara', 'O campo %s deve seguir o seguinte padr&atilde;o: %s');
        $this->setMensagemErro('mascaraPlural', 'Os campos %s devem seguir o seguinte padr&atilde;o: %s');

        if (strlen($string) != strlen($parametro)) {
            return FALSE;
        }

        for ($i = 0; $i <= strlen($parametro); $i++) {

            # Parametro e NUMERO
            if (isset($parametro[$i]) && $parametro[$i] == '9') {

                if (! is_numeric($string[$i])) {
                    return FALSE;
                }

            }
            # Parametro e LETRA
            elseif (isset($parametro[$i]) && $parametro[$i] == 'A') {

                # Se string NAO tem um letra retorna FALSE
                $padrao = '[a-zA-Záàãâéèẽêíìĩîóòõôúùũûç]';
                $padrao = utf8_decode($padrao);

                if (! eregi($padrao,utf8_decode($string[$i]))) {
                    return FALSE;
                }

            }
            # Se NAO e LETRA nem NUMERO entao os valores $parametro $string devem ser iguais
            else {

                if (isset($parametro[$i]) && ($string[$i] != $parametro[$i])) {
                    return FALSE;
                }
            }
        }

        return TRUE;
    }

    private function cnpj($str){

        $this->setMensagemErro('cnpj', 'O campo %s deve conter um CNPJ v&aacute;lido.');
        $this->setMensagemErro('cnpjPlural', 'Os campos %s devem conter CNPJs v&aacute;lidos.');

        $str = preg_replace ("@[./-]@", "", $str);

        if (strlen($str) <> 14) return FALSE;

        $soma = 0;

        $soma += ($str[0] * 5);
        $soma += ($str[1] * 4);
        $soma += ($str[2] * 3);
        $soma += ($str[3] * 2);
        $soma += ($str[4] * 9);
        $soma += ($str[5] * 8);
        $soma += ($str[6] * 7);
        $soma += ($str[7] * 6);
        $soma += ($str[8] * 5);
        $soma += ($str[9] * 4);
        $soma += ($str[10] * 3);
        $soma += ($str[11] * 2);

        $d1 = $soma % 11;
        $d1 = $d1 < 2 ? 0 : 11 - $d1;

        $soma = 0;
        $soma += ($str[0] * 6);
        $soma += ($str[1] * 5);
        $soma += ($str[2] * 4);
        $soma += ($str[3] * 3);
        $soma += ($str[4] * 2);
        $soma += ($str[5] * 9);
        $soma += ($str[6] * 8);
        $soma += ($str[7] * 7);
        $soma += ($str[8] * 6);
        $soma += ($str[9] * 5);
        $soma += ($str[10] * 4);
        $soma += ($str[11] * 3);
        $soma += ($str[12] * 2);


        $d2 = $soma % 11;
        $d2 = $d2 < 2 ? 0 : 11 - $d2;

        if ($str[12] == $d1 && $str[13] == $d2) {
            return TRUE;
        }
        else {
            return FALSE;
        }

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

    function setUmPorUm ($booleano) {
        $this->umPorUm = $booleano;
    }

    function setInterfaceErros ($integer) {
        if ($integer == 1) {
            $this->interfaceErros = 1;
        }
        elseif ($integer == 2) {
            $this->interfaceErros = 2;
        }
    }

    function setMensagemErro ($regra, $mensagem) {

        # Se a mensagem ja existe no array de erros, ela nao sera adicionada novamente
        if (! array_key_exists($regra, $this->mensagensErros)) {

            $this->mensagensErros[$regra] = $mensagem;

        }

    }


    function setEstilosErros ($prefixo, $sufixo) {
        $this->estilosErros['prefixo'] = $prefixo;
        $this->estilosErros['sufixo']  = $sufixo;
    }

    static function mostrarArray($array) {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }

    /*
     * Post
     * Metodo para exibir os dados no atributo value dos input
     * de forma segura
     */
    static function Post ($campo) {
        // Disponivel em (PHP 5 >= 5.2.0)
        if (function_exists('filter_has_var') && function_exists('filter_input')) {

            if (filter_has_var(INPUT_POST,$campo)) {

                $string = filter_input(INPUT_POST,$campo,FILTER_SANITIZE_SPECIAL_CHARS);
                echo trim($string);

            }

        }
        else {

            $string = (isset($_POST[$campo])) ? htmlentities($_POST[$campo]) : null;
            echo trim($string);

        }
    }

    function executar () {

        if ($this->umPorUm) {
            $this->errosCompactados = FALSE;
        }

        foreach ($this->regras as $campo => $regras) {

            # Se nao ha regras para o campo entao nao processa o proximo campo.
            if (empty($regras)) {

                continue;

            }

            $regras = explode('|', $regras);

            foreach ($regras as $regra) {

                if (preg_match("/(.*?)\[(.*?)\]/", $regra, $combinacao)) {

                    $regra = $combinacao[1];
                    $parametro = $combinacao[2];

                }
                else {
                    $parametro = null;
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

                    # $campo2 e $parametro2 sao usados apenas para exibir os erros
                    $campo2 = (! empty($this->campos[$campo])) ? $this->campos[$campo] : ucwords($campo);
                    $parametro2 = (isset($parametro)) ? ucwords($parametro) : null;

                    # Insere algumas informacoes no array para compactar mensagens de erros
                    if ($this->errosCompactados == TRUE) {

                        $indiceArray = ($parametro != '') ? $regra . "[{$parametro}]" : $regra;

                        $erros[$indiceArray][$campo2] = '';

                    }
                    else {

                        # So usa o prefixo se a interface for exibicao em texto puro
                        $this->mensagemErroFinal .= ($this->interfaceErros==1) ? $this->estilosErros['prefixo'] : null;

                        $this->mensagemErroFinal .= sprintf($this->mensagensErros[$regra], $campo2, $parametro2);

                        # So usa o sufixo se a interface for exibicao em texto puro
                        $this->mensagemErroFinal .= ($this->interfaceErros==1) ? $this->estilosErros['sufixo'] : null;

                        # Quebra de linha para erros mostrados em texto puro e em alert javascript
                        $this->mensagemErroFinal .= ($this->interfaceErros == 1) ? "<br>\n" : '\n';

                    }

                    if ($this->umPorUm == TRUE) {
                        return FALSE;
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

                    # $parametro2 e usado apenas para exibir os erros
                    $parametro2 = (isset($parametro)) ? ucwords($parametro) : null;

                    $campos = array_keys($campos);
                    $numeroCampos = count($campos);
                    $ultimoCampo = array_pop($campos);


                    $inicioMsg = implode(', ', $campos);
                    $finalMsg = ($numeroCampos == 1) ? $ultimoCampo : " e $ultimoCampo";

                    $camposFormatados = $inicioMsg . $finalMsg;

                    $regra = ($numeroCampos == 1) ? $regra : $regra."Plural";

                    # So usa o prefixo se a interface for exibicao em texto puro
                    $this->mensagemErroFinal .= ($this->interfaceErros==1) ? $this->estilosErros['prefixo'] : null;

                    $this->mensagemErroFinal .= sprintf($this->mensagensErros[$regra], $camposFormatados, $parametro2);

                    # So usa o sufixo se a interface for exibicao em texto puro
                    $this->mensagemErroFinal .= ($this->interfaceErros==1) ? $this->estilosErros['sufixo'] : null;

                    # Quebra de linha para erros mostrados em texto puro e em alert javascript
                    $this->mensagemErroFinal .= ($this->interfaceErros == 1) ? "<br>\n" : '\n';

                }

            }

        }

        if ($this->totalErros == 0) {
            return TRUE;
        }
        return FALSE;

    }

    function mostrarErros() {
        if ($this->mensagemErroFinal == ''){
            return;
        }

        # Erros em texto puro
        if ($this->interfaceErros == 1) {
            echo $this->mensagemErroFinal;
        }
        elseif ($this->interfaceErros == 2) {
            $saida = '';
            $saida .= "\n".'<script type="text/javascript">'."\n";
            $saida .= "\t".'var msg="'.html_entity_decode($this->mensagemErroFinal).'";'."\n";
            $saida .= "\t".'alert(msg);'."\n";
            $saida .= '</script>'."\n";

            echo $saida;
        }

    }

}
