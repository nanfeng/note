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

### ELK搭建
```
准备2台机器a、b
a（IP任意，与b在同一网络）安装logstash，b（IP为10.10.10.10为例子）
a服务器执行
docker pull logstash:7.5.1
docker run -d --rm -it -v /httplogs:/logs -v /logstash/config/logstash.yml:/usr/share/logstash/config/logstash.yml -v /logstash/pipeline:/usr/share/logstash/pipeline logstash:7.5.1

logstash.yml文件内容：
http.host: "0.0.0.0"
xpack.monitoring.elasticsearch.hosts: [ "http://10.10.10.10:9200" ]

pipeline目录下的文件为abc.conf，内容为：
input {
    file {
        path => "/logs/ent_h5_nginx_access.log"
        codec => "json"
        type => "ent_h5"
    }
}
filter {
    mutate {
        split => [ "upstreamtime", "," ]
    }
    mutate {
        convert => [ "upstreamtime", "float" ]
    }
  mutate {
    rename => { "[host][name]" => "host" }
  }
}
output {
    elasticsearch {
        hosts => ["10.10.10.10:9200"]
        index => "logstash-%{type}-%{+YYYY.MM.dd}"
    }
}

#####################
服务器b执行
docker pull kibana:7.5.1
docker pull elasticsearch:7.5.1
docker network create elk
docker run -d -p 9200:9200 -p 9300:9300 --network=elk --name elasticsearch -e "discovery.type=single-node" elasticsearch:7.5.1
docker run -d -p 5601:5601 --network=elk --link elasticsearch -e ELASTICSEARCH_URL=http://elasticsearch:9200 --name kibana kibana:7.5.1

在浏览器中输入http://10.10.10.10:5601/
```
