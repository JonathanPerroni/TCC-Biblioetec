    // JavaScript para controlar o pop-up modal de Primeiro Acesso
    var primeiroAcessoModal = document.getElementById("loginModalPrimeiroAcesso");
    var primeiroAcessoBtn = document.getElementById("primeiroAcessoBtn");
    var primeiroAcessoSpan = primeiroAcessoModal.getElementsByClassName("close")[0];

    primeiroAcessoBtn.onclick = function() {
        primeiroAcessoModal.style.display = "block";
    }

    primeiroAcessoSpan.onclick = function() {
        primeiroAcessoModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == primeiroAcessoModal) {
            primeiroAcessoModal.style.display = "none";
        }
    }
    document.getElementById("autorizedPrimeiroAcesso").onsubmit = function(event) {
        event.preventDefault();
        var email = document.getElementById("loginPrimeiroAcesso").value;
        var password = document.getElementById("senhaPrimeiroAcesso").value;
    
        // Faz a validação do login e senha usando Ajax
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "validations/validacaoPopUp.php/", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                if (xhr.responseText.trim() === "success") {
                    window.location.href = "Screen/primeiroAcesso.php";
                } else {
                    alert("Email ou senha incorretos!");
                }
            }
        };
        xhr.send("email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password));
    }

    // JavaScript para controlar o pop-up modal de Esqueceu a senha
    var esqueceuSenhaModal = document.getElementById("loginModalEsqueceuSenha");
    var esqueceuSenhaBtn = document.getElementById("EsqueceuSenhaBtn");
    var esqueceuSenhaSpan = esqueceuSenhaModal.getElementsByClassName("close")[0];

    esqueceuSenhaBtn.onclick = function() {
        esqueceuSenhaModal.style.display = "block";
    }

    esqueceuSenhaSpan.onclick = function() {
        esqueceuSenhaModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == esqueceuSenhaModal) {
            esqueceuSenhaModal.style.display = "none";
        }
    }



    

    document.getElementById("autorizedEsqueceuSenha").onsubmit = function(event) {
        event.preventDefault();
        var email = document.getElementById("loginEsqueceuSenha").value;
        var password = document.getElementById("senhaEsqueceuSenha").value;

        // Faz a validação do login e senha usando Ajax
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "validations/validacaoPopUp.php/", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                if (xhr.responseText === "success") {
                    window.location.href = "Screen/esqueceuSenha.php";
                } else {
                    alert("email ou senha incorretos!");
                }
            }
        };
        xhr.send("email=" + encodeURIComponent(email) + "&password=" + encodeURIComponent(password));
    }