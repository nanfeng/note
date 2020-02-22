### JSON格式日志
```
http下定义log格式我为log_json
log_format log_json '{"@timestamp":"$time_iso8601",'
                     '"host":"$server_addr",'
                     '"clientip":"$remote_addr",'
                     '"size":$body_bytes_sent,'
                     '"responsetime":$request_time,'
                     '"upstreamtime":"$upstream_response_time",'
                     '"upstreamhost":"$upstream_addr",'
                     '"http_host":"$host",'
                     '"url":"$uri",'
                     '"xff":"$http_x_forwarded_for",'
                     '"referer":"$http_referer",'
                     '"agent":"$http_user_agent",'
                     '"status":"$status"}';

http、server、localtion中使用log_json
    location /yearposter/ {
        access_log /httplogs/yearposter_nginx_access.log log_json; #日志位置和日志级别
    }
```

### 反向代理
```
localtion 中使用proxy_pass
location /valentine/ {
    add_header x-via '45' ; #增加头信息
    error_log /httplogs/ent_h5_nginx_error.log; #日志位置和日志级别
    access_log /httplogs/ent_h5_nginx_access.log log_json; #日志位置和日志级别
    client_max_body_size 20M;
    proxy_redirect off;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_pass http://ent_h5_server/;
}

http中使用upstream
upstream ent_h5_server {
    server localhost:8080;
    server localhost:8090;
}
```
