<?php
session_start();
include("../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

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

// Recebe e filtra os dados do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cod = $_POST['codigo'];

    try {
        // Inicia a transação
        $conn->begin_transaction();

        // Recupera o nome da escola a ser excluída
        $sqlSelect = "SELECT nome_escola FROM tbescola WHERE codigo = ?";
        $stmtSelect = $conn->prepare($sqlSelect);
        if (!$stmtSelect) {
            throw new Exception("Erro ao preparar a consulta de seleção: " . $conn->error);
        }
        $stmtSelect->bind_param("i", $cod);
        $stmtSelect->execute();
        $stmtSelect->bind_result($nome_escola);
        $stmtSelect->fetch();
        $stmtSelect->close();

        // Verifica se a escola foi encontrada
        if (!$nome_escola) {
            echo "Escola não encontrada!";
            exit;
        }

        // Define a consulta para excluir o registro
        $sqlDelete = "DELETE FROM tbescola WHERE codigo = ?";
        $stmtDelete = $conn->prepare($sqlDelete);

        if (!$stmtDelete) {
            throw new Exception("Erro ao preparar a consulta de exclusão: " . $conn->error);
        }

        $stmtDelete->bind_param("i", $cod);

        // Executa a exclusão
        if ($stmtDelete->execute()) {
            // Registra a ação no histórico
            $historico_acao = "excluir";
            $historico_responsavel = $_SESSION['nome'];
            $historico_usuario = $nome_escola; // Aqui usamos o nome da escola excluída
            $historico_acesso = "Escola"; // O tipo de acesso é fixado como "Escola"
            $historico_data_hora = date('Y-m-d H:i:s');

            registraHistorico($conn, $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora);
            
            // Confirma a transação
            $conn->commit();
            echo "Registro excluído com sucesso!";
        } else {
            throw new Exception("Erro ao excluir o registro: " . $stmtDelete->error);
        }
    } catch (Exception $e) {
        // Reverte a transação em caso de erro
        $conn->rollback();
        echo $e->getMessage();
    }

    // Verifica se a variável foi inicializada antes de fechá-la
    if (isset($stmtDelete)) {
        $stmtDelete->close();
    }
    
    // Fecha a conexão
    $conn->close();

    // Redireciona para a página de listar
    header("Location: ../list/listaescolaNew.php");
    exit;
} else {
    echo "Método de requisição inválido!";
    exit;
}
?>
