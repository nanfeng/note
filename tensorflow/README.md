### Tensorflow笔记__人工智能实践
tensorflow:1.3.0最新版已经2.0了，为什么还用1.3.0版本呢，是因为学习了下面的视频，用2.0版本的会报错  
https://www.ixigua.com/pseries/6794284888146051596_6794017591590388231/

### docker环境
```
docker pull tensorflow/tensorflow:1.3.0
docker run -d -it -v ~/work/tensorflow:/ai tensorflow/tensorflow:1.3.0
```

### 文件目录
```
-rw-r--r--  1 root root 6348 Mar 18 06:26 0.png	//手写数字图片0-9
-rw-r--r--  1 root root 5580 Mar 18 06:21 2.png	//手写数字图片0-9
-rw-r--r--  1 root root 6533 Mar 18 06:26 3.png	//手写数字图片0-9
-rw-r--r--  1 root root 5853 Mar 18 06:22 4.png	//手写数字图片0-9
-rw-r--r--  1 root root 5794 Mar 18 06:12 5.png	//手写数字图片0-9
-rw-r--r--  1 root root 5775 Mar 18 06:11 6.png	//手写数字图片0-9
-rw-r--r--  1 root root 5618 Mar 18 06:27 7.png	//手写数字图片0-9
-rw-r--r--  1 root root 6218 Mar 18 06:11 8.png	//手写数字图片0-9
-rw-r--r--  1 root root 5805 Mar 18 06:28 9.png	//手写数字图片0-9
drwxr-xr-x  6 root root  192 Mar 18 05:24 data	//mnist数据
-rwxrwxrwx  1 root root 1708 Mar 18 06:19 mnist_app.py //测试手写图片
-rwxrwxrwx  1 root root 2042 Mar 18 06:08 mnist_backward.py //反向传播
-rwxrwxrwx  1 root root  657 Mar 18 05:33 mnist_forward.py //前向传播
-rwxrwxrwx  1 root root 1443 Mar 18 06:15 mnist_test.py //test
drwxr-xr-x 19 root root  608 Mar 18 06:26 model //模型记录
```

### 运行
```
//查看容器ID
$ docker ps
CONTAINER ID        IMAGE                         COMMAND                  CREATED             STATUS              PORTS                NAMES
a800b456462a        tensorflow/tensorflow:1.3.0   "/run_jupyter.sh --a…"   35 minutes ago      Up 35 minutes       6006/tcp, 8888/tcp   crazy_napier

//进入docker环境
$ docker exec -it a800b456462a /bin/sh 

//运行代码
# python mnist_backward.py

//测试手写图片
# python mnist_app.py
input the number of test picture: 9 //输入测试手写图片数量
the path of test picture  0.png //输入图片路径
the prediction number is  [9] //预测手写图片，把0误认为9了，其它的都正确
the path of test picture  2.png
the prediction number is  [2]
the path of test picture  3.png
the prediction number is  [3]
the path of test picture  4.png
the prediction number is  [4]
the path of test picture  5.png
the prediction number is  [5]
the path of test picture  6.png
the prediction number is  [6]
the path of test picture  7.png
the prediction number is  [7]
the path of test picture  8.png
the prediction number is  [8]
the path of test picture  9.png
the prediction number is  [9]
```