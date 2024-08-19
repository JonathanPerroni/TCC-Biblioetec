  <?php
  session_start();
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Document</title>
      <link rel="stylesheet" href="/biblioetec/Desenvolvedor/style/defaults.css">
      <link rel="stylesheet" href="/biblioetec/Desenvolvedor/style/loginDev.css">
    
  </head>
  <body>
      <main>
          <header>
              <h1 id="brand-title">Biblietec</h1>
              <span class="separation-line"></span>
              <h1 id="login-title">Login Dev</h1>
          </header>

          <div class="formsContainer">
              <form action="validations/validacao.php" method="post">
                  <div class="form-row">
                      <div class="input-group">
                          <input type="email" name="email" id="email" placeholder="" required>
                          <label for="email" id="labelUsername" class="placeholder">Email</label>
                      </div>

                      <div class="input-group">
                          <input type="password" name="password" id="password" placeholder="" required>
                          <label for="password" id="labelPassword" class="placeholder">Senha</label>
                      </div>
                  </div>

                  <div class="form-row">
                      <input type="submit" name="conectar" value="Entrar">
                  </div>

                  

              </form>
              
              <div class="containerApoio">
                  <div class="primeiroAcesso">
                      <button id="primeiroAcessoBtn">Primeiro Acesso</button>
                      <!-- Modal de Primeiro Acesso -->
                      <div id="loginModalPrimeiroAcesso" class="modal">
                          <div class="modal-content">
                              <span class="close">&times;</span>
                              <h2>Valide o acesso</h2>
                              <form id="autorizedPrimeiroAcesso">

                                  <div class="modal-row">
                                  <label for="loginPrimeiroAcesso">Email:</label>
                                  <input type="text" id="loginPrimeiroAcesso" name="email" required><br><br>
                                  </div>

                                  <div class="modal-row">
                                  <label for="senhaPrimeiroAcesso">Senha:</label>
                                  <input type="password" id="senhaPrimeiroAcesso" name="password" required><br><br>
                                  </div>
                                   
                                  <div class="modal-row">
                                  <button type="submit">Entrar</button>
                                  </div>


                                </form>
                          </div>
                      </div>
                  </div>  

                 <div class="recuperarsenha">
                    <a href="validations_codigos/validar_cod_aut_recuperar_senha.php">Recuperar Senha</a>
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
