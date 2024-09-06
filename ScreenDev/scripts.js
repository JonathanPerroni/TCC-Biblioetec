// JS para o dropdown na sidebar
document.addEventListener('DOMContentLoaded', function () {
    const dropdowns = document.querySelectorAll('.dropdown');
    const sidebar = document.querySelector('.sidebar');

    // Alternar a visibilidade do dropdown ao clicar
    dropdowns.forEach(dropdown => {
        dropdown.querySelector('.dropdown-toggle').addEventListener('click', function (e) {
            e.preventDefault();
            const menu = dropdown.querySelector('.dropdown-menu');
            const isActive = dropdown.classList.contains('active');
            // Alternar a classe 'active' no dropdown
            dropdowns.forEach(d => d.classList.remove('active')); // Fecha todos os dropdowns
            if (!isActive) {
                dropdown.classList.add('active'); // Abre o dropdown clicado
            }
        });
    });

    // Fechar dropdown quando clicar fora
    document.addEventListener('click', function (e) {
        if (!sidebar.contains(e.target)) {
            dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
    });

    // Fechar dropdown quando a sidebar for fechada
    sidebar.addEventListener('mouseleave', function () {
        dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
    });
});
