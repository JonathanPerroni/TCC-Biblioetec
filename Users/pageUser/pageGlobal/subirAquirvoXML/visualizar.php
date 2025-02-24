<?php
if (isset($_GET['arquivo'])) {
    $arquivo = 'xml/' . $_GET['arquivo'];

    // Verifica se o arquivo existe
    if (file_exists($arquivo)) {
        // Carrega o XML com suporte a namespaces
        $xml = simplexml_load_file($arquivo, 'SimpleXMLElement', LIBXML_NOCDATA);
        $namespaces = $xml->getNamespaces(true);

        // Registra o namespace para o XPath
        $xml->registerXPathNamespace('ss', $namespaces['ss']);

        echo "<h1>Conteúdo do Arquivo XML</h1>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr>
                <th>Nome Escola</th>
                <th>Classe</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Editora</th>
                <th>Ano Publicação</th>
                <th>ISBN</th>
                <th>Gênero</th>
                <th>Número de Páginas</th>
                <th>Idioma</th>
                <th>Data Adição</th>
                <th>Estante</th>
                <th>Prateleira</th>
                <th>Edição</th>
                <th>Quantidade</th>
            </tr>";

        // Navega pela estrutura correta
        $rows = $xml->xpath('//ss:Worksheet/ss:Table/ss:Row');

        // Percorre as linhas encontradas
        foreach ($rows as $row) {
            echo "<tr>";
            // Para cada linha, capturar as células (Cell)
            $cells = $row->xpath('./ss:Cell/ss:Data');
            // Para cada célula, exibe o valor
            foreach ($cells as $cell) {
                echo "<td>" . (string)$cell . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Arquivo não encontrado.";
    }
} else {
    echo "Nenhum arquivo especificado.";
}
?>
