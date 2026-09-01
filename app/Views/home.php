<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app/css/style.css">
    <title>home</title>
</head>
<body>
    <section class="hero-section">
    
    <div class="conteiner">
        <br><br><br><br><br><br>
            <h1 class="h1">O profissional certo em poucos cliques. <a href="" class="a">Contrate já</a></h1>
    <p>Segurança e rapidez em encontrar o profissional ideal para suas necessidades.</p>
    <br>
    <br>
    <a href="?route=cadastro-form">cadastro</a>
    <a href="?route=cadastro-servico">cadastro serivco</a>
    <a href="?route=prestador-form">cadastro prestador</a>
    <?php 
    if (isset($_SESSION["id"])) {
        echo "<a href='?route=logout'>logout</a>";
        echo "<h3> Olá {$_SESSION['nome']} </h3>";
    }
    else {
        echo "<a href='?route=login-form'>login</a>";
    }
    ?>

    <form action="" method="POST">
        <input type="text" name="nome" placeholder="Digite o nome do serviço">
        <br>
        <button type="submit">Pesquisar</button>
    </form>
    
    </div>
        <section class="categories-section">
        <h3>Profissionais Por Categoria</h3>
        <div class="categories-grid">
            <div class="category-card">
                <img src="app/css/img/icone-raio.png" alt="Assistência técnica">
                <span><a class="icone" href="">Assistência técnica geral</a></span>
            </div>
            <div class="category-card">
                <img src="app/css/img/icone_ferramentas.png" alt="Pequenos concertos">
                <span><a class="icone" href="">Pequenos Concertos</a></span>
            </div>
            <div class="category-card">
                <img src="app/css/img/icone-rolo.png" alt="Reformas">
                <span><a class="icone" href="">Reformas e Reparos</a></span>
            </div>
            <div class="category-card">
                <img src="app/css/img/icone-engrenagem.png" alt="Serviços gerais">
                <span><a class="icone" href="">Serviços Em Geral</a></span>
            </div>
            <div class="category-card">
                <img src="app/css/img/icone-casa.png" alt="Serviços domésticos">
                <span><a class="icone" href="">Serviços Domésticos</a></span>
            </div>
            <div class="category-card">
                <img src="app/css/img/icone-carro.png" alt="Meio automotivo">
                <span><a class="icone" href="">Meio Automotivo</a></span>
            </div>
        </div>
    </section>

    </section>
<br><br>
<h1 class="sub-h1">Nossos Serviços Populares</h1>
<br>
<!-- Adicione aqui a seção de cards de serviços populares -->
 <!-- 3 cards de serviços populares -->
<div class="popular-services">
    
    <div class="service-card">
        <img src="app/css/img/icone-raio.png" alt="Serviço 1">
        <h4>Serviços de jardinagem</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
    <div class="service-card">
        <img src="app/css/img/icone_ferramentas.png" alt="Serviço 2">
        <h4>Serviços de eletrodomésticos</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
    <div class="service-card">
        <img src="app/css/img/icone-rolo.png" alt="Serviço 3">
        <h4>Serviços de pintura</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
     <div class="service-card">
        <img src="app/css/img/icone-raio.png" alt="Serviço 1">
        <h4>Serviços de pedreiros</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
    <div class="service-card">
        <img src="app/css/img/icone_ferramentas.png" alt="Serviço 2">
        <h4>Serviços de cuidadores</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
    <div class="service-card">
        <img src="app/css/img/icone-rolo.png" alt="Serviço 3">
        <h4>Serviços de garçons</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
</div>

    <br>
    <!--faixa de propaganda-->
    <div class="ad-banner">
        <div class="titulo-ad">
        <h1>Quer ganhar mais renda e visibilidade?</h1>
<!-- comentários -->
<div class="testimonials-container">
    <div class="testimonial-card"><h4><img  src="app/css/img/icone-user.png" alt="">Mario Fernandez <img class ="star" src="app/css/img/icone-estrela.png"  alt=""> 4.9</h4><p class="comentario">Uma das melhores decisões que tomei foi me cadastrar na FastService!! Desde os primeiros dias, os números de
clientes e serviços realizados só vem aumentando mais!!</p>
</div>
    <div class="testimonial-card"><h4><img src="app/css/img/icone-user.png" alt="">Jéssica Silva <img class ="star" src="app/css/img/icone-estrela.png"  alt=""> 4.8</h4><p class="comentario">Sem dúvidas é algo que realmente faz a diferença no meu
serviço! Cada vez mais consigo serviços, queria ter feito cadastro antes. Recomendo muito!!!</p>
</div>
    <div class="testimonial-card"><h4><img class="user" src="app/css/img/icone-user.png" alt="">Cléber Santos <img class ="star" src="app/css/img/icone-estrela.png"  alt=""> 4.7</h4> <p class="comentario">Além de ser uma plataforma que de muita visibilidade, é
interessante como ajuda muito com dados, ela apresenta números de serviços, estatísticas em geral e outras funcões</p></div>
</div>
        <button class="ad-button"><a class="a-card" href=""><h4>Virar profissional da Fast Service</h4></a></button>
    
 </div>
 </div>

 <br>
 <br>
 <h1 class="h1-center"><img src="app/css/img/icone-treco.png" alt=""></h1>
 <p class="p-center">Fast Service é a melhor plataforma de contratação de pequenos serviços do Brasil.Fazemos a conexão de trabalhadores com clientes mais próximos, que solicitam e recebem atendimento de qualidade, rapidez e com segurança.</p>
 <div class="conteiner-card-esp">
    <div class="card-esp">
        <img src="app/css/img/icone-casa.png" alt="">
        <h4>Serviços domésticos</h4>
        <p>Encontre profissionais para serviços domésticos, como limpeza, jardinagem, manutenção e muito mais.</p>

    </div>
 </div>
</body>
</html>