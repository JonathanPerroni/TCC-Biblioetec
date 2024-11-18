<?php
session_start();
include("../../../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');


// Função para validar ISBN
function validarIsbn($isbn) {
    if (preg_match('/^\d{10}(\d{3})?$/', $isbn)) {
        return true;
    } else {
        return "ISBN inválido.";
    }
}

// Captura o código (chave primária) do registro que será atualizado
if (isset($_POST['codigo']) && !empty($_POST['codigo'])) {
    $codigo = $_POST['codigo'];
} else {
    echo "Código não informado.";
    exit;
}

// Captura e escapa os outros dados do formulário
$titulo = $_POST['titulo']; /*ok */
$isbn = $_POST['isbn']; /*ok */
$autor = $_POST['autor']; /*ok */
$editora = $_POST['editora'];/*ok */
$ano_publicacao = $_POST['ano_publicacao']; /*ok */
$quantidade = $_POST['quantidade']; /*ok */
$classe = $_POST['classe']; /*ok */
$genero = $_POST['genero']; /*ok */
$edicao = $_POST['edicao']; /*ok */
$num_paginas = $_POST['num_paginas']; /*ok */
$idioma = $_POST['idioma']; /*ok */
$estante = $_POST['estante']; /*ok */
$prateleira = $_POST['prateleira']; /*ok */



// Protege contra SQL Injection
$titulo = $conn->real_escape_string($_POST['titulo']);
$isbn = $conn->real_escape_string($_POST['isbn']);
$autor = $conn->real_escape_string($_POST['autor']);
$editora = $conn->real_escape_string($_POST['editora']);
$ano_publicacao = $conn->real_escape_string($_POST['ano_publicacao']);
$quantidade = $conn->real_escape_string($_POST['quantidade']);
$classe = $conn->real_escape_string($_POST['classe']);
$genero = $conn->real_escape_string($_POST['genero']);
$edicao = $conn->real_escape_string($_POST['edicao']);
$num_paginas = $conn->real_escape_string($_POST['num_paginas']);
$idioma = $conn->real_escape_string($_POST['idioma']);
$estante = $conn->real_escape_string($_POST['estante']);
$prateleira = $conn->real_escape_string($_POST['prateleira']);

// Variável para armazenar mensagens de erro
$_SESSION['msg'] = '';


// Validação do CPF
$isbnErro = validarIsbn($isbn);
if ($isbnErro !== true) {
    $_SESSION['msg'] .= $isbnErro . "<br>";
} else {
    // Verifica se o CPF já está cadastrado no banco
    $queryIsbn = "SELECT COUNT(*) FROM tblivros WHERE isbn = ? AND codigo != ?";
    $stmtIsbn = $conn->prepare($queryIsbn);
    $stmtIsbn->bind_param("si", $isbn, $codigo);
    
    $stmtIsbn->execute();
    $stmtIsbn->bind_result($isbnExists);
    $stmtIsbn->fetch();
    $stmtIsbn->close();
    

    if ($isbnExists > 0) {
        $_SESSION['msg'] .= "Já existe um livro cadastrado com esse Codigo ISBN.<br>";
    }
}

// Verifica se há mensagens de erro
if (!empty($_SESSION['msg'])) {
    header("Location: editarLivro.php?codigo=" . urlencode($codigo));
    exit();
}


try {
   // Atualiza os dados no banco de dados
   $conn->begin_transaction();

        $sql = "UPDATE tblivros SET
            titulo = ?, 
            isbn = ?, 
            autor = ?, 
            editora = ?, 
            ano_publicacao = ?, 
            quantidade = ?, 
            classe = ?, 
            genero = ?, 
            num_paginas = ?, 
            idioma = ?, 
            estante = ?, 
            prateleira = ?          
            WHERE codigo = ?";

        // Tenta preparar a consulta
        $stmt = $conn->prepare($sql);

    if (!$stmt) {
    // Exibe o erro e a consulta SQL para depuração
    echo "Erro ao preparar a consulta SQL: " . $conn->error . "<br>";
    echo "Consulta SQL: " . $sql;
    exit;
}
    


   // Aqui continuamos com o bind_param e a execução se não houver erro
$stmt->bind_param(
    "ssssssssssssi", 
    $titulo, 
    $isbn, 
    $autor, 
    $editora, 
    $ano_publicacao, 
    $quantidade, 
    $classe, 
    $genero, 
    $num_paginas, 
    $idioma, 
    $estante, 
    $prateleira, 
    $codigo
);
    
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
// Executa a consulta
if ($stmt->execute()) {
    // Registro do histórico
    if (empty($_SESSION['msg'])) {
        registraHistorico($conn, "editar", $_SESSION['nome'], $nome, $acesso, date('Y-m-d H:i:s'));
    }
    $conn->commit();
    $_SESSION['sucesso'] = "Registro atualizado com sucesso!";
    header("Location: editarLivro.php?codigo=" . urlencode($codigo));
    exit();
    } else {
        // Tratar possíveis erros aqui
        throw new Exception("Erro ao atualizar o registro: " . $stmt->error);
    }
} catch (Exception $e) {
    $conn->rollback();
    echo $e->getMessage();
}


$conn->close();
?>
