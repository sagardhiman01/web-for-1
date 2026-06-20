const puppeteer = require('puppeteer');
const path = require('path');
const fs = require('fs');

async function generatePDF() {
    console.log('Launching browser...');
    const browser = await puppeteer.launch({
        headless: "new"
    });
    const page = await browser.newPage();
    
    const htmlPath = 'file://' + path.resolve(__dirname, 'presentation.html');
    console.log('Loading HTML from:', htmlPath);
    
    await page.goto(htmlPath, {
        waitUntil: 'networkidle0'
    });

    console.log('Generating PDF...');
    await page.pdf({
        path: 'CoreAsset_Presentation.pdf',
        format: 'A4',
        printBackground: true,
        displayHeaderFooter: false,
        margin: {
            top: '0px',
            bottom: '0px',
            left: '0px',
            right: '0px'
        }
    });

    console.log('PDF saved as CoreAsset_Presentation.pdf');
    await browser.close();
}

generatePDF().catch(err => {
    console.error('Error generating PDF:', err);
    process.exit(1);
});
