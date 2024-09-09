<?php
session_start();
include_once("../../../../conexao.php"); // Conexão com o banco de dados principal
date_default_timezone_set('America/Sao_Paulo');

// Recebe e filtra os dados do formulário
$nome_escola = filter_input(INPUT_POST, 'nome_escola', FILTER_SANITIZE_STRING);
$cnpj = filter_input(INPUT_POST, 'cnpj', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$confirma_email = filter_input(INPUT_POST, 'confirma_email', FILTER_SANITIZE_EMAIL);
$tipoEscola = filter_input(INPUT_POST, 'tipoEscola', FILTER_SANITIZE_STRING);
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
    'tipoEscola' => $tipoEscola,
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

// Normalizar o CNPJ removendo caracteres não numéricos
$cnpj = preg_replace('/[^0-9]/', '', $cnpj);

if (!empty($cnpj) && !validarCNPJ($cnpj)) {
    $errors['cnpj'] = "CNPJ inválido!";
   
}
    // O CNPJ existe na tabela secundária
    // Verificar se o CNPJ também está na tabela principal
    $query_verifica_cnpj_principal = "SELECT COUNT(*) AS total FROM tbescola WHERE cnpj = ?";
    $stmt_verifica_cnpj_principal = $conn->prepare($query_verifica_cnpj_principal);

    if (!$stmt_verifica_cnpj_principal) {
        die("Erro na preparação da query: " . $conn->error);
    }

    $stmt_verifica_cnpj_principal->bind_param("s", $cnpj);
    $stmt_verifica_cnpj_principal->execute();
    $result_verifica_cnpj_principal = $stmt_verifica_cnpj_principal->get_result();
    $row_verifica_cnpj_principal = $result_verifica_cnpj_principal->fetch_assoc();


    if ($row_verifica_cnpj_principal['total'] > 0) {
        $errors['cnpj'] = "CNPJ já cadastrado!";
    } 


// Verificar se o código da escola já está em uso
$query_verifica_codigo_escola = "SELECT COUNT(*) AS total FROM tbescola WHERE codigo_escola = ?";
$stmt_verifica_codigo_escola = $conn->prepare($query_verifica_codigo_escola);

if (!$stmt_verifica_codigo_escola) {
    die("Erro na preparação da query: " . $conn->error);
}

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
    $query_verifica_email = "SELECT COUNT(*) AS total FROM tbescola WHERE email = ?";
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


function validarCEP($cep) {
    // Remove todos os caracteres não numéricos
    $cep = preg_replace('/[^0-9]/', '', $cep);

    // Verifica se o CEP possui exatamente 8 dígitos
    if (strlen($cep) !== 8) {
        return false;
    }

    // Verifica se todos os caracteres são números
    if (!is_numeric($cep)) {
        return false;
    }

    return true;
}

// Se houver erros, armazene os erros e valores na sessão e redirecione de volta ao formulário
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['values'] = $values;
    header("Location: cadastrar_escola.php");
    exit();
}

/* Se não houver erros, prossiga com o cadastro*/
$query_cadastro = "INSERT INTO tbescola (nome_escola, tipoEscola, codigo_escola, cnpj, email, telefone, celular, endereco, bairro, numero, cep, cidade, estado, cadastrado_por, data_cadastro)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";


// Verificar a conexão com o banco de dados
if ($conn === false) {
    // Verificação da conexão antes de proceder
    die("Erro na conexão com o banco de dados: " . mysqli_connect_error());
}


// Verificar se a preparação da consulta foi bem-sucedida
$stmt = mysqli_prepare($conn, $query_cadastro);

// Verificar se a preparação da consulta foi bem-sucedida
if ($stmt === false) {
    // Tratamento de erro para falha na preparação da consulta
    die("Erro na preparação da consulta: " . mysqli_error($conn));
}

$bind_result = mysqli_stmt_bind_param($stmt,"sssssssssssssss", $nome_escola, $tipoEscola, $codigo_escola, $cnpj, $email, $telefone, $celular, $endereco, $bairro, $numero, $cep, $cidade, $estado, $cadastrado_por, $data_cadastro);

// Verificar se a vinculação dos parâmetros foi bem-sucedida
if ($bind_result === false) {
    // Tratamento de erro para falha na vinculação dos parâmetros
    die("Erro ao vincular os parâmetros: " . mysqli_stmt_error($stmt));
}


// Executar a consulta
if (mysqli_stmt_execute($stmt)) {
    // Exibir mensagem de sucesso
    echo "<script type='text/javascript'>
            alert('Escola cadastrada com sucesso!');
            window.location.href = 'cadastrar_escola.php';
          </script>";
} else {
    // Tratamento de erro para falha na execução da consulta
    die("Erro ao executar a consulta: " . mysqli_stmt_error($stmt));
}

// Fechar a declaração e a conexão
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

