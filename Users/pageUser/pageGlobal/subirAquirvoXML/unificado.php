<?php
session_start();
include_once("../../../../conexao/conexao.php"); // Para conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lógica para Upload do Arquivo
    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0) {
        $extensao = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
        $nome = md5(basename($_FILES['arquivo']['name']) . time()) . '.' . $extensao;
        $diretorio = 'xml/';
        $arquivoDestino = $diretorio . $nome;

        // Verifica se a pasta 'xml' existe, se não cria
        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }

        // Move o arquivo para o diretório 'xml'
        if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $arquivoDestino)) {
            $_SESSION['arquivo_enviado'] = $nome;
        } else {
            $_SESSION['msg'] = "Erro ao enviar o arquivo.";
        }
    }

    // Lógica para Salvar Dados no Banco de Dados
    if (isset($_POST['salvar']) && isset($_SESSION['arquivo_enviado'])) {
        $arquivo = 'xml/' . $_SESSION['arquivo_enviado'];
        if (file_exists($arquivo)) {
            // Carrega o XML
            $xml = simplexml_load_file($arquivo, 'SimpleXMLElement', LIBXML_NOCDATA);
            $namespaces = $xml->getNamespaces(true);
            $xml->registerXPathNamespace('ss', $namespaces['ss']);
            
            $rows = $xml->xpath('//ss:Worksheet/ss:Table/ss:Row');
            
            foreach ($rows as $row) {
                $cells = $row->xpath('./ss:Cell/ss:Data');
                $dados = [];
                
                foreach ($cells as $cell) {
                    $dados[] = (string)$cell;
                }

                // Verifica se o livro já existe
                $isbn = $dados[6]; // Assumindo que o ISBN está na 6ª posição
                $nome_escola = $dados[0]; // Assumindo que o nome da escola está na 0ª posição
                
                // Consulta para verificar duplicidade
                $query = "SELECT * FROM tblivros WHERE isbn = ? AND nome_escola = ?";
                $stmt = $conexao->prepare($query);
                $stmt->bind_param("ss", $isbn, $nome_escola);
                $stmt->execute();
                $resultado = $stmt->get_result();
                
                if ($resultado->num_rows > 0) {
                    $_SESSION['msg'] .= "Livro com ISBN $isbn e Nome Escola $nome_escola já cadastrado.<br>";
                } else {
                    // Se o livro não existe, insere no banco
                    $queryInsert = "INSERT INTO tblivros (nome_escola, classe, titulo, autor, editora, ano_publicacao, isbn, genero, num_paginas, idioma, data_adicao, estante, prateleira, edicao, quantidade) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmtInsert = $conexao->prepare($queryInsert);
                    $stmtInsert->bind_param("sssssssssssssss", ...$dados);
                    $stmtInsert->execute();
                    
                    $_SESSION['msg'] .= "Livro cadastrado com sucesso!<br>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload e Cadastro de Livros</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Upload e Cadastro de Livros</h1>
        <form action="uploadXML.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="arquivo" accept=".xml" required>
            <button type="submit">Enviar Arquivo</button>
        </form>
        
        <?php if (isset($_SESSION['arquivo_enviado'])): ?>
            <p>Arquivo enviado: <?php echo $_SESSION['arquivo_enviado']; ?></p>
            <form method="POST" action="uploadXML.php">
                <button type="submit" name="visualizar">Visualizar</button>
            </form>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['arquivo_enviado'])): ?>
            <form method="POST" action="uploadXML.php">
                <button type="submit" name="salvar">Salvar no Banco de Dados</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
