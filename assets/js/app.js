document.querySelectorAll('.gallery-thumb').forEach((button) => {
    button.addEventListener('click', () => {
        const main = document.getElementById('main-product-image');
        if (main) {
            main.src = button.dataset.image || main.src;
        }
    });
});

setTimeout(() => {
    document.querySelectorAll('.flash-message').forEach((node) => {
        node.style.opacity = '0';
        node.style.transition = 'opacity 0.4s ease';
    });
}, 3500);

if (window.lucide && typeof window.lucide.createIcons === 'function') {
    window.lucide.createIcons();
}
