const fs = require('fs');

const files = ['index.html', 'phones.html', 'peripherals.html', 'pc_parts.html', 'accessories.html'];

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    
    // Update category links in categoryOverview
    content = content.replace('<h6>Computers</h6>', '<h6>Computers</h6>'); // just finding location
    
    // The structure is:
    // <a href="#">
    //   <div class="category-img-box">
    //     <img src="./assets/COMPUTER_CATEGORY.jpg" alt="computer category">
    //   </div>
    // </a>
    // <h6>Computers</h6>
    
    // Let's just do a generic replace. It's safer to use regex if possible.
    content = content.replace(/<a href="#">(\s*<div class="category-img-box">\s*<img src="\.\/assets\/COMPUTER_CATEGORY\.jpg"[^>]+>\s*<\/div>\s*)<\/a>\s*<h6>Computers<\/h6>/g, '<a href="pc_parts.html">$1</a>\n                    <h6>Computers</h6>');
    content = content.replace(/<a href="#">(\s*<div class="category-img-box">\s*<img src="\.\/assets\/iPhone-17[^>]+>\s*<\/div>\s*)<\/a>\s*<h6>Phones<\/h6>/g, '<a href="phones.html">$1</a>\n                    <h6>Phones</h6>');
    content = content.replace(/<a href="#">(\s*<div class="category-img-box">\s*<img src="\.\/assets\/peripheral\.jpg"[^>]+>\s*<\/div>\s*)<\/a>\s*<h6>Peripherals<\/h6>/g, '<a href="peripherals.html">$1</a>\n                    <h6>Peripherals</h6>');
    content = content.replace(/<a href="#">(\s*<div class="category-img-box">\s*<img src="\.\/assets\/Otterbox[^>]+>\s*<\/div>\s*)<\/a>\s*<h6>Accessories<\/h6>/g, '<a href="accessories.html">$1</a>\n                    <h6>Accessories</h6>');

    fs.writeFileSync(file, content);
    console.log(`Updated links in ${file}`);
});
