<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
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

    // 2. Variáveis Globais
    const servicos = <?= json_encode($servicos ?? []) ?>;
    const container = document.getElementById("container-servicos");
    const containerSelecionados = document.getElementById("container-selecionados");
    const servicosSelecionados = new Set();

    // 3. Função que atualiza a lista visual de Selecionados e os inputs do formulário
    function renderizarSelecionados() {
        if (servicosSelecionados.size === 0) {
            containerSelecionados.innerHTML = "<p>Nenhum serviço selecionado.</p>";
            return;
        }

        const html = Array.from(servicosSelecionados).map(id => {
            // Busca o objeto completo do serviço para pegar o nome correto
            const servicoObj = servicos.find(s => String(s.PK_id_TB_servico) === String(id));
            const nome = servicoObj ? servicoObj.nome_TB_servico : `Serviço #${id}`;

            return `
                <div class="item-servico-selecionado">
                    <!-- Input enviado no submit -->
                    <input type="hidden" name="servicos[]" value="${id}">
                    <span>${nome}</span>
                    <!-- Botão para remover direto da lista -->
                    <button type="button" class="btn-remover" data-id="${id}">X</button>
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

    // 5. Remove o serviço ao clicar da lista de selecionados
    containerSelecionados.addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("btn-remover")) {
            const idParaRemover = e.target.getAttribute("data-id");

            // Remove do Set
            servicosSelecionados.delete(idParaRemover);

            // Se o checkbox do serviço estiver visível na categoria atual, desmarca ele
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
            container.innerHTML = "<p>Nenhum serviço disponível para esta categoria.</p>";
            return;
        }

        const htmlServicos = servicosFiltrados.map(servico => {
            const id = String(servico.PK_id_TB_servico);
            const marcado = servicosSelecionados.has(id) ? "checked" : "";

            return `
                <div class="item-servico">
                    <input type="checkbox" 
                           class="chk-servico" 
                           value="${id}" 
                           id="srv-${id}" 
                           ${marcado}>
                    <label for="srv-${id}">${servico.nome_TB_servico}</label>
                </div>`;
        }).join("");

        container.innerHTML = htmlServicos;
    });
</script>

</html>