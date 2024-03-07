#!/bin/bash
#!/bin/bash
rsync -avm --include-from=include_filter.txt  /hdd/user/PhpstormProjects/docker-compose-lamp/www/oc38/ ./module3/upload/ 
