<?php
session_start();
include_once("../../../../conexao.php");
date_default_timezone_set('America/Sao_Paulo');
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
            margin-top: 20px;
            height: 100%;
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
        .input-list{
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
              <h1 id="cad-title">Escola </h1>
          </header>
        <div class="validacao">
            
            <form action="validacao_cad_escola.php" method="post">
            
            <div class="form-row">
                    <div class="input-container">                        
                        <input type="text" name="nome_escola" id="nome_escola" placeholder="" class="<?php echo !empty($_SESSION['errors']['nome_escola']) ? 'input-error' : ''; ?>" value="<?php echo isset($_SESSION['values']['nome_escola']) ? htmlspecialchars($_SESSION['values']['nome_escola']) : ''; ?>" required>
                        <label for="nome_escola"  class="placeholder">Nome da Escola:</label>
                     
                    </div>
                    
                    <div class="input-container">
                       
                        <input type="text" name="cnpj" id="cnpj" placeholder="" class="<?php echo !empty($_SESSION['errors']['cnpj']) ? 'input-error' : ''; ?>" value="<?php echo isset($_SESSION['values']['cnpj']) ? htmlspecialchars($_SESSION['values']['cnpj']) : ''; ?>" required>
                        <label for="cnpj" class="placeholder">CNPJ:</label>
                       
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
                       
                        <select name="tipo_escola" id="tipo_escola"  class="input-list" required>
                            <option value="">Escolha o tipo</option>
                            <option value="ensinoMedio">Ensino Medio</option>
                            <option value="tecnico">Ensino Tecnico</option>
                            <option value="ensinoMedioTecnico">Ensino Medio e Tecnico</option>
                            <option value="colegioAgricula">Colegio Agricola</option>
                        </select>
                    </div>
                    <div class="input-container">
                       
                        <input type="text" name="codigo_escola" id="codigo_escola" placeholder="" class="<?php echo !empty($_SESSION['errors']['codigo_escola']) ? 'input-error' : ''; ?>" required>
                        <label for="codigo_escola" class="placeholder">Codigo Escola:</label>
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
                       
                       <input type="text" name="endereco" id="endereco" placeholder="" value="<?php echo isset($_SESSION['values']['endereco']) ? htmlspecialchars($_SESSION['values']['endereco']) : ''; ?>" required>
                       <label for="endereco" class="placeholder">Endereço:</label>
                   </div>

                    <div class="input-container">
                       
                        <input type="text" name="bairro" id="bairro" placeholder="" value="<?php echo isset($_SESSION['values']['bairro']) ? htmlspecialchars($_SESSION['values']['bairro']) : ''; ?>" required>
                        <label for="bairro" class="placeholder">Bairro:</label>
                    </div>
                    
                  
                </div>
                <div class="form-row">
                 <div class="input-container">
                       
                       <input type="text" name="numero" id="numero" placeholder="" value="<?php echo isset($_SESSION['values']['numero']) ? htmlspecialchars($_SESSION['values']['numero']) : ''; ?>" required>
                       <label for="numero" class="placeholder">Nº :</label>
                   </div>

                    <div class="input-container">
                       
                        <input type="text" name="cep" id="cep" placeholder="" value="<?php echo isset($_SESSION['values']['cep']) ? htmlspecialchars($_SESSION['values']['cep']) : ''; ?>" required>
                        <label for="cep" class="placeholder">CEP:</label>
                    </div>
                    
                  
                </div>

                <div class="form-row">
                 <div class="input-container">
                       
                       <input type="text" name="cidade" id="cidade" placeholder="" value="<?php echo isset($_SESSION['values']['cidade']) ? htmlspecialchars($_SESSION['values']['cidade']) : ''; ?>" required>
                       <label for="cidade" class="placeholder">Cidade:</label>
                   </div>

                    <div class="input-container">
                       
                        <select name="estado" id="estado" class="input-list" required>
                            <option value="">Escolha um estado</option>
                            <option value="AC">Acre - AC</option>
                            <option value="AL">Alagoas - AL</option>
                            <option value="AP">Amapá - AP</option>
                            <option value="AM">Amazonas - AM</option>
                            <option value="BA">Bahia - BA</option>
                            <option value="CE">Ceará - CE</option>
                            <option value="DF">Distrito Federal - DF</option>
                            <option value="ES">Espírito Santo - ES</option>
                            <option value="GO">Goiás - GO</option>
                            <option value="MA">Maranhão - MA</option>
                            <option value="MT">Mato Grosso - MT</option>
                            <option value="MS">Mato Grosso do Sul - MS</option>
                            <option value="MG">Minas Gerais - MG</option>
                            <option value="PA">Pará - PA</option>
                            <option value="PB">Paraíba - PB</option>
                            <option value="PR">Paraná - PR</option>
                            <option value="PE">Pernambuco - PE</option>
                            <option value="PI">Piauí - PI</option>
                            <option value="RJ">Rio de Janeiro - RJ</option>
                            <option value="RN">Rio Grande do Norte - RN</option>
                            <option value="RS">Rio Grande do Sul - RS</option>
                            <option value="RO">Rondônia - RO</option>
                            <option value="RR">Roraima - RR</option>
                            <option value="SC">Santa Catarina - SC</option>
                            <option value="SP">São Paulo - SP</option>
                            <option value="SE">Sergipe - SE</option>
                            <option value="TO">Tocantins - TO</option>
                        </select>
                     </div>
                    
                  
                </div>

                <div class="container-btn">
                <input type="submit" value="Enviar"><br>
                
                 <a href="../../../pagedev.php"  class="voltar">Voltar</a>
                
               
                </div>
                
                <div class="erros-notification">
                <span class="error-message"><?php echo isset($_SESSION['errors']['nome_escola']) ? $_SESSION['errors']['nome_escola'] : ''; ?></span> <br>
                <span class="error-message"><?php echo isset($_SESSION['errors']['cnpj']) ? $_SESSION['errors']['cnpj'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['confirma_email']) ? $_SESSION['errors']['confirma_email'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['password']) ? $_SESSION['errors']['password'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['telefone']) ? $_SESSION['errors']['telefone'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['codigo_escola']) ? $_SESSION['errors']['codigo_escola'] : ''; ?></span>
                <span class="error-message"><?php echo isset($_SESSION['errors']['cep']) ? $_SESSION['errors']['cep'] : ''; ?></span>
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