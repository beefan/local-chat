# local-chat-gpt

ChatGPT 100% saves your chats and uses them to train their model. Plus, $20/mo is as low as you can go if you want access to newer models. 

Apparently, they don't use the data you send to the API in training, but also the API is pretty cheap and you can get a lot more for your money vs paying chat gpt pro (provided you don't need access to the newest features).

This repository is one php dev's effort to build a chat gpt client to host on a small server on my network. This will give devices on my network access to a local chat gpt app. Right now, it is a simple chat interface with no state management. I hope to implement saving chat history, multi-modal support, and custom system instructions soon. 

Feel free to copy this repo as a launching point for your own local chat gpt, but keep in mind I offer zero support.

## To run
```
cd app
sail up -d
npm run dev
```

## To view
localhost (by default)

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
It's an Inertia.js project which requires a frontend resources to be built to render the ui properly. Run a shell to the docker container and build the frontend resources:
```
sudo docker exec -it app-laravel.test-1 bash

npm run build
```

You can then access the site using the servers host name or ip from a device in the network. 
