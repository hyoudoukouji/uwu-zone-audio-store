function applyFilters() {
    const minPrice = document.getElementById('min_price').value;
    const maxPrice = document.getElementById('max_price').value;
    const sort = document.getElementById('sort').value;
    const searchParams = new URLSearchParams(window.location.search);
    const search = searchParams.get('search') || '';

    const url = new URL(window.location.href);
    url.searchParams.set('search', search);
    if (minPrice) url.searchParams.set('min_price', minPrice);
    if (maxPrice) url.searchParams.set('max_price', maxPrice);
    url.searchParams.set('sort', sort);

    window.location.href = url.toString();
}
