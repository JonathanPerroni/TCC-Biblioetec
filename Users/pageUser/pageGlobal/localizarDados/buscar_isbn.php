<?php
session_start();
include_once 'api.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['livros'])) {
    $livros = $_SESSION['livros'];
    $resultados = [];

    // Inicialização das variáveis para contagem e separação
    $livrosCompletos = [];
    $livrosComFaltando = [];
    $totalLivros = 0;
    $totalCompletos = 0;

    function exibirTabela($livros) {
        if (empty($livros)) {
            echo "<p>Nenhum livro encontrado.</p>";
            return;
        }
        
        echo "<table>";
        echo "<tr>";
        foreach (array_keys($livros[0]) as $header) {
            echo "<th>" . htmlspecialchars($header) . "</th>";
        }
        echo "</tr>";
        
        foreach ($livros as $livro) {
            echo "<tr>";
            foreach ($livro as $valor) {
                echo "<td>" . htmlspecialchars($valor) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Busca</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        } 
        

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .summary {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .summary p {
            margin: 5px 0;
            font-size: 16px;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }

        .tabs a {
            text-decoration: none;
            color: #495057;
            padding: 8px 16px;
            border-radius: 4px;
            background-color: #e9ecef;
            transition: all 0.3s ease;
        }

        .tabs a:hover {
            background-color: #dee2e6;
        }

        .tabs a.active {
            background-color: #007bff;
            color: #fff;
        }

        .tab-content  {width: 100%;
                overflow-x: auto; /* Apenas rolagem horizontal */
                -webkit-overflow-scrolling: touch; /* Para melhorar a rolagem em dispositivos móveis */
            }

            table {
                width: 100%;
                min-width: 900px; /* Ajuste conforme o conteúdo da tabela */
                border-collapse: collapse;
                background-color: #fff;
            }

            th, td {
                padding: 12px;
                text-align: left;
                border: 1px solid #dee2e6;
            }

            th {
                background-color: #f8f9fa;
                font-weight: bold;
                color: #495057;
            }

            tr:nth-child(even) {
                background-color: #f8f9fa;
            }

            tr:hover {
                background-color: #f2f2f2;
            }

        .export-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .export-button:hover {
            background-color: #218838;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #5a6268;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        foreach ($livros as $livro) {
            // Mantém os campos originais do XML
            $tombo = isset($livro['TOMBO']) ? $livro['TOMBO'] : '';
            $aquisicao = isset($livro['AQUISIÇÃO']) ? $livro['AQUISIÇÃO'] : '';
            $cddCutter = isset($livro['CDD/CUTTER']) ? $livro['CDD/CUTTER'] : '';

            if (isset($livro['TITULO']) && isset($livro['EDITORA'])) {
                $titulo = $livro['TITULO'];
                $editora = $livro['EDITORA'];

                // Buscar informações nas APIs
                $infoGoogle = buscarInfoLivroGoogleBooks($titulo, $editora, $livros);
               // $infoOpenLibrary = buscarInfoLivroOpenLibrary($titulo, $editora, $livros);
                $infoISBNdb = buscarInfoLivroISBNdb($titulo, $editora, $livros);
                $infoAmazon = buscarInfoLivroAmazon($titulo);

                // Combinar as informações das APIs
                $isbn = isset($infoGoogle['isbn']) ? $infoGoogle['isbn'] : 
                       (//isset($infoOpenLibrary['isbn']) ? $infoOpenLibrary['isbn'] : 
                       (isset($infoISBNdb['isbn']) ? $infoISBNdb['isbn'] : 
                       (isset($infoAmazon['isbn']) ? $infoAmazon['isbn'] : 'N/A')));

                $autor = isset($infoGoogle['autor']) ? $infoGoogle['autor'] : 
                        (//isset($infoOpenLibrary['autor']) ? $infoOpenLibrary['autor'] : 
                        (isset($infoISBNdb['autor']) ? $infoISBNdb['autor'] : 
                        (isset($infoAmazon['autor']) ? $infoAmazon['autor'] : 'Desconhecido')));

                $anoPublicacao = isset($infoGoogle['ano_publicacao']) ? $infoGoogle['ano_publicacao'] : 
                               (//isset($infoOpenLibrary['ano_publicacao']) ? $infoOpenLibrary['ano_publicacao'] : 
                               (isset($infoISBNdb['ano_publicacao']) ? $infoISBNdb['ano_publicacao'] : 
                               (isset($infoAmazon['ano_publicacao']) ? $infoAmazon['ano_publicacao'] : 'Desconhecido')));

                $numPaginas = isset($infoGoogle['num_paginas']) ? $infoGoogle['num_paginas'] : 
                            (//isset($infoOpenLibrary['num_paginas']) ? $infoOpenLibrary['num_paginas'] : 
                            (isset($infoISBNdb['num_paginas']) ? $infoISBNdb['num_paginas'] : 
                            (isset($infoAmazon['num_paginas']) ? $infoAmazon['num_paginas'] : 'Desconhecido')));

                $idioma = isset($infoGoogle['idioma']) ? $infoGoogle['idioma'] : 
                         (//isset($infoOpenLibrary['idioma']) ? $infoOpenLibrary['idioma'] : 
                         (isset($infoISBNdb['idioma']) ? $infoISBNdb['idioma'] : 
                         (isset($infoAmazon['idioma']) ? $infoAmazon['idioma'] : 'Desconhecido')));

                $genero = isset($infoGoogle['genero']) ? $infoGoogle['genero'] : 
                         (//isset($infoOpenLibrary['genero']) ? $infoOpenLibrary['genero'] : 
                         (isset($infoISBNdb['genero']) ? $infoISBNdb['genero'] : 
                         (isset($infoAmazon['genero']) ? $infoAmazon['genero'] : 'Desconhecido')));

                // Criar o resultado mantendo os campos originais
                $livroResult = [
                    'TOMBO' => $tombo,
                    'AQUISIÇÃO' => $aquisicao,
                    'CDD/CUTTER' => $cddCutter,
                    'ISBN' => $isbn,
                    'TITULO' => $titulo,
                    'AUTOR' => $autor,
                    'EDITORA' => $editora,
                    'ANO DE PUBLICAÇÃO' => $anoPublicacao,
                    'NUMERO DE PAGINAS' => $numPaginas,
                    'IDIOMA' => $idioma,
                    'GENERO' => $genero
                ];

                $totalLivros++;

                if ($titulo && $editora && $isbn !== 'N/A' && $autor !== 'Desconhecido' && 
                    $anoPublicacao !== 'Desconhecido' && $numPaginas !== 'Desconhecido' && 
                    $idioma !== 'Desconhecido' && $genero !== 'Desconhecido') {
                    $livrosCompletos[] = $livroResult;
                    $totalCompletos++;
                } else {
                    $livrosComFaltando[] = $livroResult;
                }

                $resultados[] = $livroResult;
            }
        }

        // Atualizar a sessão com os novos dados
        $_SESSION['livros'] = $resultados;
        ?>

        <h1>Resultados da Busca</h1>
        
        <div class="summary">
            <p><strong>Total de livros:</strong> <?php echo $totalLivros; ?></p>
            <p><strong>Total de livros com todas as informações:</strong> <?php echo $totalCompletos; ?></p>
        </div>

        <div class="tabs">
            <a href="#todos" class="active" onclick="showTab(event, 'todos')">Todos os Dados</a>
            <a href="#completos" onclick="showTab(event, 'completos')">Livros Completos</a>
            <a href="#faltando" onclick="showTab(event, 'faltando')">Livros Incompletos</a>
        </div>

        <div id="todos" class="tab-content">
            <h2>Todos os Livros</h2>
            <?php exibirTabela($resultados); ?>
        </div>

        <div id="completos" class="tab-content" style="display: none;">
            <h2>Livros com todas as informações</h2>
            <?php exibirTabela($livrosCompletos); ?>
        </div>

        <div id="faltando" class="tab-content" style="display: none;">
            <h2>Livros com informações faltando</h2>
            <?php exibirTabela($livrosComFaltando); ?>
        </div>

        <div class="button-container">
            <a href="index.php" class="back-button">Voltar</a>
            <a href="export.php" class="export-button">Exportar para XML</a>
        </div>
    </div>

    <script>
        function showTab(event, tabId) {
            event.preventDefault();
            
            // Ocultar todos os conteúdos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });

            // Remover classe active de todas as abas
            document.querySelectorAll('.tabs a').forEach(tab => {
                tab.classList.remove('active');
            });

            // Mostrar o conteúdo selecionado
            document.getElementById(tabId).style.display = 'block';
            
            // Adicionar classe active na aba selecionada
            event.target.classList.add('active');
        }
    </script>
</body>
</html>

