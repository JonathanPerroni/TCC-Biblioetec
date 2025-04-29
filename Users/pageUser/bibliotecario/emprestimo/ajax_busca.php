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
// DETALHES DO ALUNO
// ===============================
if ($tipo === 'detalhesAluno' && isset($_POST['codigoAluno'])) {
    $codigoAluno = $_POST['codigoAluno'];
    $query = "SELECT nome, ra_aluno, periodo, nome_escola, nome_curso, tipo_ensino, status FROM tbalunos WHERE codigo = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $codigoAluno);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $aluno = $res->fetch_assoc();
        echo json_encode($aluno); // devolve em formato JSON
    } else {
        echo json_encode(['erro' => 'Aluno não encontrado.']);
    }
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
// DETALHES DO LIVRO
// ===============================
if ($tipo === 'detalhesLivro' && isset($_POST['codigoLivro'])) {
    $codigoLivro = $_POST['codigoLivro'];

    // Primeiro busca o livro
    $queryLivro = "SELECT titulo, tombo, autor, editora, isbn_falso FROM tblivros WHERE codigo = ?";
    $stmt = $conn->prepare($queryLivro);
    $stmt->bind_param("i", $codigoLivro);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $livro = $res->fetch_assoc();

        // Agora busca quantos livros tem o mesmo título e editora
        $queryQtd = "SELECT COUNT(*) as quantidade FROM tblivros WHERE titulo = ? AND editora = ?";
        $stmtQtd = $conn->prepare($queryQtd);
        $stmtQtd->bind_param("ss", $livro['titulo'], $livro['editora']);
        $stmtQtd->execute();
        $resQtd = $stmtQtd->get_result();
        $qtd = $resQtd->fetch_assoc();

        // Junta as informações
        $livro['quantidade'] = $qtd['quantidade'];

        echo json_encode($livro);
    } else {
        echo json_encode(['erro' => 'Livro não encontrado.']);
    }
    exit();
}


    
}
?>
