<?php
session_start();
require '../../../../../conexao/conexao.php';
include_once('../../seguranca.php');

$ra = $_GET['ra'] ?? '';

if(empty($ra)) {
    $_SESSION['msg'] = "RA do aluno não fornecido";
    $_SESSION['msg_type'] = "danger";
    header('Location: pesquisa_aluno.php');
    exit;
}

try {
    // Carrega dados do aluno
    $sql = "SELECT codigo, nome, ra_aluno, situacao, nome_curso, tipo_ensino, status 
            FROM tbalunos 
            WHERE ra_aluno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $ra);
    $stmt->execute();
    $result = $stmt->get_result();
    $aluno = $result->fetch_assoc();

    if(!$aluno) {
        $_SESSION['msg'] = "Aluno não encontrado";
        $_SESSION['msg_type'] = "danger";
        header('Location: pesquisa_aluno.php');
        exit;
    }

    // Validações
    if($aluno['status'] != 1) {
        $_SESSION['msg'] = "Aluno bloqueado. Não pode realizar empréstimos";
        $_SESSION['msg_type'] = "danger";
        header('Location: pesquisa_aluno.php');
        exit;
    }

    $sql_emprestimos = "SELECT COUNT(*) as total 
                        FROM tbemprestimos 
                        WHERE ra_aluno = ? AND data_devolucao_efetiva IS NULL";
    $stmt = $conn->prepare($sql_emprestimos);
    $stmt->bind_param("s", $ra);
    $stmt->execute();
    $result = $stmt->get_result();
    $emprestimo = $result->fetch_assoc();

    if($emprestimo['total'] > 0) {
        $_SESSION['msg'] = "Aluno possui empréstimos pendentes";
        $_SESSION['msg_type'] = "danger";
        header('Location: pesquisa_aluno.php');
        exit;
    }

    // Tudo validado - armazena na sessão
    $_SESSION['aluno_emprestimo'] = $aluno;
    $_SESSION['bibliotecario_emprestimo'] = $_SESSION['bibliotecario'];
    
    header('Location: ../../livro/pesquisa_livro.php');
    exit;

} catch(Exception $e) {
    $_SESSION['msg'] = "Erro: " . $e->getMessage();
    $_SESSION['msg_type'] = "danger";
    header('Location: pesquisa_aluno.php');
    exit;
}
?>