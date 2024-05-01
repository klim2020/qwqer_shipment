module.exports = {
        preset: "jest-puppeteer",
        globals: { 
            PAGES:['/oc38','/oc23'],
            URL: "http://localhost:8071",
            PRODUCT:"/index.php?route=product/product&product_id=40"
        },
        
        verbose: true
    };
