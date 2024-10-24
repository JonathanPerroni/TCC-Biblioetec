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
      <link rel="stylesheet" href="../Dev/DevCss/defaults.css">    
      <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="w-screen h-screen flex flex-col items-center justify-center bg-[var(--off-white)]">
        <main class="max-w-xs sm:max-w-md flex flex-col gap-4 items-center pb-2 sm:pb-2 p-4 sm:p-8 bg-white rounded-md shadow-md">
            <header class="flex gap-2 items-center">
                <h1 class="text-2xl sm:text-4xl font-semibold text-primary">Biblio<span class="text-secondary">etec</span></h1>
                <span class="w-[2px] h-6 sm:h-8  bg-secondary"></span>
                <h1 class="text-2xl sm:text-4xl font-regular text-secondary">Login Dev</h1>
            </header>

            <form action= "DevValidation/validacao.php" method="post" class="flex flex-col items-center gap-4 min-w-full">
                <div class="input-group min-w-full flex flex-col">
                    <label for="email" id="labelUsername" class="text-secondary font-medium">Email</label>
                    <input type="email" name="email" id="email" placeholder="Insira seu email" required class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
                </div>

                <div class="input-group min-w-full flex flex-col">    
                    <label for="password" id="labelPassword" class="text-secondary font-medium">Senha</label>
                    <input type="password" name="password" id="password" placeholder="Insira sua senha" required class="border-2 border-[var(--secondary)] rounded text-secondary placeholder:text-[var(--grey)]]">
                    <a href="./DevValidation/validar_cod_aut_recuperar_senha.php" class="text-secondary text-sm text-right underline">Esqueci minha senha</a>
                </div>

                <div class="input-group min-w-full flex flex-col gap-1">
                    <input type="submit" name="conectar" value="Entrar" class="w-full h-12 rounded shadow-sm bg-secondary text-2xl text-white font-semibold cursor-pointer">
                   
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

      
      <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
      <script>
        const primeiroAcesso = document.getElementById("primeiroAcessoBtn");

        primeiroAcesso.addEventListener('click', event => {
            event.preventDefault();
        });
      </script>
  </body>
  </html>
