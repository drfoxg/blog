# tgwsapiproxy/Dockerfile

FROM python:3.12-alpine

RUN apk add --no-cache git
RUN pip install --no-cache-dir "git+https://github.com/winchanii/TgWsApiProxy"

# CMD не нужен — команда задаётся в docker-compose.yml
