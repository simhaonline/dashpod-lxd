#!/bin/bash

#Set the password for the web service
echo $ADMIN_PASS | htpasswd -c -i /etc/nginx/.htpasswd admin


#Setup LXC data directory
if [ ! -d /var/dashpod/data/lxc ]
then
  mkdir -p /var/dashpod/data/lxc
fi


#Create LXD cert if necessary, because LXD dameon is not running we need to make a fake remote connection to create it
if [ -f /var/dashpod/data/lxc/client.crt ]
then
  mkdir -p $HOME/.config/lxc/
  cp -a /var/dashpod/data/lxc/client.crt $HOME/.config/lxc/client.crt
  cp -a /var/dashpod/data/lxc/client.key $HOME/.config/lxc/client.key

else
  lxc remote add localhost
  cp -a $HOME/.config/lxc/client.crt /var/dashpod/data/lxc/client.crt
  cp -a $HOME/.config/lxc/client.key /var/dashpod/data/lxc/client.key
fi


#Pull LXC remotes from db and put back into LXD client remotes
if [ -f /var/dashpod/data/sqlite/dashpod.sqlite ]
then
  cmd="SELECT * FROM lxd_remotes"
  IFS=$'\n'
  fqry=(`sqlite3 /var/dashpod/data/sqlite/dashpod.sqlite "$cmd"`)

  for f in "${fqry[@]}"; do
    name=$(echo "$f" | cut -d "|" -f 1)
    host=$(echo "$f" | cut -d "|" -f 2)
    port=$(echo "$f" | cut -d "|" -f 3)
    return_value=$(lxc remote add "$name" $host:$port --accept-certificate 2>&1)
    #Add exit status and return value to table
    cmd="UPDATE lxd_remotes set exit_status='$?', return_value='$return_value' WHERE name='$name'"
    sqlite3 /var/dashpod/data/sqlite/dashpod.sqlite "$cmd"
  done
fi


#Create SQLite database directory if needed
if [ ! -d /var/dashpod/data/sqlite ]
then
  mkdir -p /var/dashpod/data/sqlite
fi
chown -R www-data:www-data /var/dashpod/data/sqlite


#Start PHP for NGINX
service php7.4-fpm start


#Clear bash history
history -c


#Start the main service
nginx -g "daemon off;"
