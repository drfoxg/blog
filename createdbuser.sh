#!/bin/bash

# create random password
#APP_DB_PASSWORD="$(openssl rand -base64 12)"

# replace "-" with "_" for database username
#MYSQL_DATABASE=${USER_NAME//[^a-zA-Z0-9]/_}

# If /root/.my.cnf exists then it won't ask for root password
if [ -f /root/.my.cnf ]; then

    #mysql -e "CREATE DATABASE ${MYSQL_DATABASE} /*\!40100 DEFAULT CHARACTER SET utf8 */;"
    mysql -e "CREATE USER ${APP_DB_USERNAME}@'%' IDENTIFIED BY '${APP_DB_PASSWORD}';"
    #mysql -e "CREATE USER ${APP_DB_USERNAME}@localhost IDENTIFIED BY '${APP_DB_PASSWORD}';"
    mysql -e "GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO '${APP_DB_USERNAME}'@'%';"
    #mysql -e "GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO '${APP_DB_USERNAME}'@'localhost';"
    mysql -e "FLUSH PRIVILEGES;"

# If /root/.my.cnf doesn't exist then it'll ask for root password
else
    #echo "Please enter root user MySQL password!"
    #echo "Note: password will be hidden when typing"
    #read -sp rootpasswd
    #mysql -uroot -p${rootpasswd} -e "CREATE DATABASE ${MYSQL_DATABASE} /*\!40100 DEFAULT CHARACTER SET utf8 */;"
    mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "CREATE USER ${APP_DB_USERNAME}@'%' IDENTIFIED BY '${APP_DB_PASSWORD}';"
    #mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "CREATE USER ${APP_DB_USERNAME}@localhost IDENTIFIED BY '${APP_DB_PASSWORD}';"
    mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO '${APP_DB_USERNAME}'@'%';"
    #mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "GRANT ALL PRIVILEGES ON ${MYSQL_DATABASE}.* TO '${APP_DB_USERNAME}'@'localhost';"
    mysql -uroot -p${MYSQL_ROOT_PASSWORD} -e "FLUSH PRIVILEGES;"
fi
