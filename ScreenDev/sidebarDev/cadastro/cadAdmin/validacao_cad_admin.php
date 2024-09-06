<?php
session_start();
include_once("../../../../conexao.php");
date_default_timezone_set('America/Sao_Paulo');

// Recebe e filtra os dados do formulário
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$confirma_email = filter_input(INPUT_POST, 'confirma_email', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$confirma_password = filter_input(INPUT_POST, 'confirma_password', FILTER_SANITIZE_STRING);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);
$acesso = filter_input(INPUT_POST, 'acesso', FILTER_SANITIZE_STRING);
$codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);

// Captura o usuário logado
$cadastrado_por = $_SESSION['nome']; // Presumindo que o nome do usuário logado está na sessão

// Captura a data e hora do cadastro
$data_cadastro = date('Y-m-d H:i:s'); // Formato padrão do MySQL para DATETIME

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

// Função para validar CPF
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

// Verificações de validação (preservadas do código original)
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
if (empty($confirma_password)) {
    $errors['confirma_password'] = "A confirmação da senha é obrigatória!";
}
if (empty($celular)) {
    $errors['celular'] = "Celular é obrigatório!";
}
if (empty($acesso)) {
    $errors['acesso'] = "Acesso é obrigatório!";
}
if (empty($codigo_escola)) {
    $errors['codigo_escola'] = "Código da Escola é obrigatório!";
}

// Verificar se a senha e a confirmação são iguais
if (!empty($password) && !empty($confirma_password) && $password !== $confirma_password) {
    $errors['confirma_password'] = "As senhas não são iguais!";
}

// Verificar se o código da escola já está em uso
$query_verifica_codigo_escola = "SELECT COUNT(*) AS total FROM tbadmin WHERE codigo_escola = ?";
$stmt_verifica_codigo_escola = $conn->prepare($query_verifica_codigo_escola);
$stmt_verifica_codigo_escola->bind_param("s", $codigo_escola);
$stmt_verifica_codigo_escola->execute();
$result_verifica_codigo_escola = $stmt_verifica_codigo_escola->get_result();
$row_verifica_codigo_escola = $result_verifica_codigo_escola->fetch_assoc();

if ($row_verifica_codigo_escola['total'] > 0) {
    $errors['codigo_escola'] = "Código da Escola já em uso!";
}

// Normalizar o CPF removendo caracteres não numéricos
$cpf = preg_replace('/[^0-9]/', '', $cpf);

// Verificar se é um número de CPF válido
if (!empty($cpf) && !validarCPF($cpf)) {
    $errors['cpf'] = "CPF inválido!";
}

// Verificar se o CPF já está cadastrado no banco de dados
if (empty($errors['cpf'])) {
    $query_verifica_cpf = "SELECT COUNT(*) AS total FROM tbadmin WHERE cpf = ?";
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
    $query_verifica_email = "SELECT COUNT(*) AS total FROM tbadmin WHERE email = ?";
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
    header("Location: cadastrar_admin.php");
    exit();
}

// Hash da senha informada pelo usuário
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Inserir os dados no banco de dados, incluindo quem cadastrou e a data de cadastro
$query = "INSERT INTO tbadmin (nome, cpf, email, password, telefone, celular, acesso, codigo_escola, cadastrado_por, data_cadastro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Verificar a conexão com o banco de dados
if ($conn === false) {
    // Verificação da conexão antes de proceder
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}

// Preparar a consulta
$stmt = mysqli_prepare($conn, $query);

// Verificar se a preparação da consulta foi bem-sucedida
if ($stmt === false) {
    // Tratamento de erro para falha na preparação da consulta
    die("Erro na preparação da consulta: " . mysqli_error($conn));
}

// Vincular os parâmetros
$bind_result = mysqli_stmt_bind_param($stmt, 'ssssssssss', $nome, $cpf, $email, $password_hash, $telefone, $celular, $acesso, $codigo_escola, $cadastrado_por, $data_cadastro);

// Verificar se a vinculação dos parâmetros foi bem-sucedida
if ($bind_result === false) {
    // Tratamento de erro para falha na vinculação dos parâmetros
    die("Erro ao vincular os parâmetros: " . mysqli_stmt_error($stmt));
}

// Executar a consulta
if (mysqli_stmt_execute($stmt)) {
    // Exibir mensagem de sucesso
    echo "<script type='text/javascript'>
            alert('Admin cadastrado com sucesso!');
            window.location.href = 'cadastrar_admin.php';
          </script>";
} else {
    // Tratamento de erro para falha na execução da consulta
    die("Erro ao executar a consulta: " . mysqli_stmt_error($stmt));
}

// Fechar a declaração e a conexão
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
