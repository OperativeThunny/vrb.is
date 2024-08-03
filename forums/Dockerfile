FROM trafex/php-nginx:latest

#USER 0
WORKDIR /var/www/html
USER 0
# Copy the application files
RUN mkdir /app

COPY VBulletin606.zip /app

RUN chown -R nobody:nobody /app && \
    apk add --no-cache unzip inotify-tools

COPY nginxReloader.sh /opt/nginxReloader.sh

RUN chmod +x /opt/nginxReloader.sh

USER nobody

RUN \
    unzip /app/VBulletin606.zip -d /var/www/html

RUN rm -rf __MACOSX && \
    rm index.php test.html && \
    mv VBulletin606/upload/* . && \
    rm -rf VBulletin606 && \
    rm /app/VBulletin606.zip
# Expose the port the app runs on
EXPOSE 8080
