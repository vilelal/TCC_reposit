<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>

    <link rel="stylesheet" href="app/css/StyleCadInicial.css">
</head>

<body>

    <div class="container">

        <!-- LADO LARANJA -->
        <div class="lado-laranja">

            <div class="bemvindo">
                Bem vindo!
            </div>

            <p>Já tem uma conta?</p>
            <p>Faça o login!</p>

            <div class="botoes">
                <a href="?route=login-form" class="btnLogar">
                    Login
                </a>
            </div>

        </div>

                <div class="img"><img src="app/css/img/logo.png" alt="Logo"></div>


        <!-- LADO DIREITO -->
        <div class="lado-direito">

            <div class="cadastro">

                <h1>Cadastro</h1>

                <div class="dados">

                    <form action="?route=cadastro" method="post" id="meuForm">

                        <!-- ========================= -->
                        <!-- ETAPA 1 - DADOS PESSOAIS -->
                        <!-- ========================= -->

                        <div class="etapa1" id="etapa1">

                            <div class="campo-login">
                                <label for="email_user">
                                    Digite seu E-mail
                                </label>

                                <input type="email" id="email_user" name="email_user" required>
                            </div>


                            <div class="campo-login">
                                <label for="password_user">
                                    Digite sua senha
                                </label>

                                <input type="password" id="password_user" name="senha_user" required>
                            </div>


                            <div class="campo-login">
                                <label for="nome_user">
                                    Digite seu nome completo
                                </label>

                                <input type="text" id="nome_user" name="nome_user" required>
                            </div>


                            <div class="campo-login">
                                <label for="telefone_user">
                                    Digite seu telefone
                                </label>

                                <input type="tel" id="telefone_user" name="telefone_user" placeholder="(00) 00000-0000"
                                    required>
                            </div>


                            <div class="campo-login">
                                <label for="cpf_user">
                                    Digite seu CPF
                                </label>

                                <input type="text" id="cpf_user" name="cpf_user" placeholder="000.000.000-00" required>
                            </div>


                            <!-- BOTÕES DA ETAPA 1 -->

                            <div class="botoes">

                                <button type="button" class="btnYellow" id="btnAvancar">
                                    Avançar
                                </button>

                                <a href="?route=home" class="btnWhite">
                                    Voltar
                                </a>

                            </div>

                        </div>


                        <!-- ========================= -->
                        <!-- ETAPA 2 - ENDEREÇO -->
                        <!-- ========================= -->

                        <div class="etapa2" id="etapa2">




                            <div class="campo-login">

                                <label for="cep">
                                    CEP
                                </label>

                                <input type="text" id="cep" name="cep_user" placeholder="00000000" maxlength="8"
                                    pattern="[0-9]{8}" required>

                            </div>


                            <div class="campo-login">

                                <label for="rua">
                                    Rua
                                </label>

                                <input type="text" id="rua" name="rua_user" placeholder="Digite sua rua" required>

                            </div>


                            <div class="campo-login">

                                <label for="cidade">
                                    Cidade
                                </label>

                                <input type="text" id="cidade" name="cidade_user" placeholder="Digite sua cidade"
                                    required>

                            </div>


                            <div class="campo-login">

                                <label for="numero">
                                    Número
                                </label>

                                <input type="text" id="numero" name="numero_user" placeholder="Digite o número"
                                    required>

                            </div>


                            <!-- BOTÃO DE ENVIAR -->

                            <div class="botoes">

                                <button type="submit" class="btnYellow">
                                    Cadastrar
                                </button>

                                <button type="button" class="btnWhite" id="btnVoltar">
                                    Voltar
                                </button>
                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>


    <!-- ========================= -->
    <!-- JAVASCRIPT -->
    <!-- ========================= -->

    <script>

        // Pegando os elementos das duas etapas
        const etapa1 = document.getElementById("etapa1");
        const etapa2 = document.getElementById("etapa2");

        // Pegando o botão Avançar
        const btnAvancar = document.getElementById("btnAvancar");


        // Quando clicar em Avançar
        btnAvancar.addEventListener("click", function () {

            // Verifica se os campos obrigatórios da etapa 1 estão preenchidos
            const camposEtapa1 = etapa1.querySelectorAll("input[required]");

            let preenchido = true;

            camposEtapa1.forEach(function (campo) {

                if (!campo.value.trim()) {

                    preenchido = false;

                    campo.reportValidity();

                }

            });


            // Se algum campo estiver vazio, não avança
            if (!preenchido) {
                return;
            }


            // Esconde a etapa 1
            etapa1.style.display = "none";

            // Mostra a etapa 2
            etapa2.style.display = "block";
            

        });

btnVoltar.addEventListener("click", function () {
    etapa2.style.display = "none";
    etapa1.style.display = "block";
});



        // =========================
        // BUSCAR CEP
        // =========================

        document.getElementById("cep").addEventListener("blur", function () {

            const cep = this.value.replace(/\D/g, "");


            // Verifica se o CEP possui 8 números


            // Consulta o ViaCEP
            fetch(`https://viacep.com.br/ws/${cep}/json/`)

                .then(response => response.json())

                .then(dados => {

                    // Se o CEP não existir
                    if (dados.erro) {

                        alert("CEP não encontrado!");

                        return;
                    }


                    // Preenche automaticamente
                    document.getElementById("rua").value =
                        dados.logradouro;

                    document.getElementById("cidade").value =
                        dados.localidade;

                })

                .catch(erro => {
                });

        });

    </script>

        <!-- TELEFONE E CPF -->


    <script>
        // MÁSCARA DE TELEFONE
document.getElementById("telefone_user").addEventListener("input", function () {
    let valor = this.value.replace(/\D/g, "");

    if (valor.length > 11) {
        valor = valor.substring(0, 11);
    }

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
    let valor = this.value.replace(/\D/g, "");

    if (valor.length > 11) {
        valor = valor.substring(0, 11);
    }

    valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
    valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
    valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

    this.value = valor;
});
    </script>



</body>

</html>
