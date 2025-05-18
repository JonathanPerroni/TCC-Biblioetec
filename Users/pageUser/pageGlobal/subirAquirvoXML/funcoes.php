<?php
/**
 * Função responsável por processar um arquivo XML e inserir os dados na tabela 'tblivros' do banco de dados.
 *
 * @param string $arquivo Caminho do arquivo XML.
 * @param mysqli $conn Conexão com o banco de dados.
 * @param string $cadastrado_por Nome do responsável pelo cadastro.
 * @param string $data_cadastro Data e hora do cadastro.
 * @return int Quantidade de linhas processadas (livros analisados).
 */
function limparTexto($texto) {
    $texto = trim($texto);
    $texto = strtolower($texto);
    $texto = preg_replace('/\s+/', ' ', $texto);
    return $texto;
}

function processarXML($arquivo, $conn, $cadastrado_por, $data_cadastro) {
    // Carrega o arquivo XML
    $xml = simplexml_load_file($arquivo);

    // Acessa todas as linhas da tabela do Excel
    $rows = $xml->Worksheet->Table->Row;

    $linhas = 0; // Contador de linhas processadas

    // Começa da segunda linha (índice 1), pulando o cabeçalho
    for ($i = 1; $i < count($rows); $i++) {
        $linhas++;
        $cells = $rows[$i]->Cell;

        // Extrai os dados das células
        $tombo = (string)($cells[0]->Data ?? '');
        $aquisicao = (string)($cells[1]->Data ?? '');

        // Converte a data de aquisição para o formato Y-m-d H:i:s, se válida
        if (!empty($aquisicao)) {
            $timestamp = strtotime(str_replace('/', '-', $aquisicao));
            $aquisicao = $timestamp !== false ? date('Y-m-d H:i:s', $timestamp) : null;
        }

        $cdd      = (string)($cells[2]->Data ?? '');
        $isbn     = (string)($cells[3]->Data ?? '');
        $titulo   = (string)($cells[4]->Data ?? '');
        $autor    = (string)($cells[5]->Data ?? '');
        $editora  = (string)($cells[6]->Data ?? '');
        $ano      = (string)($cells[7]->Data ?? '');
        $paginas  = (string)($cells[8]->Data ?? '');
        $idioma   = (string)($cells[9]->Data ?? '');
        $genero   = (string)($cells[10]->Data ?? '');

        $quantidade = !empty($cells[11]->Data) && $cells[11]->Data != "0" ? (int)$cells[11]->Data : 1;
        $classe = "LIVRO";

        // Gera um código CDD/Cutter fictício se estiver vazio, inválido ou indefinido
        if (empty($cdd) || $cdd == "N/D" || $cdd == "undefined") {
            $cdd = substr(strtoupper(preg_replace('/[^A-Z]/', '', $titulo)), 0, 3) . rand(100, 999);
        }

        // Verifica se o livro já existe pelo tombo
        $stmt = $conn->prepare("SELECT * FROM tblivros WHERE tombo = ?");
        $stmt->bind_param("s", $tombo);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows == 0) {
            $res->free();
            $stmt->close();

            $conn->begin_transaction();

            try {
                $conn->query("LOCK TABLES tblivros WRITE");

                // Normaliza título e editora para comparação
                $titulo_normalizado = limparTexto($titulo);
                $editora_normalizada = limparTexto($editora);

                // Busca se já existe isbn_falso para título + editora iguais
                $stmtBusca = $conn->prepare("SELECT isbn_falso FROM tblivros WHERE LOWER(TRIM(titulo)) = ? AND LOWER(TRIM(editora)) = ? LIMIT 1");
                $stmtBusca->bind_param("ss", $titulo_normalizado, $editora_normalizada);
                $stmtBusca->execute();
                $resBusca = $stmtBusca->get_result();

                if ($resBusca->num_rows > 0) {
                    $rowBusca = $resBusca->fetch_assoc();
                    $isbn_falso = $rowBusca['isbn_falso'];
                } else {
                    // Gera novo isbn_falso
                    $resISBN = $conn->query("SELECT MAX(CAST(SUBSTRING(isbn_falso, 2) AS UNSIGNED)) AS ultimo FROM tblivros WHERE isbn_falso LIKE 'f%'");
                    $rowISBN = $resISBN->fetch_assoc();
                    $novoNumero = ($rowISBN['ultimo'] ?? 0) + 1;
                    $isbn_falso = 'f' . str_pad($novoNumero, 12, '0', STR_PAD_LEFT);
                    $resISBN->free();
                }

                $stmtBusca->close();

                // Prepara inserção
                $query = "INSERT INTO tblivros (
                    tombo, data_aquisicao, cdd_cutter, isbn, isbn_falso, titulo, autor, editora,
                    ano_publicacao, num_paginas, idioma, genero, quantidade, classe, cadastrado_por, data_cadastro
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmtInsert = $conn->prepare($query);
                $stmtInsert->bind_param("ssssssssssssssss",
                    $tombo, $aquisicao, $cdd, $isbn, $isbn_falso, $titulo, $autor,
                    $editora, $ano, $paginas, $idioma, $genero, $quantidade, $classe,
                    $cadastrado_por, $data_cadastro
                );
                $stmtInsert->execute();
                $stmtInsert->close();

                $conn->query("UNLOCK TABLES");
                $conn->commit();

            } catch (Exception $e) {
                $conn->rollback();
                $conn->query("UNLOCK TABLES");
                throw $e;
            }
        } else {
            $stmt->close();
        }
    }

    return $linhas;
}
