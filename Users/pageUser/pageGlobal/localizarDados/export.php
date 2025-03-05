<?php
session_start();

// Verificar se a sessão contém os resultados
if (isset($_SESSION['resultados']) && !empty($_SESSION['resultados'])) {
    $livros = $_SESSION['resultados'];

    // Definir os cabeçalhos para download do arquivo XML
    header('Content-Type: text/xml');
    header('Content-Disposition: attachment; filename="livros.xml"');

    // Criar o objeto DOMDocument
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;  // Formatar o XML para ficar legível

    // Criar o nó raiz do XML
    $root = $xml->createElement('livros');
    $xml->appendChild($root);

    // Adicionar cada livro como um nó filho
    foreach ($livros as $livro) {
        $livroNode = $xml->createElement('livro');

        // Adicionar as propriedades de cada livro
        foreach ($livro as $chave => $valor) {
            $element = $xml->createElement($chave, htmlspecialchars($valor));  // Usar htmlspecialchars para evitar problemas com caracteres especiais
            $livroNode->appendChild($element);
        }

        $root->appendChild($livroNode);
    }

    // Exibir o XML
    echo $xml->saveXML();

    exit;
} else {
    // Caso não tenha dados para exportar
    echo "Nenhum dado encontrado para exportar.";
}
?>
