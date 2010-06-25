<?php

/*
 * Exemplo de uso da classe de validação
 */

if (!empty($_POST)) {

    include_once('Validacao.class.php');
    
    $validar = new Validacao();
    
    # Definindo as regras de validação para os campos
    $regras['nome']            = 'obrigatorio|alfa|minCaracter[3]';
    $regras['sobrenome']       = 'obrigatorio|alfa|minCaracter[3]';
    $regras['email']           = 'email';
    $regras['cpf']             = 'cpf';
    $regras['data_nascimento'] = 'obrigatorio|mascara[99/99/9999]';
    $regras['mac']             = 'mac';
    $regras['ip']              = 'ip';
    $regras['idade']           = 'numerico';
    $regras['data']            = 'obrigatorio|mascara[99/99/99]';
    
    $validar->setRegras($regras);
    
    # Definindo o nome dos campos para a mensagem de erro, caso exista
    # pois se o nome não for definido, o nome usado é o nome do input
    $campos['data_nascimento'] = "Data de Nascimento";
    $campos['mac']             = "MAC";
    $campos['ip']              = "IP";
    $campos['cpf']             = "CPF";
    $campos['email']           = 'E-mail';
    
    $validar->setCampos($campos);
    

    if ($validar->executar() === TRUE) {
    
        echo 'validado com sucesso';
    
    }

    $validar->mostrarErros();

}

?>

<form action="index.php" method="post">

    Nome            : <input type="text" name="nome" value="<?php echo $_POST['nome']; ?>" /> <br />
    Sobrenome       : <input type="text" name="sobrenome" value="<?php echo $_POST['sobrenome']; ?>" /> <br />
    E-mail          : <input type="text" name="email" value="<?php echo $_POST['email']; ?>" /> <br />
    Data Nascimento : <input type="text" name="data_nascimento" value="<?php echo $_POST['data_nascimento']; ?>" /> <br />
    CPF             : <input type="text" name="cpf" value="<?php echo $_POST['cpf']; ?>" /> <br />
    MAC             : <input type="text" name="mac" value="<?php echo $_POST['mac'] ?>" /><br />
    IP              : <input type="text" name="ip" value="<?php echo $_POST['ip'] ?>" /><br />
    Idade           : <input type="text" name="idade" value="<?php echo $_POST['idade']; ?>" /> <br />
    Data            : <input type="text" name="data" value="<?php echo $_POST['data']; ?>" /> <br />

<input type="submit" value="Validar" />
</form>

