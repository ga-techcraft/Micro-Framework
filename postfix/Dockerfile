FROM debian:bullseye

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y \
    postfix mailutils libsasl2-modules && \
    apt-get clean

COPY main.cf /etc/postfix/main.cf
COPY sasl_passwd /etc/postfix/sasl_passwd
COPY entrypoint.sh /entrypoint.sh

RUN chmod 600 /etc/postfix/sasl_passwd && \
    postmap /etc/postfix/sasl_passwd && \
    chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]
