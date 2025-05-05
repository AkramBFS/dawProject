// Carousel functionality
const initCarousel = () => {
    const nextDom = document.getElementById('next');
    const prevDom = document.getElementById('prev');
    const carouselDom = document.querySelector('.carousel');
    
    if (!nextDom || !prevDom || !carouselDom) {
        console.warn("Carousel elements not found, skipping carousel init.");
        return;
    }

    const sliderDom = carouselDom.querySelector('.list');
    const thumbnailBorderDom = carouselDom.querySelector('.thumbnail');
    const thumbnailItemsDom = thumbnailBorderDom.querySelectorAll('.item');

    if (!sliderDom || !thumbnailBorderDom || thumbnailItemsDom.length === 0) {
        console.warn("Carousel sub-elements not found properly, skipping carousel init.");
        return;
    }

    thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
    let timeRunning = 3000;
    let timeAutoNext = 7000;
    let runTimeOut;
    let runNextAuto = setTimeout(() => nextDom.click(), timeAutoNext);

    function showSlider(type) {
        const sliderItemsDom = sliderDom.querySelectorAll('.item');
        const thumbnailItemsDom = thumbnailBorderDom.querySelectorAll('.item');
        
        if (type === 'next') {
            sliderDom.appendChild(sliderItemsDom[0]);
            thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
            carouselDom.classList.add('next');
        } else {
            sliderDom.prepend(sliderItemsDom[sliderItemsDom.length - 1]);
            thumbnailBorderDom.prepend(thumbnailItemsDom[thumbnailItemsDom.length - 1]);
            carouselDom.classList.add('prev');
        }
        
        clearTimeout(runTimeOut);
        runTimeOut = setTimeout(() => {
            carouselDom.classList.remove('next');
            carouselDom.classList.remove('prev');
        }, timeRunning);

        clearTimeout(runNextAuto);
        runNextAuto = setTimeout(() => nextDom.click(), timeAutoNext);
    }

    nextDom.onclick = () => showSlider('next');
    prevDom.onclick = () => showSlider('prev');
};

// Hamburger menu
const initHamburgerMenu = () => {
    const hamburger = document.querySelector('.hamburger');
    const nav = document.querySelector('nav');
    
    if (!hamburger || !nav) return;

    hamburger.addEventListener('click', () => {
        nav.classList.toggle('active');
    });
};

// Scroll functionality
const initScrollButtons = () => {
    document.getElementById("see-more-products")?.addEventListener("click", () => {
        document.getElementById("product-section")?.scrollIntoView({ behavior: "smooth" });
    });

    ["see-more-contact", "see-more-about"].forEach(id => {
        document.getElementById(id)?.addEventListener("click", () => {
            document.querySelector("footer")?.scrollIntoView({ behavior: "smooth" });
        });
    });
};

// Initialize everything when DOM loads
document.addEventListener("DOMContentLoaded", () => {
    initCarousel();
    initHamburgerMenu();
    initScrollButtons();
});
