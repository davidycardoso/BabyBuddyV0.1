<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BabyBuddy - Início</title>
    <link rel="stylesheet" href="css/index.css?v=1.0">
</head>
<body>
<header>
    <nav>
        <div class="logo">
            <a href="index.php">
                <img src="images/logo.png" alt="Logo">
            </a>
        </div>    

        <ul class="nav-links">
            <li><a href="login.php"><button id="login" class="btn-login">Login</button></a></li>
            <li><a href="register.php"><button id="cadastrar" class="btn-cadastrar">Cadastrar</button></a></li>
            <li><a href="#services">Serviços</a></li>
            <li><a href="#about">Sobre</a></li>
            <li><a href="#contact">Contato</a></li>
        </ul>
    </nav>
</header>

<section class="hero">
    <div class="slides">
        <div class="slide">
            <img src="images/slide1.jpg" alt="Slide 1">
            <div class="slide-overlay">
                <div class="hero-content">
                    <h1>Encontre Babás Confiáveis Perto de Você</h1>
                    <p>Com a ajuda da geolocalização, o BabyBuddy conecta você à babás mais próximas e qualificadas.</p>
                    <a href="#services" class="btn">Confira os Serviços</a>
                </div>
            </div>
        </div>
        <div class="slide">
            <img src="images/slide2.jpg" alt="Slide 2">
            <div class="slide-overlay">
                <div class="hero-content">
                    <h1>Comunique-se Direto com a Babá</h1>
                    <p>Converse com as babás antes de tomar sua decisão, garantindo confiança e tranquilidade.</p>
                    <a href="#contact" class="btn">Entre em Contato</a>
                </div>
            </div>
        </div>
        <div class="slide">
            <img src="images/slide3.jpg" alt="Slide 3">
            <div class="slide-overlay">
                <div class="hero-content">
                    <h1>Perfis Verificados para Sua Segurança</h1>
                    <p>Avalie perfis detalhados com informações sobre experiências e qualificações de cada babá.</p>
                    <a href="#about" class="btn">Saiba Mais Sobre o BabyBuddy</a>
                </div>
            </div>
        </div>
    </div>
    <div class="progress-container">
        <div class="progress-bar" id="progress1"></div>
        <div class="progress-bar" id="progress2"></div>
        <div class="progress-bar" id="progress3"></div>
    </div>
    <button id="prev" class="nav-btn">&#9664;</button>
    <button id="next" class="nav-btn">&#9654;</button>
</section>



<section id="about">
    <div class="about-content">
        <h2>Sobre o BabyBuddy</h2>
        <p class="intro">O BabyBuddy é uma plataforma inovadora que conecta pais e babás de forma simples e segura. Nosso objetivo é proporcionar uma experiência tranquila e confiável para que as famílias encontrem as melhores opções de babás perto de suas casas.</p>

        <div class="about-mission-values">
            <div class="about-section">
                <h3 class="section-title">Nossa Missão</h3>
                <p>Garantir que as famílias tenham acesso a profissionais qualificados, perto de sua localização, oferecendo um serviço transparente e seguro.</p>
            </div>

            <div class="about-section">
                <h3 class="section-title">Nossos Valores</h3>
                <ul>
                    <li><i class="fas fa-shield-alt"></i> <strong>Segurança:</strong> Priorizamos a segurança e confiança para todas as famílias.</li>
                    <li><i class="fas fa-award"></i> <strong>Qualidade:</strong> Apenas babás com alta qualificação são cadastradas em nossa plataforma.</li>
                    <li><i class="fas fa-clock"></i> <strong>Facilidade:</strong> Tornamos a contratação de babás simples e rápida, para que você tenha mais tempo para sua família.</li>
                </ul>
            </div>
        </div>
    </div>
</section>



<section id="services">
    <div class="services-content">
        <h2>Serviços que Oferecemos</h2>
        <p>Conectamos pais e babás de maneira simples e eficiente, com serviços que garantem uma experiência tranquila e segura.</p>

        <div class="service-list">
            <div class="service-item">
                <img src="images/chat.jpg" alt="Chat Direto">
                <h3>Comunicação Direta</h3>
                <p>Converse diretamente com as babás antes de tomar uma decisão, garantindo transparência e confiança.</p>
            </div>

            <div class="service-item">
                <img src="images/filters.jpg" alt="Filtros de Pesquisa">
                <h3>Filtros de Pesquisa</h3>
                <p>Encontre a babá ideal para sua família, filtrando por disponibilidade, experiência e qualificação.</p>
            </div>

            <div class="service-item">
                <img src="images/profiles.jpg" alt="Perfis das Babás">
                <h3>Perfis Detalhados</h3>
                <p>Acesse informações completas sobre cada babá, incluindo experiência e qualificações, para fazer a escolha certa.</p>
            </div>
        </div>
    </div>
