FROM groovy:3.0-jdk11
WORKDIR /app

# Install dependencies
RUN grape install com.sparkjava spark-core 2.9.4 && \
    grape install org.slf4j slf4j-simple 2.0.7

COPY /private/service2.groovy ./service2.groovy
VOLUME /vstorage
EXPOSE 8080
CMD ["groovy", "service2.groovy"]