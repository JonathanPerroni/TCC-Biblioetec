
<div class="areaDebusca">
    <!-- formulario_pesquisa_aluno_livro.php -->
<h3>Buscar Aluno</h3>
<input type="text" id="buscaAluno" placeholder="Digite o nome ou RA do aluno">
<button onclick="buscar('Aluno')">Buscar</button>
<div id="resultadoAluno"></div>

<!-- Hidden fields para armazenar seleção -->
<input type="hidden" name="codigo_aluno" id="codigoAlunoSelecionado">


<!-- Limite de livros -->
<label for="limiteLivros">Limite de livros por aluno:</label>
<input type="number" id="limiteLivros" value="3" min="1" style="width: 60px;">

<!-- Wrapper dos blocos de busca de livro -->
<div id="livrosWrapper">
  <div class="livro-bloco">
    <h3>Buscar Livro</h3>
    <input type="text" class="buscaLivro" placeholder="Digite nome ou tombo">
    <button type="button" onclick="buscarLivro(this)">Buscar</button>
    <div class="resultadoLivro"></div>
    <input type="hidden" name="codigos_livros[]" class="codigoLivroSelecionado">
    <button type="button" onclick="removerCampoLivro(this)">Excluir</button> <!-- Botão de Excluir -->
  </div>
</div>

<!-- Botão de adicionar -->
<button type="button" id="btnAdicionarLivro" onclick="adicionarCampoLivro()">
  + Adicionar outro livro
</button>

<!-- Campo: Data do Empréstimo -->
<label for="data_emprestimo">Data do Empréstimo:</label>
<input type="date" id="data_emprestimo" name="data_emprestimo" value="<?= date('Y-m-d') ?>" readonly><br><br>

<!-- Campo: Data da Devolução -->
<label for="data_devolucao">Data da Devolução:</label>
<input type="date" id="data_devolucao" name="data_devolucao"><br><br>

<!-- js de data -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const dataEmprestimo = document.getElementById("data_emprestimo");
    const dataDevolucao = document.getElementById("data_devolucao");

    // Função para ajustar datas de devolução de fim de semana
    function ajustarDataDevolucao(data) {
        const diaSemana = data.getDay();
        if (diaSemana === 6) {
            // Sábado -> Sexta
            data.setDate(data.getDate() - 1);
        } else if (diaSemana === 0) {
            // Domingo -> Segunda
            data.setDate(data.getDate() + 1);
        }
        return data;
    }

    // Define a data de devolução padrão ao carregar
    function definirDataDevolucaoPadrao() {
        let data = new Date(dataEmprestimo.value);
        data.setDate(data.getDate() + 7);
        data = ajustarDataDevolucao(data);
        dataDevolucao.value = data.toISOString().split('T')[0];
    }

    // Ao carregar a página, define a devolução automaticamente
    definirDataDevolucaoPadrao();

    // Quando o bibliotecário altera a data, ajusta se necessário
    dataDevolucao.addEventListener("change", function () {
    const dataEmprestimoValue = new Date(dataEmprestimo.value);
    let data = new Date(this.value);

    if (data < dataEmprestimoValue) {
        alert("A data de devolução não pode ser anterior à data de empréstimo.");
        definirDataDevolucaoPadrao();
        return;
    }

    const diaSemana = data.getDay();
    if (diaSemana === 6 || diaSemana === 0) {
        data = ajustarDataDevolucao(data);
        this.value = data.toISOString().split('T')[0];
        alert("A data de devolução foi ajustada para evitar sábado/domingo.");
    }
    });
    });
</script>

<!--  js de Busca livro -->
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
    document.getElementById('codigoAlunoSelecionado').value = selected.value;
    }

    function selecionarLivro() {
    const select = document.getElementById('selecaoLivro');
    const selected = select.options[select.selectedIndex];
    document.getElementById('codigoLivroSelecionado').value = selected.value;
    }
</script>

<!-- js de clonar o campo de busca de livro e aumentar limite de livro -->
<script>
   const limiteInput = document.getElementById('limiteLivros');
  const livrosWrapper = document.getElementById('livrosWrapper');
  const btnAdd = document.getElementById('btnAdicionarLivro');

  function atualizarEstadoBotao() {
    const limite = parseInt(limiteInput.value, 10);
    const total = livrosWrapper.querySelectorAll('.livro-bloco').length;

    // Desabilita o botão de adicionar se o limite for atingido
    btnAdd.disabled = total >= limite;

    // Remover os campos excedentes se o limite for diminuído
    while (total > limite) {
      livrosWrapper.removeChild(livrosWrapper.lastElementChild);
    }
  }

  limiteInput.addEventListener('change', atualizarEstadoBotao);


  function adicionarCampoLivro() {
    const limite = parseInt(limiteInput.value, 10);
    const total = livrosWrapper.querySelectorAll('.livro-bloco').length;
    if (total >= limite) {
      alert(`Você já atingiu o limite de ${limite} livro(s).`);
      return;
    }
    const blocoOriginal = livrosWrapper.querySelector('.livro-bloco');
    const novoBloco = blocoOriginal.cloneNode(true);
    novoBloco.querySelector('.buscaLivro').value = '';
    novoBloco.querySelector('.resultadoLivro').innerHTML = '';
    novoBloco.querySelector('.codigoLivroSelecionado').value = '';
    livrosWrapper.appendChild(novoBloco);
    atualizarEstadoBotao();
  }

  function removerCampoLivro(botao) {
    const wrapper = document.getElementById('livrosWrapper');
    const campoLivro = botao.closest('.livro-bloco');
    wrapper.removeChild(campoLivro);

    // Atualiza o estado do botão de adicionar
    atualizarEstadoBotao();
    }


    function buscarLivro(botao) {
        const input = botao.previousElementSibling;
        const resultadoDiv = botao.nextElementSibling;
        const termo = input.value;

        fetch('ajax_busca.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `tipo=livro&livro=${encodeURIComponent(termo)}`
        })
        .then(response => response.text())
        .then(html => {
            resultadoDiv.innerHTML = html;

            // Associa a seleção ao campo hidden
            const select = resultadoDiv.querySelector('select');
            if (select) {
                select.onchange = function () {
                    const codigo = this.value;
                    const hiddenInput = resultadoDiv.nextElementSibling;
                    hiddenInput.value = codigo;
                };
            }
        });
    }
</script>
</div>