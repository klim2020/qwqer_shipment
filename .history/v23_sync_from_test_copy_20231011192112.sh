#!/bin/bash
rsync -avm --include-from=include_filter.txt /hdd/user/PhpstormProjects/docker-compose-lamp/www/oc23/ ./module23/upload/ 
