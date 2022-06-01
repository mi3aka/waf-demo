FROM debian:9-slim
RUN sed -i 's/deb.debian.org/mirrors.ustc.edu.cn/g' /etc/apt/sources.list; \
    sed -i 's/security.debian.org/mirrors.ustc.edu.cn/g' /etc/apt/sources.list; \
    apt-get update && apt-get upgrade -y; \
    apt-get install gnupg wget curl apt-transport-https lsb-release ca-certificates unzip nano -y; \
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://mirror.sjtu.edu.cn/sury/php/apt.gpg; \
    echo "deb https://mirror.sjtu.edu.cn/sury/php/ stretch main" > /etc/apt/sources.list.d/php.list; \
    apt-get update; \
    apt-get install php5.6 php5.6-cli php5.6-common php5.6-curl php5.6-mbstring php5.6-mysqlnd php5.6-xml mariadb-server -y; \
    /etc/init.d/mysql start; \
    mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root' WITH GRANT OPTION;FLUSH PRIVILEGES;GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' IDENTIFIED BY 'root' WITH GRANT OPTION;FLUSH PRIVILEGES;"; \
    mysql -uroot -proot -e "show databases;"; \
    echo "<Directory /var/www/>\n        Options Indexes FollowSymLinks\n        AllowOverride All\n        Require all granted\n</Directory>" >> /etc/apache2/sites-enabled/000-default.conf; \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf; \
    /etc/init.d/apache2 restart
COPY waf /tmp/waf
RUN wget https://github.com/OWASP/Vulnerable-Web-Application/archive/refs/heads/master.zip -P /tmp; \
    unzip /tmp/master.zip -d /var/www; \
    rm /tmp/master.zip; \
    rm -rf /var/www/html/; \
    mv /var/www/Vulnerable-Web-Application-master /var/www/html; \
    mv /tmp/waf /var/www/html; \
    sed -i "s/\$dbpass = '';/\$dbpass = 'root';/g" /var/www/html/index.php; \
    chown -R www-data:www-data /var/www/html; \
    mv /var/www/html/waf/htaccess /var/www/html/.htaccess
WORKDIR /var/www/html/
CMD /etc/init.d/apache2 restart && /etc/init.d/mysql restart && tail -F /var/log/apache2/access.log;
