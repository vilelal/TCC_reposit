<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="app/css/styleCad.css">
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
            <form action="?route=cadastro-prestador" method="post">
                <?php
                if (isset($_SESSION["id"])) {
                    $user = UserModel::getClientById($_SESSION["id"]);
                    $cripto = new CriptoController();
                    $user["cpf_TB_cliente"] = $cripto::decrypt($user["cpf_TB_cliente"]);
                    $user["tel_TB_cliente"] = $cripto::decrypt($user["tel_TB_cliente"]);
                } else {
                    echo '<input type="email" name="email_user" placeholder="nome@exemplo.com" required>';
                    echo '<input type="text" name="senha_user" placeholder="Digite sua senha" required>';
                }
                ?>
                <input type="text" name="nome_user" placeholder="Digite seu nome" required
                    value="<?= htmlspecialchars($user["nome_TB_cliente"] ?? "") ?>">
                <input type="tel" name="tel_user" placeholder="Digite seu telefone"
                    value="<?= htmlspecialchars($user["tel_TB_cliente"] ?? "") ?>">
                <!-- Preencher no formato de telefone automaticamente **FAZER   -->
                <input type="text" name="cpf_cnpj_user" placeholder="CPF ou CNPJ"
                    value="<?= htmlspecialchars($user["cpf_TB_cliente"] ?? "") ?>">
                <!-- Preencher no formato de cpf automaticamente **FAZER   -->
                <input type="text" name="bio_user" placeholder="Sobre mim">
                <h3> Endereço </h3>
                <input type="text" id="cep" name="cep_user" placeholder="Digite seu CEP" maxlength="8" pattern="\d{8}"
                    value="<?= htmlspecialchars($user["cep_TB_cliente"] ?? "") ?>">
                <input type="text" name="rua_user" id="rua" placeholder="Digite sua rua"
                    value="<?= htmlspecialchars($user["rua_TB_cliente"] ?? "") ?>">
                <input type="text" name="cidade_user" id="cidade" placeholder="Digite a sua cidade"
                    value="<?= htmlspecialchars($user["cidade_TB_cliente"] ?? "") ?>">
                <input type="text" name="numero_user" placeholder="Digite o numero"
                    value="<?= htmlspecialchars($user["numeroCasa_TB_cliente"] ?? "") ?>">

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
                }
            })
            .catch(() => {});
    });
</script>

</html>