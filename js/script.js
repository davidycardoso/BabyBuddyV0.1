let currentSlide = 0;
const slides = document.querySelectorAll('.slide');
const totalSlides = slides.length;
const progressBars = document.querySelectorAll('.progress-bar');
let slideInterval;

// Função para mostrar um slide específico
function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.style.display = i === index ? 'block' : 'none';
    });
    progressBars.forEach((bar, i) => {
        bar.classList.toggle('active', i === index);
    });
}

// Função para atualizar a progress bar
function updateProgressBar(index) {
    progressBars.forEach((bar, i) => {
        if (i === index) {
            bar.style.width = '100%';
            bar.style.transition = 'width 3s linear'; // Transição suave
        } else {
            bar.style.width = '0%';
            bar.style.transition = 'none';
        }
    });
}

// Função para ir para o próximo slide
function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
    updateProgressBar(currentSlide);
}

// Função para ir para o slide anterior
function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
    updateProgressBar(currentSlide);
}

// Função para iniciar o autoavançar os slides
function startSlideInterval() {
    slideInterval = setInterval(nextSlide, 3000); // Muda a cada 3 segundos
}

// Função para parar o autoavançar os slides
function stopSlideInterval() {
    clearInterval(slideInterval);
}

// Adiciona os eventos de click para os botões de navegação
document.getElementById('next').addEventListener('click', () => {
    stopSlideInterval(); // Para o autoavançar ao clicar
    nextSlide();
    startSlideInterval(); // Reinicia o autoavançar
});

document.getElementById('prev').addEventListener('click', () => {
    stopSlideInterval(); // Para o autoavançar ao clicar
    prevSlide();
    startSlideInterval(); // Reinicia o autoavançar
});

// Adiciona eventos para pausar ao passar o mouse
document.querySelector('.hero').addEventListener('mouseover', stopSlideInterval);
document.querySelector('.hero').addEventListener('mouseout', startSlideInterval);

// Permite scroll fora do slide
document.querySelector('.hero').addEventListener('wheel', (event) => {
    event.stopPropagation();
});

// Inicializa o slide, progress bar e intervalo
showSlide(currentSlide);
updateProgressBar(currentSlide);
startSlideInterval();
