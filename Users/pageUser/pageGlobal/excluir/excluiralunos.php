<?php
session_start();
include("../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

// Função para registrar histórico
function registraHistorico($conn, $historico_responsavel, $historico_acao, $historico_usuario, $historico_acesso, $historico_data_hora) {
    $stmt = $conn->prepare("INSERT INTO historico_usuarios (historico_responsavel, historico_acao, historico_usuario, historico_acesso, historico_data_hora) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta de histórico: " . $conn->error);
    }
    $stmt->bind_param("sssss", $historico_responsavel, $historico_acao, $historico_usuario, $historico_acesso, $historico_data_hora);
    $stmt->execute();
    $stmt->close();
}

// Recebe e filtra os dados do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod = $_POST['codigo'];

    // Recupera o nome do aluno e tipo de acesso a ser excluído
    $sqlSelect = "SELECT nome, acesso FROM tbalunos WHERE codigo = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    if (!$stmtSelect) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    $stmtSelect->bind_param("i", $cod);
    $stmtSelect->execute();
    $stmtSelect->bind_result($nome_usuario, $tipo_acesso);
    $stmtSelect->fetch();
    $stmtSelect->close();

    // Verifica se o aluno foi encontrado
    if (!$nome_usuario) {
        echo "<script>alert('Aluno não encontrado!');</script>";
        exit;
    }

    // Define a consulta para excluir o registro
    $sqlDelete = "DELETE FROM tbalunos WHERE codigo = ?";
    $stmtDelete = $conn->prepare($sqlDelete);
    
    if (!$stmtDelete) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmtDelete->bind_param("i", $cod);

    // Executa a exclusão
    if ($stmtDelete->execute()) {
        // Registra a ação no histórico
        $historico_acao = "excluir";
        $historico_responsavel = $_SESSION['nome'];
        $historico_usuario = $nome_usuario;
        $historico_acesso = $tipo_acesso;
        $historico_data_hora = date('Y-m-d H:i:s');

        registraHistorico($conn, $historico_responsavel, $historico_acao, $historico_usuario, $historico_acesso, $historico_data_hora);

        // Notificação de exclusão bem-sucedida
        echo "<script>
            alert('O aluno \"$nome_usuario\" foi excluído com sucesso!');
            window.location.href = '../list/listaalunoNew.php';
        </script>";
    } else {
        echo "<script>alert('Erro ao excluir o registro: " . $stmtDelete->error . "');</script>";
    }

    $stmtDelete->close();
    $conn->close();
    exit;
} else {
    echo "<script>alert('Método de requisição inválido!');</script>";
    exit;
}
?>
