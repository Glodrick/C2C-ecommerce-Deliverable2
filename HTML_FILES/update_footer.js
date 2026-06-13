const fs = require('fs');
const path = require('path');

const newFooter = `<footer class="bg-dark text-white py-5 mt-5" style="background-color: #343a40;">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h5 class="font-rubik font-weight-bold mb-3">Buy & Sell</h5>
                <ul class="list-unstyled font-rale" style="line-height: 2;">
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">How it Works</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">Buyer Protection</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">Seller Guidelines</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">Safe Trade Tips</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">Dispute Resolution</a></li>
                </ul>
            </div>
            <div class="col-md-6 mb-4">
                <h5 class="font-rubik font-weight-bold mb-3">About & Support</h5>
                <ul class="list-unstyled font-rale" style="line-height: 2;">
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">About Us</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">Help Center</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">Contact Us</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">Terms of Service</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none" style="color: rgba(255,255,255,.5);">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="row mt-3 border-top pt-3 border-secondary">
            <div class="col-12 text-center font-rale font-size-14" style="color: rgba(255,255,255,.5);">
                &copy; 2026 C2C Plat. All Rights Reserved.
            </div>
        </div>
    </div>
</footer>`;

const files = fs.readdirSync(__dirname).filter(file => file.endsWith('.html'));

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    
    // Replace the footer tag, whatever is inside it
    // Using a regex to match <footer> ... </footer> including newlines
    const newContent = content.replace(/<footer>[\s\S]*?<\/footer>/, newFooter);
    
    fs.writeFileSync(file, newContent);
    console.log(`Updated footer in ${file}`);
});
