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
$nome_curso = isset($_POST['nome_curso']) ? $_POST['nome_curso'] : '';
$tempo_curso = isset($_POST['tempo_curso']) ? $_POST['tempo_curso'] : '';


// Protege contra SQL Injection
$nome_curso = $conn->real_escape_string($nome_curso);
$tempo_curso = $conn->real_escape_string($tempo_curso);



// Variável para armazenar mensagens de erro
$_SESSION['msg'] = '';




// Verifica se há mensagens de erro
if (!empty($_SESSION['msg'])) {
    header("Location: editarCurso.php?codigo=" . urlencode($codigo));
    exit();
}


try {
    // Inicia a transação
    $conn->begin_transaction();

    // Atualiza os dados no banco de dados
    $sql = "UPDATE tbcursos SET
                nome_curso = ?,
                tempo_curso = ?
            WHERE codigo = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->bind_param("ssi", $nome_curso, $tempo_curso,  $codigo);

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
            registraHistorico($conn, "editar", $_SESSION['nome'], $nome_curso, "Curso", date('Y-m-d H:i:s'));
        }

        // Confirma a transação
        $conn->commit();

        $_SESSION['sucesso'] = "Registro atualizado com sucesso!";
        
        // Redireciona para a página de edição
        header("Location: editarCurso.php?codigo=" . urlencode($codigo));
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