</section>


<section id="why-choose">
    <div class="benefits-content">
        <h2>Por que Escolher o BabyBuddy?</h2>
        <p>Escolher o BabyBuddy significa optar por um serviço que valoriza a segurança, praticidade e confiança. Estamos aqui para conectar você com as melhores babás, oferecendo tudo o que você precisa para ter uma experiência tranquila e confiável. Confira abaixo as razões pelas quais você vai adorar nosso serviço:</p>

        <div class="benefits-list">
            <div class="benefit-item">
                <img src="images/benefit1.jpg" alt="Babás confiáveis e próximas">
                <h3>Babás Confiáveis e Perto de Você</h3>
                <p>Com o BabyBuddy, você tem acesso a um banco de babás altamente qualificadas e dispostas a oferecer o melhor cuidado, todas localizadas perto de você.</p>
            </div>

            <div class="benefit-item">
                <img src="images/benefit2.jpg" alt="Perfis verificados e detalhados">
                <h3>Perfis Completos e Verificados</h3>
                <p>Saiba tudo sobre as babás antes de tomar sua decisão. Visualize suas qualificações, experiências e avaliações de outras famílias, garantindo a melhor escolha.</p>
            </div>

            <div class="benefit-item">
                <img src="images/benefit3.jpg" alt="Conversa direta com a babá">
                <h3>Comunicação Direta com a Babá</h3>
                <p>Entre em contato diretamente com a babá antes de fechar o contrato, garantindo que todas as suas dúvidas sejam esclarecidas e que ambas as partes estejam alinhadas.</p>
            </div>

            <div class="benefit-item">
                <img src="images/benefit4.jpg" alt="Avaliações de outras famílias">
                <h3>Avaliações de Famílias Reais</h3>
                <p>Confira as avaliações e recomendações de outras famílias que já utilizaram o BabyBuddy. Nossa plataforma permite que você escolha com total confiança, com base em experiências reais.</p>
            </div>
        </div>
    </div>
</section>



<section id="testimonials">
    <h2>O que nossos clientes dizem</h2>
    <div class="testimonials-container">
        <div class="testimonial">
            <div class="testimonial-image">
                <img src="images/client1.jpg" alt="Maria Oliveira">
            </div>
            <div class="testimonial-content">
                <h3>Maria Oliveira</h3>
                <p>"Excelente serviço! A babá foi muito atenciosa e chegou pontualmente. Recomendo a todos."</p>
            </div>
        </div>

        <div class="testimonial">
            <div class="testimonial-image">
                <img src="images/client2.jpg" alt="João Santos">
            </div>
            <div class="testimonial-content">
                <h3>João Santos</h3>
                <p>"A plataforma é muito fácil de usar, e as babás são super profissionais. Adoramos o BabyBuddy!"</p>
            </div>
        </div>

        <div class="testimonial">
            <div class="testimonial-image">
                <img src="images/client3.jpg" alt="Ana Pereira">
            </div>
            <div class="testimonial-content">
                <h3>Ana Pereira</h3>
                <p>"A experiência com o BabyBuddy foi incrível. Encontrei a babá perfeita para meus filhos. Super recomendo!"</p>
            </div>
        </div>
    </div>
</section>



<section id="contact">
    <h2>Entre em Contato</h2>
    <form action="send_message.php" method="post">
        <input type="text" name="name" placeholder="Seu Nome" required>
        <input type="email" name="email" placeholder="Seu E-mail" required>
        <textarea name="message" placeholder="Sua Mensagem" required></textarea>
        <button type="submit">Enviar Mensagem</button>
    </form>
</section>

<footer>
    <p>&copy; 2024 BabyBuddy. Todos os direitos reservados.</p>
</footer>

<script src="js/script.js"></script>
</body>
</html>
