## 商城插件仓库
### 存放商城所有插件, 插件代码与商城代码分开

### 使用方式
 1. 先拉取 ```flexmall``` 商城项目代码 
   * flexmall: https://github.com/flexmall/flexmall.git
   * ``` git clone https://github.com/flexmall/flexmall.git ```
 2. 以git子模块的方式拉取插件代码
   * plugin: https://github.com/flexmall/plugin.git
   * ```git submodule add -f https://github.com/flexmall/plugin.git app/Plugin```

### 插件代码开发通过git同步
### 插件代码仓库仅在本地开发使用, 线上环境通过后台在线安装所需插件