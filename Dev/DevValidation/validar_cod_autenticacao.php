<?php
session_start();

ob_start(); // Limpar o buffer de saída

// Fuso horário do lugar informado 
date_default_timezone_set('America/Sao_Paulo');

include_once("../../conexao/conexao.php");

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
            header('Location: ../DevScreen/pagedevNew.php');
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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar Codigo de Acesso</title>

    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../DevCss/defaults.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="w-screen h-screen flex flex-col items-center justify-center bg-[var(--off-white)]">
    <main class="min-w-[320px] w-[320px] sm:w-[392px] flex flex-col gap-4 pb-2 sm:pb-2 p-4 sm:p-8 bg-white rounded-md shadow-md">
        <header class="flex gap-2 justify-center items-center">
            <h1 class="text-3xl font-semibold text-primary">Biblio<span class="text-secondary">etec</span></h1>
            <span class="w-[2px] h-6 sm:h-8  bg-secondary"></span>
            <h1 class="text-3xl font-regular text-secondary">Código</h1>
        </header>
        <form action="" method="post" class="flex flex-col gap-4">
        <div class="flex flex-col gap-0">
            <label for="codigo_autenticacao" class="text-secondary font-medium">Código:</label>
            <input type="text"  name="codigo_autenticacao" id="codigo_autenticacao" placeholder="Insira o código" class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
        </div>
        
            
        <div class="flex flex-col gap-2">
            <input type="submit" name="ValCodigo" value="Validar" class="w-full h-12 rounded shadow-sm bg-secondary text-2xl text-white font-semibold cursor-pointer">
            <a href="../loginDev.php" class="text-secondary text-sm text-center underline">Sair</a>
        </div>
        
        <?php
            if (isset($_SESSION['msg'])) {
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
            }
        ?>
        </form>
    </main>   
</body>
</html>
