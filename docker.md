# Using docker
By using docker we can run the server inside a *container*, which has its own
file system and binaries. This allows us to get a working environment with
working versions of `php`, `npm` etc. on any host machine. First we prepare a
docker *image*, which is a snapshot of a filesystem with everything set up. Then
we can start a container from the image, and run commands inside it.

## Making the docker image
The file `/Dockerfile` describes how to turn a clean install of ubuntu 18.04
into a well-oiled development server. To build the image, run
```
npm run docker:dev:build
```
This will start a new container based on `unbuntu:18.04`, install `php7.2`,
`npm` etc., and make an empty `/app` folder. When we later start our container,
we will mount the `vektorprogrammet/` development directory into the `/app`
folder. We don't want to rebuild the docker image while developing, so we simply
use docker for the binaries, and mount in the actual folder. This means we don't
even have to restart the container for changes to take effect.

## Running setup
We need to get all npm and php requirements installed, so run
```
npm run docker:dev:setup
```
This will run `npm run setup` inside a docker container based on the
`vektordev:1.0` image, but the setup will happen in the `vektorprogrammet/`
folder on the host machine.

## Running the server
To start the server, simply run
```
npm run docker:dev:start
```
This will run `npm start` in a new container, but the server will still run from
the `vektorprogrammet/` folder on the host machine. This means the server will
behave as if there were no container. The port `8000` is passed through the
container to the host machine, so open [0.0.0.0:8000] to access the server 
normally.

## Running other commands
Sometimes you may want to run other commands, like
* `npm run dp:update` to update the database and reload fixtures
* `npm run cs` to fix code style problems
* `npm run test` to do tests

These must also be run from within the container with the `vektorprogrammet/`
folder mounted. This is the general command used by all in-container operations:
```
npm run docker:dev:run -- <your> <command> <here>
```

