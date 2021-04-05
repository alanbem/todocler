FROM alpine:3

RUN apk update && apk add --no-cache python3 py3-pip docker docker-compose
RUN pip3 install --upgrade pip && pip3 install docker-compose supervisor

VOLUME /var/run/docker.sock
VOLUME /etc/supervisord.conf

ENTRYPOINT ["supervisord"]
CMD ["--nodaemon", "--configuration", "/etc/supervisord.conf"]
