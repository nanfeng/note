### Dockerfile
```
FROM golang:1.13.4
ENV TZ="Asia/Shanghai"
ENV GO111MODULE="on"
ENV GIN_MODE="release"
WORKDIR /data1/ms/ent_h5
COPY . .
RUN go build -mod=vendor -o main
CMD ["./main"]
EXPOSE 80
```

### php Dockerifle
```
FROM php:7.2-fpm-alpine
ENV TZ="Asia/Shanghai"
WORKDIR /var/www/html
COPY . .
RUN apk add gnu-libiconv && \
	chmod 777 /usr/lib/preloadable_libiconv.so && \
	docker-php-ext-install pdo_mysql && \
	cp .env.product .env && \
	chown -R www-data.www-data . && \
	cp etc/php-fpm.conf /usr/local/etc && \
	cp etc/www.conf /usr/local/etc/php-fpm.d
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php
CMD ["php-fpm"]
EXPOSE 9000

```

### 镜像加速
```
/etc/docker/daemon.json

{
  "registry-mirrors": ["https://9kt9f6mz.mirror.aliyuncs.com"]
}
```

### 网络
```
创建ent
[root@localhost conf.d]# docker network create ent
查看
[root@localhost conf.d]# docker network ls
NETWORK ID          NAME                DRIVER              SCOPE
51c24352eb74        bridge              bridge              local
9334ae868022        ent                 bridge              local
967ce07d6048        host                host                local
7fff1d687947        none                null                local
使用
docker run -d --rm -it --network=ent -p 8090:80 -v /ent_h5/log:/ent_h5/log registry.docker.xxx.com/ent_h5:latest
```
