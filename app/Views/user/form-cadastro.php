<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
</head>
<body>
    <form action="index.php?route=cadastro" method="post">
        <input type="email" name="email_user" placeholder="nome@exemplo.com" required>
        <input type="text" name="senha_user" placeholder="Digite sua senha" required>
        <button type="submit"> cadastrar </button>
    </form>
</body>
</html>