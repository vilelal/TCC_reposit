<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="app/css/edit-perfil.css">
</head>

<body>
    <?php
    if (!isset($user)) $user = [];
    ?>

    <div class="container">
        <div class="cadastro">
            <h3>Dados Pessoais</h3>
            <form action="?route=editPerfil" method="post">
                <img class="logo" src="app/css/img/logo.png" alt="">
                <input type="text" name="nome_user" placeholder="Digite seu nome" required
                    value="<?= htmlspecialchars($user["nome_TB_cliente"] ?? $user["nome_TB_prestador"]) ?>">
                <input type="tel" name="tel_user" placeholder="Digite seu telefone"
                    value="<?= htmlspecialchars($user["tel_TB_cliente"] ?? $user["tel_TB_prestador"]) ?>">
                <!-- Preencher no formato de telefone automaticamente **FAZER   -->
                <input type="text" name="cpf_user" placeholder="CPF ou CNPJ"
                    value="<?= htmlspecialchars($user["cpf_TB_cliente"] ?? $user["cpf_cnpj_TB_prestador"]) ?>">
                <!-- Preencher no formato de cpf automaticamente **FAZER   -->
                 <?php if ($_SESSION["tipo"] == "prestador"): ?>
                 <textarea type="text" name="bio_user" placeholder="Sobre mim" rows="5" cols="40"><?= htmlspecialchars($user["bio_TB_prestador"] ?? "") ?></textarea>
                 <?php endif; ?>
                <h3 style="margin-bottom: 2%;"> Endereço </h3>
                <input type="text" id="cep" name="cep_user" placeholder="Digite seu CEP" maxlength="8" pattern="\d{8}"
                    value="<?= htmlspecialchars($user["cep_TB_cliente"] ?? $user["cep_TB_prestadorPerfil"]) ?>">
                <input type="text" name="rua_user" id="rua" placeholder="Digite sua rua"
                    value="<?= htmlspecialchars($user["rua_TB_cliente"] ?? $user["rua_TB_prestadorPerfil"]) ?>">
                <input type="text" name="cidade_user" id="cidade" placeholder="Digite a sua cidade"
                    value="<?= htmlspecialchars($user["cidade_TB_cliente"] ?? $user["cidade_TB_prestadorPerfil"]) ?>">
                <input type="text" name="numero_user" placeholder="Digite o numero"
                    value="<?= htmlspecialchars($user["numeroCasa_TB_cliente"] ?? $user["numeroCasa_TB_prestadorPerfil"]) ?>">
                <button type="submit"> atualizar </button>
            </form>
        </div>
    </div>
</body>

<script>
    // 1. Busca de CEP
    document.getElementById('cep').addEventListener('blur', function() {
        const cep = document.getElementById("cep").value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(dados => {
                    if (!dados.erro) {
                        document.getElementById("rua").value = dados.logradouro;
                        document.getElementById("cidade").value = dados.localidade;
                    }
                })
                .catch(() => {});
        }
    });
</script>

</html>