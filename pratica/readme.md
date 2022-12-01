# Servidor Socket

## Executar o arquivo em php que inicia o servidor socket

```shell
docker run -it --rm --name pratica-socket-server -v "$PWD":/opt/project -w /opt/project php:8.1-cli php server.php
```

```log
Fatal error: Uncaught Error: Call to undefined function socket_create() in /opt/project/server.php:8
Stack trace:
#0 {main}
  thrown in /opt/project/server.php on line 8

```

## Instalar extensão socket

```shell    
touch Dockerfile
```

```dockerfile
FROM php:8.1-cli

RUN docker-php-ext-install sockets
```

## build image

```shell
docker build -t php-sockets .
``` 

## Executar o arquivo em php que inicia o servidor socket

```shell
docker run -it --rm --name pratica-socket-server -v "$PWD":/opt/project -w /opt/project php-sockets server.php
```