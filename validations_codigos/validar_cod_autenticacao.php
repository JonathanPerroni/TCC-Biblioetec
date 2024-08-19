<?php
session_start();

ob_start(); // Limpar o buffer de saída

// Fuso horário do lugar informado 
date_default_timezone_set('America/Sao_Paulo');

include_once("../conexao.php");

// Verificar se a conexão está usando `mysqli`
if ($conn instanceof mysqli) {
    // Recebe os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Acessar o if quando o usuário clicar no botão acessar do formulário
    if (!empty($dados['ValCodigo'])) {
        
        // RECUPERAR OS DADOS DO USUÁRIO NO BANCO DE DADOS
        $query_usuario = "SELECT codigo, nome, cpf, email, password, telefone, celular, acesso, data_codigo_autenticacao
                          FROM tbdev
                          WHERE codigo = ? AND email = ? AND codigo_autenticacao = ?
                          LIMIT 1";

        // Preparar a query 
        $stmt = $conn->prepare($query_usuario);

        // Verificar se a preparação da query foi bem-sucedida
        if ($stmt === false) {
            die("Erro na preparação da query: " . $conn->error);
        }

        // Substituir os placeholders da query pelos valores do formulário
        $stmt->bind_param('sss', $_SESSION['codigo'], $_SESSION['email'], $dados['codigo_autenticacao']);

        // Executar a query
        $stmt->execute();

        // Acessar o if quando encontrar o usuário no banco de dados
        $result_usuario = $stmt->get_result();

        if ($result_usuario->num_rows > 0) {
            // Ler o registro retornado do banco de dados
            $row_usuario = $result_usuario->fetch_assoc(); 

            // query para salvar no banco de dados o código e a data gerada
            $query_up_usuario = "UPDATE tbdev SET 
                codigo_autenticacao = NULL,
                data_codigo_autenticacao = NULL
                WHERE codigo = ?
                LIMIT 1";

            // Preparar a query
            $stmt_up = $conn->prepare($query_up_usuario);

            // Verificar se a preparação da query foi bem-sucedida
            if ($stmt_up === false) {
                die("Erro na preparação da query de atualização: " . $conn->error);
            }

            // Substituir o placeholder pelo valor de $_SESSION['codigo']
            $stmt_up->bind_param('s', $_SESSION['codigo']);

            // Executar a query de atualização
            $stmt_up->execute();

            // Salvar os dados do usuário na sessão
            $_SESSION['nome'] = $row_usuario['nome'];
            $_SESSION['codigo_autenticacao'] = true;

            // Redirecionar o usuário 
            header('Location: ../Screen/pagedev.php');
            exit();

        } else {
            $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Codigo Invalido</p>";
            
        }
    }
} else {
    die("Conexão com o banco de dados não estabelecida.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Codigo de Acesso</title>
    <link rel="stylesheet" href="/biblioetec/Desenvolvedor/style/defaults.css">
    <Style>
 body{
        height: 90vh;
        justify-content: center;
        align-items: center;
        }

        main{
    max-width:400px;   
    width: 100%;
    height: auto;
    padding: 16px;
    border: none;
    border-radius: 16px;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.25 );
    background-color: white;
    display: flex;
    flex-direction: column;

}
header{
    display: flex;
    justify-content: center;
    flex-wrap: wrap;    
    text-align: center;
}

h1{
    font-size: 25px;
}

.separation-line{
    height: 2px;
        width: 80%;
        margin: 0px 16px;
        background-color: var(--off-white);
        color: var(--off-white);
}



#brand-title{
    color:  var(--primary-emphasis);
    font-weight: 600;   
    width: 200px;
}

#cad-title{
    color: var(--off-black);
    font-weight: 400;   
    width: 200px;
}

.formsContainer{
    margin-top: 10px;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    
    
}

form{
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;    
    justify-content:  center;
    align-items: center;  
    
}

.form-row{
    
    display: flex;
    flex-direction: column;
    justify-content: space-around ;
    align-items: center;
    margin-bottom: 10px;
    width: 100%;
    min-width: 100%;
    gap: 5px;
   
}
.input-container {
            margin-bottom: 15px;
            position: relative;
            flex-grow: 1;   
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            
        }

        .input-container label {
            display: block;
            margin-bottom: 5px;
        }
        .input-container input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .input-error {
            border: 1px solid red;
        }
        .input-success {
            border: 1px solid green;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        input{
    outline: none;
    border: 1px solid;
    width: 100%;
    height: 32px;
    padding: 0 8px;
    border-radius: 8px;
    border: 2px solid rgba(0, 0, 0, 0.5);
    font-size: 12px;
    font-weight: 500;
    color: var(--secondary-emphasis);
    caret-color: var(--secondary-emphasis);
    background-color: var(--off-white);
    font-family: 'Poppins', sans-serif;
    
}

input:focus{
    border: 2px solid var(--secondary-emphasis);
    transition: ease-in .1s;
}


.placeholder{
    position: absolute;
    top: 4px;
    left: 8px;
    color: var(--secondary-emphasis);
    font-weight: 500;    
    transition: .3s;
    pointer-events: none;
}


input:focus + .placeholder,
input:not(:placeholder-shown) + .placeholder {
    font-size: 12px;
    top: -16px;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    appearance: textfield;
}

input[type="submit"]{
    color: white;
    background-color: var(--secondary-emphasis);
    cursor: pointer;
    transition: ease-in-out .2s;
    margin-bottom: 5px;
}
input[type="submit"]:hover{
    scale: 1.025;
    background-color: #3B6603;
}

    </Style>
</head>
<body>
<main>
    <header>
        <h1 id="brand-title">Biblietec</h1>
        <span class="separation-line"></span>
        <h1 id="login-title">Validar Codigo</h1>
    </header>
    <div class="formsContainer">
        <form action="" method="post">
        <div class="form-row">
        <div class="input-container"> 
           
            <input type="text"  name="codigo_autenticacao" placeholder="" >
            <label for=""class="placeholder">Código </label>
            </div>
           
            <div class="input-container"> 
            <input type="submit" name="ValCodigo" value="Validar"><br>
          
            </div>
            <?php
                  if (isset($_SESSION['msg'])) {
                      echo $_SESSION['msg'];
                      unset($_SESSION['msg']);
                  }
                  ?>
            </div>
         
        </form>
   
    </div>
</main>   
</body>
</html>
