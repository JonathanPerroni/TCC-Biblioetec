<?php
session_start();
include_once("../../../../conexao/conexao.php"); // Para conexão com o banco de dados
date_default_timezone_set('America/Sao_Paulo');

$visualizacaoHTML = ""; // Variável para armazenar a visualização do XML

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $cadastrado_por = filter_input(INPUT_POST, 'cadastrado_por', FILTER_SANITIZE_STRING);
    $data_cadastro = filter_input(INPUT_POST, 'data_cadastro', FILTER_SANITIZE_STRING);
    // Captura o usuário logado
  //  $cadastrado_por = $_SESSION['nome']; // Presumindo que o nome do usuário logado está na sessão
    // Captura a data e hora do cadastro
    $data_cadastro = date('Y-m-d H:i:s'); // Formato padrão do MySQL para DATETIME


   // Lógica para excluir todos os arquivos da pasta 'xml'
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
        
        // Verifica se o arquivo existe
        if (file_exists($arquivo)) {
            // Carrega o XML
            $xml = simplexml_load_file($arquivo, 'SimpleXMLElement', LIBXML_NOCDATA);
            $namespaces = $xml->getNamespaces(true);
            if (isset($namespaces['ss'])) {
                $xml->registerXPathNamespace('ss', $namespaces['ss']);
            }
            
            $rows = $xml->xpath('//ss:Worksheet/ss:Table/ss:Row');
            
            $_SESSION['msg'] = ""; // Inicializa a mensagem para não acumular mensagens anteriores

            foreach ($rows as $row) {
                $cells = $row->xpath('./ss:Cell/ss:Data');
                $dados = [];
                foreach ($cells as $cell) {
                    $dados[] = (string)$cell;
                }
            
                // Verifica se há células suficientes antes de tentar acessar os dados
                if (count($dados) >= 15) { // 15 é o número total de campos esperados
                    $isbn = $dados[6];        // ISBN
                    $nome_escola = $dados[0];   // Nome da Escola
                    $titulo = $dados[2];        // Título (ajuste o índice conforme necessário)
            
                    // Consulta para verificar duplicidade
                    $query = "SELECT * FROM tblivros WHERE isbn = ? AND nome_escola = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $isbn, $nome_escola);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
            
                    if ($resultado->num_rows > 0) {
                        $_SESSION['msg'] .= "Livro com ISBN: $isbn - Título: $titulo da Nome Escola $nome_escola já foi cadastrado.<br>";
                    } else {
                        // Se o livro não existe, insere no banco
                        $queryInsert = "INSERT INTO tblivros (nome_escola, classe, titulo, autor, editora, ano_publicacao, isbn, genero, num_paginas, idioma, data_adicao, estante, prateleira, edicao, quantidade) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmtInsert = $conn->prepare($queryInsert);
                        $stmtInsert->bind_param("sssssssssssssss", 
                            $dados[0], $dados[1], $dados[2], $dados[3], $dados[4], $dados[5],
                            $dados[6], $dados[7], $dados[8], $dados[9], $dados[10], $dados[11],
                            $dados[12], $dados[13], $dados[14]
                        );
                        $stmtInsert->execute();
                        $_SESSION['msg'] .= "Livro cadastrado com sucesso!<br>";
                    }
                } else {
                    $_SESSION['msg'] .= "Dados incompletos para o livro, linha ignorada.<br>";
                }
            }
        } else {
            $_SESSION['msg'] = "Arquivo não encontrado.";
        }
    }
    
    // Lógica para Visualizar o Arquivo XML
    if (isset($_POST['visualizar']) && isset($_SESSION['arquivo_enviado'])) {
        $arquivo = 'xml/' . $_SESSION['arquivo_enviado'];
        if (file_exists($arquivo)) {
            $xml = simplexml_load_file($arquivo, 'SimpleXMLElement', LIBXML_NOCDATA);
            $namespaces = $xml->getNamespaces(true);
            if (isset($namespaces['ss'])) {
                $xml->registerXPathNamespace('ss', $namespaces['ss']);
            }
            $rows = $xml->xpath('//ss:Worksheet/ss:Table/ss:Row');
            
            $visualizacaoHTML = "<h2>Conteúdo do Arquivo XML</h2>";
            $visualizacaoHTML .= "<table border='1' cellpadding='5' cellspacing='0' style='margin: 0 auto;'>";
            $visualizacaoHTML .= "<tr>
                                    <th>Nome Escola</th>
                                    <th>ISBN</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Quantidade</th>
                                  </tr>";
            foreach ($rows as $row) {
                $cells = $row->xpath('./ss:Cell/ss:Data');
                if (!$cells) continue;
                $dados = [];
                foreach ($cells as $cell) {
                    $dados[] = (string)$cell;
                }
                // Verifica se há células suficientes (15 campos esperados)
                if (count($dados) >= 15) {
                    $nome_escola = $dados[0];  // Nome da Escola
                    $titulo       = $dados[2];  // Título
                    $autor        = $dados[3];  // Autor
                    $isbn         = $dados[6];  // ISBN
                    $quantidade   = $dados[14]; // Quantidade
    
                    $visualizacaoHTML .= "<tr>";
                    $visualizacaoHTML .= "<td>" . htmlspecialchars($nome_escola) . "</td>";
                    $visualizacaoHTML .= "<td>" . htmlspecialchars($isbn) . "</td>";
                    $visualizacaoHTML .= "<td>" . htmlspecialchars($titulo) . "</td>";
                    $visualizacaoHTML .= "<td>" . htmlspecialchars($autor) . "</td>";
                    $visualizacaoHTML .= "<td>" . htmlspecialchars($quantidade) . "</td>";
                    $visualizacaoHTML .= "</tr>";
                }
            }
            $visualizacaoHTML .= "</table>";
        } else {
            $_SESSION['msg'] = "Arquivo para visualização não encontrado.";
        }
    }
