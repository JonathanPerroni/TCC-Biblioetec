<?php
$alunoId = $_GET['alunoId'] ?? '';
$alunoNome = $_GET['alunoNome'] ?? '';
$livroId = $_GET['livroId'] ?? '';
$livroTitulo = $_GET['livroTitulo'] ?? '';
$livroIsbn = $_GET['livroIsbn'] ?? '';
$nEmprestimo = $_GET['nEmprestimo'] ?? '';
$bibliotecario = $_GET['bibliotecario'] ?? '';
$dtEmprestimo = $_GET['dtEmprestimo'] ?? '';
$dtDevolucao = $_GET['dtDevolucao'] ?? '';
?>

<label for="nEmprestimo">Nº do Emprestimo</label>
<input type="text" name="nEmprestimo" id="nEmprestimo" value="<?php echo htmlspecialchars($nEmprestimo); ?>">

<label for="bibliotecario">Bibliotecario</label>
<input type="text" name="bibliotecario" id="bibliotecario" value="<?php echo htmlspecialchars($bibliotecario); ?>">

<label for="dtEmprestimo">Data do Emprestimo</label>
<input type="text" name="dtEmprestimo" id="dtEmprestimo" value="<?php echo htmlspecialchars($dtEmprestimo); ?>">

<label for="dtDevolucao">Data da Devolução</label>
<input type="text" name="dtDevolucao" id="dtDevolucao" value="<?php echo htmlspecialchars($dtDevolucao); ?>">

<label for="nomeAluno">Nome do Aluno</label>
<input type="text" name="nomeAluno" id="nomeAluno" value="<?php echo htmlspecialchars($alunoNome); ?>">

<label for="raAluno">RA do Aluno</label>
<input type="text" name="raAluno" id="raAluno" value="<?php echo htmlspecialchars($alunoId); ?>">

<label for="nomeLivro">Nome do Livro</label>
<input type="text" name="nomeLivro" id="nomeLivro" value="<?php echo htmlspecialchars($livroTitulo); ?>">

<label for="isbn">Isbn do Livro</label>
<input type="text" name="isbn" id="isbn" value="<?php echo htmlspecialchars($livroIsbn); ?>">

<button type="button" id="btnCancelarEmprestimo" onclick="cancelarEmprestimo()">CANCELAR EMPRESTIMO</button>

<button type="button" id="btnConfitmarEmprestimo" onclick="confirmarEmprestimo()">CONFIRMAR EMPRESTIMO</button>
