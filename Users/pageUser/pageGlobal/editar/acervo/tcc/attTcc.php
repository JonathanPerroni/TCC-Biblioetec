<?php
session_start();
include("../../../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

// Captura o código (chave primária) do registro que será atualizado
if (isset($_POST['codigo']) && !empty($_POST['codigo'])) {
    $codigo = $_POST['codigo'];
} else {
    echo "Código não informado.";
    exit;
}

// Captura e escapa os outros dados do formulário
$turma = $_POST['turma'];
$titulo = $_POST['titulo'];
$autor = $_POST['autor'];
$orientador = $_POST['orientador'];
$curso = $_POST['curso'];
$ano = $_POST['ano'];
$data_edicao = $_POST['data_edicao'];
$estante = $_POST['estante'];
$prateleira = $_POST['prateleira'];
$quantidade = $_POST['quantidade'];

// Protege contra SQL Injection
$turma = $conn->real_escape_string($turma);
$titulo = $conn->real_escape_string($titulo);
$autor = $conn->real_escape_string($autor);
$orientador = $conn->real_escape_string($orientador);
$curso = $conn->real_escape_string($curso);
$ano = $conn->real_escape_string($ano);
$data_edicao = $conn->real_escape_string($data_edicao);
$estante = $conn->real_escape_string($estante);
$prateleira = $conn->real_escape_string($prateleira);
$quantidade = $conn->real_escape_string($quantidade);

// Atualiza os dados no banco de dados
try {
    $conn->begin_transaction();

    $sql = "UPDATE tbtcc SET 
                turma = ?, 
                titulo = ?, 
                autor = ?, 
                orientador = ?, 
                curso = ?, 
                ano = ?, 
                data_edicao = ?, 
                estante = ?, 
                prateleira = ?, 
                quantidade = ? 
            WHERE codigo = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta SQL: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssssssi",
        $turma,
        $titulo,
        $autor,
        $orientador,
        $curso,
        $ano,
        $data_edicao,
        $estante,
        $prateleira,
        $quantidade,
        $codigo
    );

    if ($stmt->execute()) {
        // Registro do histórico
        $historico_acao = "editar";
        $historico_responsavel = $_SESSION['nome'];
        $historico_usuario = $titulo;
        $historico_acesso = $_SESSION['acesso'];
        $historico_data_hora = date('Y-m-d H:i:s');

        $stmtHist = $conn->prepare("INSERT INTO historico_usuarios (historico_acao, historico_responsavel, historico_usuario, historico_acesso, historico_data_hora) VALUES (?, ?, ?, ?, ?)");
        $stmtHist->bind_param("sssss", $historico_acao, $historico_responsavel, $historico_usuario, $historico_acesso, $historico_data_hora);
        $stmtHist->execute();
        $stmtHist->close();

        $conn->commit();
        $_SESSION['sucesso'] = "Registro atualizado com sucesso!";
        header("Location: editarTcc.php?codigo=" . urlencode($codigo));
        exit;
    } else {
        throw new Exception("Erro ao atualizar o registro: " . $stmt->error);
    }
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['msg'] = "Erro ao atualizar registro: " . $e->getMessage();
    header("Location: editarTcc.php?codigo=" . urlencode($codigo));
    exit;
} finally {
    $stmt->close();
    $conn->close();
}
?>
