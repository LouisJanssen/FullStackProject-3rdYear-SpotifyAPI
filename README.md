# H3 Fullstack Project

This project was done after a workshop for the H3 fullstack lessons at HETIC.

In this project, we had one week to develop a website based on an API of our choice. We chose to work with the API of Spotify to propose a vote service and leaderboards for our users based on small rising artists to find the new stars of the french music.

This is the backend part of the app. You can find the front end here : https://github.com/ZeAmine/roots-production

This project is a web application, and uses Symfony and React with Typescript.

## Install the project

This project comes ready with a docker-compose environment that can be used for development purposes.
This environment has three containers :
- `workshop-nginx` : An nginx container that serves the content, it listens on port 8080. Your machine's 8080 port is forwarded to the container's 8080 port.
- `workshop-php` : A php container that runs a php development image with basic extensions enabled, as well as the symfony cli and composer cli installed
- `workshop-mariadb` : A mariadb container that listens on port 3307. Your machine's 3307 port is forwarded to the container's 3307 port.

To start the environment, you need to run the following commands :
```bash
# Copy the versionned .env.dist file to .env
cp .env.dist .env
# Edit the .env file to add the correct variables values
vim .env
# Now start the stack
docker-compose up -d

# At all times you can check the state of the containers by running
docker ps
# or
docker-compose ps
```

### Using Symfony commands in the php container
To run Symfony specific commands, you'll need to be in the `workshop-php` container. You can achieve this like this
```
docker exec -it workshop-php $COMMAND
# For example, to use the symfony cache:clear command
docker exec -it workshop-php php bin/console cache:clear
```

To make this easier you can add this alias to your zsh profile
```
alias sf="docker exec -it workshop-php"

# You can use the alias this way
sf php bin/console cache:clear
```

### Using mariabd cli in the mariadb container

You have several ways to connect to the mariadb container. You can either directly use the mysql-client cli using this command :
```
docker exec -it workshop-mysql mysql -u workshop -p
```

Or you can configure your GUI mysql editor to connect to `127.0.0.1:3307` (if you're using Sequel Pro or TablePlus for example).
This will let you acces the database without having to code.

You're now ready to work with this stack !

## Navigating the git tree

This project has two branches :
- `main` : the main branch of the project
- `develop` : the development branch of the project
