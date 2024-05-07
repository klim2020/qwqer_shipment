module.exports = {
        preset: "jest-puppeteer",
        globals: { 
            
               
        },
        transformIgnorePatterns: [
            '/node_modules/(?!react-select)',
            '/node_modules/(?!react-day-picker)',
            './src/(?!opencart)',
          ],
        
        verbose: true
    };
