#!/bin/bash
rsync -avm --include-from=include_filter.txt ./module3/upload/ ./module23/upload/ 
cd module23
find . -type f -name "*qwqer*.*" -exec sed -i 's#shipping_qwqer_#qwqer_#g' {} +
cd ..

