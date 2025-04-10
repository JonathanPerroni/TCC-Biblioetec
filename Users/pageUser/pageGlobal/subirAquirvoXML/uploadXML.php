<?php

session_start();
include_once("../../../../conexao/conexao.php");
date_default_timezone_set('America/Sao_Paulo');

if (isset($_POST['visualizar']) && isset($_SESSION['arquivo_enviado'])) {
    $arquivo = 'xml/' . $_SESSION['arquivo_enviado'];

    if (file_exists($arquivo)) {
        try {   
            $xml = simplexml_load_file($arquivo);
            $xml->registerXPathNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');
            $rows = $xml->Worksheet->Table->Row;

            $visualizacaoHTML = "<h2>Pré-visualização dos dados</h2><table border='1'><tr>
                <th>Tombo</th><th>Aquisição</th><th>CDD</th><th>ISBN</th><th>Título</th><th>Autor</th>
                <th>Editora</th><th>Ano</th><th>Páginas</th><th>Idioma</th><th>Gênero</th><th>Quantidade</th></tr>";

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $cells = $row->Cell;

                $visualizacaoHTML .= "<tr>";
                for ($j = 0; $j < 12; $j++) { 
                    $dado = isset($cells[$j]->Data) ? (string)$cells[$j]->Data : 'N/A';
                    $visualizacaoHTML .= "<td>$dado</td>";
                }
                $visualizacaoHTML .= "</tr>";
            }
            $visualizacaoHTML .= "</table>";

        } catch (Exception $e) {
            $_SESSION['msg'] = "Erro ao visualizar o XML: " . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cadastrado_por = filter_input(INPUT_POST, 'cadastrado_por', FILTER_SANITIZE_STRING);
    $data_cadastro = date('Y-m-d H:i:s');

    if (isset($_POST['excluir'])) {
        $diretorio = 'xml/';
        $files = glob($diretorio . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        unset($_SESSION['arquivo_enviado']);
        $_SESSION['msg'] = "Todos os arquivos foram excluídos com sucesso!";
    }

    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] == 0) {
        $extensao = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
        $nome = md5(basename($_FILES['arquivo']['name']) . time()) . '.' . $extensao;
        $diretorio = 'xml/';
        $arquivoDestino = $diretorio . $nome;

        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }

        if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $arquivoDestino)) {
            $_SESSION['arquivo_enviado'] = $nome;
        } else {
            $_SESSION['msg'] = "Erro ao enviar o arquivo.";
        }
    }

    if (isset($_POST['salvar']) && isset($_SESSION['arquivo_enviado'])) {
        $arquivo = 'xml/' . $_SESSION['arquivo_enviado'];
        
        if (file_exists($arquivo)) {
            try {
                $xml = simplexml_load_file($arquivo);
                $xml->registerXPathNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');
                $rows = $xml->Worksheet->Table->Row;
                
                $_SESSION['msg'] = "";
                $conn->begin_transaction();
                
                $linhasProcessadas = 0;
                for ($i = 1; $i < count($rows); $i++) {
                    $linhasProcessadas++;
                    $row = $rows[$i];
                    $cells = $row->Cell;
                    
                    $tombo = (string)$cells[0]->Data;
                    $aquisicao = (string)$cells[1]->Data;
                    
                    // Conversão da data para o formato DATETIME
                    if (!empty($aquisicao)) {
                        $timestamp = strtotime(str_replace('/', '-', $aquisicao));
                        if ($timestamp !== false) {
                            $aquisicao = date('Y-m-d H:i:s', $timestamp);
                        } else {
                            $aquisicao = null;
                        }
                    }
                    
                    $cdd = (string)$cells[2]->Data;
                    $isbn = (string)$cells[3]->Data;
                    $titulo = (string)$cells[4]->Data;
                    $autor = (string)$cells[5]->Data;
                    $editora = (string)$cells[6]->Data;
                    $ano_publicacao = (string)$cells[7]->Data;
                    $num_paginas = (string)$cells[8]->Data;
                    $idioma = (string)$cells[9]->Data;
                    $genero = (string)$cells[10]->Data;
                    
                    // Definir quantidade como 1 caso seja vazia ou 0
                    $quantidade = !empty($cells[11]->Data) && $cells[11]->Data != "0" ? (int)$cells[11]->Data : 1;
                    
                    // Definir classe como "LIVRO"
                    $classe = "LIVRO";
                    
                    // Gerar CDD aleatório caso esteja inválido
                    if (empty($cdd) || $cdd == "N/D" || $cdd == "undefined") {
                        $cdd = substr(strtoupper(preg_replace('/[^A-Z]/', '', $titulo)), 0, 3) . rand(100, 999);
                    }
                    
                    $query = "SELECT * FROM tblivros WHERE tombo = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $tombo);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    
                    if ($resultado->num_rows == 0) {
                        $stmt->close(); // <- fecha o SELECT
                    
                        // Gerar isbn_falso único
                        $resultISBN = $conn->query("SELECT MAX(CAST(SUBSTRING(isbn_falso, 2) AS UNSIGNED)) AS ultimo FROM tblivros WHERE isbn_falso LIKE 'f%'");
                        $rowISBN = $resultISBN->fetch_assoc();
                        $ultimoNumero = $rowISBN['ultimo'] ?? 0;
                        $resultISBN->free(); // <- libera o resultado da query manual
                    
                        $novoNumero = $ultimoNumero + 1;
                        $isbn_falso = 'f' . str_pad($novoNumero, 12, '0', STR_PAD_LEFT);
                    
                        // Insert
                        $queryInsert = "INSERT INTO tblivros (tombo, data_aquisicao, cdd_cutter, isbn, isbn_falso, titulo, autor, editora, ano_publicacao, num_paginas, idioma, genero, quantidade, classe, cadastrado_por, data_cadastro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmtInsert = $conn->prepare($queryInsert);
                        $stmtInsert->bind_param("ssssssssssssssss", 
                            $tombo, $aquisicao, $cdd, $isbn, $isbn_falso, $titulo, $autor, $editora,
                            $ano_publicacao, $num_paginas, $idioma, $genero, $quantidade, $classe, $cadastrado_por, $data_cadastro
                        );
                        $stmtInsert->execute();
                        $stmtInsert->close(); // <- fecha o insert
                    } else {
                        $stmt->close(); // <- fecha mesmo que já exista
                    }

 
                }
                
                $conn->commit();
                $_SESSION['msg'] .= "Linhas processadas: $linhasProcessadas";
            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['msg'] .= "Erro: " . $e->getMessage();
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
            <div class="file-info">
                <input type="file" name="arquivo" accept=".xml" >
                <?php if (isset($_SESSION['arquivo_enviado'])): ?>
                    <p>Arquivo enviado: <?php echo $_SESSION['arquivo_enviado']; ?></p>
                <?php endif; ?>
            </div>
            <div class="button-group">
                <button type="submit" name="enviar">Enviar Arquivo</button>
                <?php if (isset($_SESSION['arquivo_enviado'])): ?>
                    <button type="submit" name="visualizar" class="visualizar">Visualizar</button>
                    <button type="submit" name="excluir" class="delete-button">Excluir Arquivo</button>
                <?php endif; ?>
            </div>
        </form>
        
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($visualizacaoHTML)): ?>
            <div class="visualizacao"><?php echo $visualizacaoHTML; ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['arquivo_enviado'])): ?>
            <form method="POST" action="uploadXML.php">
                <button class="salvar" type="submit" name="salvar">Salvar no Banco de Dados</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>