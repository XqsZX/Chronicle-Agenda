## 简介
实现了一个功能齐全的前端界面和后端服务，使用户能够发布新闻、评论、点赞或点踩，并管理他们的日程。

## 网络安全
- 为防止SQL注入，我使用了预处理查询来安全地处理数据库交互。
- 通过加盐哈希密码，增强了密码存储的安全性，有效防止了彩虹表攻击。
- 在表单中加入了令牌，以防止跨站请求伪造（CSRF）攻击。
- 在服务器端检查所有前提条件，防止功能滥用攻击，并确保应用能够抵御XSS攻击，特别是在处理JSON数据时。

## 项目链接

### 消息发布界面
http://ec2-3-141-32-140.us-east-2.compute.amazonaws.com/~XqsZX0611/module3/index.php

### 日历界面
http://ec2-3-141-32-140.us-east-2.compute.amazonaws.com/~XqsZX0611/module5/Calendar/index.php
