<?php
session_start();
include_once 'api.php';  // Inclui as funções do api.php, então não precisa declarar novamente

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['livros'])) {
    $livros = $_SESSION['livros'];
    $resultados = [];
    
    // Inicialização das variáveis para contagem e separação
    $livrosCompletos = [];
    $livrosComFaltando = [];
    $totalLivros = 0;
    $totalCompletos = 0;

    foreach ($livros as $livro) {
        $titulo = $livro['titulo'];
        $editora = $livro['editora'];

        // Buscar ISBN e outras informações nas APIs
        $infoGoogle = buscarInfoLivroGoogleBooks($titulo, $editora);
        $infoOpenLibrary = buscarInfoLivroOpenLibrary($titulo, $editora);
        $infoISBNdb = buscarInfoLivroISBNdb($titulo, $editora);
        $infoAmazon = buscarInfoLivroAmazon($titulo);

        // Extrair as informações de cada API com verificação
        $isbn = isset($infoGoogle['isbn']) ? $infoGoogle['isbn'] : (isset($infoOpenLibrary['isbn']) ? $infoOpenLibrary['isbn'] : (isset($infoISBNdb['isbn']) ? $infoISBNdb['isbn'] : (isset($infoAmazon['isbn']) ? $infoAmazon['isbn'] : 'N/A')));

        $autor = isset($infoGoogle['autor']) ? $infoGoogle['autor'] : (isset($infoOpenLibrary['autor']) ? $infoOpenLibrary['autor'] : (isset($infoISBNdb['autor']) ? $infoISBNdb['autor'] : (isset($infoAmazon['autor']) ? $infoAmazon['autor'] : 'Desconhecido')));

        $anoPublicacao = isset($infoGoogle['ano_publicacao']) ? $infoGoogle['ano_publicacao'] : (isset($infoOpenLibrary['ano_publicacao']) ? $infoOpenLibrary['ano_publicacao'] : (isset($infoISBNdb['ano_publicacao']) ? $infoISBNdb['ano_publicacao'] : (isset($infoAmazon['ano_publicacao']) ? $infoAmazon['ano_publicacao'] : 'Desconhecido')));

        $numPaginas = isset($infoGoogle['num_paginas']) ? $infoGoogle['num_paginas'] : (isset($infoOpenLibrary['num_paginas']) ? $infoOpenLibrary['num_paginas'] : (isset($infoISBNdb['num_paginas']) ? $infoISBNdb['num_paginas'] : (isset($infoAmazon['num_paginas']) ? $infoAmazon['num_paginas'] : 'Desconhecido')));

        $idioma = isset($infoGoogle['idioma']) ? $infoGoogle['idioma'] : (isset($infoOpenLibrary['idioma']) ? $infoOpenLibrary['idioma'] : (isset($infoISBNdb['idioma']) ? $infoISBNdb['idioma'] : (isset($infoAmazon['idioma']) ? $infoAmazon['idioma'] : 'Desconhecido')));

        $genero = isset($infoGoogle['genero']) ? $infoGoogle['genero'] : (isset($infoOpenLibrary['genero']) ? $infoOpenLibrary['genero'] : (isset($infoISBNdb['genero']) ? $infoISBNdb['genero'] : (isset($infoAmazon['genero']) ? $infoAmazon['genero'] : 'Desconhecido')));

        // Armazenar os resultados
        $livroResult = [
            'titulo' => $titulo,
            'editora' => $editora,
            'isbn' => $isbn,
            'autor' => $autor,
            'ano_publicacao' => $anoPublicacao,
            'num_paginas' => $numPaginas,
            'idioma' => $idioma,
            'genero' => $genero
        ];

        // Incrementa a contagem total de livros
        $totalLivros++;

        // Verifica se o livro tem todas as informações preenchidas
        if ($titulo && $editora && $isbn !== 'N/A' && $autor !== 'Desconhecido' && $anoPublicacao !== 'Desconhecido' && $numPaginas !== 'Desconhecido' && $idioma !== 'Desconhecido' && $genero !== 'Desconhecido') {
            $livrosCompletos[] = $livroResult;
            $totalCompletos++;
        } else {
            $livrosComFaltando[] = $livroResult;
        }

        $resultados[] = $livroResult;
    }

    // Exibir os resultados para o usuário
    echo "<h1>Resultados da Busca</h1>";
    echo "<p>Total de livros: $totalLivros</p>";
    echo "<p>Total de livros com todas as informações: $totalCompletos</p>";
    echo "<table border='1'>
            <tr>
                <th>ISBN</th>
                <th>Título</th>
                <th>Editora</th>
                <th>Autor</th>
                <th>Ano de Publicação</th>
                <th>Número de Páginas</th>
                <th>Idioma</th>
                <th>Gênero</th>
            </tr>";

    foreach ($resultados as $livro) {
        echo "<tr>
                <td>" . htmlspecialchars($livro['isbn']) . "</td>
                <td>" . htmlspecialchars($livro['titulo']) . "</td>
                <td>" . htmlspecialchars($livro['editora']) . "</td>
                <td>" . htmlspecialchars($livro['autor']) . "</td>
                <td>" . htmlspecialchars($livro['ano_publicacao']) . "</td>
                <td>" . htmlspecialchars($livro['num_paginas']) . "</td>
                <td>" . htmlspecialchars($livro['idioma']) . "</td>
                <td>" . htmlspecialchars($livro['genero']) . "</td>
              </tr>";
    }

    echo "</table>";

    // Exibir os livros completos
    echo "<h2>Livros com todas as informações</h2>";
    echo "<table border='1'>
            <tr>
                <th>ISBN</th>
                <th>Título</th>
                <th>Editora</th>
                <th>Autor</th>
                <th>Ano de Publicação</th>
                <th>Número de Páginas</th>
                <th>Idioma</th>
                <th>Gênero</th>
            </tr>";
    foreach ($livrosCompletos as $livro) {
        echo "<tr>
                <td>" . htmlspecialchars($livro['isbn']) . "</td>
                <td>" . htmlspecialchars($livro['titulo']) . "</td>
                <td>" . htmlspecialchars($livro['editora']) . "</td>
                <td>" . htmlspecialchars($livro['autor']) . "</td>
                <td>" . htmlspecialchars($livro['ano_publicacao']) . "</td>
                <td>" . htmlspecialchars($livro['num_paginas']) . "</td>
                <td>" . htmlspecialchars($livro['idioma']) . "</td>
                <td>" . htmlspecialchars($livro['genero']) . "</td>
              </tr>";
    }
    echo "</table>";

    // Exibir os livros com informações faltando
    echo "<h2>Livros com informações faltando</h2>";
    echo "<table border='1'>
            <tr>
                <th>ISBN</th>
                <th>Título</th>
                <th>Editora</th>
                <th>Autor</th>
                <th>Ano de Publicação</th>
                <th>Número de Páginas</th>
                <th>Idioma</th>
                <th>Gênero</th>
            </tr>";
    foreach ($livrosComFaltando as $livro) {
        echo "<tr>
                <td>" . htmlspecialchars($livro['isbn']) . "</td>
                <td>" . htmlspecialchars($livro['titulo']) . "</td>
                <td>" . htmlspecialchars($livro['editora']) . "</td>
                <td>" . htmlspecialchars($livro['autor']) . "</td>
                <td>" . htmlspecialchars($livro['ano_publicacao']) . "</td>
                <td>" . htmlspecialchars($livro['num_paginas']) . "</td>
                <td>" . htmlspecialchars($livro['idioma']) . "</td>
                <td>" . htmlspecialchars($livro['genero']) . "</td>
              </tr>";
    }
    echo "</table>";

    // Botão para exportar os resultados
    echo '<a href="export.php"><button>Exportar para XML</button></a>';
}
?>
