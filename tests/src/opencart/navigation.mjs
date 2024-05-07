export const navigator = {
    routes:{
        iPhone: 'index.php?route=product/product&product_id=40'
    },
    goto: async(path, where)=>{
        await page.goto(path + where, {waitUntil: 'domcontentloaded'});
    },
}
