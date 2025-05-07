<?php
// ajax_busca.php
session_start();
require_once '../../../../conexao/conexao.php'; // Ajuste o caminho se necessário

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo'])) {
    $tipo = $_POST['tipo'];
    $conn = new mysqli("localhost", "root", "", "bdescola"); // Atualize se necessário

    if ($conn->connect_error) {
        die("Erro na conexão com o banco de dados: " . $conn->connect_error);
    }

    // ===============================
    // BUSCA DE ALUNOS
    // ===============================
    if ($tipo === 'aluno' && isset($_POST['aluno'])) {
        $input = $_POST['aluno'];
        $query = "SELECT * FROM tbalunos WHERE nome LIKE ? OR ra_aluno LIKE ?";
        $stmt = $conn->prepare($query);
        $busca = "%" . $input . "%";
        $stmt->bind_param("ss", $busca, $busca);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            echo "<select id='selecaoAluno' onchange='selecionarAluno()'>";
            echo "<option disabled selected>Escolha um aluno</option>";
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['codigo']}' data-nome='" . htmlspecialchars($row['nome']) . "'>{$row['ra_aluno']} - {$row['nome']}</option>";
            }
            echo "</select>";
        } else {
            echo "Nenhum aluno encontrado.";
        }
        exit();
    }

// ===============================
// DETALHES DO ALUNO COM VERIFICAÇÕES
// ===============================
if ($tipo === 'detalhesAluno' && isset($_POST['codigoAluno'])) {
    $codigoAluno = $_POST['codigoAluno'];

    // Consulta dados do aluno
    $query = "SELECT nome, ra_aluno, periodo, nome_escola, nome_curso, tipo_ensino, status FROM tbalunos WHERE codigo = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $codigoAluno);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(['erro' => 'Aluno não encontrado.']);
        exit();
    }

    $aluno = $res->fetch_assoc();

    // ===============================
// VERIFICAÇÃO COMPLETA DO ALUNO
// ===============================
if ($tipo === 'verificaAluno' && isset($_POST['codigoAluno'])) {
    $codigoAluno = $_POST['codigoAluno'];

    // Verifica se o aluno está bloqueado
    $queryAluno = "SELECT * FROM tbalunos WHERE codigo = ?";
    $stmtAluno = $conn->prepare($queryAluno);
    $stmtAluno->bind_param("i", $codigoAluno);
    $stmtAluno->execute();
    $resAluno = $stmtAluno->get_result();

    if ($resAluno->num_rows === 0) {
        echo json_encode(['erro' => 'Aluno não encontrado.']);
        exit;
    }

    $aluno = $resAluno->fetch_assoc();

    if ($aluno['status'] == 0) {
        echo json_encode(['erro' => 'Aluno está bloqueado.']);
        exit;
    }

    // Verifica pendência de devolução
    $queryPendencia = "SELECT COUNT(*) as pendencias FROM tbemprestimos WHERE ra_aluno = ? AND (data_devolucao_efetiva IS NULL OR data_devolucao_efetiva = '')";
    $stmtPendencia = $conn->prepare($queryPendencia);
    $stmtPendencia->bind_param("s", $aluno['ra_aluno']);
    $stmtPendencia->execute();
    $resPendencia = $stmtPendencia->get_result();
    $dadosPendencia = $resPendencia->fetch_assoc();

    if ($dadosPendencia['pendencias'] > 0) {
        echo json_encode(['erro' => 'Aluno possui livros não devolvidos.']);
        exit;
    }

    // Tudo certo, salvar na sessão
    $_SESSION['aluno'] = $aluno;
    echo json_encode(['sucesso' => true]);
    exit;
}



    // Se passou nas verificações, salva na sessão e retorna dados
    $_SESSION['aluno'] = $aluno;
    echo json_encode($aluno);
    exit();
}

    // ===============================
    // BUSCA DE LIVROS
    // ===============================
    if ($tipo === 'livro' && isset($_POST['livro'])) {
        $input = $_POST['livro'];
        $query = "SELECT * FROM tblivros WHERE titulo LIKE ? OR tombo LIKE ?";
        $stmt = $conn->prepare($query);
        $busca = "%" . $input . "%";
        $stmt->bind_param("ss", $busca, $busca);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            echo "<select id='selecaoLivro' onchange='selecionarLivro()'>";
            echo "<option disabled selected>Escolha um livro</option>";
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['codigo']}' data-titulo='" . htmlspecialchars($row['titulo']) . "'>{$row['tombo']} - {$row['titulo']}</option>";
            }
            echo "</select>";
        } else {
            echo "Nenhum livro encontrado.";
        }
        exit();
    }
    
// ===============================
// DETALHES DO LIVRO (Para múltiplos livros)
// ===============================
if ($tipo === 'detalhesLivro' && isset($_POST['codigoLivro'])) {
    $codigosLivros = $_POST['codigoLivro']; // array de códigos
    $livros = [];

    foreach ($codigosLivros as $codigoLivro) {
        $queryLivro = "SELECT codigo, titulo, tombo, autor, editora, isbn_falso, isbn FROM tblivros WHERE codigo = ?";
        $stmt = $conn->prepare($queryLivro);
        $stmt->bind_param("i", $codigoLivro);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $livro = $res->fetch_assoc(); // contém o campo 'codigo'

            // Quantidade de exemplares com mesmo título e editora
            $queryQtd = "SELECT COUNT(*) as quantidade FROM tblivros WHERE titulo = ? AND editora = ?";
            $stmtQtd = $conn->prepare($queryQtd);
            $stmtQtd->bind_param("ss", $livro['titulo'], $livro['editora']);
            $stmtQtd->execute();
            $resQtd = $stmtQtd->get_result();
            $qtd = $resQtd->fetch_assoc();

            $livro['quantidade'] = $qtd['quantidade'];
            $livros[] = $livro;
        }
    }

    // Acumular na sessão
    if (!isset($_SESSION['livros'])) {
        $_SESSION['livros'] = [];
    }

    foreach ($livros as $novoLivro) {
        $jaExiste = false;
        foreach ($_SESSION['livros'] as $livroExistente) {
            if ($livroExistente['codigo'] == $novoLivro['codigo']) {
                $jaExiste = true;
                break;
            }
        }
        if (!$jaExiste) {
            $_SESSION['livros'][] = $novoLivro;
        }
    }

    // Retorna todos os livros da sessão
    echo json_encode($_SESSION['livros']);
    exit();
}

    
}
?>
