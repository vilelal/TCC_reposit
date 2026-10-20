<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de prestador</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/styleCad_prest.css">
</head>

<body>
    <div class="container">

        <!-- Painel de boas-vindas -->
        <aside class="lado-laranja">
            <h1 class="h1">Bem-vindo!</h1>
            <p>Já tem uma conta? Faça o login!</p>
                          <a href="?route=login-form" class="btnLogar">
                    Login
                </a>
        </aside>

        <!-- Formulário -->
        <main class="cadastro">
            <div class="cadastro-topo">
                <div>
                    <h1>Cadastro</h1>
                    <p class="cadastro-sub">Preencha seus dados e escolha os serviços que você oferece.</p>
                </div>
                <img class="logo" src="app/css/img/logo.png" alt="Logo">
            </div>

            <form action="?route=cadastro-prestador" method="post">
                <?php
                $logado = isset($_SESSION["id"]);
                if ($logado) {
                    $user = UserModel::getClientById($_SESSION["id"]);
                    $cripto = new CriptoController();
                    $user["cpf_TB_cliente"] = $cripto::decrypt($user["cpf_TB_cliente"]);
                    $user["tel_TB_cliente"] = $cripto::decrypt($user["tel_TB_cliente"]);
                }
                ?>

                <?php if (!$logado): ?>
                    <section class="secao">
                        <h3>Acesso</h3>
                        <div class="grade">
                            <div class="campo">
                                <label for="email_user">E-mail</label>
                                <input type="email" id="email_user" name="email_user" placeholder="Digite seu e-mail" autocomplete="email" required>
                            </div>
                            <div class="campo">
                                <label for="senha_user">Senha</label>
                                <input type="password" id="senha_user" name="senha_user" placeholder="Digite sua senha" autocomplete="new-password" required>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="secao">
                    <h3>Dados pessoais</h3>
                    <div class="grade">
                        <div class="campo campo-full">
                            <label for="nome_user">Nome</label>
                            <input type="text" id="nome_user" name="nome_user" placeholder="Digite seu nome" autocomplete="name" required
                                value="<?= htmlspecialchars($user["nome_TB_cliente"] ?? "") ?>">
                        </div>
                        <div class="campo">
                            <label for="tel_user">Telefone</label>
                            <input type="tel" id="tel_user" name="tel_user" placeholder="(00) 00000-0000" autocomplete="tel" maxlength="15"
                                value="<?= htmlspecialchars($user["tel_TB_cliente"] ?? "") ?>">
                        </div>
                        <div class="campo">
                            <label for="cpf_cnpj_user">CPF ou CNPJ</label>
                            <input type="text" id="cpf_cnpj_user" name="cpf_cnpj_user" placeholder="Digite seu CPF ou CNPJ" inputmode="numeric" maxlength="18"
                                value="<?= htmlspecialchars($user["cpf_TB_cliente"] ?? "") ?>">
                        </div>
                        <div class="campo campo-full">
                            <label for="bio_user">Sobre mim</label>
                            <textarea id="bio_user" name="bio_user" placeholder="Conte um pouco sobre você e sua experiência" rows="3"></textarea>
                        </div>
                    </div>
                </section>

                <section class="secao">
                    <h3>Endereço</h3>
                    <div class="grade">
                        <div class="campo">
                            <label for="cep">CEP</label>
                            <input type="text" id="cep" name="cep_user" placeholder="Somente números" maxlength="8" pattern="\d{8}" inputmode="numeric" autocomplete="postal-code"
                                value="<?= htmlspecialchars($user["cep_TB_cliente"] ?? "") ?>">
                        </div>
                        <div class="campo">
                            <label for="cidade">Cidade</label>
                            <input type="text" id="cidade" name="cidade_user" placeholder="Digite a sua cidade"
                                value="<?= htmlspecialchars($user["cidade_TB_cliente"] ?? "") ?>">
                        </div>
                        <div class="campo">
                            <label for="rua">Rua</label>
                            <input type="text" id="rua" name="rua_user" placeholder="Digite sua rua"
                                value="<?= htmlspecialchars($user["rua_TB_cliente"] ?? "") ?>">
                        </div>
                        <div class="campo">
                            <label for="numero_user">Número</label>
                            <input type="text" id="numero_user" name="numero_user" placeholder="Digite o número"
                                value="<?= htmlspecialchars($user["numeroCasa_TB_cliente"] ?? "") ?>">
                        </div>
                    </div>
                </section>

                <section class="secao">
                    <h3>Serviços</h3>
                    <div class="campo">
                        <label for="categorias">Categoria</label>
                        <select name="categorias" id="categorias">
                            <option value="">Tipo de serviço</option>
                            <?php
                            if (isset($categorias)) {
                                foreach ($categorias as $cat) {
                                    echo "<option value='{$cat["PK_id_TB_categoria"]}'>{$cat['nome_TB_categoria']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- serviços da categoria escolhida -->
                    <div class="container-servicos" id="container-servicos"></div>

                    <!-- serviços já selecionados -->
                    <p class="selecionados-titulo">Serviços selecionados</p>
                    <div class="container-selecionados" id="container-selecionados"></div>
                </section>

                <button type="submit">Cadastrar</button>
            </form>
        </main>
    </div>

    <script>
        // 1. Busca de CEP
        document.getElementById('cep').addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
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

        // 1.1 Máscaras automáticas de telefone e CPF/CNPJ
        function mascaraTelefone(v) {
            v = v.replace(/\D/g, '').slice(0, 11);
            if (v.length > 10) return v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            if (v.length > 6) return v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            if (v.length > 2) return v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            return v.length ? '(' + v : v;
        }

        function mascaraCpfCnpj(v) {
            v = v.replace(/\D/g, '').slice(0, 14);
            if (v.length <= 11) {
                return v
                    .replace(/^(\d{3})(\d)/, '$1.$2')
                    .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
                    .replace(/\.(\d{3})(\d)/, '.$1-$2');
            }
            return v
                .replace(/^(\d{2})(\d)/, '$1.$2')
                .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                .replace(/\.(\d{3})(\d)/, '.$1/$2')
                .replace(/(\d{4})(\d)/, '$1-$2');
        }

        const inputTel = document.getElementById('tel_user');
        const inputDoc = document.getElementById('cpf_cnpj_user');

        inputTel.addEventListener('input', () => inputTel.value = mascaraTelefone(inputTel.value));
        inputDoc.addEventListener('input', () => inputDoc.value = mascaraCpfCnpj(inputDoc.value));

        // aplica a máscara nos valores que já vêm preenchidos
        inputTel.value = mascaraTelefone(inputTel.value);
        inputDoc.value = mascaraCpfCnpj(inputDoc.value);

        // 2. Variáveis Globais
        const servicos = <?= json_encode($servicos ?? []) ?>;
        const container = document.getElementById("container-servicos");
        const containerSelecionados = document.getElementById("container-selecionados");
        const servicosSelecionados = new Set();

        // 3. Atualiza a lista visual de Selecionados e os inputs do formulário
        function renderizarSelecionados() {
            if (servicosSelecionados.size === 0) {
                containerSelecionados.innerHTML = '<p class="vazio">Nenhum serviço selecionado.</p>';
                return;
            }

            const html = Array.from(servicosSelecionados).map(id => {
                const servicoObj = servicos.find(s => String(s.PK_id_TB_servico) === String(id));
                const nome = servicoObj ? servicoObj.nome_TB_servico : `Serviço #${id}`;

                return `
                <div class="item-servico-selecionado">
                    <input type="hidden" name="servicos[]" value="${id}">
                    <span>${nome}</span>
                    <button type="button" class="btn-remover" data-id="${id}" aria-label="Remover ${nome}">✕</button>
                </div>`;
            }).join("");

            containerSelecionados.innerHTML = html;
        }

        // 4. Marca / Desmarca a partir do Checkbox da Categoria
        container.addEventListener("change", function(e) {
            if (e.target && e.target.classList.contains("chk-servico")) {
                const idServico = String(e.target.value);

                if (e.target.checked) {
                    servicosSelecionados.add(idServico);
                } else {
                    servicosSelecionados.delete(idServico);
                }

                renderizarSelecionados();
            }
        });

        // 5. Remove o serviço ao clicar na lista de selecionados
        containerSelecionados.addEventListener("click", function(e) {
            const btn = e.target.closest(".btn-remover");
            if (btn) {
                const idParaRemover = btn.getAttribute("data-id");
                servicosSelecionados.delete(idParaRemover);

                const chkVisivel = document.getElementById(`srv-${idParaRemover}`);
                if (chkVisivel) {
                    chkVisivel.checked = false;
                }

                renderizarSelecionados();
            }
        });

        // 6. Atualiza a lista da categoria no <select>
        document.getElementById("categorias").addEventListener("change", function() {
            const categoria = this.value;

            if (!categoria) {
                container.innerHTML = "";
                return;
            }

            const servicosFiltrados = servicos.filter(s => String(s.FK_id_TB_categoria) === String(categoria));

            if (servicosFiltrados.length === 0) {
                container.innerHTML = '<p class="vazio">Nenhum serviço disponível para esta categoria.</p>';
                return;
            }

            container.innerHTML = servicosFiltrados.map(servico => {
                const id = String(servico.PK_id_TB_servico);
                const marcado = servicosSelecionados.has(id) ? "checked" : "";

                return `
                <div class="item-servico">
                    <input type="checkbox" class="chk-servico" value="${id}" id="srv-${id}" ${marcado}>
                    <label for="srv-${id}">${servico.nome_TB_servico}</label>
                </div>`;
            }).join("");
        });

        // estado inicial da lista de selecionados
        renderizarSelecionados();
    </script>
</body>

</html>