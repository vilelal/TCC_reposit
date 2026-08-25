<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>

<body>
    <?php
    if (isset($_SESSION["id"])) {
        echo $_SESSION["nome"];
    }

    ?>
    <form action="?route=cadastro" method="post">
        <input type="email" name="email_user" placeholder="nome@exemplo.com" required>
        <input type="password" name="senha_user" placeholder="Digite sua senha" required>
        <input type="text" name="nome_user" placeholder="Digite seu nome" required>
        <input type="tel" name="telefone_user" placeholder="Digite seu telefone (Apenas números)" maxlength="11">

        <!-- Aceita 11 dígitos se digitar puro, ou 14 se usar máscara/pontos -->
        <input type="text" name="cpf_user" placeholder="Digite seu CPF" maxlength="14" required>

        <h3> Endereço </h3>
        <input type="text" id="cep" name="cep_user" placeholder="Digite seu CEP" maxlength="9" required>
        <input type="text" name="rua_user" id="rua" placeholder="Digite sua rua" required>
        <input type="text" name="cidade_user" id="cidade" placeholder="Digite a sua cidade" required>
        <input type="text" name="numero_user" placeholder="Digite o numero" required>

        <button type="submit"> Cadastrar </button>
    </form>

    <script>
        document.getElementById('cep').addEventListener('blur', function() {
            // Remove traços ou espaços antes de enviar para a API ViaCEP
            const cep = document.getElementById("cep").value.replace(/\D/g, '');

            if (cep.length !== 8) {
                return;
            }

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
</body>

</html>