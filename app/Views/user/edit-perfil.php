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
    $ehPrestador = ($_SESSION["tipo"] ?? "") == "prestador";
    ?>

    <main class="container">
        <div class="cadastro">

            <div class="topo">
                <a href="?route=<?= $ehPrestador ? 'dashboard' : 'perfil' ?>" class="btn-voltar">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    Voltar
                </a>

                <img class="logo" src="app/css/img/logo.png" alt="FastService">
            </div>

            <form action="?route=editPerfil" method="post">

                <!-- ==================== DADOS PESSOAIS ==================== -->
                <section class="secao">
                    <h3>Dados Pessoais</h3>

                    <div class="grade">

                        <div class="campo full">
                            <label for="nome_user">Nome</label>
                            <input type="text" id="nome_user" name="nome_user" placeholder="Digite seu nome" required
                                autocomplete="name"
                                value="<?= htmlspecialchars($user["nome_TB_cliente"] ?? $user["nome_TB_prestador"] ?? "") ?>">
                        </div>

                        <div class="campo">
                            <label for="tel_user">Telefone</label>
                            <input type="tel" id="tel_user" name="tel_user" placeholder="Digite seu telefone"
                                autocomplete="tel"
                                value="<?= htmlspecialchars($user["tel_TB_cliente"] ?? $user["tel_TB_prestador"] ?? "") ?>">
                            <!-- Preencher no formato de telefone automaticamente **FAZER   -->
                        </div>

                        <div class="campo">
                            <label for="cpf_user">CPF ou CNPJ</label>
                            <input type="text" id="cpf_user" name="cpf_user" placeholder="CPF ou CNPJ"
                                value="<?= htmlspecialchars($user["cpf_TB_cliente"] ?? $user["cpf_cnpj_TB_prestador"] ?? "") ?>">
                            <!-- Preencher no formato de cpf automaticamente **FAZER   -->
                        </div>

                        <?php if ($ehPrestador): ?>
                            <div class="campo full">
                                <label for="bio_user">Sobre mim</label>
                                <textarea id="bio_user" name="bio_user" placeholder="Conte um pouco sobre você e seu trabalho"
                                    rows="5"><?= htmlspecialchars($user["bio_TB_prestador"] ?? "") ?></textarea>
                            </div>
                        <?php endif; ?>

                    </div>
                </section>

                <!-- ==================== ENDEREÇO ==================== -->
                <section class="secao">
                    <h3>Endereço</h3>

                    <div class="grade">

                        <div class="campo">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep_user" placeholder="Digite seu CEP" maxlength="8"
                                pattern="\d{8}" inputmode="numeric" autocomplete="postal-code"
                                value="<?= htmlspecialchars($user["cep_TB_cliente"] ?? $user["cep_TB_prestadorPerfil"] ?? "") ?>">
                        </div>

                        <div class="campo">
                            <label for="numero_user">Número</label>
                            <input type="text" id="numero_user" name="numero_user" placeholder="Digite o número"
                                value="<?= htmlspecialchars($user["numeroCasa_TB_cliente"] ?? $user["numeroCasa_TB_prestadorPerfil"] ?? "") ?>">
                        </div>

                        <div class="campo full">
                            <label for="rua">Rua</label>
                            <input type="text" name="rua_user" id="rua" placeholder="Digite sua rua"
                                autocomplete="address-line1"
                                value="<?= htmlspecialchars($user["rua_TB_cliente"] ?? $user["rua_TB_prestadorPerfil"] ?? "") ?>">
                        </div>

                        <div class="campo full">
                            <label for="cidade">Cidade</label>
                            <input type="text" name="cidade_user" id="cidade" placeholder="Digite a sua cidade"
                                autocomplete="address-level2"
                                value="<?= htmlspecialchars($user["cidade_TB_cliente"] ?? $user["cidade_TB_prestadorPerfil"] ?? "") ?>">
                        </div>

                    </div>
                </section>

                <button type="submit">Atualizar</button>
            </form>
        </div>
    </main>

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
</body>

</html>