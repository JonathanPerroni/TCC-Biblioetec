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
            $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Dev não encontrado!</p>";
            header("Location: ../loginDev.php");
            exit();
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
            <label for="">Código: </label>
            <input type="text" name="codigo_autenticacao" placeholder="Digite o codigo" id=""><br><br>

            <input type="submit" name="ValCodigo" value="Validar"><br><br>
        </form>
    </div>
</main>   
</body>
</html>
