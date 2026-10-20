<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/StyleCadInicial.css">
</head>

<body>
    <div class="container">

        <!-- LADO LARANJA -->
        <aside class="lado-laranja">
            <div class="bemvindo">Bem-vindo!</div>
            <p>Já tem uma conta? Faça o login!</p>
            <div class="botoes">
                <a href="?route=login-form" class="btnLogar">Login</a>
            </div>
        </aside>

        <!-- LADO DIREITO -->
        <main class="lado-direito">
            <div class="cadastro">

                <div class="cadastro-topo">
                    <h1>Cadastro</h1>
                    <img class="logo" src="app/css/img/logo.png" alt="Logo">
                </div>

                <!-- Indicador de etapas -->
                <div class="progresso" aria-live="polite">
                    <div class="progresso-barras">
                        <span class="ativa" id="barra1"></span>
                        <span id="barra2"></span>
                    </div>
                    <p class="progresso-texto" id="textoEtapa">Etapa 1 de 2 · Seus dados</p>
                </div>

                <form action="?route=cadastro" method="post" id="meuForm">

                    <!-- ETAPA 1 - DADOS PESSOAIS -->
                    <div class="etapa1" id="etapa1">
                        <div class="grade">
                            <div class="campo-login campo-full">
                                <label for="email_user">E-mail</label>
                                <input type="email" id="email_user" name="email_user" placeholder="Digite seu e-mail" autocomplete="email" required>
                            </div>

                            <div class="campo-login campo-full">
                                <label for="password_user">Senha</label>
                                <input type="password" id="password_user" name="senha_user" placeholder="Digite sua senha" autocomplete="new-password" required>
                            </div>

                            <div class="campo-login campo-full">
                                <label for="nome_user">Nome completo</label>
                                <input type="text" id="nome_user" name="nome_user" placeholder="Digite seu nome completo" autocomplete="name" required>
                            </div>

                            <div class="campo-login">
                                <label for="telefone_user">Telefone</label>
                                <input type="tel" id="telefone_user" name="telefone_user" placeholder="(00) 00000-0000" autocomplete="tel" maxlength="15" required>
                            </div>

                            <div class="campo-login">
                                <label for="cpf_user">CPF</label>
                                <input type="text" id="cpf_user" name="cpf_user" placeholder="000.000.000-00" inputmode="numeric" maxlength="14" required>
                            </div>
                        </div>

                        <div class="botoes">
                            <a href="?route=home" class="btnWhite">Voltar</a>
                            <button type="button" class="btnYellow" id="btnAvancar">Avançar</button>
                        </div>
                    </div>

                    <!-- ETAPA 2 - ENDEREÇO -->
                    <div class="etapa2" id="etapa2">
                        <div class="grade">
                            <div class="campo-login">
                                <label for="cep">CEP</label>
                                <input type="text" id="cep" name="cep_user" placeholder="Somente números" maxlength="8" pattern="[0-9]{8}" inputmode="numeric" autocomplete="postal-code" required>
                            </div>

                            <div class="campo-login">
                                <label for="cidade">Cidade</label>
                                <input type="text" id="cidade" name="cidade_user" placeholder="Digite sua cidade" required>
                            </div>

                            <div class="campo-login">
                                <label for="rua">Rua</label>
                                <input type="text" id="rua" name="rua_user" placeholder="Digite sua rua" required>
                            </div>

                            <div class="campo-login">
                                <label for="numero">Número</label>
                                <input type="text" id="numero" name="numero_user" placeholder="Digite o número" required>
                            </div>
                        </div>

                        <div class="botoes">
                            <button type="button" class="btnWhite" id="btnVoltar">Voltar</button>
                            <button type="submit" class="btnYellow">Cadastrar</button>
                        </div>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <script>
        const etapa1 = document.getElementById("etapa1");
        const etapa2 = document.getElementById("etapa2");
        const btnAvancar = document.getElementById("btnAvancar");
        const btnVoltar = document.getElementById("btnVoltar");
        const barra2 = document.getElementById("barra2");
        const textoEtapa = document.getElementById("textoEtapa");

        // Mostra a etapa 1 ou 2 e atualiza o indicador
        function mostrarEtapa(n) {
            etapa1.style.display = n === 1 ? "block" : "none";
            etapa2.style.display = n === 2 ? "block" : "none";
            barra2.classList.toggle("ativa", n === 2);
            textoEtapa.textContent = n === 1 ? "Etapa 1 de 2 · Seus dados" : "Etapa 2 de 2 · Endereço";
            window.scrollTo({ top: 0, behavior: "smooth" });
        }

        // Avançar: só passa se todos os campos da etapa 1 forem válidos
        btnAvancar.addEventListener("click", function () {
            const campos = etapa1.querySelectorAll("input[required]");

            for (const campo of campos) {
                if (!campo.checkValidity()) {
                    campo.reportValidity();
                    return;
                }
            }

            mostrarEtapa(2);
        });

        btnVoltar.addEventListener("click", function () {
            mostrarEtapa(1);
        });

        // BUSCAR CEP
        document.getElementById("cep").addEventListener("blur", function () {
            const cep = this.value.replace(/\D/g, "");

            // só consulta quando o CEP tem 8 números
            if (cep.length !== 8) {
                return;
            }

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(dados => {
                    if (dados.erro) {
                        alert("CEP não encontrado!");
                        return;
                    }

                    document.getElementById("rua").value = dados.logradouro;
                    document.getElementById("cidade").value = dados.localidade;
                })
                .catch(() => {});
        });

        // MÁSCARA DE TELEFONE
        document.getElementById("telefone_user").addEventListener("input", function () {
            let valor = this.value.replace(/\D/g, "").substring(0, 11);

            if (valor.length <= 10) {
                valor = valor.replace(/^(\d{2})(\d)/, "($1) $2");
                valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
            } else {
                valor = valor.replace(/^(\d{2})(\d)/, "($1) $2");
                valor = valor.replace(/(\d{5})(\d)/, "$1-$2");
            }

            this.value = valor;
        });

        // MÁSCARA DE CPF
        document.getElementById("cpf_user").addEventListener("input", function () {
            let valor = this.value.replace(/\D/g, "").substring(0, 11);

            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
            valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
            valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

            this.value = valor;
        });
    </script>
</body>

</html>