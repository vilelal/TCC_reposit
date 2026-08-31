<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="/TCC_reposit/TCC_reposit/CSS/styleCad.css">
</head>

<body>

<div class="container">
    <div class="lado-laranja">
        <h1>Bem vindo!</h1>
            <p>Já tem uma conta? 
            Faça o login! </p>
             <button class="btnLogar">Logar</button>
    </div>

    <div class="cadastro">
           <h1>Cadastro</h1>

             <form action="?route=cadastro" method="post">
        <input type="email" name="email_user" placeholder="nome@exemplo.com" required>
        <input type="text" name="senha_user" placeholder="Digite sua senha" required>
        <input type="text" name="nome_user" placeholder="Digite seu nome" required>
        <input type="tel" name="telefone_user" placeholder="Digite seu telefone"> <!-- Preencher no formato de telefone automaticamente **FAZER   -->
        <input type="text" name="cpf_user" placeholder="Digite seu CPF"> <!-- Preencher no formato de cpf automaticamente **FAZER   -->
        <h3> Endereço </h3>
        <input type="text" id="cep" name="cep_user" placeholder="Digite seu CEP" maxlength="8" pattern="\d{8}">
        <input type="text" name="rua_user" id="rua" placeholder="Digite sua rua">
        <input type="text" name="cidade_user" id="cidade" placeholder="Digite a sua cidade">
        <input type="text" name="numero_user" placeholder="Digite o numero">

        <button type="submit"> cadastrar </button>
    </form>
    </div>
</div>
</body>

<script>
    document.getElementById('cep').addEventListener('blur', function() {
        const cep = document.getElementById("cep").value;
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(dados => {
                if (!dados.erro) {
                    document.getElementById("rua").value = dados.logradouro;
                    document.getElementById("cidade").value = dados.localidade;
                } else {
                    alert("CEP não encontrado!");
                }
            })
            .catch(() => alert("Erro ao buscar o CEP na API."));
    });
</script>

</html>