<?php
session_start(); // Inicia a sessão para armazenar dados do usuário logado
include_once("../conexao.php"); // Inclui o arquivo de conexão com o banco de dados
include_once("../pageProtect.php"); // Certifique-se de que este arquivo existe e está correto

// Captura os dados enviados pelo formulário de login
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); // Filtra e captura o email
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING); // Filtra e captura a senha

if (!empty($email) && !empty($password)) { // Verifica se email e senha foram preenchidos
    // Prepara a consulta SQL para buscar o usuário pelo email no banco de dados
    $query = "SELECT codigo, password FROM tbdev WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query); // Prepara a consulta SQL
    mysqli_stmt_bind_param($stmt, 's', $email); // Associa o parâmetro ao statement SQL
    mysqli_stmt_execute($stmt); // Executa o statement SQL
    $result = mysqli_stmt_get_result($stmt); // Obtém o resultado da consulta

    if ($result && mysqli_num_rows($result) > 0) { // Verifica se a consulta retornou resultados
        $row = mysqli_fetch_assoc($result); // Obtém os dados do usuário encontrados no banco de dados
        
        if (password_verify($password, $row['password'])) { // Verifica se a senha informada é válida
            $_SESSION['codigo'] = $row['codigo']; // Armazena o código do usuário na sessão
            echo "success"; // Retorna "success" para indicar sucesso na autenticação
        } else {
            echo "Senha incorreta"; // Retorna mensagem de erro se a senha estiver incorreta
        }
    } else {
        echo "Usuário não encontrado na tabela tbdev"; // Retorna mensagem se o usuário não for encontrado no banco de dados
    }
} else {
    echo "Preencha todos os campos"; // Retorna mensagem se algum campo estiver vazio no formulário de login
}
?>
