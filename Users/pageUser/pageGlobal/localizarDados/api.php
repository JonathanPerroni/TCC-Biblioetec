    <?php
    set_time_limit(3600); // 1 hora
    // Verifica se a função já existe antes de declarada
    if (!function_exists('valorInvalido')) {
        function valorInvalido($valor) {
            global $valoresInvalidos;
            return in_array(strtolower(trim($valor)), $valoresInvalidos);
        }
    }

    // Definindo a variável global dentro da função corretamente
    global $valoresInvalidos;

    // Inicializando a variável $valoresInvalidos caso ela não tenha sido definida antes
    if (!isset($valoresInvalidos)) {
        $valoresInvalidos = ['desconhecido', 'null', 'nulo', 'underfinid', 'n/d', 'nd', 'undefined'];
    }

    function traduzirGenero($genero) {
        // Traduções de gênero
        $traducoes = [
            'Fiction' => 'Ficção',
            'Novel' => 'Romance',
            'Fantasy' => 'Fantasia',
            'Science Fiction' => 'Ficção Científica',
            'Mystery' => 'Mistério',
            'Thriller' => 'Suspense',
            'Horror' => 'Terror',
            'Romance' => 'Romance',
            'Adventure' => 'Aventura',
            'Children' => 'Infantil',
            'Young Adult' => 'Jovem Adulto',
            'Biography' => 'Biografia',
            'History' => 'História',
            'Poetry' => 'Poesia',
            'Drama' => 'Drama',
            'Comics' => 'Quadrinhos',
            'Art' => 'Arte',
            'Self Help' => 'Autoajuda',
            'Business' => 'Negócios',
            'Technology' => 'Tecnologia',
            'Science' => 'Ciência',
            'Education' => 'Educação',
            'Philosophy' => 'Filosofia',
            'Religion' => 'Religião',
            'Cooking' => 'Culinária',
            'Travel' => 'Viagem',
            'Sports' => 'Esportes',
            'Health' => 'Saúde',
            'Family' => 'Família',
            'Humor' => 'Humor'
        ];

        if (strpos($genero, ',') !== false) {
            $generos = explode(',', $genero);
            $generosTraducoes = array_map(function($g) use ($traducoes) {
                $g = trim($g);
                return isset($traducoes[$g]) ? $traducoes[$g] : $g;
            }, $generos);
            return implode(', ', $generosTraducoes);
        }

        return isset($traducoes[$genero]) ? $traducoes[$genero] : $genero;
    }



    function gerarISBNUnico($titulo, $livros) {
        if (!is_array($livros)) {
            $livros = []; // Garante que $livros seja um array
        }
    
        $tituloLimpo = preg_replace('/[^a-zA-Z0-9]/', '', $titulo);
        $prefixo = substr($tituloLimpo, 0, 3);
        $numeroAleatorio = str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
        $isbn = strtolower($prefixo) . $numeroAleatorio;
    
        // Garante que array_column() só recebe um array
        $isbnExistente = is_array($livros) ? array_column($livros, 'ISBN') : [];
    
        while (in_array($isbn, $isbnExistente)) {
            $numeroAleatorio = str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
            $isbn = strtolower($prefixo) . $numeroAleatorio;
        }
        return $isbn;
    }

    function gerarCDDCutterUnico($titulo, $autor, $livros) {
        if (!is_array($livros)) {
            $livros = []; // Garante que $livros seja um array
        }
    
        $tituloLimpo = preg_replace('/[^a-zA-Z0-9]/', '', $titulo);
        $autorLimpo = preg_replace('/[^a-zA-Z]/', '', $autor);
        $prefixo = strtoupper(substr($tituloLimpo, 0, 3)) . strtoupper(substr($autorLimpo, 0, 3));
        $numeroAleatorio = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $cdd_cutter = $prefixo . $numeroAleatorio;
    
        // Garante que array_column() só receba um array
        $cddExistente = is_array($livros) ? array_column($livros, 'CDD/CUTTER') : [];
    
        while (in_array($cdd_cutter, $cddExistente)) {
            $numeroAleatorio = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $cdd_cutter = $prefixo . $numeroAleatorio;
        }
    
        return $cdd_cutter;
    }

    function buscarInfoLivroGoogleBooks($titulo, $editora, $livros) {
        $url = "https://www.googleapis.com/books/v1/volumes?q=intitle:" . urlencode($titulo) . "&inauthor:" . urlencode($editora);
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['items'][0]['volumeInfo'])) {
            $info = $data['items'][0]['volumeInfo'];

            // Verifica se o ISBN existe, se não, gera um novo
            $isbn = isset($info['industryIdentifiers'][0]['identifier']) ? 
                $info['industryIdentifiers'][0]['identifier'] : 
                gerarISBNUnico($titulo, $livros);

            // Atribui um valor ao autor caso esteja presente ou um valor padrão
            $autor = isset($info['authors']) ? implode(', ', $info['authors']) : 'Desconhecido';
            
            // Gera CDD/CUTTER se não existir
            $cddCutter = isset($info['subject']) ? 
                        implode(', ', $info['subject']) : 
                        gerarCDDCutterUnico($titulo, $autor, $livros);

            // Traduz o gênero para português
            $genero = isset($info['categories']) ? 
                    traduzirGenero(implode(', ', $info['categories'])) : 
                    'Desconhecido';

            return [
                'isbn' => $isbn,
                'autor' => $autor,
                'ano_publicacao' => isset($info['publishedDate']) ? $info['publishedDate'] : 'Desconhecido',
                'num_paginas' => isset($info['pageCount']) ? $info['pageCount'] : 'Desconhecido',
                'idioma' => isset($info['language']) ? strtoupper($info['language']) : 'Desconhecido',
                'genero' => $genero,
                'cdd_cutter' => $cddCutter
            ];
        }

        return null;
    }


    function buscarInfoLivroISBNdb($titulo, $editora, $livros) {
        $apiKey = 'SUA_CHAVE_API_ISBNDB';
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

         
            $autor = isset($info['authors']) ? implode(', ', $info['authors']) : 'Desconhecido';
            
            $cddCutter = gerarCDDCutterUnico($titulo, $autor, $livros);

            $genero = isset($info['categories']) ? 
                    traduzirGenero(implode(', ', $info['categories'])) : 
                    'Desconhecido';

            return [
                'isbn' => $isbn,
                'autor' => $autor,
                'ano_publicacao' => isset($info['publish_date']) ? $info['publish_date'] : 'Desconhecido',
                'num_paginas' => isset($info['num_pages']) ? $info['num_pages'] : 'Desconhecido',
                'idioma' => isset($info['language']) ? strtoupper($info['language']) : 'Desconhecido',
                'genero' => $genero,
                'cdd_cutter' => $cddCutter
            ];
        }

        return null;
    }
 /*   function buscarInfoLivroOpenLibrary($titulo, $livros) {
        $url = "https://openlibrary.org/search.json?title=" . urlencode($titulo);
        $response = file_get_contents($url);
        $data = json_decode($response, true);
    
        if (isset($data['docs'][0])) {
            $info = $data['docs'][0];
    
            $isbn = isset($info['isbn'][0]) ? $info['isbn'][0] : gerarISBNUnico($titulo, $livros);
            $autor = isset($info['author_name']) ? implode(', ', $info['author_name']) : 'Desconhecido';
            $ano_publicacao = isset($info['first_publish_year']) ? $info['first_publish_year'] : 'Desconhecido';
            $num_paginas = isset($info['number_of_pages_median']) ? $info['number_of_pages_median'] : 'Desconhecido';
            $idioma = isset($info['language'][0]) ? strtoupper($info['language'][0]) : 'Desconhecido';
            $genero = isset($info['subject']) ? traduzirGenero(implode(', ', $info['subject'])) : 'Desconhecido';
            $cdd_cutter = gerarCDDCutterUnico($titulo, $autor, $livros);
    
            return [
                'isbn' => $isbn,
                'autor' => $autor,
                'ano_publicacao' => $ano_publicacao,
                'num_paginas' => $num_paginas,
                'idioma' => $idioma,
                'genero' => $genero,
                'cdd_cutter' => $cdd_cutter
            ];
        }
    
        return null;
    }
*/
    function buscarInfoLivroAmazon($titulo, $editora = '', $livros = []) {
        $accessKey = 'SUA_CHAVE_ACESSO_AMAZON';
        $secretKey = 'SUA_CHAVE_SECRETA_AMAZON';
        $associateTag = 'SEU_ASSOCIATE_TAG';

        $url = "https://api.amazon.com/products?keywords=" . urlencode($titulo);
        if (!empty($editora)) {
            $url .= "&publisher=" . urlencode($editora);
        }
        if (!empty($associateTag)) {
            $url .= "&partner=" . $associateTag;
        }
        
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

            
            
            $autor = isset($info['author']) ? $info['author'] : 'Desconhecido';
            
            $cddCutter = gerarCDDCutterUnico($titulo, $autor, $livros);

            $genero = isset($info['categories']) ? 
                    traduzirGenero(implode(', ', $info['categories'])) : 
                    'Desconhecido';

            return [
                'isbn' => $isbn,
                'autor' => $autor,
                'ano_publicacao' => isset($info['publicationDate']) ? $info['publicationDate'] : 'Desconhecido',
                'num_paginas' => isset($info['numberOfPages']) ? $info['numberOfPages'] : 'Desconhecido',
                'idioma' => isset($info['language']) ? strtoupper($info['language']) : 'Desconhecido',
                'genero' => $genero,
                'cdd_cutter' => $cddCutter
            ];
        }

        return null;
    }
    ?>
