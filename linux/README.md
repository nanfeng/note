### 磁盘清理
```
磁盘使用率超过90%时，清理一天前的文件

#!/bin/bash
#cleaning top two max applications' log reguarly when disk's usage exceeds 90%
maxUsed=$(df -h|awk 'NR==8 {print $5}'|sed 's/%$//')
std=90
echo "maxUsed: $maxUsed std: $std"
if [ "$maxUsed" -gt "$std" ];then
	echo "clean ...."
	find /upload/* -mmin +1440 -name "*" -exec rm {} \;
fi
```
