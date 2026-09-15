<?php
// Certifique-se de que a sessão foi iniciada caso ainda não tenha sido no index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app/css/header.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <title>FastService</title>
</head>
<body>
    <header class="header"> 
        <div class="logo_area">
            <a href="?route=">
                <img src="app/css/img/logo.png" alt="Logo FastService" class="logo">
            </a>
            <h2 class="nome_empresa">FastService</h2>
        </div>

        <nav class="cabecalho">
            <div class="separar">
                <a href="?route=funcionamento" style="font-size: clamp(0.8rem, 1.07vw, 3.5rem);" class="funcionamento">Funcionamento</a>
                <a href="?route=solicitar-servico" style="font-size: clamp(0.8rem, 1.07vw, 3.5rem);" class="servico">Serviços</a>
            </div>

            <?php if (isset($_SESSION["id"])): ?>
                <!-- USUÁRIO LOGADO -->
                
                <?php if ($_SESSION["tipo"] !== "prestador"): ?>
                   
                    <a href="?route=prestador-form" class="btn-profissional">Seja um Profissional</a>
                    
                <?php endif; ?>

                
                <a href="?route=chat">
                    <img src="app/css/img/chat.png" alt="Chat" class="chat">
                </a>

                <!-- Saudação e Logout -->
               
                <a href="?route=logout" class="btn-login">Sair</a>

            <?php else: ?>
                <!-- USUÁRIO NÃO LOGA -->
                <a href="?route=prestador-form" class="btn-profissional">Seja um Profissional</a>
                <a href="?route=login-form" class="btn-login">Login</a>
            <?php endif; ?>
        </nav>
    </header>
</body>
</html>