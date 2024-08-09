FROM alpine:latest

RUN mkdir -p /opt/pocketbase/ && \
    cd /opt/pocketbase

WORKDIR /opt/pocketbase

RUN --mount=type=secret,id=default_admin_email --mount=type=secret,id=default_admin_password \
    apk add --no-cache unzip inotify-tools wget curl file && \
    curl -L -o pocketbase.zip "https://github.com/pocketbase/pocketbase/releases/download/v0.22.18/pocketbase_0.22.18_linux_amd64.zip" && \
    unzip pocketbase.zip && \
    ./pocketbase update && \
    ./pocketbase migrate up && \
    ./pocketbase admin create $(cat /run/secrets/default_admin_email) $(cat /run/secrets/default_admin_password) && \
    rm pocketbase.zip

EXPOSE 8090

CMD [ "/opt/pocketbase/pocketbase", "serve" ]