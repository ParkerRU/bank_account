## readme

### commands
docker compose up -d
docker stop $(docker ps -q)
docker run -d -v ./www:/var/www/public_html php81fpm
docker run -d -p 80:80 -v ./vhost.conf:/etc/nginx/conf.d/default.conf -v ./www:/var/www/public_html nginx

### tasks
1. Использовать фреймворки и БД не требуется
2. ТЗ проверяет знания ООП, SOLID, DRY, KISS
3. Тесты (Unit, Functional) приветствуются
4. Проект должен быть завернут в docker контейнер, при запуске которого должен выполняться сценарий, указанный в ТЗ

### result
Работу сделал через кеш сессии. Использовать БД, конечно, было бы удобнее ))
Для запуска сценария необходимо открыть в браузере страничку localhost
Предварительно необходимо создать сеть docker network-php
`docker network create --driver bridge network-php`
Тесты пока не писал, как раз вспоминаю, как их подключить
В идеале дописать логгирование всех операций со счетом
