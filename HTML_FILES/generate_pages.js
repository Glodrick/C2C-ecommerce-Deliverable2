const fs = require('fs');

const indexHtml = fs.readFileSync('index.html', 'utf8');

const headAndHeader = indexHtml.split('<main id="main-site">')[0];
const footerAndScripts = indexHtml.split('</main>')[1];

const categories = [
    { id: 'phones', title: 'Phones', filename: 'phones.html' },
    { id: 'peripherals', title: 'Peripherals', filename: 'peripherals.html' },
    { id: 'pc parts', title: 'PC Parts', filename: 'pc_parts.html' },
    { id: 'accessories', title: 'Accessories', filename: 'accessories.html' }
];

categories.forEach(cat => {
    const mainContent = `
 <main id="main-site" style="min-height: 60vh;">
    <section class="container mt-5" style="margin-top: 50px; margin-bottom: 50px;">
        <h2 class="mb-4 text-capitalize" style="font-size: 36px; font-weight: 800; color: #111; margin-bottom: 20px;">${cat.title}</h2>
        
        <div class="filters-container mb-4" style="margin-bottom: 30px;">
            <button class="filter-pill">Price (R) <i class="fas fa-caret-down"></i></button>
            <button class="filter-pill">Brand <i class="fas fa-caret-down"></i></button>
            <button class="filter-pill">On sale</button>
            <button class="filter-pill">Sent from <i class="fas fa-caret-down"></i></button>
        </div>

        <div class="product-grid" id="categoryGrid" data-category="${cat.id}">
            <!-- Items injected by app.js -->
        </div>
    </section>
`;
    
    const fullHtml = headAndHeader + mainContent + footerAndScripts;
    fs.writeFileSync(cat.filename, fullHtml);
    console.log(`Generated ${cat.filename}`);
});
