<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se o arquivo foi enviado
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
            echo "Arquivo enviado com sucesso!";
        } else {
            echo "Erro ao enviar o arquivo.";
        }
    } else {
        echo "Nenhum arquivo enviado ou houve um erro no envio.";
    }
}
?>
