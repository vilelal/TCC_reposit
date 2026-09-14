<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="app/css/stylelog.css">
</head>


<body>

    <div class="container">
        <div class="lado-laranja">
            <div class="bemvindo">Bem vindo <br> de volta!</div>

            <p>Não tem uma conta? </p>
            <p>cadastre-se agora </p>

    <div class="botoes">
    <a href="?route=cadastro-form" class="btnLogar">Cadastrar</a>
    </div>
        </div>


    <div class="img"><img src="app/css/img/logo.png" alt="Logo"></div>
        

        <div class="lado-direito">
            <div class="cadastro">
                <h1>Login</h1>
                <div class="dados">


                    <form action="?route=login" method="post">
                        <div class="campo-login">
                            <label for="email_user">Digite seu E-mail</label>
                            <input id="email_user" type="email" name="email_user" placeholder="" required>
                        </div>
                        <div class="campo-login">
                            <label for="senha_user">Digite sua Senha</label>
                            <input id="senha_user" type="password" name="senha_user" placeholder="" required>
                        </div>

                            <div class="botoes">
                            <button type="submit" class="btnYellow"> Logar </button>
    <a href="?route=home" class="btnWhite">Voltar</a>
</div>
                    </form>
                </div>
            </div>
        </div>
</body>

</html>