### ajax带cookie发送请求
```
<script>
    var id = 1234;
    var file = `xxx`;
    $.ajax({
        type: 'POST',
        url: 'http://api.com.cn/upload',
        data: {id: id, file: file},
        xhrFields: {
            withCredentials: true // 这里设置了withCredentials，把cookie传入后端
        },
        success: function(data){
            console.log(data);
        }
    })
</script>

```
