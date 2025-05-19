<?php
 
 if (isset($dados['confirmeAluno'])) {
    if (empty($dados['codigo_aluno'])) {
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Necessário selecionar um aluno!</p>";
    } else {
        $codigoAluno = $dados['codigo_aluno'];

        // Buscar dados do aluno
        $query = "SELECT * FROM tbalunos WHERE codigo = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $codigoAluno);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Aluno não encontrado.</p>";
        } else {
            $aluno = $res->fetch_assoc();

            if ($aluno['status'] == 0) {
                $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Aluno está bloqueado!</p>";
            } else {
                // Verificar pendência de devolução
                $queryPendencia = "SELECT COUNT(*) as pendencias FROM tbemprestimos WHERE ra_aluno = ? AND (data_devolucao_efetiva IS NULL OR data_devolucao_efetiva = '')";
                $stmtPendencia = $conn->prepare($queryPendencia);
                $stmtPendencia->bind_param("s", $aluno['ra_aluno']);
                $stmtPendencia->execute();
                $resPendencia = $stmtPendencia->get_result();
                $pendencias = $resPendencia->fetch_assoc();

                if ($pendencias['pendencias'] > 0) {
                    $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Aluno possui empréstimos pendentes!</p>";
                } else {
                    $_SESSION['aluno'] = $aluno;
                    $_SESSION['msg'] = "<p style='color: #084;'>Aluno aprovado, para o empréstimo!</p>";
                    $_SESSION['etapa'] = 2;
                }
            }
        }
    }
}

    
if(isset($dados['confirmelivro'])){
    //verifica se os campos estão preenchidos
    if(empty($dados['codigos_livros'])){
        //salva a mensagem na variavel e depois onde colocarmos ela sera exibida
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Necessário selecionar um livro!</p>";

    }else{
        $_SESSION['msg'] = "<p style='color: #084;'>livros aprovado, para o emprestimo!</p>";
        //a proxima etapa da sessão 2
        $_SESSION['etapa'] = 3;           
        
    }
}   

if (isset($dados['confirmarEmprestimo'])) {
    // Recupera o número que veio do formulário
    $nEmprestimo = $dados['nEmprestimo'] ?? '';

    // Limpa a sessão antes de redirecionar
    unset($_SESSION['livros'], $_SESSION['aluno'], $_SESSION['etapa']);
    
    // Redireciona para o recibo de empréstimo
    header("Location: recibo_emprestimo.php?n=" . urlencode($nEmprestimo));
    exit;
}


?>