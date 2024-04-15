#!bin/bash
cd react-app
npm run build
echo copying.....
#find ./build/static/js -name main.*.js -exec cp {} ./../module23/upload/catalog/view/javascript/qwqer/shipping_qwqer.js \;
#find ./build/static/css -name main.*.css -exec cp {} ./../module23/upload/catalog/view/stylesheet/qwqer/shipping_qwqer.css \;

find ./build/static/js -name main.*.js -exec cp {}  /hdd/user/PhpstormProjects/docker-compose-lamp/www/ocs23/catalog/view/javascript/qwqer/shipping_qwqer.js \;
find ./build/static/css -name main.*.css -exec cp {} /hdd/user/PhpstormProjects/docker-compose-lamp/www/ocs23/catalog/view/stylesheet/qwqer/shipping_qwqer.css \;

find ./build/static/js -name main.*.js -exec cp {}  /hdd/user/PhpstormProjects/docker-compose-lamp/www/oc23/catalog/view/javascript/qwqer/shipping_qwqer.js \;
find ./build/static/css -name main.*.css -exec cp {} /hdd/user/PhpstormProjects/docker-compose-lamp/www/oc23/catalog/view/stylesheet/qwqer/shipping_qwqer.css \;


find ./build/static/js -name main.*.js -exec cp {}   ./../module23/upload/catalog/view/javascript/qwqer/shipping_qwqer.js \;
find ./build/static/css -name main.*.css -exec cp {} ./../module23/upload/catalog/view/stylesheet/qwqer/shipping_qwqer.css \;


cd ..
echo done