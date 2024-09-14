document.addEventListener('DOMContentLoaded', function () {
    const hero = document.querySelector('.hero');
    const heroBackgrounds = [
        'dist/img/Page1.jpg',
        'dist/img/Page0.jpg',
        'dist/img/Page3.jpg',
        'dist/img/Page4.jpg',
        'dist/img/Page5.jpg',
        'dist/img/Page6.jpg'
    ];
    let currentBackgroundIndex = 0;

    function changeBackground() {
        currentBackgroundIndex = (currentBackgroundIndex + 1) % heroBackgrounds.length;
        hero.style.backgroundImage = `linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(${heroBackgrounds[currentBackgroundIndex]})`;
    }

    // Image Lazy Loading
    const lazyImages = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const image = entry.target;
                image.src = image.dataset.src;
                image.classList.add('lazy-loaded');
                observer.unobserve(image);
            }
        });
    });

    lazyImages.forEach(image => {
        imageObserver.observe(image);
    });

    setInterval(changeBackground, 5000);
});
