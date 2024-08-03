#!/bin/bash
###########
# from https://cyral.com/blog/how-to-auto-reload-nginx/#:~:text=Create%20a%20new%20file%20called,and%20then%20automatically%20reload%20Nginx.&text=nginxReloader.sh%20will%20continuously%20watch,%2Fetc%2Fnginx%2Fconf.d%20directory.

while true
do
    inotifywait --exclude .swp -e create -e modify -e delete -e move /etc/nginx/conf.d
    nginx -t
    if [ $? -eq 0 ]
    then
        echo "Detected Nginx Configuration Change"
        echo "Executing: nginx -s reload"
        nginx -s reload
    else
        echo "Nginx Configuration Test Failed"
        echo "Please Check Configuration Files"
    fi
done
