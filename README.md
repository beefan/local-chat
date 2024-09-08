# local-chat-gpt

## To run
```
cd app
sail up -d
```

## To view
localhost

## To share
```
sail share --subdomain=my-sail-site
```

## to install on fresh ubuntu server instance
### install dependencies
```
sudo apt install composer 
sudo apt-get install php-xml
sudo apt-get install php-dom
```
to install docker, [follow their documentation](https://docs.docker.com/engine/install/ubuntu/#install-using-the-repository)

### copy .env.example to .env
.env.example has all necessary environment variables. Database initialization will use these, so fill it out before running sail up.

### composer install
inside the app directory run `composer install`. It may be necessary to also run `composer update`.

### sail up
`./vendor/bin/sail up -d` - this will build the docker container with all required dependencies for the project. Sudo may be required depending on how you are set up. 

### migrations
Once up, run `./vendor/bin/sail artisan migrate` to run initial migrations

### npm run dev
It's an Inertia.js project which requires a vite server running to render the frontend. Run a shell to the docker container and initialize the vite server. 
```
sudo docker exec -it app-laravel.test-1 bash

npm run build
npm run dev
```

to expose to the rest of your network, use the `--host` option:
```
npm run dev --host your.server.ip.here
```

You can then access the site using the servers host name or ip from an device in the network. 
