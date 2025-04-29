<!-- FORMULÁRIO DA ETAPA 2 -->
<form action="" method="post">
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
    <button type="button" class="btn-excluir" onclick="removerCampoLivro(this)">Excluir</button>
  </div>
</div>



  <!-- Botão de adicionar -->
  <button type="button" id="btnAdicionarLivro" onclick="adicionarCampoLivro()">+ Adicionar outro livro</button>

  <!-- Campo: Data do Empréstimo -->
  <br><br>
  <label for="data_emprestimo">Data do Empréstimo:</label>
  <input type="date" id="data_emprestimo" name="data_emprestimo" value="<?= date('Y-m-d') ?>" readonly><br><br>

  <!-- Campo: Data da Devolução -->
  <label for="data_devolucao">Data da Devolução:</label>
  <input type="date" id="data_devolucao" name="data_devolucao"><br><br>

  <button type="submit" name="confirmelivro" value="Confirma livro">Confirma livro</button>
</form>

<!-- resumo geral -->
<div id="resumoLivros" style="margin-top:30px;"></div>


<script>
  // ----- 1. Referências ao DOM -----
  const limiteInput     = document.getElementById('limiteLivros');
  const livrosWrapper   = document.getElementById('livrosWrapper');
  const btnAdd          = document.getElementById('btnAdicionarLivro');
  const resumoLivrosDiv = document.getElementById('resumoLivros');

  // ----- 2. Atualiza estado do botão “Adicionar” e visibilidade dos “Excluir” -----
  function atualizarEstadoBotao() {
    const limite = parseInt(limiteInput.value, 10);
    const blocos = livrosWrapper.querySelectorAll('.livro-bloco');
    
    // Habilita/desabilita “Adicionar”
    btnAdd.disabled = blocos.length >= limite;

   // remove blocos extras (dinamicamente)
   while (livrosWrapper.childElementCount > limite) {
      livrosWrapper.removeChild(livrosWrapper.lastElementChild);
    }

    // exibe/oculta o botão “Excluir” em cada bloco
    const botoesExcluir = livrosWrapper.querySelectorAll('.btn-excluir');
    botoesExcluir.forEach(btn => {
      btn.style.display = (livrosWrapper.childElementCount > 1 ? 'inline' : 'none');
    });

    // Atualiza o resumo
    atualizarResumoLivros();
  }

  // dispara sempre que altera o limite
  limiteInput.addEventListener('change', atualizarEstadoBotao);

  // ----- 3. Adiciona um novo bloco de busca -----
  function adicionarCampoLivro() {
    const limite = parseInt(limiteInput.value, 10);
    const blocos = livrosWrapper.querySelectorAll('.livro-bloco');
    if (blocos.length >= limite) {
      alert(`Você já atingiu o limite de ${limite} livro(s).`);
      return;
    }

    // Clona e limpa o novo bloco
    const original = blocos[0];
    const clone = original.cloneNode(true);
    clone.querySelector('.buscaLivro').value = '';
    clone.querySelector('.resultadoLivro').innerHTML = '';
    clone.querySelector('.codigoLivroSelecionado').value = '';

    // Garante que o botão Excluir tem a classe correta
    const btnExcluir = clone.querySelector('button[onclick^="removerCampoLivro"]');
    btnExcluir.classList.add('btn-excluir');

    livrosWrapper.appendChild(clone);
    atualizarEstadoBotao();
  }

  // ----- 4. Remove um bloco de busca -----
  function removerCampoLivro(btnExcluir) {
    const bloco = btnExcluir.closest('.livro-bloco');
    livrosWrapper.removeChild(bloco);
    atualizarEstadoBotao();
  }

  // ----- 5. Busca de livros e captura do código -----
  function buscarLivro(botaoBuscar) {
    const input        = botaoBuscar.previousElementSibling;
    const resultadoDiv = botaoBuscar.nextElementSibling;
    const hiddenInput  = resultadoDiv.nextElementSibling;
    const termo        = input.value.trim();

    fetch('ajax_busca.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `tipo=livro&livro=${encodeURIComponent(termo)}`
    })
    .then(r => r.text())
    .then(html => {
      resultadoDiv.innerHTML = html;
      const select = resultadoDiv.querySelector('select');
      if (!select) return;

      select.onchange = () => {
        hiddenInput.value = select.value;
        atualizarResumoLivros();
      };
    });
  }

  // ----- 6. Atualiza o bloco de resumo geral -----
  function atualizarResumoLivros() {
    resumoLivrosDiv.innerHTML = '<h3>Livros Selecionados</h3>';
    const blocos = livrosWrapper.querySelectorAll('.livro-bloco');
    let contador = 1;

    blocos.forEach(bloco => {
      const codigo = bloco.querySelector('.codigoLivroSelecionado').value;
      if (!codigo) return;

      fetch('ajax_busca.php', {
        method: 'POST',
        headers:{ 'Content-Type':'application/x-www-form-urlencoded' },
        body:`tipo=detalhesLivro&codigoLivro=${encodeURIComponent(codigo)}`
      })
      .then(r => r.json())
      .then(dados => {
        if (dados.erro) return;
        resumoLivrosDiv.innerHTML += `
          <div style="margin-bottom:15px;">
            <strong>Livro ${contador}</strong><br>
            <strong>Título:</strong> ${dados.titulo}<br>
            <strong>Tombo:</strong> ${dados.tombo}<br>
            <strong>Autor:</strong> ${dados.autor}<br>
            <strong>Editora:</strong> ${dados.editora}<br>
            <strong>ISBN Falso:</strong> ${dados.isbn_falso}<br>
            <strong>Quantidade:</strong> ${dados.quantidade}<br>
          </div>
          <hr>
        `;
        contador++;
      });
    });
  }

  // ----- 7. Ajuste automático da data de devolução -----
  document.addEventListener("DOMContentLoaded", () => {
    const dataEmp = document.getElementById("data_emprestimo");
    const dataDev = document.getElementById("data_devolucao");

    function ajustarDataDevolucao(d) {
      const dia = d.getDay();
      if (dia === 6) d.setDate(d.getDate() - 1);
      if (dia === 0) d.setDate(d.getDate() + 1);
      return d;
    }

    function definirDataPadrao() {
      let d = new Date(dataEmp.value);
      d.setDate(d.getDate() + 7);
      dataDev.value = ajustarDataDevolucao(d)
                        .toISOString().split('T')[0];
    }

    definirDataPadrao();

    dataDev.addEventListener("change", () => {
      const emp = new Date(dataEmp.value);
      let dev = new Date(dataDev.value);
      if (dev < emp) {
        alert("Data de devolução não pode ser anterior à de empréstimo.");
        definirDataPadrao();
        return;
      }
      if ([0,6].includes(dev.getDay())) {
        alert("Ajustando para evitar fim de semana.");
        dataDev.value = ajustarDataDevolucao(dev)
                          .toISOString().split('T')[0];
      }
    });
  });
</script>

