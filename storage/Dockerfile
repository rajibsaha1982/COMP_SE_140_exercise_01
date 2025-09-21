FROM php:8.1-cli
WORKDIR /app
COPY public/ public/
RUN mkdir -p /data && chown www-data:www-data /data && chmod 777 /data
VOLUME /data
EXPOSE 8000
USER www-data
CMD ["php", "-S", "0.0.0.0:8000", "-t", "/app/public"]