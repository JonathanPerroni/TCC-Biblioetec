<?php
function buscarInfoLivroGoogleBooks($titulo, $editora) {
    $url = "https://www.googleapis.com/books/v1/volumes?q=intitle:" . urlencode($titulo) . "&inauthor:" . urlencode($editora);
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['items'][0]['volumeInfo'])) {
        $info = $data['items'][0]['volumeInfo'];

        // Verificando se o ISBN existe
        $isbn = isset($info['industryIdentifiers'][0]['identifier']) ? $info['industryIdentifiers'][0]['identifier'] : 'Desconhecido';

        // Verificando se o autor existe
        $autor = isset($info['authors']) ? implode(', ', $info['authors']) : 'Desconhecido';

        // Verificando se o ano de publicação existe
        $anoPublicacao = isset($info['publishedDate']) ? $info['publishedDate'] : 'Desconhecido';

        // Verificando se o número de páginas existe
        $numPaginas = isset($info['pageCount']) ? $info['pageCount'] : 'Desconhecido';

        // Verificando se o idioma existe
        $idioma = isset($info['language']) ? strtoupper($info['language']) : 'Desconhecido';

        // Verificando se o gênero existe
        $genero = isset($info['categories']) ? implode(', ', $info['categories']) : 'Desconhecido';

        return [
            'isbn' => $isbn,
            'autor' => $autor,
            'ano_publicacao' => $anoPublicacao,
            'num_paginas' => $numPaginas,
            'idioma' => $idioma,
            'genero' => $genero
        ];
    }

    return null; // Retorna null caso os dados não sejam encontrados
}

function buscarInfoLivroOpenLibrary($titulo, $editora) {
    $url = "https://openlibrary.org/search.json?title=" . urlencode($titulo) . "&publisher=" . urlencode($editora);
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['docs'][0])) {
        $info = $data['docs'][0];

        // Verificando se o ISBN existe
        $isbn = isset($info['isbn'][0]) ? $info['isbn'][0] : 'Desconhecido';

        // Verificando se o autor existe
        $autor = isset($info['author_name']) ? implode(', ', $info['author_name']) : 'Desconhecido';

        // Verificando se o ano de publicação existe
        $anoPublicacao = isset($info['first_publish_year']) ? $info['first_publish_year'] : 'Desconhecido';

        // Verificando se o número de páginas existe
        $numPaginas = isset($info['number_of_pages_median']) ? $info['number_of_pages_median'] : 'Desconhecido';

        // Verificando se o idioma existe
        $idioma = isset($info['language']) && is_array($info['language']) ? strtoupper($info['language'][0]) : 'Desconhecido';

        // Verificando se o gênero existe
        $genero = isset($info['subject']) ? implode(', ', $info['subject']) : 'Desconhecido';

        return [
            'isbn' => $isbn,
            'autor' => $autor,
            'ano_publicacao' => $anoPublicacao,
            'num_paginas' => $numPaginas,
            'idioma' => $idioma,
            'genero' => $genero
        ];
    }

    return null; // Retorna null caso os dados não sejam encontrados
}

function buscarInfoLivroISBNdb($titulo, $editora) {
    $apiKey = 'SUA_CHAVE_API_ISBNDB';  // Substitua pela sua chave de API ISBNdb
    $url = "https://api.isbndb.com/books?q=" . urlencode($titulo) . "&publisher=" . urlencode($editora);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: $apiKey"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (isset($data['books'][0])) {
        $info = $data['books'][0];
        $isbn = $info['isbn'];
        $autor = isset($info['authors']) ? implode(', ', $info['authors']) : 'Desconhecido';
        $anoPublicacao = isset($info['publish_date']) ? $info['publish_date'] : 'Desconhecido';
        $numPaginas = isset($info['num_pages']) ? $info['num_pages'] : 'Desconhecido';
        $idioma = isset($info['language']) ? strtoupper($info['language']) : 'Desconhecido';
        $genero = isset($info['categories']) ? implode(', ', $info['categories']) : 'Desconhecido';

        return [
            'isbn' => $isbn,
            'autor' => $autor,
            'ano_publicacao' => $anoPublicacao,
            'num_paginas' => $numPaginas,
            'idioma' => $idioma,
            'genero' => $genero
        ];
    }
    return null;
}

function buscarInfoLivroAmazon($titulo) {
    $accessKey = 'SUA_CHAVE_ACESSO_AMAZON';  // Substitua pela sua chave de acesso Amazon
    $secretKey = 'SUA_CHAVE_SECRETA_AMAZON';  // Substitua pela sua chave secreta Amazon
    $associateTag = 'SEU_ASSOCIATE_TAG';  // Substitua pelo seu associate tag

    $url = "https://api.amazon.com/products?keywords=" . urlencode($titulo) . "&partner=" . $associateTag;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "x-api-key: $accessKey"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (isset($data['items'][0])) {
        $info = $data['items'][0];
        $isbn = isset($info['asin']) ? $info['asin'] : null;
        // A Amazon pode não retornar todos os dados diretamente, como autor ou gênero
        // Portanto, você pode usar a informação ASIN ou considerar outras fontes
        return [
            'isbn' => $isbn,
            'autor' => 'Desconhecido',
            'ano_publicacao' => 'Desconhecido',
            'num_paginas' => 'Desconhecido',
            'idioma' => 'Desconhecido',
            'genero' => 'Desconhecido'
        ];
    }
    return null;
}
?>
