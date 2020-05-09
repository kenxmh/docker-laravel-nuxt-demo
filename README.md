# docker-laravel-nuxt

>这只是一个前后端分离的尝试，运行于docker环境
>前端使用nuxt，laravel只做api提供数据，后台管理使用element-admin
>该项目是以一个简单的blog为例

## 简介：
* 前端
nuxt + buefy 的简单博客
* api
Laravel 5.8 + JWT
* admin
基于vue-elememt-admin的修改
功能仅包括：RBAC的权限管理和文章管理

## 如何使用：

### 第一步：配置 docker 环境

#### 1、配置 .env
.env 的主要配置项
* 各服务的版本（如果想换版本，需要先去[Docker hub](https://hub.docker.com/search?q=&type=image)确认版本是否存在）
* 各服务的本地的数据卷路径（如果想调整目录结构）
* PHP所需扩展（可选扩展基本齐全，按项目所需安装即可）
* 数据库密码

#### 2、配置 docker-compose.yml
docker-compose.yml 的主要配置项
* 端口（可以修改端口映射，或者项目较多可以添加新端口）
* 数据卷的对应（基本无需修改）

#### 3、构建镜像和后台挂载
```
docker-compose build
docker-compose up -d
```

### 第二步：配置 Laravel

#### 1、配置 .env
* 创建一个数据库
* 修改数据库名，数据库用户，数据库密码

#### 2、进入容器 `docker exec -it php /bin/bash`
#### 3、在容器内安装依赖 `composer install`
#### 4、在容器内执行命令
```
// 生成项目app key
php artisan key:generate

// 生成jwt key
php artisan jwt:secret

// 导入数据表
php artisan migrate

// 导入数据
php artisan db:seed
```

#### 5、配置nginx conf

### 第三步：配置 Element-Admin

#### 1、进入容器 `docker exec -it node sh`
#### 2、在容器安装依赖
```
npm config set registry https://registry.npm.taobao.org
npm i node-sass --sass_binary_site=https://npm.taobao.org/mirrors/node-sass/
npm rebuild node-sass
npm install

// 开发
npm run dev

// 部署
npm run build:prod
```

### 第四步：配置 Nuxt

#### 1、进入容器 `docker exec -it node sh`
#### 2、在容器安装依赖
```
npm config set registry https://registry.npm.taobao.org
npm i node-sass --sass_binary_site=https://npm.taobao.org/mirrors/node-sass/
npm rebuild node-sass
npm install

// 开发
npm run dev

// 部署
npm run build
npm run start
```