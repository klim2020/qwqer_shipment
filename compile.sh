#!bin/bash
cd react-app
npm run build
find ./build/static/js -name main.*.js -exec cp {} ./../module23/upload/catalog/view/javascript/qwqer/shipping_qwqer.js \;
find ./build/static/css -name main.*.css -exec cp {} ./../module23/upload/catalog/view/stylesheet/qwqer/shipping_qwqer.css \;
cd ..