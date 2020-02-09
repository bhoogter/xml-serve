#!/bin/bash

curl -s https://api.github.com/repos/bhoogter/xml-modules/releases/latest grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/'
# curl -s https://api.github.com/users/bhoogter/xml-modules/releases/latest \
# grep '"tag_name":' |                                            # Get tag line
#     sed -E 's/.*"([^"]+)".*/\1/' 

# | grep "browser_download_url.*deb" \
# | cut -d : -f 2,3 \
# | tr -d \" \
# | wget -qi -