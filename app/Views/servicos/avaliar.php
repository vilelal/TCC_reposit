<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliação</title>
</head>
<body>
    <form action="?route=avaliar" method="post">
        <input type="hidden" name="user" value="<?= $user ?? "" ?>">
        <input type="number" max="5" name="nota">
        <button type="submit"> avaliar </button>
    </form>
</body>
</html>