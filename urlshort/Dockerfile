FROM alpine:latest

ARG default_admin_email=example@example.com
ARG default_admin_password=example

ENV POCKETBASE_ADMIN_EMAIL=$default_admin_email
ENV POCKETBASE_ADMIN_PASSWORD=$default_admin_password

RUN mkdir -p /opt/pocketbase/ && \
    cd /opt/pocketbase

WORKDIR /opt/pocketbase

RUN apk add --no-cache unzip inotify-tools wget curl && \
    curl pocketbase.zip "https://github.com/pocketbase/pocketbase/releases/download/v0.22.18/pocketbase_0.22.18_linux_amd64.zip" && \
    unzip pocketbase.zip && \
    ./pocketbase update && \
    ./pocketbase migrate up && \
    ./pocketbase admin create ${POCKETBASE_ADMIN_EMAIL} ${POCKETBASE_ADMIN_PASSWORD} && \
    rm pocketbase.zip


EXPOSE 8090

CMD [ "/opt/pocketbase/pocketbase", "serve" ]