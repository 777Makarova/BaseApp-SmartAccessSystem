# Равертка DEV  среды

1. Необходимо скопировать  файл **.env** в **.env.local**
2. `$ docker-compose up -d `

## Генерация ключей

0. `$ /srv/api` (или локальная дериктория проекта)
1. `openssl genrsa -out private.key`
2. `openssl rsa -in private.key -out public.key -pubout`
3. `cp {public.key,private.key} ./tests/Fixtures/`


## Первоначальная настройка CI/CD

## Настройка окружения Базы данных