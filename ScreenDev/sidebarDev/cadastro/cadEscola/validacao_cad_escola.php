<?php
session_start();
include_once("../../../../conexao.php");
date_default_timezone_set('America/Sao_Paulo');

// Recebe e filtra os dados do formulário
$nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);
$cnpj = filter_input(INPUT_POST, 'cnpj', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$confirma_email = filter_input(INPUT_POST, 'confirma_email', FILTER_SANITIZE_EMAIL);
$tipo_escola = filter_input(INPUT_POST, 'tipo_escola', FILTER_SANITIZE_STRING);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$celular = filter_input(INPUT_POST, 'celular', FILTER_SANITIZE_STRING);
$codigo_escola = filter_input(INPUT_POST, 'codigo_escola', FILTER_SANITIZE_STRING);
$endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
$bairro = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING);
$numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING);
$cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
$cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
$estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

// Captura o usuário logado
$cadastrado_por = $_SESSION['nome']; // Presumindo que o nome do usuário logado está na sessão

// Captura a data e hora do cadastro
$data_cadastro = date('Y-m-d H:i:s'); // Formato padrão do MySQL para DATETIME

// Inicializa arrays para armazenar erros e valores dos campos
$errors = [];
$values = [
    'nome_escola' => $nome_escola,
    'cnpj' => $cnpj,
    'email' => $email,
    'confirma_email' => $confirma_email,
    'telefone' => $telefone,
    'celular' => $celular,
    'tipo_escola' => $tipo_escola,
    'codigo_escola' => $codigo_escola,
    'endereco' => $endereco,
    'bairro' => $bairro,
    'numero' => $numero,
    'cep' => $cep,
    'cidade' => $cidade,
    'estado' => $estado,
];

// Função para validar se é um número de CNPJ válido
function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }

    $peso1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    $peso2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

    $soma = 0;
    for ($i = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $peso1[$i];
    }
    $resto = $soma % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;

    if ($cnpj[12] != $digito1) {
        return false;
    }

    $soma = 0;
    for ($i = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $peso2[$i];
    }
    $resto = $soma % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;

    return $cnpj[13] == $digito2;
}

// Normalizar o CPF removendo caracteres não numéricos
$cnpj = preg_replace('/[^0-9]/', '', $cnpj);

if (!empty($cnpj) && !validarCNPJ($cnpj)) {
    $errors['cnpj'] = "CNPJ inválido!";
}

// Verificar se o cnpj já está cadastrado no banco de dados
if (empty($errors['cnpj'])) {
    $query_verifica_cnpj = "SELECT COUNT(*) AS total FROM tbescola WHERE cnpj = ?";
    $stmt_verifica_cnpj = $conn->prepare($query_verifica_cnpj);
    $stmt_verifica_cnpj->bind_param("s", $cnpj);
    $stmt_verifica_cnpj->execute();
    $result_verifica_cnpj = $stmt_verifica_cnpj->get_result();
    $row_verifica_cnpj = $result_verifica_cnpj->fetch_assoc();

    if ($row_verifica_cnpj['total'] > 0) {
        $errors['cnpj'] = "CNPJ já cadastrado!";
    }
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

// Função para validar CEP
function validarCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    return strlen($cep) == 8;
}

if (!validarCEP($cep)) {
    $errors['cep'] = "CEP inválido!";
}

// Se houver erros, armazene os erros e valores na sessão e redirecione de volta ao formulário
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['values'] = $values;
    header("Location: cadastrar_escola.php");
    exit();
}

// Inserir os dados no banco de dados
$query = "INSERT INTO tbescola (nome_escola, cnpj, email, tipo_escola, telefone, celular, codigo_escola, endereco, bairro, cidade, estado, cep, numero,cadastrado_por, data_cadastro) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Erro na preparação da query: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, 'sssssssssssssss', $nome_escola, $cnpj, $email, $tipo_escola, $telefone, $celular, $codigo_escola, $endereco, $bairro, $cidade, $estado, $cep, $numero, $cadastrado_por, $data_cadastro);

if (mysqli_stmt_execute($stmt)) {
    echo "<script type='text/javascript'>
            alert('Escola cadastrada com sucesso!');
            window.location.href = 'cadastrar_escola.php';
          </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Erro ao cadastrar escola: " . mysqli_error($conn) . "');
            window.location.href = 'cadastrar_escola.php';
          </script>";
}
?>
