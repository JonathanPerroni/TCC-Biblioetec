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
$nome = $_POST['nome']; /*ok */
$cpf = $_POST['cpf'];/*ok */
$data_nascimento = $_POST['data_nascimento']; /*ok */
$responsavel = $_POST['responsavel']; /*ok */
$celular = $_POST['celular']; /*ok */
$endereco = $_POST['endereco']; /*ok */
$cidade = $_POST['cidade']; /*ok */
$estado = $_POST['estado']; /*ok */
$nome_escola = $_POST['nome_escola']; /*ok */
$tipo_ensino = $_POST['tipo_ensino']; /*ok */
$nome_curso = $_POST['nome_curso']; /*ok */
$situacao = $_POST['situacao']; /*ok */
$periodo = $_POST['periodo']; /*ok */
$email = $_POST['email']; /*ok */
$acesso = $_POST['acesso']; /*ok */
$password = $_POST['password']; /*ok */
$password2 = $_POST['password2']; /*ok */


// Protege contra SQL Injection
$nome = $conn->real_escape_string($_POST['nome']);
$cpf = $conn->real_escape_string($_POST['cpf']);
$data_nascimento = $conn->real_escape_string($_POST['data_nascimento']);
$responsavel = $conn->real_escape_string($_POST['responsavel']);
$celular = $conn->real_escape_string($_POST['celular']);
$endereco = $conn->real_escape_string($_POST['endereco']);
$cidade = $conn->real_escape_string($_POST['cidade']);
$estado = $conn->real_escape_string($_POST['estado']);
$nome_escola = $conn->real_escape_string($_POST['nome_escola']);
$tipo_ensino = $conn->real_escape_string($_POST['tipo_ensino']);
$nome_curso = $conn->real_escape_string($_POST['nome_curso']);
$situacao = $conn->real_escape_string($_POST['situacao']);
$periodo = $conn->real_escape_string($_POST['periodo']);
$email = $conn->real_escape_string($_POST['email']);
$acesso = $conn->real_escape_string($_POST['acesso']);
$password = $conn->real_escape_string($_POST['password']);
$password2 = $conn->real_escape_string($_POST['password2']);;

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
    $queryCpf = "SELECT COUNT(*) FROM tbalunos WHERE cpf = ? AND codigo != ?";
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
    $queryEmail = "SELECT COUNT(*) FROM tbalunos WHERE email = ? AND codigo != ?";
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
    header("Location: editarAluno.php?codigo=" . urlencode($codigo));
    exit();
}

// Criptografa a senha
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$status = isset($_POST['status']) ? (int)$_POST['status'] : 0; // Default para 0 se não estiver definido
try {
   // Atualiza os dados no banco de dados
   $conn->begin_transaction();

        $sql = "UPDATE tbalunos SET
            nome = ?, 
            cpf = ?, 
            data_nascimento = ?, 
            responsavel = ?, 
            celular = ?, 
            endereco = ?, 
            cidade = ?, 
            estado = ?, 
            nome_escola = ?, 
            tipo_ensino = ?, 
            nome_curso = ?, 
            situacao = ?, 
            periodo = ?, 
            email = ?, 
            acesso = ?, 
            password = ?
            WHERE codigo = ?";

        // Tenta preparar a consulta
        $stmt = $conn->prepare($sql);

    if (!$stmt) {
    // Exibe o erro e a consulta SQL para depuração
    echo "Erro ao preparar a consulta SQL: " . $conn->error . "<br>";
    echo "Consulta SQL: " . $sql;
    exit;
}
    


   // Aqui continuamos com o bind_param e a execução se não houver erro
$stmt->bind_param(
    "ssssssssssssssssi", 
    $nome, 
    $cpf, 
    $data_nascimento, 
    $responsavel, 
    $celular, 
    $endereco, 
    $cidade, 
    $estado, 
    $nome_escola, 
    $tipo_ensino, 
    $nome_curso, 
    $situacao, 
    $periodo, 
    $email, 
    $acesso, 
    $hashedPassword, 
    $codigo
);
    
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
// Executa a consulta
if ($stmt->execute()) {
    // Registro do histórico
    if (empty($_SESSION['msg'])) {
        registraHistorico($conn, "editar", $_SESSION['nome'], $nome, $acesso, date('Y-m-d H:i:s'));
    }
    $conn->commit();
    $_SESSION['sucesso'] = "Registro atualizado com sucesso!";
    header("Location: editarAluno.php?codigo=" . urlencode($codigo));
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
