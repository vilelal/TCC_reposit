<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="app/css/styleLogin.css">
</head>


<body>

    <div class="container">
        <div class="lado-laranja">
            <div class="bemvindo">Bem vindo <br> de volta!</div>

            <p>Não tem uma conta? </p>
            <p>cadastra-se agora </p>
            <button class="btnLogar">Cadastrar-se</button>
        </div>


        <div class="lado-direito">
            <div class="cadastro">
                <h1>Login</h1>
                <div class="dados">


                    <form action="?route=login" method="post">
                        Digite seu E-mail
                        <input type="email" name="email_user" placeholder="nome@exemplo.com" required>
                        Digite sua Senha
                        <input type="password" name="senha_user" placeholder="Digite sua senha" required>

                        <div class="">
                            <button type="submit"> Login </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</body>

</html>