const timeout = 15000; 
beforeAll(async () => {await page.goto(URL, {waitUntil: 'domcontentloaded'});
  
}); 
describe('Test page title and header', () => { 
test('page title', async () => { 
  const title = await page.title(); expect(title).toBe('Testing'); }, timeout); 

});