<?php
session_start();
include("../../../conexao/conexao.php");
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

    // Recupera o nome do usuário e tipo de acesso a ser excluído
    $sqlSelect = "SELECT nome, acesso FROM tbadmin WHERE codigo = ?";
    $stmtSelect = $conn->prepare($sqlSelect);
    if (!$stmtSelect) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }
    $stmtSelect->bind_param("i", $cod);
    $stmtSelect->execute();
    $stmtSelect->bind_result($nome_usuario, $tipo_acesso); // Agora também busca o tipo de acesso
    $stmtSelect->fetch();
    $stmtSelect->close();

    // Verifica se o usuário foi encontrado
    if (!$nome_usuario) {
        echo "Usuário não encontrado!";
        exit;
    }

    // Define a consulta para excluir o registro
    $sqlDelete = "DELETE FROM tbadmin WHERE codigo = ?";
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
        $historico_usuario = $nome_usuario; // Aqui usamos o nome do usuário excluído
        $historico_acesso = $tipo_acesso; // Aqui usamos o tipo de acesso do usuário excluído
        $historico_data_hora = date('Y-m-d H:i:s');

        registraHistorico($conn, $historico_responsavel, $historico_acao, $historico_usuario, $historico_acesso, $historico_data_hora);
        
        echo "Registro excluído com sucesso!";
    } else {
        echo "Erro ao excluir o registro: " . $stmtDelete->error;
    }

    $stmtDelete->close();
    $conn->close();

    // Redireciona para a página de listar
    header("Location: ../list/listaadminNew.php");
    exit;
} else {
    echo "Método de requisição inválido!";
    exit;
}
?>
