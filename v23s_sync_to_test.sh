#!/bin/bash
rsync -avm  --include-from=include_filter.txt ./module23/upload/ /hdd/user/PhpstormProjects/docker-compose-lamp/www/ocs23/ 
