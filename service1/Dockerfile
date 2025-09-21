FROM php:8.1-cli
WORKDIR /app
COPY public public
VOLUME /vstorage
EXPOSE 8199
CMD ["php", "-S", "0.0.0.0:8199", "-t", "public"]