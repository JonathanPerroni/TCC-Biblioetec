<?php
session_start();
include("../../../../../conexao/conexao.php");
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

    // Recupera o título do jornal/revista e o responsável pelo cadastro
    $sqlSelect = "SELECT titulo, classe FROM tbjornal_revista WHERE codigo = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    if (!$stmtSelect) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    $stmtSelect->bind_param("i", $cod);
    $stmtSelect->execute();
    $stmtSelect->bind_result($titulo, $classe);
    $stmtSelect->fetch();
    $stmtSelect->close(); 

    // Verifica se o registro foi encontrado
    if (!$titulo) {
        echo "<script>alert('Jornal/Revista não encontrado!');</script>";
        exit;
    }

    // Define a consulta para excluir o registro
    $sqlDelete = "DELETE FROM tbjornal_revista WHERE codigo = ?";
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
        $historico_usuario = $titulo; // Nome do jornal/revista excluído
        $historico_acesso = $classe; // Responsável pelo cadastro
        $historico_data_hora = date('Y-m-d H:i:s');

        registraHistorico($conn, $historico_responsavel, $historico_acao, $historico_usuario, $historico_acesso, $historico_data_hora);

        // Notificação de exclusão bem-sucedida
        echo "<script>
            alert('O jornal/revista \"$titulo\" foi excluído com sucesso!');
            window.location.href = '../../list/acervo/listajornalrevistaNew.php';
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
