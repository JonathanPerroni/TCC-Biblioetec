<?php
session_start();
include_once("../conexao.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Primeiro Acesso</title>
    <link rel="stylesheet" href="../style/defaults.css">
    <style>
        
        body{
            height: 90vh;
            justify-content: center;
            align-items: center;
        }

        main{
    max-width: 700px;   
    width: 100%;
    height: auto;
    padding: 16px;
    border: none;
    border-radius: 16px;
    box-shadow: 0px 4px 8px rgba(0,0,0,0.25 );
    background-color: white;
    display: flex;
    flex-direction: column;

}
header{
    display: flex;
    justify-content: center;
    flex-wrap: wrap;    
    text-align: center;
}

h1{
    font-size: 25px;
}

.separation-line{
    height: 40px;
    width: 2px;
    margin: 0px 16px;
    background-color: var(--off-white);
    color: var(--off-white);
}

#brand-title{
    color:  var(--primary-emphasis);
    font-weight: 600;   
    width: 200px;
}

#cad-title{
    color: var(--off-black);
    font-weight: 400;   
    width: 200px;
}

.PrimeiroAcesso{
    margin-top: 10px;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    
    
}

form{
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;    
    justify-content:  center;
    align-items: center;  
    
}

.form-row{
    
    display: flex;
    flex-direction: row;
    justify-content: space-around ;
    align-items: center;
    margin-bottom: 10px;
    width: 100%;
    min-width: 100%;
    gap: 16px;
   
}





        .input-container {
            margin-bottom: 15px;
            position: relative;
            flex-grow: 1;   
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            
        }
        .input-container label {
            display: block;
            margin-bottom: 5px;
        }
        .input-container input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .input-error {
            border: 1px solid red;
        }
        .input-success {
            border: 1px solid green;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
        }

        input{
    outline: none;
    border: 1px solid;
    width: 100%;
    height: 32px;
    padding: 0 8px;
    border-radius: 8px;
    border: 2px solid rgba(0, 0, 0, 0.5);
    font-size: 16px;
    font-weight: 500;
    color: var(--secondary-emphasis);
    caret-color: var(--secondary-emphasis);
    background-color: var(--off-white);
    font-family: 'Poppins', sans-serif;
    
}

input:focus{
    border: 2px solid var(--secondary-emphasis);
    transition: ease-in .1s;
}


.placeholder{
    position: absolute;
    top: 4px;
    left: 8px;
    color: var(--secondary-emphasis);
    font-weight: 500;    
    transition: .3s;
    pointer-events: none;
}

input:focus + .placeholder,
input:not(:placeholder-shown) + .placeholder {
    font-size: 12px;
    top: -16px;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    appearance: textfield;
}

input[type="submit"]{
    color: white;
    background-color: var(--secondary-emphasis);
    cursor: pointer;
    transition: ease-in-out .2s;
    margin-bottom: 5px;
}
input[type="submit"]:hover{
    scale: 1.025;
    background-color: #3B6603;
}

.container-btn{
    
    width: 100%;
    display: flex;
    flex-direction: row;
    gap: 0 5px;
}
a{  
    
    color: white;
    background-color: var(--secondary-emphasis);
    cursor: pointer;
    transition: ease-in-out .2s;
    margin-bottom: 5px;
    width: 100%;
    border-radius: 8px;
    border: 2px solid rgba(0, 0, 0, 0.5);
    text-decoration: none;
    text-align: center;
    
    display:flex;
    justify-content: center;
    align-items: center;
    font-weight: 600;
    
}




a:hover{
    scale: 1.025;
    background-color: #B20000;
}

.erros-notification{
    display: flex;
    flex-direction: row;
    width: 100%;
    height: auto;
    gap:  5px  10px;
    justify-content:  center;
    align-items: center; 
    
}

@media (max-width:465px) {
    body{
    display:flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    }
    
    main{
        display: flex;
        align-items: center;
       
    }

    header{
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        text-align: center;
        width: 200px;
        height:auto;
    }

    .separation-line{
        height: 2px;
        width: 100%;
        margin: 0px 16px;
        background-color: var(--off-white);
        color: var(--off-white);
    }


    .PrimeiroAcesso {
        display:flex;
        flex-direction: column;

    }

    form{
        display: flex;
        flex-wrap: wrap;
        flex-direction: column;

    }
    .form-row{
        display: flex;
        flex-wrap: wrap;
        flex-direction: column;
    }

    .container-btn{
   
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0 5px;
}

.erros-notification{
    display: flex;
    flex-direction: column;
    width: 100%;
    height: auto;
    gap:  5px  10px;
    justify-content:  center;
    align-items: center; 
    
}
}

    </style>
