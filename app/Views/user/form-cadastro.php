<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="app/css/styleCadastro.css">
</head>

<body>

    <div class="container">
        <div class="lado-laranja">
            <div class="bemvindo">Bem vindo!</div>

            <p>Já tem uma conta? </p>
            <p>Faça o login! </p>
            <button class="btnLogar">Logar</button>
        </div>

    <div class="img"><img src="app/css/img/logo.png" alt="Logo"></div>        

        <div class="lado-direito">
            <div class="cadastro">
                
                <h1>Cadastro</h1>
                <div class="dados">
                    <form action="?route=cadastro" method="post">
<div class="campo">
    <label for="email_user">Digite seu E-mail</label>
    <input type="email" id="email_user" name="email_user" required>
</div>

<div class="campo">
    <label for="senha_user">Digite sua senha</label>
    <input type="password" id="senha_user" name="senha_user" required>
</div>

<div class="campo">
    <label for="nome_user">Digite seu nome completo</label>
    <input type="text" id="nome_user" name="nome_user" required>
</div>

<div class="campo">
    <label for="telefone_user">Digite seu telefone</label>
    <input type="tel" id="telefone_user" name="telefone_user">
</div>

<div class="campo">
    <label for="cpf_user">Digite seu CPF</label>
    <input type="text" id="cpf_user" name="cpf_user">
</div>

                        <div class="endereco">
                            <h3> Endereço </h3>
                            <input type="text" id="cep" name="cep_user" placeholder="Digite seu CEP" maxlength="8" pattern="\d{8}">
                            <input type="text" name="rua_user" id="rua" placeholder="Digite sua rua">
                            <input type="text" name="cidade_user" id="cidade" placeholder="Digite a sua cidade">
                            <input type="text" name="numero_user" placeholder="Digite o numero">
                        </div>

                        <div class="botoes">
                            <button class="btn-cadastrar">Cadastrar</button>
                            <button class="btn-avancar">Avançar</button>
                            <button class="btn-voltar">Voltar</button>

                        </div>
                    </form>
                </div>
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
                }
            })
            .catch(() => {});
    });
</script>

</html>