#!/bin/bash
    curl -Sl https://rpm.nodesource.com/setup_14.x  | sudo bash -
    sudo yum -y install nodejs
    npm install 
    npm run dev