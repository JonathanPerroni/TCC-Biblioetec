<?php
session_start();
include_once 'api.php';  // Supondo que as funções de API já estão aqui

$xmlFile = 'uploaded.xml';
if (!file_exists($xmlFile)) {
    die("Arquivo XML não encontrado.");
}

$xml = simplexml_load_file($xmlFile);
if ($xml === false) {
    die("Erro ao carregar o arquivo XML.");
}

$xml->registerXPathNamespace('ss', 'urn:schemas-microsoft-com:office:spreadsheet');
$rows = $xml->xpath('//ss:Worksheet/ss:Table/ss:Row');

if (empty($rows)) {
    die("Nenhuma linha encontrada no XML.");
}

$livros = [];
$livrosCompletos = [];
$livrosComFaltando = [];
$totalLivros = 0;
$totalCompletos = 0;

for ($i = 1; $i < count($rows); $i++) {
    $cells = $rows[$i]->xpath('ss:Cell/ss:Data');
    if (count($cells) >= 2) {
        $titulo = (string) $cells[0]; // Assume que o título está na primeira célula
        $editora = (string) $cells[1]; // Assume que a editora está na segunda célula

        // Adiciona o livro ao array geral
        $livros[] = [
            'titulo' => $titulo,
            'editora' => $editora
        ];

        // Verifica se o livro tem todos os campos válidos
        if ($titulo && $editora && $titulo !== 'undefined' && $editora !== 'undefined' && 
            $titulo !== 'null' && $editora !== 'null' && 
            $titulo !== 'desconhecido' && $editora !== 'desconhecido' && 
            $titulo !== 'N/A' && $editora !== 'N/A') {
            $livrosCompletos[] = [
                'titulo' => $titulo,
                'editora' => $editora
            ];
            $totalCompletos++;
        } else {
            $livrosComFaltando[] = [
                'titulo' => $titulo,
                'editora' => $editora
            ];
        }
        $totalLivros++;
    }
}

$_SESSION['livros'] = $livros;

// Exibir as contagens
echo "<p>Total de livros: $totalLivros</p>";
echo "<p>Total de livros com todas as informações: $totalCompletos</p>";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livros - Lista</title>
</head>
<body>
    <h1>Livros com Todas as Informações</h1>
    <table border="1">
        <tr>
            <th>Título</th>
            <th>Editora</th>
        </tr>
        <?php foreach ($livrosCompletos as $livro): ?>
            <tr>
                <td><?php echo $livro['titulo']; ?></td>
                <td><?php echo $livro['editora']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h1>Livros com Informações Faltando</h1>
    <table border="1">
        <tr>
            <th>Título</th>
            <th>Editora</th>
        </tr>
        <?php foreach ($livrosComFaltando as $livro): ?>
            <tr>
                <td><?php echo $livro['titulo']; ?></td>
                <td><?php echo $livro['editora']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Botão para buscar ISBN para todos os livros -->
    <form action="buscar_isbn.php" method="POST">
        <button type="submit">Buscar ISBN para Todos</button>
    </form>
</body>
</html>
