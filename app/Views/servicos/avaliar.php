<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliação - FastService</title>
    <link rel="stylesheet" href="app/css/avaliacao.css">
</head>

<body>
    <div class="container">
        <div class="card-avaliacao">
            <div class="card-header">
                <div class="icone-topo">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <div>
                    <h2>Como foi o serviço?</h2>
                    <p class="subtitulo">Sua opinião ajuda a manter a qualidade e a segurança da FastService.</p>
                </div>
            </div>

            <form action="?route=avaliar" method="post">
                <input type="hidden" name="user" value="<?= htmlspecialchars($userCliente ?? $userPrestador ?? '') ?>">

                <!-- Sistema de estrelas -->
                <div class="rating-group">
                    <label class="label-categoria">SUA NOTA GERAL</label>
                    <div class="stars">
                        <input type="radio" id="star5" name="nota" value="5" required>
                        <label for="star5" title="5 estrelas">★</label>
                        
                        <input type="radio" id="star4" name="nota" value="4">
                        <label for="star4" title="4 estrelas">★</label>
                        
                        <input type="radio" id="star3" name="nota" value="3">
                        <label for="star3" title="3 estrelas">★</label>
                        
                        <input type="radio" id="star2" name="nota" value="2">
                        <label for="star2" title="2 estrelas">★</label>
                        
                        <input type="radio" id="star1" name="nota" value="1">
                        <label for="star1" title="1 estrela">★</label>
                    </div>
                </div>

                <button type="submit" class="btn-avaliar">Enviar Avaliação</button>
            </form>
        </div>

    </div>
</body>

</html>