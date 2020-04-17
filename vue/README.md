#### 安装
```
npm install -g yarn
yarn global add @vue/cli
yarn global add @vue/cli-service-global
```

#### 创建项目
```
vue create mydag
添加组件
vue add element-ui

npm install sass-loader 
安装node-sass  https://blog.csdn.net/ken_ding/article/details/85623092
SASS_BINARY_SITE=https://npm.taobao.org/mirrors/node-sass/ npm install node-sass

cp *.vue

yarn serve


//python2
yarn add vue-codemirror
yarn add jsplumb vuedraggable
```

#### 遇到的问题
```
1、jQuery not defined
在项目根目录下增加 vue.config.js，
const webpack = require('webpack')
module.exports = {
  //解决handelbars的错误
  chainWebpack: (config) => {
    config.resolve.alias
      // key,value自行定义，比如.set('@assets', resolve('src/assets'))
      .set('handlebars','handlebars/dist/handlebars.js')
  },
 //解决jQuery not defined
 configureWebpack: {
   plugins: [
      new webpack.ProvidePlugin({
        $:"jquery",
        jQuery:"jquery",
        "windows.jQuery":"jquery"
      })
    ]
  }
}


```
