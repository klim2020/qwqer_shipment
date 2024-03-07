#!/bin/bash
rsync -av  --include-from=include_filter.txt --exclude='*' ./module3/upload/ /hdd/user/PhpstormProjects/docker-compose-lamp/www/oc38/ 
