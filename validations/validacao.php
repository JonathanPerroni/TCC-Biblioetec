<?php
session_start();
include_once("../conexao.php");

$conectar = filter_input(INPUT_POST, 'conectar', FILTER_SANITIZE_STRING);
if ($conectar) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Pesquisar o usuário no banco de dados
    if (!empty($email) && !empty($password)) {
        // Usando prepared statements para evitar SQL Injection
        $stmt = $conn->prepare("SELECT codigo, nome, cpf, email, password, telefone, celular, acesso FROM tbdev WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado_usuario = $stmt->get_result();

        if ($resultado_usuario && $resultado_usuario->num_rows > 0) {
            $row_usuario = $resultado_usuario->fetch_assoc();
            
            // Verifique se a senha está sendo comparada corretamente
            if (password_verify($password, $row_usuario['password'])) {
                $_SESSION['nome'] = $row_usuario['nome'];
                $_SESSION['cpf'] = $row_usuario['cpf'];
                $_SESSION['email'] = $row_usuario['email'];
                $_SESSION['telefone'] = $row_usuario['telefone'];
                $_SESSION['celular'] = $row_usuario['celular'];
                $_SESSION['acesso'] = $row_usuario['acesso'];
                header("Location: ../Screen/pagedev.php");
                exit();
            } else {
                $_SESSION['msg'] = "Senha incorreta!";
                header("Location: ../loginDev.php");
                exit();
            }
        } else {
            $_SESSION['msg'] = "Email incorreto!";
            header("Location: ../loginDev.php");
            exit();
        }
    } else {
        $_SESSION['msg'] = "Preencha todos os campos!";
        header("Location:  ../loginDev.php");
        exit();
    }
} else {
    $_SESSION['msg'] = "PÁGINA NÃO ENCONTRADA";
    header("Location: ../loginDev.php");
    exit();
}
?>
