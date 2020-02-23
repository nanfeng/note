# 搭建CI/CD环境

以 https://git.staff.sina.com.cn/ 为例
#### 1 获取token
```
打开 https://git.staff.sina.com.cn/xxx 找到自己的项目，找到Settings
  ->CI/CD
  ->Runners
  ->Set up a group Runner manually
  ->Use the following registration token during setup: pt-xpiCVNGzUZaq1234 
```

#### 2 注册
```
找一台同网络环境的一台Linux机器，执行
docker run -it --rm -v /srv/gitlab-runner/config:/etc/gitlab-runner gitlab/gitlab-runner register \
    --non-interactive \
    --url "https://git.staff.sina.com.cn/" \ #gitlab地址
    --registration-token "pt-xpiCVNGzUZaq1234" \ #上一步获取的token
    --executor "docker" \
    --docker-image golang:latest \
    --description "ent-cicd-docker-runner" \ #描述
    --tag-list "ent" \ #tag，多个以逗号分割，在项目代码的.gitlab-ci.yml中会用到tag
    --run-untagged="true" \
    --locked="false" \
    --access-level="not_protected"
```

#### 3 修改config文件的privileged和volumes
```
[root@localhost logstash]# cat /srv/gitlab-runner/config/config.toml
concurrent = 1
check_interval = 0

[session_server]
  session_timeout = 1800

[[runners]]
  name = "ent-cicd-docker-runner"
  url = "https://git.staff.sina.com.cn/"
  token = "FKPKkfWxwDLqitC1234"
  executor = "docker"
  [runners.custom_build_dir]
  [runners.docker]
    tls_verify = false
    image = "golang:latest"
    privileged = true #这里改成true，运行时需要足够的权限
    disable_entrypoint_overwrite = false
    oom_kill_disable = false
    disable_cache = false
    volumes = ["/certs/client", "/cache"] #增加"/certs/client",
    shm_size = 0
  [runners.cache]
    [runners.cache.s3]
    [runners.cache.gcs]
```

#### 4 启动
```
docker run -d --name gitlab-runner --restart always \
    -v /srv/gitlab-runner/config:/etc/gitlab-runner \
    -v /var/run/docker.sock:/var/run/docker.sock \
    gitlab/gitlab-runner:latest
```

#### 5 在自己的项目中增加Dockerfile
```
cat Dockerfile
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

#### 6 在自己的项目中增加.gitlab-ci.yml文件
```
每次提交代码时都会执行以下配置中的任务
cat .gitlab-ci.yml
image: php:7.2-fpm-alpine

variables:
before_script:
  - echo "before_script......"

after_script:
  - echo "after_script......"

stages:
  - release
  - push

job_release:
  stage: release
  variables:
    project: ent-api
    DOCKER_DRIVER: overlay2
    # Create the certificates inside this directory for both the server
    # and client. The certificates used by the client will be created in
    # /certs/client so we only need to share this directory with the
    # volume mount in `config.toml`.
    DOCKER_TLS_CERTDIR: "/certs"
  image: docker:git
#  services:
#    - docker:19.03.0-dind
  #  when: manual
  script:
    - rm -rf ${project}
    - day=$(date "+%Y-%m-%d %T")
    - git clone "https://${REGISTRY_USER}:${REGISTRY_PASSWORD}@git.staff.sina.com.cn/xxx/${project}.git"
    - cd ${project}
    - git status
    - git config --global user.email "${REGISTRY_USER}@staff.sina.com.cn"
    - git config --global user.name "${REGISTRY_USER}"
    - git tag -a "${CI_COMMIT_SHORT_SHA}" -m "gitlab cicd auto ${day} ${CI_COMMIT_SHORT_SHA}"
    - git push origin --tags
    - echo ${CI_COMMIT_SHORT_SHA}
  only:
    - master
  tags:
    - ent

job_push:
  stage: push
  variables:
    DOCKER_IMAGE: registry.docker.xxx.com/xxx/ent-api
    DOCKER_DRIVER: overlay2
    # Create the certificates inside this directory for both the server
    # and client. The certificates used by the client will be created in
    # /certs/client so we only need to share this directory with the
    # volume mount in `config.toml`.
    DOCKER_TLS_CERTDIR: "/certs"
  image: docker:git
  services:
    - docker:19.03.0-dind
  #  when: manual
  script:
    - env
    - docker info
    - TAG=${CI_COMMIT_SHORT_SHA}
    - docker build -t ${DOCKER_IMAGE}:${TAG} -t ${DOCKER_IMAGE}:latest .
    - echo "${REGISTRY_PASSWORD}" | docker login registry.docker.xxx.com --username "${REGISTRY_USER}" --password-stdin
    - docker push ${DOCKER_IMAGE}:${TAG}
    - echo ${DOCKER_IMAGE}:${TAG}
    - docker push ${DOCKER_IMAGE}:latest
  only:
    - tags
  except:
    - master
  tags:
    - ent

```

#### 7 正式环境部署
```
docker pull registry.docker.xxx.com/xxx/ent-api:latest
chmod 777 /logs/ent-api
docker run -d --rm -it -p 9000:9000 -v /logs/ent-api:/var/www/html/storage/logs registry.docker.xxx.com/xxx/ent-api:latest
```
