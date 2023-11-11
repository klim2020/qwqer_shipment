#!/bin/bash
rsync -avm --include-from=include_filter.txt ./module23/upload/ ./module23/upload/ 
