<?php
session_start();

if (!isset($_SESSION['livros']) || empty($_SESSION['livros'])) {
    header('Location: index.php');
    exit;
}

// Criar o XML
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"></Workbook>');

// Criar worksheet
$worksheet = $xml->addChild('Worksheet');
$worksheet->addAttribute('Name', 'Livros');

// Criar tabela
$table = $worksheet->addChild('Table');

// Adicionar cabeçalhos
$headerRow = $table->addChild('Row');
foreach (array_keys($_SESSION['livros'][0]) as $header) {
    $cell = $headerRow->addChild('Cell');
    $cell->addChild('Data', htmlspecialchars($header));
}

// Adicionar dados
foreach ($_SESSION['livros'] as $livro) {
    $row = $table->addChild('Row');
    foreach ($livro as $valor) {
        $cell = $row->addChild('Cell');
        $cell->addChild('Data', htmlspecialchars($valor));
    }
}

// Configurar headers para download
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="biblioteca_export_' . date('Y-m-d_H-i-s') . '.xml"');
header('Cache-Control: max-age=0');

// Outputar XML
echo $xml->asXML();
exit;
?>