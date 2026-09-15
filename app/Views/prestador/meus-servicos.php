<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app/css/dashboard.css">
        <link rel="stylesheet" href="app/css/meus-servicos.css">
    <title>Serviços</title>
</head>

<body>


<div class="menu">
            <div class="conteudo">
                <div class="titulo">
              <a href="?route=">
                <img src="app/css/img/logo.png" alt="" class="logo">
                </a>
        <h1 class="nome_empresa">FastService</h1> </div>
       
                 <a class="menu-a" href="?route=dashboard"><img class="casa" src="app/css/img_dashboard/icone-sino2.png" alt=""><h2>Inicio</h2></a>
                  <a class="menu-a" href="?route=dashboard"><img src="app/css/img_dashboard/icone-traco.png"  alt=""><h2>Relatório</h2></a>
                <a class="menu-a" href="?route=lista-servicos"><img src="app/css/img_dashboard/icone-traco2.png"  alt=""><h2>Serviços</h2></a>
                <a class="menu-a" href="?route=perfil"><img src="" alt=""><h2>Perfil</h2></a>
                </div>
        </div>
    <div class="area-direita">
       <header class="header">
        <!--d
-->
            <div class="header-right">
                <a href="">
                    <img src="app/css/img/chat.png" alt="" class="chat">
                </a>
                <a href="?route=notificacoes">
                    <img src="app/css/img_dashboard/icone-sino.png" alt="" class="chat">
                </a>
                <img src="" alt="">
                <?php
                    if (isset($_SESSION["id"])) {
                        echo "<h3 class='user-name'>{$_SESSION['nome']}</h3>";
                    }
                ?>
            </div><img class="verificado"src="app/css/img_dashboard/icone-check.png" alt="">
        </header>

   
        
                <h4><form  class="form" action="?route=edit-servicos" method="post">
                    <h2 class="h2-titulo">Editar meus serviços</h2>
                    <br>
                    <h4 class="h4-esq">Selecione o tipo de serviço que você quer prestar:</h4>
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
                    <br><br>
                    <!-- div com servicos por categoria -->
                     <h4 class="h4-esq">Marque os serviços que voce gostaria de prestar: </h4>
                     <br>
                    <div class="container-servicos" id="container-servicos"></div> <br><br>
                    <!-- div com serviços selecionados -->
                     <h4>Caso não queira mais prestar um serviço: (Desmarque)</h4><br>
                    <div class="container-selecionados" id="container-selecionados"></div>
                    <button type="submit" class="btn"><h3> Atualizar</h3> </button>
                </form></h4>
            </div>



    
</body>


<script>
    // 2. Variáveis Globais
    const servicos = <?= json_encode($servicos ?? []) ?>;
    const servicosPrestador = <?= json_encode($servicosSelecionados ?? []) ?>;
    const container = document.getElementById("container-servicos");
    const containerSelecionados = document.getElementById("container-selecionados");

    const servicosId = servicosPrestador.map(servico => String(servico.PK_id_TB_servico));
    const servicosSelecionados = new Set(servicosId);

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
    
    renderizarSelecionados();
</script>

</html>