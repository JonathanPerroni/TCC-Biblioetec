<?php
session_start();
include_once("../conexao.php");
include_once("../pageProtect.php");

// Recebe e filtra os dados do formulário
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$confirma_email = filter_input(INPUT_POST, 'confirma_email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);
$acesso = filter_input(INPUT_POST, 'acesso', FILTER_SANITIZE_STRING);

// Inicializa arrays para armazenar erros e valores dos campos
$errors = [];
$values = [
    'nome' => $nome,
    'cpf' => $cpf,
    'email' => $email,
    'confirma_email' => $confirma_email,
    'telefone' => $telefone,
    'celular' => $celular,
    'acesso' => $acesso,
];

// Função para validar se é um número de CPF válido
function validarCPF($cpf) {
    // Remover caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11) {
        return false;
    }
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
    for ($i = 9; $i < 11; $i++) {
        $sum = 0;
        for ($j = 0; $j < $i; $j++) {
            $sum += $cpf[$j] * (($i + 1) - $j);
        }
        $remainder = $sum % 11;
        $digit = ($remainder < 2) ? 0 : 11 - $remainder;
        if ($digit != $cpf[$i]) {
            return false;
        }
    }
    return true;
}

// Verificar se todos os campos obrigatórios foram preenchidos
if (empty($nome)) {
    $errors['nome'] = "Nome é obrigatório!";
}
if (empty($cpf)) {
    $errors['cpf'] = "CPF é obrigatório!";
}
if (empty($email)) {
    $errors['email'] = "Email é obrigatório!";
}
if (empty($confirma_email)) {
    $errors['confirma_email'] = "Confirmação de Email é obrigatória!";
}
if (empty($password)) {
    $errors['password'] = "Senha é obrigatória!";
}
if (empty($celular)) {
    $errors['celular'] = "Celular é obrigatório!";
}
if (empty($acesso)) {
    $errors['acesso'] = "Acesso é obrigatório!";
}

// Normalizar o CPF removendo caracteres não numéricos
$cpf = preg_replace('/[^0-9]/', '', $cpf);

// Verificar se é um número de CPF válido
if (!empty($cpf) && !validarCPF($cpf)) {
    $errors['cpf'] = "CPF inválido!";
}

// Verificar se o CPF já está cadastrado no banco de dados
if (empty($errors['cpf'])) {
    $query_verifica_cpf = "SELECT COUNT(*) AS total FROM tbdev WHERE cpf = ?";
    $stmt_verifica_cpf = $conn->prepare($query_verifica_cpf);
    $stmt_verifica_cpf->bind_param("s", $cpf);
    $stmt_verifica_cpf->execute();
    $result_verifica_cpf = $stmt_verifica_cpf->get_result();
    $row_verifica_cpf = $result_verifica_cpf->fetch_assoc();

    if ($row_verifica_cpf['total'] > 0) {
        $errors['cpf'] = "CPF já cadastrado!";
    }
}

// Verificar se os e-mails são iguais
if (!empty($email) && !empty($confirma_email) && $email !== $confirma_email) {
    $errors['confirma_email'] = "Os e-mails não são iguais!";
}

// Verificar se o email é válido
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Email inválido!";
}

// Verificar se o email já está cadastrado no banco de dados
if (empty($errors['email']) && empty($errors['confirma_email'])) {
    $query_verifica_email = "SELECT COUNT(*) AS total FROM tbdev WHERE email = ?";
    $stmt_verifica_email = $conn->prepare($query_verifica_email);
    $stmt_verifica_email->bind_param("s", $email);
    $stmt_verifica_email->execute();
    $result_verifica_email = $stmt_verifica_email->get_result();
    $row_verifica_email = $result_verifica_email->fetch_assoc();

    if ($row_verifica_email['total'] > 0) {
        $errors['email'] = "Email já cadastrado!";
    }
}

// Verificar se o telefone é um número válido
if (!empty($telefone) && !preg_match('/^(\(?\d{2}\)?\s?)?\d{5}-?\d{4}$/', $telefone)) {
    $errors['telefone'] = "Telefone inválido! O formato deve ser (XX) 9XXXX-XXXX ou XX9XXXX-XXXX";
}

// Se houver erros, armazene os erros e valores na sessão e redirecione de volta ao formulário
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['values'] = $values;
    header("Location: primeiroAcesso.php");
    exit();
}

// Hash da senha informada pelo usuário
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Inserir os dados no banco de dados
$query = "INSERT INTO tbdev (nome, cpf, email, password, telefone, celular, acesso) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'sssssss', $nome, $cpf, $email, $password_hash, $telefone, $celular, $acesso);

if (mysqli_stmt_execute($stmt)) {
    echo "<script type='text/javascript'>
            alert('Usuário cadastrado com sucesso!');
            window.location.href = '../loginDev.php';
          </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Erro ao cadastrar usuário: " . mysqli_error($conn) . "');
            window.location.href = 'primeiroAcesso.php';
          </script>";
}