</head>
<body>
    <main class="container"> 
        
        <header>   
             
   
              <h1 id="brand-title">Biblietec</h1>
              <span class="separation-line"></span>
              <h1 id="cad-title">Cadastro Dev</h1>
          </header>
        <div class="PrimeiroAcesso">
            
            <form action="processarPrimeiroAcesso.php" method="post">
            
            <div class="form-row">
                    <div class="input-container">                        
                        <input type="text" name="nome" id="nome" placeholder="" class="<?php echo !empty($_SESSION['errors']['nome']) ? 'input-error' : ''; ?>" value="<?php echo isset($_SESSION['values']['nome']) ? htmlspecialchars($_SESSION['values']['nome']) : ''; ?>" required>
                        <label for="nome"  class="placeholder">Nome:</label>
                     
                    </div>
                    
                    <div class="input-container">
                       
                        <input type="text" name="cpf" id="cpf" placeholder="" class="<?php echo !empty($_SESSION['errors']['cpf']) ? 'input-error' : ''; ?>" value="<?php echo isset($_SESSION['values']['cpf']) ? htmlspecialchars($_SESSION['values']['cpf']) : ''; ?>" required>
                        <label for="cpf" class="placeholder">CPF:</label>
                       
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-container">
                        
                        <input type="email" name="email" id="email" placeholder="" class="<?php echo !empty($_SESSION['errors']['email']) ? 'input-error' : ''; ?>" value="<?php echo isset($_SESSION['values']['email']) ? htmlspecialchars($_SESSION['values']['email']) : ''; ?>" required>
                        <label for="email" class="placeholder">Email:</label> 
                      
                    </div>
                    
                    <div class="input-container">
                      
                        <input type="email" name="confirma_email" placeholder="" id="confirma_email" class="<?php echo !empty($_SESSION['errors']['confirma_email']) ? 'input-error' : ''; ?>" value="<?php echo isset($_SESSION['values']['confirma_email']) ? htmlspecialchars($_SESSION['values']['confirma_email']) : ''; ?>" required>
                        <label for="confirma_email" class="placeholder">Confirmação de Email:</label>
                       
                    </div>
                </div>

                <div class="form-row">
                    <div class="input-container">
                       
                        <input type="password" name="password" id="password" placeholder="" class="<?php echo !empty($_SESSION['errors']['password']) ? 'input-error' : ''; ?>" required>
                        <label for="password" class="placeholder">Senha:</label>
                                     </div>
                    
                    <div class="input-container">
                      
                        <input type="text" name="telefone" id="telefone" placeholder="" class="<?php echo !empty($_SESSION['errors']['telefone']) ? 'input-error' : ''; ?>" value="<?php echo isset($_SESSION['values']['telefone']) ? htmlspecialchars($_SESSION['values']['telefone']) : ''; ?>">
                        <label for="telefone" class="placeholder">Telefone:</label>
                                     </div>
                </div>

                <div class="form-row">
                    <div class="input-container">
                       
                        <input type="text" name="celular" id="celular" placeholder="" value="<?php echo isset($_SESSION['values']['celular']) ? htmlspecialchars($_SESSION['values']['celular']) : ''; ?>" required>
                        <label for="celular" class="placeholder">Celular:</label>
                    </div>
                    
                    <div class="input-container">
                       
                        <input type="text" name="acesso" id="acesso" placeholder="" value="<?php echo isset($_SESSION['values']['acesso']) ? htmlspecialchars($_SESSION['values']['acesso']) : ''; ?>" required>
                        <label for="acesso" class="placeholder">Acesso:</label>
                    </div>
                
                </div>

                <div class="container-btn">
                <input type="submit" value="Enviar"><br>
                
                 <a href="logout.php"  class="voltar">Voltar</a>
                
               
                </div>
                
                <div class="erros-notification">
                <span class="error-message"><?php echo isset($_SESSION['errors']['nome']) ? $_SESSION['errors']['nome'] : ''; ?></span> <br>
                <span class="error-message"><?php echo isset($_SESSION['errors']['cpf']) ? $_SESSION['errors']['cpf'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['confirma_email']) ? $_SESSION['errors']['confirma_email'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['password']) ? $_SESSION['errors']['password'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['telefone']) ? $_SESSION['errors']['telefone'] : ''; ?></span>
                </div>
            </form>
        </div>
    </main>

    <?php
    if (isset($_SESSION['msg'])) {
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
    }
    // Limpar valores e erros após exibir
    unset($_SESSION['values']);
    unset($_SESSION['errors']);
    ?>
</body>
</html>