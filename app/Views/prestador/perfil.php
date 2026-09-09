<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="app/css/styleCad_prest.css">
    <link rel="stylesheet" href="app/css/perfil-prestador.css">
</head>

<body>
    <?php
    if (!isset($user)) $user = [];
    ?>

    <div class="container">
        <div class="cadastro">
            <h3>Dados Pessoais</h3>
            <form action="?route=cadastro-prestador" method="post">
                <img class="logo" src="app/css/img/logo.png" alt="">
                <?php
                if (isset($_SESSION["id"])) {
                    $user = UserModel::getClientById($_SESSION["id"]);
                    $cripto = new CriptoController();
                    $user["cpf_TB_cliente"] = $cripto::decrypt($user["cpf_TB_cliente"]);
                    $user["tel_TB_cliente"] = $cripto::decrypt($user["tel_TB_cliente"]);
                } else {
    
                    echo '<input type="email" name="email_user" placeholder="Digite seu email" required>';
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
    
                <h3>Serviços</h3>
                <select name="categorias" id="categorias">
                    <option value=""> Tipo de serviço </option>
                    <?php
                    if (isset($categorias)) {
                        foreach ($categorias as $cat) {
                            echo "<option value='{$cat["PK_id_TB_categoria"]}'>{$cat['nome_TB_categoria']}</option>";
                        }
                    }
                    ?>
                </select>
                <!-- div com servicos por categoria -->
                <div class="container-servicos" id="container-servicos"></div> <br>
                <!-- div com serviços selecionados -->
                <div class="container-selecionados" id="container-selecionados"></div>
                <button type="submit"> cadastrar </button>
            </form>
        </div>
    </div>
</body>

</html>