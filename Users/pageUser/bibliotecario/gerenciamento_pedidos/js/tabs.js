document.addEventListener("DOMContentLoaded", function () {
    const tabButtons = document.querySelectorAll(".tab-button");
    const tabContent = document.getElementById("tab-content");
  
    function loadTab(tab) {
      tabContent.innerHTML = "<p>Carregando...</p>";
  
      fetch(tab + ".php")
        .then(response => {
          if (!response.ok) {
            throw new Error("Erro ao carregar a aba");
          }
          return response.text();
        })
        .then(data => {
          tabContent.innerHTML = data;
        })
        .catch(error => {
          tabContent.innerHTML = "<p>Erro ao carregar o conteúdo.</p>";
          console.error(error);
        });
    }
  
    // Carrega a primeira aba por padrão
    loadTab("lista_emprestimo");
  
    tabButtons.forEach(button => {
      button.addEventListener("click", () => {
        // Remove classe ativa de todos os botões
        tabButtons.forEach(btn => btn.classList.remove("active"));
  
        // Adiciona a classe ativa ao botão clicado
        button.classList.add("active");
  
        const tab = button.getAttribute("data-tab");
        loadTab(tab);
      });
    });
  });
  