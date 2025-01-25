document.getElementById('search').addEventListener('keyup', function(e) {
    const search = e.target.value.toLowerCase();
    const cards = document.getElementsByClassName('product-card');

    Array.from(cards).forEach(card => {
        const name = card.querySelector('.product-name').textContent.toLowerCase();
        card.style.display = name.includes(search) ? '' : 'none';
    });
});