<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus serviços | FastService</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/css/painel.css">
    <link rel="stylesheet" href="app/css/meus-servicos.css">
</head>

<body>
    <?php
    // Tela do fluxo de perfil: mantém "Perfil" destacado no menu
    $paginaAtiva = 'perfil';
    $paginaTitulo = 'Meus serviços';
    require __DIR__ . '/partials/menu.php';
    ?>

    <main class="conteudo">
        <a class="voltar" href="?route=perfil">
            <svg class="icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 6-6 6 6 6"/></svg>
            Voltar ao perfil
        </a>

        <div class="pagina-cab">
            <h2>Meus serviços</h2>
            <p>Escolha os serviços que você quer prestar. Só eles vão aparecer para os clientes.</p>
        </div>

        <form class="form-servicos" action="?route=edit-servicos" method="post">
            <section class="etapa">
                <h3>Adicionar serviços</h3>

                <label class="rotulo" for="categorias">Categoria</label>
                <select name="categorias" id="categorias">
                    <option value="">Selecione uma categoria</option>
                    <?php
                    if (isset($categorias)) {
                        foreach ($categorias as $cat) {
                            echo "<option value='" . (int) $cat["PK_id_TB_categoria"] . "'>" . htmlspecialchars($cat['nome_TB_categoria']) . "</option>";
                        }
                    }
                    ?>
                </select>

                <p class="rotulo">Serviços da categoria</p>
                <div class="container-servicos" id="container-servicos">
                    <p class="dica">Escolha uma categoria para ver os serviços disponíveis.</p>
                </div>
            </section>

            <section class="etapa etapa-selecionados">
                <h3>Seus serviços <span class="contador" id="contador-selecionados">0</span></h3>
                <p class="dica">Para deixar de prestar um serviço, remova-o da lista.</p>

                <div class="container-selecionados" id="container-selecionados"></div>

                <button type="submit" class="btn-salvar">Salvar alterações</button>
            </section>
        </form>
    </main>
    </div>

    <script>
        const servicos = <?= json_encode($servicos ?? []) ?>;
        const servicosPrestador = <?= json_encode($servicosSelecionados ?? []) ?>;
        const container = document.getElementById("container-servicos");
        const containerSelecionados = document.getElementById("container-selecionados");
        const contador = document.getElementById("contador-selecionados");

        const servicosId = servicosPrestador.map(servico => String(servico.PK_id_TB_servico));
        const servicosSelecionados = new Set(servicosId);

        // Evita que nomes vindos do banco sejam interpretados como HTML
        function esc(texto) {
            const el = document.createElement("span");
            el.textContent = texto;
            return el.innerHTML;
        }

        // Atualiza a lista visual de selecionados e os inputs enviados no submit
        function renderizarSelecionados() {
            contador.textContent = servicosSelecionados.size;

            if (servicosSelecionados.size === 0) {
                containerSelecionados.innerHTML = "<p class='vazio'>Nenhum serviço selecionado.</p>";
                return;
            }

            containerSelecionados.innerHTML = Array.from(servicosSelecionados).map(id => {
                const servicoObj = servicos.find(s => String(s.PK_id_TB_servico) === String(id));
                const nome = servicoObj ? servicoObj.nome_TB_servico : `Serviço #${id}`;

                return `
                <div class="item-servico-selecionado">
                    <input type="hidden" name="servicos[]" value="${esc(id)}">
                    <span>${esc(nome)}</span>
                    <button type="button" class="btn-remover" data-id="${esc(id)}" aria-label="Remover ${esc(nome)}">&times;</button>
                </div>`;
            }).join("");
        }

        // Marca / desmarca a partir dos checkboxes da categoria
        container.addEventListener("change", function (e) {
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

        // Remove ao clicar no X da lista de selecionados
        containerSelecionados.addEventListener("click", function (e) {
            const botao = e.target.closest(".btn-remover");
            if (!botao) return;

            const idParaRemover = botao.getAttribute("data-id");
            servicosSelecionados.delete(idParaRemover);

            // Se o checkbox estiver visível na categoria atual, desmarca também
            const chkVisivel = document.getElementById(`srv-${idParaRemover}`);
            if (chkVisivel) {
                chkVisivel.checked = false;
            }

            renderizarSelecionados();
        });

        // Lista os serviços da categoria escolhida no <select>
        document.getElementById("categorias").addEventListener("change", function () {
            const categoria = this.value;

            if (!categoria) {
                container.innerHTML = "<p class='dica'>Escolha uma categoria para ver os serviços disponíveis.</p>";
                return;
            }

            const servicosFiltrados = servicos.filter(s => String(s.FK_id_TB_categoria) === String(categoria));

            if (servicosFiltrados.length === 0) {
                container.innerHTML = "<p class='dica'>Nenhum serviço disponível para esta categoria.</p>";
                return;
            }

            container.innerHTML = servicosFiltrados.map(servico => {
                const id = String(servico.PK_id_TB_servico);
                const marcado = servicosSelecionados.has(id) ? "checked" : "";

                return `
                <div class="item-servico">
                    <input type="checkbox" class="chk-servico" value="${esc(id)}" id="srv-${esc(id)}" ${marcado}>
                    <label for="srv-${esc(id)}">${esc(servico.nome_TB_servico)}</label>
                </div>`;
            }).join("");
        });

        renderizarSelecionados();
    </script>
</body>

</html>
