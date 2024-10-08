  <?php
  session_start();
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Document</title>

      <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.css" rel="stylesheet" />
      <link rel="stylesheet" href="./User_etec/css/defaults.css">
      <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="w-screen h-screen flex flex-col items-center justify-center bg-[var(--off-white)]">
        <main class="max-w-xs md:max-w-md flex flex-col gap-4 items-center p-4 md:p-8 bg-white rounded-md shadow-md">
            <header class="flex gap-2 items-center">
                <h1 class="text-2xl md:text-4xl font-semibold text-primary">Biblio<span class="text-secondary">etec</span></h1>
                <span class="w-[2px] h-6 md:h-8  bg-secondary"></span>
                <h1 class="text-2xl md:text-4xl font-regular text-secondary">Login Dev</h1>
            </header>

            <form action="validations/validacao.php" method="post" class="flex flex-col items-center gap-4 min-w-full">
                <div class="input-group min-w-full flex flex-col">
                    <label for="email" id="labelUsername" class="text-secondary font-medium">Email</label>
                    <input type="email" name="email" id="email" placeholder="Insira seu email" required class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
                </div>

                <div class="input-group min-w-full flex flex-col">    
                    <label for="password" id="labelPassword" class="text-secondary font-medium">Senha</label>
                    <input type="password" name="password" id="password" placeholder="Insira sua senha" required class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
                    <a href="validations_codigos/validar_cod_aut_recuperar_senha.php" class="text-secondary text-sm text-right underline">Esqueci minha senha</a>
                </div>

                <div class="input-group min-w-full flex flex-col gap-1">
                    <input type="submit" name="conectar" value="Entrar" class="w-full h-12 rounded shadow-sm bg-secondary text-2xl text-white font-semibold cursor-pointer">
                    <button id="primeiroAcessoBtn" class="text-[var(--secondary-emphasis)] text-sm underline" data-modal-target="modal-primeiro-acesso" data-modal-toggle="modal-primeiro-acesso">Primeiro Acesso</button>
                </div>
            </form>
            <p>
                <?php
                if (isset($_SESSION['msg'])) {
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                }
                ?>
            </p>
        </main>

        <div id="modal-primeiro-acesso" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="bg-white rounded-md shadow-md min-w-80 px-8 py-4 flex flex-col gap-8">
                <div class="flex flex-col">
                    <button class="w-full text-center text-xs text-[var(--primary-emphasis)] underline" data-modal-hide="modal-primeiro-acesso">Sair</button>
                    <h2 class="text-primary font-semibold text-2xl text-center text-nowrap">Valide o acesso</h2>
                </div>
                <form id="autorizedPrimeiroAcesso" class="flex flex-col gap-8">
                    <div class="flex flex-col gap-1">
                        <label for="loginPrimeiroAcesso" class="text-secondary font-medium">Email:</label>
                        <input type="text" id="loginPrimeiroAcesso" name="email" required class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="senhaPrimeiroAcesso" class="text-secondary font-medium">Senha:</label>
                        <input type="password" id="senhaPrimeiroAcesso" name="password" required class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
                    </div>
                
                    <button type="submit" class="h-12 rounded shadow-sm bg-secondary text-2xl text-white font-semibold">Entrar</button>
                </form>
            </div>
        </div>

      <script src="Script/form.js"></script>
      <script src="Script/popup.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
      <script>
        const primeiroAcesso = document.getElementById("primeiroAcessoBtn");

        primeiroAcesso.addEventListener('click', event => {
            event.preventDefault();
        });
      </script>
  </body>
  </html>
