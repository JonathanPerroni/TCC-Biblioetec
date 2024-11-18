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
$classe = $_POST['classe'];
$titulo = $_POST['titulo'];
$data_publicacao = $_POST['data_publicacao'];
$editora = $_POST['editora'];
$categoria = $_POST['categoria'];
$issn = $_POST['issn'];
$data_adicao = $_POST['data_adicao'];
$estante = $_POST['estante'];
$prateleira = $_POST['prateleira'];
$edicao = $_POST['edicao'];
$quantidade = $_POST['quantidade'];

// Protege contra SQL Injection
$classe = $conn->real_escape_string($classe);
$titulo = $conn->real_escape_string($titulo);
$data_publicacao = $conn->real_escape_string($data_publicacao);
$editora = $conn->real_escape_string($editora);
$categoria = $conn->real_escape_string($categoria);
$issn = $conn->real_escape_string($issn);
$data_adicao = $conn->real_escape_string($data_adicao);
$estante = $conn->real_escape_string($estante);
$prateleira = $conn->real_escape_string($prateleira);
$edicao = $conn->real_escape_string($edicao);
$quantidade = $conn->real_escape_string($quantidade);

// Validação do ISSN
if (!preg_match('/^\d{4}-\d{3}[\dxX]$/', $issn)) {
    $_SESSION['msg'] = "ISSN inválido.";
    header("Location: editarJornal.php?codigo=" . urlencode($codigo));
    exit;
}

// Verifica se já existe outro registro com o mesmo ISSN
$queryIssn = "SELECT COUNT(*) FROM tbjornal_revista WHERE issn = ? AND codigo != ?";
$stmtIssn = $conn->prepare($queryIssn);
$stmtIssn->bind_param("si", $issn, $codigo);
$stmtIssn->execute();
$stmtIssn->bind_result($issnExists);
$stmtIssn->fetch();
$stmtIssn->close();

if ($issnExists > 0) {
    $_SESSION['msg'] = "Já existe um registro com este ISSN.";
    header("Location: editarJornal.php?codigo=" . urlencode($codigo));
    exit;
}

// Atualiza os dados no banco de dados
try {
    $conn->begin_transaction();

    $sql = "UPDATE tbjornal_revista SET 
                classe = ?, 
                titulo = ?, 
                data_publicacao = ?, 
                editora = ?, 
                categoria = ?, 
                issn = ?, 
                data_adicao = ?, 
                estante = ?, 
                prateleira = ?, 
                edicao = ?, 
                quantidade = ? 
            WHERE codigo = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta SQL: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssssi",
        $classe,
        $titulo,
        $data_publicacao,
        $editora,
        $categoria,
        $issn,
        $data_adicao,
        $estante,
        $prateleira,
        $edicao,
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
        header("Location: editarJornal.php?codigo=" . urlencode($codigo));
        exit;
    } else {
        throw new Exception("Erro ao atualizar o registro: " . $stmt->error);
    }
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['msg'] = "Erro ao atualizar registro: " . $e->getMessage();
    header("Location: editarJornal.php?codigo=" . urlencode($codigo));
    exit;
} finally {
    $stmt->close();
    $conn->close();
}
?>
