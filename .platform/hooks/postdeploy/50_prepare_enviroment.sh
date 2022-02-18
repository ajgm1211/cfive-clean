#!/bin/bash
    sudo cp /var/www/html/config/custom.example.php /var/www/html/config/custom.php
    sudo chmod 755 /var/www/html/config/custom.php
    curl -Sl https://rpm.nodesource.com/setup_14.x  | sudo bash -
    sudo yum -y install nodejs
    npm install 
    npm run dev