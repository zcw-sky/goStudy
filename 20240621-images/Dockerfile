# 基础镜像为 Ubuntu
FROM ubuntu:20.04

# 环境变量设置
ENV DEBIAN_FRONTEND=noninteractive

# 更新软件源并安装必要的软件包
RUN apt-get update && apt-get install -y \
    nginx \
    php-fpm \
    php-mysql \
    mysql-server \
    supervisor \
    && apt-get clean

# 配置 MySQL
RUN service mysql start && mysql -e "CREATE USER 'newuser'@'%' IDENTIFIED BY 'newpassword';" \
    && mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'newuser'@'%' WITH GRANT OPTION;" \
    && mysql -e "FLUSH PRIVILEGES;"

# 配置 PHP-FPM
RUN echo "cgi.fix_pathinfo=0" >> /etc/php/7.4/fpm/php.ini

# 配置 Nginx
COPY nginx/default.conf /etc/nginx/sites-available/default

# 配置 Supervisor
COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# 创建必要的目录
RUN mkdir -p /var/www/html
COPY html/ /var/www/html/

# 暴露端口
EXPOSE 80 3306

# 启动 Supervisor 管理 Nginx、PHP-FPM 和 MySQL
CMD ["/usr/bin/supervisord"]