/*
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

    if ($stmt->execute()) {
        if (empty($_SESSION['msg'])) {
            registraHistorico($conn, "cadastrar", $_SESSION['nome'], $nome, $acesso, $data_cadastro);
        }
        $conn->commit();
        $_SESSION['sucesso'] = "Registro atualizado com sucesso!";
        // Redireciona para a página de edição
        header("Location: cadastrar_admin.php");
        exit();
    } else {
        // Tratar possíveis erros aqui
        throw new Exception("Erro ao atualizar o registro: " . $stmt->error);
    }
   */ 
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
        
        <!-- Formulário para upload e exclusão -->
        <form action="uploadXML.php" method="POST" enctype="multipart/form-data">   
            <div class="file-info">
                <input type="file" name="arquivo" accept=".xml" >
                <?php if (isset($_SESSION['arquivo_enviado'])): ?>
                    <p>Arquivo enviado: <?php echo $_SESSION['arquivo_enviado']; ?></p>
                <?php endif; ?>
            </div>
            <div class="button-group">
                <button type="submit" name="enviar">Enviar Arquivo</button>
                 <!-- Botão para visualizar o conteúdo do XML -->
                 <?php if (isset($_SESSION['arquivo_enviado'])): ?>
                        <form method="POST" action="uploadXML.php">
                            <button class="visualizar" type="submit" name="visualizar">Visualizar</button>
                        </form>
                    <?php endif; ?>

                <?php if (isset($_SESSION['arquivo_enviado'])): ?>
                    <button type="submit" name="excluir" class="delete-button">Excluir Arquivo</button>
                <?php endif; ?>

                 

            </div>
        </form>
        
      
        
       
        
        <!-- Exibição de mensagens -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="msg"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
        <?php endif; ?>
        
        <!-- Exibição da visualização do XML, se existir -->
        <?php if (!empty($visualizacaoHTML)): ?>
            <div class="visualizacao"><?php echo $visualizacaoHTML; ?></div>
        <?php endif; ?>

         <!-- Botão para salvar os dados no banco -->
         <?php if (isset($_SESSION['arquivo_enviado'])): ?>
            <form method="POST" action="uploadXML.php">
                <button class="salvar" type="submit" name="salvar">Salvar no Banco de Dados</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
