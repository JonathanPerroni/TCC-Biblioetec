<?php
session_start();
include_once("../../../../conexao.php");
date_default_timezone_set('America/Sao_Paulo');


// Recupera os dados das escolas e puxando o nome da escola
$sql = "SELECT codigo_escola, unidadeEscola FROM dados_etec"; // substitua 'dados_etec' pelo nome da sua tabela
$result = $conn->query($sql);

$dadosEtec = [];
if ($result && $result->num_rows > 0) {
    // Adiciona cada código e nome ao array
    while ($row = $result->fetch_assoc()) {
        $dadosEtec[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Primeiro Acesso</title>
    <link rel="stylesheet" href="../../../../style/defaults.css">
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
            font-size: 20px
        }

        .validacao{
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


        .message{
    padding: 10px 60px 19px 10px ;         
    position: fixed;
    text-align: center;
    top: 20px;
    right: 105px;
    border-left : 8px solid var(--dark-grey);
    box-shadow:  -5px 0px 0px 0px #8B0000; /*Borda interna vermelha vinho */    
   
    background-color: #fff;    
    border-radius: 5px;
    color: green; 
    font-weight: 600;   
    animation: slidein 0.5s cubic-bezier(0.25, 0.46, 0.45,0.94) both;
   
}

@keyframes slidein {
    0%{
        transform: translateX(1000px);
        opacity: 0;
    }

    100%{
        transform: translateX(0);
        opacity: 1;
        
    }

}


.message::before{
    content: "";
    position: absolute;
    left: 0;
    bottom: 0;
    width: 0;
    height: 5px;   
    background-color: green; 
    animation: time 5s forwards;

}

@keyframes time {
    0%{
        width:0;
    }

    100%{
        width:  100%;
    }
    
}
.message-content {
  display: flex;
  align-items: center;
}
.spacer {
  width: 10px; /* Ajuste o valor para o espaço desejado */
  
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


                    .validacao {
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
              <h1 id="cad-title">Cadastro Admin</h1>
          </header>
        <div class="validacao">
            
            <form action="validacao_cad_admin.php" method="post">
            
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
                       
                        <input type="password" name="confirma_password" id="confirma_password" placeholder="" class="<?php echo !empty($_SESSION['errors']['confirma_password']) ? 'input-error' : ''; ?>" required>
                        <label for="confirma_password" class="placeholder">Senha:</label>
                    </div>
                    
                  
                </div>

                <div class="form-row">
                    <div class="input-container">
                       
                        <input type="text" name="celular" id="celular" placeholder="" value="<?php echo isset($_SESSION['values']['celular']) ? htmlspecialchars($_SESSION['values']['celular']) : ''; ?>" required>
                        <label for="celular" class="placeholder">Celular:</label>
                    </div>

                    <div class="input-container">                      
                      <input type="text" name="telefone" id="telefone" placeholder="" class="<?php echo !empty($_SESSION['errors']['telefone']) ? 'input-error' : ''; ?>" value="<?php echo isset($_SESSION['values']['telefone']) ? htmlspecialchars($_SESSION['values']['telefone']) : ''; ?>">
                      <label for="telefone" class="placeholder">Telefone:</label>
                   </div>


                  
                
                </div>
                <div class="form-row">
                <div class="input-container">
                       
                       <input type="text" name="acesso" id="acesso" placeholder="" value="<?php echo isset($_SESSION['values']['acesso']) ? htmlspecialchars($_SESSION['values']['acesso']) : ''; ?>" required>
                       <label for="acesso" class="placeholder">Acesso:</label>
                   </div>

                    <div class="input-container">
                       
                    <input type="text" id="codigo_escola" name="codigo_escola" list="codigos" class="input-list" placeholder="Código Etec" required>
                        <datalist id="codigos" name="codigo_escola">
                            <?php
                            foreach ($dadosEtec as $escola) {
                                $codigo = $escola['codigo_escola'];
                                $nome = $escola['unidadeEscola'];

                                echo "<option value=\"$codigo - $nome\">$codigo - $nome</option>";
                            }
                            ?>
                        </datalist>
                    </div>
                    
                  
                    </div>

                            <div class="alert" id="alert">                                
                            </div>          


            <div class="container-btn">
                <input type="submit" value="Enviar" class="button"><br>
                
                 <a href="../../../pagedev.php"  class="voltar">Voltar</a>
                
               
                </div>
                
                <div class="erros-notification">
                <span class="error-message"><?php echo isset($_SESSION['errors']['nome']) ? $_SESSION['errors']['nome'] : ''; ?></span> <br>
                <span class="error-message"><?php echo isset($_SESSION['errors']['cpf']) ? $_SESSION['errors']['cpf'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['confirma_email']) ? $_SESSION['errors']['confirma_email'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['password']) ? $_SESSION['errors']['password'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['telefone']) ? $_SESSION['errors']['telefone'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['confirma_password']) ? $_SESSION['errors']['confirma_password'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['codigo_escola']) ? $_SESSION['errors']['codigo_escola'] : ''; ?></span>
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

    <script src="cadAdmin.js"></script>
</body>
</html>