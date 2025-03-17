
let nextDom = document.getElementById('next');
let prevDom = document.getElementById('prev');

let carouselDom = document.querySelector('.carousel');
let SliderDom = carouselDom.querySelector('.carousel .list');
let thumbnailBorderDom = document.querySelector('.carousel .thumbnail');
let thumbnailItemsDom = thumbnailBorderDom.querySelectorAll('.item');
let timeDom = document.querySelector('.carousel .time');

thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
let timeRunning = 3000;
let timeAutoNext = 7000;

nextDom.onclick = function(){
    showSlider('next');    
}

prevDom.onclick = function(){
    showSlider('prev');    
}
let runTimeOut;
let runNextAuto = setTimeout(() => {
    next.click();
}, timeAutoNext)
function showSlider(type){
    let  SliderItemsDom = SliderDom.querySelectorAll('.carousel .list .item');
    let thumbnailItemsDom = document.querySelectorAll('.carousel .thumbnail .item');
    
    if(type === 'next'){
        SliderDom.appendChild(SliderItemsDom[0]);
        thumbnailBorderDom.appendChild(thumbnailItemsDom[0]);
        carouselDom.classList.add('next');
    }else{
        SliderDom.prepend(SliderItemsDom[SliderItemsDom.length - 1]);
        thumbnailBorderDom.prepend(thumbnailItemsDom[thumbnailItemsDom.length - 1]);
        carouselDom.classList.add('prev');
    }
    clearTimeout(runTimeOut);
    runTimeOut = setTimeout(() => {
        carouselDom.classList.remove('next');
        carouselDom.classList.remove('prev');
    }, timeRunning);

    clearTimeout(runNextAuto);
    runNextAuto = setTimeout(() => {
        next.click();
    }, timeAutoNext)
}


document.addEventListener("DOMContentLoaded", () => {
    const categoryFilter = document.getElementById("categoryFilter");
    const sizeFilter = document.getElementById("sizeFilter");
    const products = document.querySelectorAll(".childprods");

    function filterAndSortProducts() {
        const category = categoryFilter.value;
        const size = sizeFilter.value;

        console.log("Selected Category:", category);
        console.log("Selected Size:", size);

        let filteredProducts = Array.from(products).filter(product => {
            const productCategory = product.getAttribute("data-category");
            const productSize = product.getAttribute("data-size");

            console.log("Product Category:", productCategory, "Product Size:", productSize);

            const categoryMatch = category === "all" || productCategory === category;
            const sizeMatch = size === "all" || productSize === size;

            return categoryMatch && sizeMatch;
        });

        console.log("Filtered Products:", filteredProducts);

        // Sort products by price (Low to High)
        filteredProducts.sort((a, b) => {
            const priceA = parseFloat(a.getAttribute("data-price"));
            const priceB = parseFloat(b.getAttribute("data-price"));
            return priceA - priceB;
        });

        // Hide all products
        products.forEach(product => product.style.display = "none");

        // Show only the filtered & sorted products
        filteredProducts.forEach(product => product.style.display = "block");
    }

    // Initialize filters on page load
    filterAndSortProducts();

    // Add event listeners for filter changes
    categoryFilter.addEventListener("change", filterAndSortProducts);
    sizeFilter.addEventListener("change", filterAndSortProducts);
});