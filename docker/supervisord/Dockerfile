FROM alpine:3

RUN apk update && \
    apk add --no-cache python3 py3-pip docker
RUN pip3 install --upgrade pip --break-system-packages && \
    pip3 install supervisor --break-system-packages

VOLUME /var/run/docker.sock
VOLUME /etc/supervisord.conf

ENTRYPOINT ["supervisord"]
CMD ["--nodaemon", "--configuration", "/etc/supervisord.conf"]
