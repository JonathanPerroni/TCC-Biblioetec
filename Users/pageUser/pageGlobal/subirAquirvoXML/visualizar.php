<?php
session_start();
if (isset($_SESSION['arquivo_enviado'])) {
    $arquivo = 'xml/' . $_SESSION['arquivo_enviado'];
    if (file_exists($arquivo)) {
        try {
            $xml = simplexml_load_file($arquivo);
            $rows = $xml->Worksheet->Table->Row;

            // Array para armazenar isbn_falso por combinação titulo+editora
            $isbnFalsoMap = [];
            $contadorISBN = 1;

            $html = "<h2>Pré-visualização dos dados</h2><table border='1'><tr>
                <th>Tombo</th><th>Aquisição</th><th>CDD</th><th>ISBN</th><th>Título</th><th>Autor</th>
                <th>Editora</th><th>Ano</th><th>Páginas</th><th>Idioma</th><th>Gênero</th><th>Quantidade</th><th>ISBN Falso</th></tr>";

            for ($i = 1; $i < count($rows); $i++) {
                $cells = $rows[$i]->Cell;

                // Pega título e editora para gerar isbn_falso
                $titulo = isset($cells[4]->Data) ? (string)$cells[4]->Data : '';
                $editora = isset($cells[6]->Data) ? (string)$cells[6]->Data : '';

                $key = strtolower(trim($titulo)) . '|' . strtolower(trim($editora));

                if (isset($isbnFalsoMap[$key])) {
                    $isbn_falso = $isbnFalsoMap[$key];
                } else {
                    // Gera novo isbn_falso no formato f + 12 dígitos zerados incrementais
                    $isbn_falso = 'f' . str_pad($contadorISBN, 12, '0', STR_PAD_LEFT);
                    $isbnFalsoMap[$key] = $isbn_falso;
                    $contadorISBN++;
                }

                $html .= "<tr>";
                for ($j = 0; $j < 12; $j++) {
                    $dado = isset($cells[$j]->Data) ? (string)$cells[$j]->Data : 'N/A';
                    $html .= "<td>" . htmlspecialchars($dado) . "</td>";
                }
                // Adiciona coluna isbn_falso
                $html .= "<td>" . htmlspecialchars($isbn_falso) . "</td>";

                $html .= "</tr>";
            }

            $html .= "</table>";
            $_SESSION['visualizacao'] = $html;
        } catch (Exception $e) {
            $_SESSION['msg'] = "Erro ao visualizar o XML: " . $e->getMessage();
        }
    }
}
header("Location: index.php");
exit;
