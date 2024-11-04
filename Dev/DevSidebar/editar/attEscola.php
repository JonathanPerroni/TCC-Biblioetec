<?php
session_start();
include("../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

// Captura o código (chave primária) do registro que será atualizado
if (isset($_POST['codigo']) && !empty($_POST['codigo'])) {
    $codigo = $_POST['codigo'];
} else {
    echo "Código não informado.";
    exit;
}

// Captura e escapa os outros dados do formulário
$nome_escola = isset($_POST['nome_escola']) ? $_POST['nome_escola'] : '';
$tipoEscola = isset($_POST['tipoEscola']) ? $_POST['tipoEscola'] : '';
$codigo_escola = isset($_POST['codigo_escola']) ? $_POST['codigo_escola'] : '';
$endereco = isset($_POST['endereco']) ? $_POST['endereco'] : '';
$numero = isset($_POST['numero']) ? $_POST['numero'] : '';
$bairro = isset($_POST['bairro']) ? $_POST['bairro'] : '';
$estado = isset($_POST['estado']) ? $_POST['estado'] : '';
$cep = isset($_POST['cep']) ? $_POST['cep'] : '';
$telefone = isset($_POST['telefone']) ? $_POST['telefone'] : '';
$celular = isset($_POST['celular']) ? $_POST['celular'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$cnpj = isset($_POST['cnpj']) ? $_POST['cnpj'] : '';

// Protege contra SQL Injection
$nome_escola = $conn->real_escape_string($nome_escola);
$tipoEscola = $conn->real_escape_string($tipoEscola);
$codigo_escola = $conn->real_escape_string($codigo_escola);
$endereco = $conn->real_escape_string($endereco);
$numero = $conn->real_escape_string($numero);
$bairro = $conn->real_escape_string($bairro);
$estado = $conn->real_escape_string($estado);
$cep = $conn->real_escape_string($cep);
$telefone = $conn->real_escape_string($telefone);
$celular = $conn->real_escape_string($celular);
$email = $conn->real_escape_string($email);
$cnpj = $conn->real_escape_string($cnpj);


// Variável para armazenar mensagens de erro
$_SESSION['msg'] = '';




// Função para validar se o CEP é válido
function validarCEP($cep) {
    $cep = preg_replace('/[^0-9]/', '', $cep);
    return strlen($cep) === 8 && is_numeric($cep);
}

// Validação do email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['msg'] .= "O email informado é inválido.<br>";
} else {
    $queryEmail = "SELECT COUNT(*) FROM tbescola WHERE email = ? AND codigo != ?";
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
    header("Location: editarEscola.php?codigo=" . urlencode($codigo));
    exit();
}


try {
    // Inicia a transação
    $conn->begin_transaction();

    // Atualiza os dados no banco de dados
    $sql = "UPDATE tbescola SET
                nome_escola = ?,
                codigo_escola = ?, 
                tipoEscola = ?, 
                endereco = ?, 
                numero = ?, 
                bairro = ?, 
                estado = ?, 
                cep = ?,
                telefone = ?, 
                celular = ?, 
                email = ?, 
                cnpj = ?          
            WHERE codigo = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->bind_param("ssssississsss", $nome_escola, $codigo_escola, $tipoEscola, $endereco, $numero, $bairro, $estado, $cep, $telefone, $celular, $email, $cnpj, $codigo);

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

    // Executa a atualização
    if ($stmt->execute()) {
        // Verifica se há uma sessão ativa para registrar no histórico
        if (empty($_SESSION['msg'])) {
            registraHistorico($conn, "editar", $_SESSION['nome'], $nome_escola, "Escola", date('Y-m-d H:i:s'));
        }

        // Confirma a transação
        $conn->commit();

        $_SESSION['sucesso'] = "Registro atualizado com sucesso!";
        
        // Redireciona para a página de edição
        header("Location: editarEscola.php?codigo=" . urlencode($codigo));
        exit();
    } else {
        throw new Exception("Erro ao atualizar o registro: " . $stmt->error);
    }
} catch (Exception $e) {
    // Reverte a transação em caso de erro
    $conn->rollback();
    echo $e->getMessage();
}

$stmt->close();
$conn->close();

?>
