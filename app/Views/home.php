<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app/css/style.css">
    <title>home</title>
</head>
<body>
    <div class="div-lado">
    <section class="hero-section">
    
    <div class="conteiner">
        <br><br><br><br><br><br>
            <h1 class="h1">O profissional certo em poucos cliques. <br><a href="" class="a">Contrate já</a></h1>
    <p>Segurança e rapidez em encontrar o profissional ideal para suas necessidades.</p>
    <br>
    <br>
    
    <a href="?route=cadastro-servico">cadastro serivco</a>

    <?php
    if (isset($_SESSION["id"])) {
        echo "<a href='?route=logout'>logout</a>";
        echo "<h3> Olá {$_SESSION['nome']} </h3>";

        if ($_SESSION["tipo"] != "prestador") {
            echo '<a href="?route=prestador-form">Torne-se prestador</a>';
            echo '<a href="?route=solicitar-servico">Solicite um serviço</a>';
        } 
    }
    else {
        echo "<a href='?route=login-form'>login</a>";
        echo '<a href="?route=cadastro-form">cadastro</a>';
        echo '<a href="?route=prestador-form">cadastro prestador</a>';
    }

    ?>

    <form action="" method="POST">
        <input type="text" name="nome" placeholder="Digite o nome do serviço">
        <br>
        <button type="submit">Pesquisar</button>
    </form>
    </div>
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
<br><br><br>
<br><br>
<h1 class="sub-h1">Nossos Serviços Populares</h1>
<br>

 <!-- 3 cards de serviços populares -->
<div class="popular-services">
    
    <div class="service-card">
        <img class="img-serv" src="app/css/img_servico/jardinagem.png" alt="Serviço 1">
        <h4>Serviços de jardinagem</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
    <div class="service-card">
        <img class="img-serv" src="app/css/img_servico/eletrodomestico.png" alt="Serviço 2">
        <h4>Serviços de eletrodomésticos</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
    <div class="service-card">
        <img class="img-serv" src="app/css/img_servico/pintura.png" alt="Serviço 3">
        <h4>Serviços de pintura</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
     <div class="service-card">
        <img class="img-serv" src="app/css/img_servico/pedreiro.png" alt="Serviço 1">
        <h4>Serviços de pedreiros</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
    <div class="service-card">
        <img class="img-serv" src="app/css/img_servico/cuidador.png" alt="Serviço 2">
        <h4>Serviços de cuidadores</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
    <div class="service-card">
        <img class="img-serv" src="app/css/img_servico/garcons.png" alt="Serviço 3">
        <h4>Serviços de garçons</h4>
        <button class="card-button"><a class="a-card" href="">fazer orçamento</a></button>
    </div>
</div>
<br><br>
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
             <div class="ad-button-wrapper">
                    <img src="app/css/img/logo.png" alt="" class="button-icon">
                    <a class="ad-button" href="#">Virar Profissional do Fast Service</a>
                </div>
                <div class="testimonial-card"><h4><img class="user" src="app/css/img/icone-user.png" alt="">Cléber Santos <img class ="star" src="app/css/img/icone-estrela.png"  alt=""> 4.7</h4> <p class="comentario">Além de ser uma plataforma que de muita visibilidade, é
            interessante como ajuda muito com dados, ela apresenta números de serviços, estatísticas em geral e outras funcões</p></div>
            </div>
             
      
 </div>
 </div>
<br>
 <br>
 <br>
 <h1 class="h1-center"><img src="app/css/img/icone-treco.png" alt=""></h1>
 <p class="p-center">Fast Service é a melhor plataforma de contratação de pequenos serviços do Brasil.Fazemos a conexão de trabalhadores com clientes mais próximos, que solicitam e recebem atendimento de qualidade, rapidez e com segurança.</p>
 <br><br>
 <div class="conteiner-card-esp">
  <div class="card-esp">
    <img src="app/css/img/icone-escudo.png" alt="" class="card-icon">
    <h4>Confiabilidade</h4>
    <p><strong>Profissionais confiáveis</strong></p>
    <p>Prestadores avaliados e organizados em um catálogo acessível</p>
  </div>
  <div class="card-esp">
    <img src="app/css/img/icone-estrela-azul.png" alt="" class="card-icon">
    <h4>Qualidade</h4>
    <p><strong>Avaliações reais</strong></p>
    <p>Consulte feedbacks antes de contratar</p>
  </div>
  <div class="card-esp">
    <img src="app/css/img/icone-raio-azul.png" alt="" class="card-icon">
    <h4>Rapidez</h4>
    <p><strong>Contratação simplificada</strong></p>
    <p>Pesquise, compare e encontre serviços rapidamente</p>
  </div>
</div>
<br><br><br><br><br><br><br>
<footer class="footer">
  <div class="footer-content">
    <!-- 1 seção -->
    <section class="footer-logo">
      <img src="app/css/img/logo.png" alt="FastService Logo">
    </section>

    <!-- 2 seção -->
    <section class="footer-section links-section">
      <h3>Acesso rápido</h3>
      <ul>
        <li><a href="#">Categorias</a></li>
        <li><a href="#">Sobre nós</a></li>
        <li><a href="#">Central de ajuda</a></li>
      </ul>
    </section>

    <!-- 3 seção-->
    <section class="footer-section info-section">
      <p class="footer-p">
        Somos a solução para o mercado digital, disponibilizando uma plataforma moderna que permite que o cliente receba o serviço e que o prestador receba pelo seu serviço. Tudo isso com praticidade e segurança.
      </p>
    </section>
  </div>
  <div class="footer-bottom">
    <p>&copy;2026, FastService LTDA</p>
  </div>
</footer>

</body>
</html>