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



        <div class="lado-direito">
            <div class="cadastro">
                <h1>Cadastro</h1>
                <div class="dados">
                    <form action="?route=cadastro" method="post">
                        Digite seu E-mail
                        <input type="email" name="email_user" placeholder="" required>
                        Digite sua senha
                        <input type="text" name="senha_user" placeholder="" required>
                        Digite seu nome completo
                        <input type="text" name="nome_user" placeholder="" required>
                        Digite seu telefone
                        <input type="tel" name="telefone_user" placeholder=""> <!-- Preencher no formato de telefone automaticamente **FAZER   -->
                        Digite seu CPF
                        <input type="text" name="cpf_user" placeholder=""> <!-- Preencher no formato de cpf automaticamente **FAZER   -->

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