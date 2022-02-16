#!/bin/bash
    cd /var/www/html
    sudo cp config/custom.example.php config/custom.php
    sudo chmod 755 custom.php
    curl -Sl https://rpm.nodesource.com/setup_14.x  | sudo bash -
    sudo yum -y install nodejs
    npm run production