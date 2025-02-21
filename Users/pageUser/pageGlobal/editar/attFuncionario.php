<?php
session_start();
include("../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

// Captura o código (chave primária) do registro que será atualizado
if (isset($_POST['codigo']) && !empty($_POST['codigo'])) {
    $codigo = $_POST['codigo'];
} else {
    echo "Código não informado.";
    exit;
}

// Captura e escapa os outros dados do formulário
$nome = $_POST['nome'];
$email = $_POST['email'];
$password = $_POST['password'];
$password2 = $_POST['password2'];
$telefone = $_POST['telefone'];
$celular = $_POST['celular'];
$cpf = $_POST['cpf'];
$codigo_escola = $_POST['codigo_escola'];
$nome_escola = $_POST['nome_escola'];
$acesso = $_POST['acesso'];

// Protege contra SQL Injection
$nome = $conn->real_escape_string($nome);
$email = $conn->real_escape_string($email);
$password = $conn->real_escape_string($password);
$password2 = $conn->real_escape_string($password2);
$telefone = $conn->real_escape_string($telefone);
$celular = $conn->real_escape_string($celular);
$cpf = $conn->real_escape_string($cpf);
$codigo_escola = $conn->real_escape_string($codigo_escola);
$nome_escola = $conn->real_escape_string($nome_escola);
$acesso = $conn->real_escape_string($acesso);

// Variável para armazenar mensagens de erro
$_SESSION['msg'] = '';

// Verifica se as senhas coincidem
if ($password !== $password2) {
    $_SESSION['msg'] .= "As senhas não coincidem.<br>";
}

// Verifica se a senha possui pelo menos um número
if (!preg_match('/[0-9]/', $password)) {
    $_SESSION['msg'] .= "A senha deve conter pelo menos um número.<br>";
}

// Verifica se a senha possui pelo menos uma letra maiúscula
if (!preg_match('/[A-Z]/', $password)) {
    $_SESSION['msg'] .= "A senha deve conter pelo menos uma letra maiúscula.<br>";
}

// Verifica se a senha possui pelo menos uma letra minúscula
if (!preg_match('/[a-z]/', $password)) {
    $_SESSION['msg'] .= "A senha deve conter pelo menos uma letra minúscula.<br>";
}

// Verifica se a senha possui pelo menos um caractere especial
if (!preg_match('/[!@#$%^&*()_\-+=]/', $password)) {
    $_SESSION['msg'] .= "A senha deve conter pelo menos um caractere especial.<br>";
}

// Verifica se a senha possui no mínimo 8 caracteres
if (strlen($password) < 8) {
    $_SESSION['msg'] .= "A senha deve ter no mínimo 8 caracteres.<br>";
}

// Validação do CPF
function validarCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return "O CPF deve conter exatamente 11 dígitos.";
    }

    // Elimina CPFs inválidos conhecidos
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return "O CPF informado é inválido.";
    }

    // Cálculo do primeiro dígito verificador
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }
        $digito = ($soma * 10) % 11;
        $digito = ($digito == 10) ? 0 : $digito;
        if ($digito != $cpf[$t]) {
            return "O CPF informado é inválido.";
        }
    }
    
    return true; // CPF é válido
}

// Validação do CPF
$cpfErro = validarCPF($cpf);
if ($cpfErro !== true) {
    $_SESSION['msg'] .= $cpfErro . "<br>";
} else {
    // Verifica se o CPF já está cadastrado no banco
    $queryCpf = "SELECT COUNT(*) FROM tbfuncionarios WHERE cpf = ? AND codigo != ?";
    $stmtCpf = $conn->prepare($queryCpf);
    $stmtCpf->bind_param("si", $cpf, $codigo);
    $stmtCpf->execute();
    $stmtCpf->bind_result($cpfExists);
    $stmtCpf->fetch();
    $stmtCpf->close();

    if ($cpfExists > 0) {
        $_SESSION['msg'] .= "O CPF informado já está cadastrado.<br>";
    }
}

// Validação do email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['msg'] .= "O email informado é inválido.<br>";
} else {
    $queryEmail = "SELECT COUNT(*) FROM tbfuncionarios WHERE email = ? AND codigo != ?";
    $stmtEmail = $conn->prepare($queryEmail);
    $stmtEmail->bind_param("si", $email, $codigo);
    $stmtEmail->execute();
    $stmtEmail->bind_result($emailExists);
    $stmtEmail->fetch();
    $stmtEmail->close();

    if ($emailExists > 0) {
        $_SESSION['msg'] .= "O email informado já está cadastrado.<br>";
    }
}

// Verifica se há mensagens de erro
if (!empty($_SESSION['msg'])) {
    header("Location: editarFuncionario.php?codigo=" . urlencode($codigo));
    exit();
}

// Criptografa a senha
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0; // Default para 0 se não estiver definido
try {
    // Atualiza os dados no banco de dados
    $sql = "UPDATE tbfuncionarios SET
                nome = ?, 
                email = ?, 
                password = ?, 
                telefone = ?, 
                celular = ?, 
                cpf = ?, 
                codigo_escola = ?, 
                nome_escola = ?,
                acesso = ?,
                status = ?
            WHERE codigo = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->bind_param("ssssssssssi", $nome, $email, $hashedPassword, $telefone, $celular, $cpf, $codigo_escola, $nome_escola, $acesso, $status, $codigo);

    // Função para registrar histórico
    function registraHistorico($conn, $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora) {
        $stmt = $conn->prepare("INSERT INTO historico_usuarios (historico_acao, historico_responsavel, historico_usuario, historico_acesso, historico_data_hora) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Erro ao preparar a consulta de histórico: " . $conn->error);
        }
        $stmt->bind_param("sssss", $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora);
        $stmt->execute();
        $stmt->close();
    }

    if ($stmt->execute()) {
        if (empty($_SESSION['msg'])) {
            registraHistorico($conn, "editar", $_SESSION['nome'], $nome, $acesso, date('Y-m-d H:i:s'));
        }
        $conn->commit();
        $_SESSION['sucesso'] = "Registro atualizado com sucesso!";
        
        // Redireciona para a página de edição
        header("Location: editarFuncionario.php?codigo=" . urlencode($codigo));
        exit();
    } else {
        // Tratar possíveis erros aqui
        throw new Exception("Erro ao atualizar o registro: " . $stmt->error);
    }
} catch (Exception $e) {
    $conn->rollback();
    echo $e->getMessage();
}









$conn->close();
?>
