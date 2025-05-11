document.addEventListener("DOMContentLoaded", function () {
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContent = document.getElementById("tab-content");
  const searchContainer = document.getElementById("search-container");

  function loadTab(tab) {
      tabContent.innerHTML = "<p>Carregando...</p>";

      fetch("areaEmprestimo/" + tab + ".php")
          .then(response => {
              if (!response.ok) {
                  throw new Error("Erro ao carregar a aba");
              }
              return response.text();
          })
          .then(data => {
              tabContent.innerHTML = data;

              // Mostrar campo de pesquisa apenas na aba de empréstimos
              if (tab === "lista_emprestimo") {
                  searchContainer?.classList.remove("hidden");
              } else {
                  searchContainer?.classList.add("hidden");
              }
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
          tabButtons.forEach(btn => btn.classList.remove("active"));
          button.classList.add("active");

          const tab = button.getAttribute("data-tab");
          loadTab(tab);
      });
  });
});
