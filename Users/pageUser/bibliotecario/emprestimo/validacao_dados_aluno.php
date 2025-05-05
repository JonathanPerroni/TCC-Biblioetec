<?php
 
if(isset($dados['confirmeAluno'])){
    //verifica se os campos estão preenchidos
    if(empty($dados['codigo_aluno'])){
        //salva a mensagem na variavel e depois onde colocarmos ela sera exibida
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Nescesssário selecionar um aluno!</p>";
    }else{
        $_SESSION['msg'] = "<p style='color: #084;'>Aluno aprovado, para o emprestimo!</p>";
        //a proxima etapa da sessão 2
        $_SESSION['etapa'] = 2;
        
    }
}

    
if(isset($dados['confirmelivro'])){
    //verifica se os campos estão preenchidos
    if(empty($dados['codigos_livros'])){
        //salva a mensagem na variavel e depois onde colocarmos ela sera exibida
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Nescesssário selecionar um livro!</p>";
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