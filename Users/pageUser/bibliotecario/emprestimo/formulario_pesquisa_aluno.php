<?php
include_once('../seguranca.php');// já verifica login e carrega CSRF
$token_csrf = gerarTokenCSRF(); // usa token no formulário

?>
<!-- FORMULARIO DA ETAPA 1, BUSCAR DADOS DO ALUNO --> 
 <!-- Botão para voltar e cancelar -->
<a href="cancelar_emprestimo.php" class="btn btn-danger">Voltar para a página do bibliotecário</a>
<form action="" method="post">
<h3>Buscar Aluno</h3>
<input type="text" id="buscaAluno" placeholder="Digite o nome ou RA do aluno">
<button type="button" onclick="buscar('Aluno')">Buscar</button>
<div id="resultadoAluno"></div>

<!-- Hidden fields para armazenar seleção -->
<input type="hidden" name="codigo_aluno" id="codigoAlunoSelecionado">

<button type="submit" name="confirmeAluno" value="Confirma Aluno">Confirma Aluno</button>
<!--<button type="button" onclick="confirmarAluno()">Confirma Aluno</button>-->
</form>

<div id="detalhesAluno" style="margin-top: 20px;"></div>
<?php
if (isset($_SESSION['msg'])) {
    echo "<div class='mensagem'>{$_SESSION['msg']}</div>";
    unset($_SESSION['msg']);
}
?>


<script>
    function buscar(tipo) {
    const input = document.getElementById(`busca${tipo}`).value;
    const resultadoDiv = document.getElementById(`resultado${tipo}`);

    // Envia via POST para ajax_busca.php
    fetch('ajax_busca.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `tipo=${tipo.toLowerCase()}&${tipo.toLowerCase()}=${encodeURIComponent(input)}`
    })
    .then(response => response.text())
    .then(html => {
        resultadoDiv.innerHTML = html;
    });
    }

    function selecionarAluno() {
    const select = document.getElementById('selecaoAluno');
    const selected = select.options[select.selectedIndex];
    const codigoAluno = selected.value;

    // Preenche o hidden
    document.getElementById('codigoAlunoSelecionado').value = codigoAluno;

    // Buscar detalhes do aluno
    fetch('ajax_busca.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `tipo=detalhesAluno&codigoAluno=${encodeURIComponent(codigoAluno)}`
    })
    .then(response => response.json())
    .then(dados => {
        if (dados.erro) {
            document.getElementById('detalhesAluno').innerHTML = dados.erro;
        } else {
            let statusAluno = dados.status == 1 ? 'Ativo' : 'Bloqueado';
            document.getElementById('detalhesAluno').innerHTML = `
                <p><strong>Nome:</strong> ${dados.nome}</p>
                <p><strong>RA:</strong> ${dados.ra_aluno}</p>
                <p><strong>Período:</strong> ${dados.periodo}</p>
                <p><strong>Nome da Escola:</strong> ${dados.nome_escola}</p>
                <p><strong>Nome do Curso:</strong> ${dados.nome_curso}</p>
                <p><strong>Tipo de Ensino:</strong> ${dados.tipo_ensino}</p>
                <p><strong>Status:</strong> ${statusAluno}</p>
            `;
        }
    });
}

function confirmarAluno() {
    const codigoAluno = document.getElementById('codigoAlunoSelecionado').value;

    fetch('ajax_busca.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `tipo=verificaAluno&codigoAluno=${encodeURIComponent(codigoAluno)}`
    })
    .then(response => response.json())
    .then(dados => {
        if (dados.erro) {
            alert(dados.erro);
        } else {
            alert('Aluno confirmado com sucesso!');
            // Pode redirecionar ou liberar próxima etapa
        }
    });
}

    </script>

    