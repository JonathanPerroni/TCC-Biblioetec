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




  <button type="submit" name="confirmelivro" value="confirmelivro">Confirma livro</button>
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

  const codigos = [];
  const blocos = livrosWrapper.querySelectorAll('.livro-bloco');

  blocos.forEach(bloco => {
    const codigo = bloco.querySelector('.codigoLivroSelecionado').value;
    if (codigo) codigos.push(codigo);
  });

  if (codigos.length === 0) return;

  const formData = new URLSearchParams();
  formData.append('tipo', 'detalhesLivro');
  codigos.forEach(c => formData.append('codigoLivro[]', c));

  fetch('ajax_busca.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: formData.toString()
  })
  .then(r => r.json())
  .then(livros => {
    if (!Array.isArray(livros)) return;

    livros.forEach((livro, idx) => {
      resumoLivrosDiv.innerHTML += `
        <div style="margin-bottom:15px;">
          <strong>Livro ${idx + 1}</strong><br>
          <strong>Título:</strong> ${livro.titulo}<br>
          <strong>Tombo:</strong> ${livro.tombo}<br>
          <strong>Autor:</strong> ${livro.autor}<br>
          <strong>Editora:</strong> ${livro.editora}<br>
          <strong>ISBN Falso:</strong> ${livro.isbn_falso}<br>
          <strong>Quantidade:</strong> ${livro.quantidade}<br>
        </div>
        <hr>
      `;
    });
  });
}

 
</script>

