document.addEventListener('DOMContentLoaded', function() {
    const codigoEscolaInput = document.getElementById('codigo_escola');
    const codigosDatalist = document.getElementById('codigos');

    codigoEscolaInput.addEventListener('input', function() {
        const codigoSelecionado = this.value;
        
        if (codigoSelecionado) {
            // Encontra a opção correspondente ao código selecionado
            const options = codigosDatalist.querySelectorAll('option');
            for (const option of options) {
                if (option.value.startsWith(codigoSelecionado)) {
                    // Preenche o campo de nome da escola
                    nomeEscolaInput.value = option.textContent.split(' - ')[1];
                    return;
                }
            }
            // Se não encontrar, limpa o campo de nome
            nomeEscolaInput.value = '';
        } else {
            // Limpa o campo de nome se nenhum código for digitado
            nomeEscolaInput.value = '';
        }
    });
});

