# THIS IS BASE IMAGE
FROM php:8.0-cli

RUN apt-get update -y
RUN apt-get install git -y

# directory inside docker
WORKDIR /action

# make local content available inside docker - copies to /action
COPY . .

# see https://nickjanetakis.com/blog/docker-tip-86-always-make-your-entrypoint-scripts-executable
ENTRYPOINT ["php", "/action/buildho.php"]
 
