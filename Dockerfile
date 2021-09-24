# THIS IS BASE IMAGE
FROM php:8.0-cli

RUN apt-get update -y
RUN apt-get install dirmngr -y
RUN apt-get install curl zip -y

RUN apt-get install git -y

RUN curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg | gpg --dearmor -o /usr/share/keyrings/githubcli-archive-keyring.gpg
RUN echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" | tee /etc/apt/sources.list.d/github-cli.list > /dev/null
RUN apt-get update
RUN apt-get install gh -y

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# directory inside docker
WORKDIR /action

# make local content available inside docker - copies to /action
COPY . .

# see https://nickjanetakis.com/blog/docker-tip-86-always-make-your-entrypoint-scripts-executable
ENTRYPOINT ["php", "/action/build.php"]
#ENTRYPOINT ["/bin/sh"]
 
