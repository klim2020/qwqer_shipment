import navigator from "./opencart/navigation";
import config from "./opencart/configs";

const timeout = 15000; 
beforeAll(async () => {
  
}); 
describe('product working', () => { 
test('page title', async () => { 
  
  await navigator.goto(config.shops.opencart.ocs23, config.shops.opencart.products.iPhone);
  
  const title = await page.title(); expect(title).toBe('Testing'); }, timeout); 
});