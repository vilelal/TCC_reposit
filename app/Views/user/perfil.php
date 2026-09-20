<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="app/css/stylePerfil.css">
    
    <!-- Cropper.js CSS e JS via CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
</head>

<body>

    <?php
    if (!isset($user)) $user = [];
    
    // Pega a foto da tabela de usuários (ou da sessão) adicionando uma query string para evitar cache do navegador
    $fotoUsuario = !empty($user['foto_TB_usuario']) ? $user['foto_TB_usuario'] : (!empty($_SESSION['foto']) ? $_SESSION['foto'] : 'app/css/img/default-user.png');
    $fotoComCacheBuster = $fotoUsuario . '?v=' . time(); 
    ?>

    <div class="container">

        <div>
            <img class="img" src="app/css/img/logo.png" alt="Logo">
        </div>

        <div class="txt">
            <h1>Meu perfil</h1>
        </div>

        <!-- Área do Perfil com Hover -->
        <div class="foto-container">
            <label for="inputFoto" class="foto-label">
                <img src="<?= $fotoComCacheBuster ?>" alt="Foto de Perfil" class="foto-perfil" id="fotoExibicao">
                <div class="foto-overlay">
                    <span>Trocar foto</span>
                </div>
            </label>
            <!-- Input escondido -->
            <input type="file" id="inputFoto" accept="image/*" style="display: none;">
        </div>

        <h3> <?= $user["nome_TB_cliente"] ?? $user["nome_TB_prestador"] ?? $_SESSION['nome'] ?> </h3>
        <a href="?route=edit-perfil">Dados pessoais</a>
        <a href="?route=seguranca">Segurança</a>
        <a href="?route=meus-servicos">Meus serviços</a>

        <?php
        if (isset($_SESSION["tipo"]) && $_SESSION["tipo"] == "prestador") {
            echo "<a href='?route=dashboard'>Voltar</a>";
        } else {
            echo "<a href='?route=home'>Voltar</a>";
        }
        ?>

        <a href="?route=logout">Sair da conta</a>
    </div>

    <!-- MODAL DE CORTE DE IMAGEM -->
    <div id="modalCropper" class="modal-cropper" style="display: none;">
        <div class="modal-content">
            <h3>Ajuste sua Foto</h3>
            <div class="cropper-wrapper">
                <img id="imageToCrop" src="">
            </div>
            <div class="modal-actions">
                <button type="button" id="btnCancelar" class="btn-cancelar">Cancelar</button>
                <button type="button" id="btnSalvarCorte" class="btn-salvar">Cortar e Salvar</button>
            </div>
        </div>
    </div>

    <!-- SCRIPT DE MANIPULAÇÃO DO CROPPER -->
    <script>
        let cropper = null;
        const inputFoto = document.getElementById('inputFoto');
        const modalCropper = document.getElementById('modalCropper');
        const imageToCrop = document.getElementById('imageToCrop');
        const btnCancelar = document.getElementById('btnCancelar');
        const btnSalvarCorte = document.getElementById('btnSalvarCorte');

        // 1. Quando escolhe um arquivo
        // 1. Quando escolhe um arquivo
        inputFoto.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];

                // --- INÍCIO DAS VALIDAÇÕES ---

                // Validação de Tamanho (Exemplo: limite de 5MB)
                const tamanhoMaximoMB = 5; 
                const tamanhoMaximoBytes = tamanhoMaximoMB * 1024 * 1024;
                if (file.size > tamanhoMaximoBytes) {
                    alert(`A imagem é muito pesada! O limite máximo é ${tamanhoMaximoMB}MB.`);
                    inputFoto.value = ''; // Limpa o arquivo inválido
                    return; // Para a execução aqui, não abre o modal
                }

                // Validação de Formato (Extensão)
                const formatosPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!formatosPermitidos.includes(file.type)) {
                    alert('Formato não suportado! Por favor, escolha uma imagem JPG, PNG ou WEBP.');
                    inputFoto.value = ''; // Limpa o arquivo inválido
                    return; // Para a execução aqui, não abre o modal
                }

                // --- FIM DAS VALIDAÇÕES ---

                const reader = new FileReader();
                
                reader.onload = function(e) {
                    imageToCrop.src = e.target.result;
                    modalCropper.style.display = 'flex';

                    if (cropper) {
                        cropper.destroy();
                    }

                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1, // Quadrado 1:1
                        viewMode: 1,
                        background: false,
                        autoCropArea: 0.8
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        // 2. Cancelar o corte
        btnCancelar.addEventListener('click', function() {
            modalCropper.style.display = 'none';
            inputFoto.value = ''; // Limpa o input
            if (cropper) cropper.destroy();
        });

        // 3. Salvar o corte e enviar via Ajax/Fetch
        btnSalvarCorte.addEventListener('click', function() {
            if (!cropper) return;

            // Gera o canvas cortado em resolução ideal (300x300)
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300
            });

            // Converte o canvas para um Blob e envia
            canvas.toBlob(function(blob) {
                const formData = new FormData();
                formData.append('nova_foto', blob, 'perfil.jpg');

                // Envia para o Controller
                fetch('?route=atualizar-foto', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualiza a imagem na página instantaneamente com o cache-buster
                        document.getElementById('fotoExibicao').src = data.caminho + '?v=' + new Date().getTime();
                        modalCropper.style.display = 'none';
                        inputFoto.value = '';
                        if (cropper) cropper.destroy();
                    } else {
                        alert('Erro ao atualizar foto: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Erro no envio da imagem.');
                });
            }, 'image/jpeg', 0.9);
        });
    </script>
</body>

</html>