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
      <main class="max-w-xs md:max-w-md flex flex-col gap-4 items-center p-4 bg-white rounded-md shadow-md">
        <header class="flex gap-2 items-center">
            <h1 class="text-2xl md:text-4xl font-semibold text-primary">Biblio<span class="text-secondary">etec</span></h1>
            <span class="w-[2px] h-6 md:h-8  bg-secondary"></span>
            <h1 class="text-2xl md:text-4xl font-regular text-secondary">Login Dev</span></h1>
        </header>

        <div class="formsContainer">
            <form action="validations/validacao.php" method="post" class="flex flex-col gap-2">
                <div class="input-group w-full flex flex-col gap-1">
                    <label for="email" id="labelUsername" class="placeholder">Email</label>
                    <input type="email" name="email" id="email" placeholder="" required>
                </div>

                <div class="input-group flex flex-col gap-1">    
                    <label for="password" id="labelPassword" class="placeholder">Senha</label>
                    <input type="password" name="password" id="password" placeholder="" required>
                </div>

                <input type="submit" name="conectar" value="Entrar" class="w-full h-12 rounded shadow-sm bg-secondary text-2xl text-white font-semibold">
            </form>
              
              <div class="containerApoio">
                  <div class="primeiroAcesso">
                      <button id="primeiroAcessoBtn">Primeiro Acesso</button>
                      <!-- Modal de Primeiro Acesso -->
                      <div id="loginModalPrimeiroAcesso" class="modal">
                          <div class="modal-content">
                              <span class="close">&times;</span>
                              <h2>Valide o acesso</h2>
                              <form id="autorizedPrimeiroAcesso" class="flex flex-col gap-2">

                                  <div class="flex flex-col gap-1">
                                    <label for="loginPrimeiroAcesso">Email:</label>
                                    <input type="text" id="loginPrimeiroAcesso" name="email" required>
                                  </div>

                                  <div class="flex flex-col gap-1">
                                    <label for="senhaPrimeiroAcesso">Senha:</label>
                                    <input type="password" id="senhaPrimeiroAcesso" name="password" required>
                                  </div>
                                   
                                  <button type="submit" class="h-12 rounded shadow-sm bg-secondary text-2xl text-white font-semibold">Entrar</button>


                                </form>
                          </div>
                      </div>
                  </div>  

                 <div class="recuperarsenha">
                    <button><a href="validations_codigos/validar_cod_aut_recuperar_senha.php">Recuperar Senha</a></button>
                 </div>
              </div>
              

              <p>
                  <?php
                  if (isset($_SESSION['msg'])) {
                      echo $_SESSION['msg'];
                      unset($_SESSION['msg']);
                  }
                  ?>
              </p>
          </div>

      </main>

      <script src="Script/form.js"></script>
      <script src="Script/popup.js"></script>
      <script>
      
      </script>
  </body>
  </html>
