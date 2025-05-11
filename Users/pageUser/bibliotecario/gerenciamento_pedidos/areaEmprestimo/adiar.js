document.addEventListener("DOMContentLoaded", function () {
    // Abrir modal
    document.querySelectorAll(".btn-abrir-modal").forEach(button => {
      button.addEventListener("click", () => {
        const modalId = button.getAttribute("data-modal-id");
        const modal = document.getElementById(modalId);
        if (modal) {
          modal.classList.remove("hidden");
        }
      });
    });
  
    // Fechar modal
    document.addEventListener("click", function (e) {
      if (e.target.classList.contains("btn-fechar-modal")) {
        const modal = e.target.closest(".modal");
        if (modal) {
          modal.classList.add("hidden");
        }
      }
    });
  });


  // Abrir o Modal de Confirmação da devolucao
document.querySelector('.btn-realizar-devolucao').addEventListener('click', function() {
    document.getElementById('modalConfirmarDevolucao').classList.remove('hidden');
});

// Fechar o Modal
function fecharModal() {
    document.getElementById('modalConfirmarDevolucao').classList.add('hidden');
}