#!/bin/bash
rsync -avm --include-from=include_filter.txt ./module23/upload/ ./module23/upload/ 
find . -type f -name "*.*" -exec sed -i 's#shipping_qwqer_#qwqer_#g' {} +
