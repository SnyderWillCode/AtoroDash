#!/bin/bash
clear


# Check if MythicalClient is already installed
INSTALL_FLAG="/opt/mythicaldash/.installed"

if [ -f "$INSTALL_FLAG" ]; then
    echo -e "\r\x1b[31;1m┃\x1b[0;31m MythicalClient is already installed. Exiting...\x1b[0m"
    exit 0
fi

echo -e "\n\x1b[35;1m
 ███▄ ▄███▓▓██   ██▓▄▄▄█████▓ ██░ ██  ██▓ ▄████▄   ▄▄▄       ██▓    
▓██▒▀█▀ ██▒ ▒██  ██▒▓  ██▒ ▓▒▓██░ ██▒▓██▒▒██▀ ▀█  ▒████▄    ▓██▒    
▓██    ▓██░  ▒██ ██░▒ ▓██░ ▒░▒██▀▀██░▒██▒▒▓█    ▄ ▒██  ▀█▄  ▒██░    
▒██    ▒██   ░ ▐██▓░░ ▓██▓ ░ ░▓█ ░██ ░██░▒▓▓▄ ▄██▒░██▄▄▄▄██ ▒██░    
▒██▒   ░██▒  ░ ██▒▓░  ▒██▒ ░ ░▓█▒░██▓░██░▒ ▓███▀ ░ ▓█   ▓██▒░██████▒
░ ▒░   ░  ░   ██▒▒▒   ▒ ░░    ▒ ░░▒░▒░▓  ░ ░▒ ▒  ░ ▒▒   ▓▒█░░ ▒░▓  ░
░  ░      ░ ▓██ ░▒░     ░     ▒ ░▒░ ░ ▒ ░  ░  ▒     ▒   ▒▒ ░░ ░ ▒  ░
░      ░    ▒ ▒ ░░    ░       ░  ░░ ░ ▒ ░░          ░   ▒     ░ ░   
    ░    ░ ░               ░  ░  ░ ░  ░ ░            ░  ░    ░  ░
         ░ ░                          ░                          
         
\x1b[0m"

mkdir -p /opt/mythicaldash
mkdir -p /opt/mythicaldash/installer_logs

echo -e "
\x1b[35;1m┃  Welcome to MythicalClient
\x1b[35;1m┃\x1b[0m
\x1b[35;1m┃\x1b[0m Thanks for downloading MythicalClient! We're
\x1b[35;1m┃\x1b[0m are so excited to have you here. If you have
\x1b[35;1m┃\x1b[0m any questions or need help, feel free to reach
\x1b[35;1m┃\x1b[0m us at any of the following:
\x1b[35;1m┃\x1b[0m
\x1b[35;1m┃ ☻ \x1b[0msupport@mythical.systems
\x1b[35;1m┃ ☻ \x1b[0mhttps://github.com/mythicalltd/mythicaldash/issues
\x1b[35;1m┃ ☻ \x1b[0mhttps://discord.mythical.systems
"

printf "\n\x1b[2;1m┃\x1b[0;2m Installing dependencies. \x1b[0m"
sleep 1
# Install the dependencies
apt install sudo wget curl git zip unzip -y >> /opt/mythicaldash/installer_logs/apt-logs.log 2>&1
printf "\n\x1b[2;1m┃\x1b[0;2m Installed all dependencies. \x1b[0m"
sleep 1
printf "\n\x1b[2;1m┃\x1b[0;2m Installing docker. \x1b[0m"
sleep 1
# Install Docker if not installed
if ! [ -x "$(command -v docker)" ]; then
    curl -sSL https://get.docker.com/ | CHANNEL=stable bash >> /opt/mythicaldash/installer_logs/logs-docker-install.log 2>&1
    sudo systemctl enable --now docker >> /opt/mythicaldash/installer_logs/docker-systemd.log 2>&1
fi
printf "\n\x1b[2;1m┃\x1b[0;2m Installed docker. \x1b[0m"
sleep 1

printf "\n\x1b[2;1m┃\x1b[0;2m Installing docker-compose. \x1b[0m"
sleep 1
# Install Docker Compose if not installed
if ! [ -x "$(command -v docker-compose)" ]; then
    apt install docker-compose -y  >> /opt/mythicaldash/installer_logs/docker-compose-apt.log 2>&1
fi
printf "\n\x1b[2;1m┃\x1b[0;2m Installed docker-compose. \x1b[0m"
sleep 1

clear
echo -e "\n
\x1b[35;1m┃  Software agreements
\x1b[35;1m┃\x1b[35
\x1b[35;1m┃\x1b[0m By using MythicalClient you (the owner and
\x1b[35;1m┃\x1b[0m ALL your clients) agree to our software
\x1b[35;1m┃\x1b[0m agreements listed on our homepage at: 
\x1b[35;1m┃\x1b[35
\x1b[35;1m┃\x1b[0m https://www.mythical.systems/eula
"
printf "\x1b[2;1m┃\x1b[0;2m Type 'AGREE' to continue and agree to our software agreements.\x1b[0m"
printf "\n\x1b[2;1m┃\x1b[0;2m Type 'DISAGREE' to exit the installation.\x1b[0m"
read -p " " AGREEMENT
if [ "$AGREEMENT" != "AGREE" ]; then
    echo -e "\x1b[31;1m┃\x1b[0;31m You must agree to our software agreements to continue.\x1b[0m"
    exit 1
fi

printf "\n\x1b[2;1m┃\x1b[0;2m Downloading files.. \x1b[0m"
sleep 1

cd /opt/mythicaldash
curl -Lo MythicalDash.zip https://github.com/MythicalLTD/MythicalDash-Nightly/releases/latest/download/MythicalDash.zip >> /opt/mythicaldash/installer_logs/download-log.log 2>&1 # TODO: Replace to the release channel!!
unzip MythicalDash.zip >> /opt/mythicaldash/installer_logs/unzip-log.log 2>&1
printf "\n\x1b[2;1m┃\x1b[0;2m Files downloaded. \x1b[0m"
sleep 1

printf "\n\x1b[2;1m┃\x1b[0;2m Preparing docker environment. \x1b[0m"
sleep 1

rm -rf ./backend/storage/.env
cp ./backend/storage/.docker.env ./backend/storage/.env

printf "\n\x1b[2;1m┃\x1b[0;2m Building docker image. (May take some time) [AVG 5m] \x1b[0m"
sleep 1
# Start the build process
docker-compose --env-file ./backend/storage/.env up -d --build >> /opt/mythicaldash/installer_logs/logs-docker.log 2>&1

printf "\n\x1b[2;1m┃\x1b[0;2m Docker image built! \x1b[0m"
sleep 1

printf "\n\x1b[2;1m┃\x1b[0;2m Setting permissions.\x1b[0m"
sleep 1
# Set the right permissions
chown -R www-data:www-data ./
chmod -R 777 ./
printf "\n\x1b[2;1m┃\x1b[0;2m Permission set.\x1b[0m"
sleep 1

printf "\n\x1b[2;1m┃\x1b[0;2m Updating internal packages. \x1b[0m"
sleep 1
# Update dependencies
docker exec mythicalclient_backend bash -c "COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader"  >> /opt/mythicaldash/installer_logs/composer-apt.log 2>&1
printf "\n\x1b[2;1m┃\x1b[0;2m Updated internal packages. \x1b[0m"
echo ""
sleep 1
# Reset the encryption key 
# Check if the installation has already been completed
INSTALL_FLAG=".installed"

if [ ! -f "$INSTALL_FLAG" ]; then
    # Run the installation steps
    touch "$INSTALL_FLAG"
    docker exec mythicalclient_backend bash -c "php mythicalclient keyRegen -force"
else
printf "\n\x1b[2;1m┃\x1b[0;2m MythicalClient already installed!!!!! \x1b[0m"
fi

# Migrations
# Wait for the database container to be ready
while [ "$(docker inspect -f '{{.State.Health.Status}}' mythicalclient_database)" == "starting" ]; do
    printf "\n\x1b[2;1m┃\x1b[0;2m Waiting for MySQL database to start \x1b[0m"
    sleep 5
done
printf "\n\x1b[2;1m┃\x1b[0;2m MySQL started and is ready to serve! \x1b[0m"
sleep 2.5

while [ "$(docker inspect -f '{{.State.Health.Status}}' mythicalclient_redis)" == "starting" ]; do
    printf "\n\x1b[2;1m┃\x1b[0;2m Waiting for Redis to start \x1b[0m"
    sleep 5
done
printf "\n\x1b[2;1m┃\x1b[0;2m Redis started and is ready to serve! \x1b[0m"
sleep 2.5

printf "\n\x1b[2;1m┃\x1b[0;2m Running database migrations.. \x1b[0m"
sleep 2.5
echo ""
docker exec mythicalclient_backend bash -c "php mythicalclient migrate"
printf "\n\x1b[2;1m┃\x1b[0;2m MythicalClient is up to date! \x1b[0m"
sleep 1

# Clean up installation files
printf "\n\x1b[2;1m┃\x1b[0;2m Cleaning up installation files.. \x1b[0m"
sleep 1
rm -rf /opt/mythicaldash/MythicalDash.zip
rm -rf /opt/mythicaldash/installer_logs

printf "\n\x1b[2;1m┃\x1b[0;2m Clean up completed. \x1b[0m"
sleep 1

echo -e "\n\x1b[32;1m┃ MythicalClient installation completed successfully! \x1b[0m"
echo -e "\x1b[32;1m┃\x1b[0m"
echo -e "\x1b[32;1m┃ You can now start using MythicalClient. \x1b[0m"
echo -e "\x1b[32;1m┃\x1b[0m"
echo -e "\x1b[32;1m┃ For more information, visit: \x1b[0m"
echo -e "\x1b[32;1m┃\x1b[0m https://mythicalclient.mythical.systems"
echo -e "\x1b[32;1m┃\x1b[0m"
echo -e "\x1b[32;1m┃ Thank you for choosing MythicalClient! \x1b[0m"
echo -e "\x1b[32;1m┃\x1b[0m"
echo -e "\x1b[36;1m┃ You can access it at: \x1b[32;1mhttp://$(hostname -I | awk '{print $1}'):9271 \x1b[0m"
echo -e "\x1b[36;1m┃ Working directory: \x1b[32;1m/opt/mythicaldash \x1b[0m"
echo -e "\x1b[36;1m┃ Channel: \x1b[32;1mdevelop (NO SUPPORT) \x1b[0m"
echo -e "\x1b[36;1m┃ License: \x1b[32;1mfree (NO SUPPORT) \x1b[0m"
echo -e "\x1b[32;1m┃\x1b[0m"
echo -e "\x1b[32;1m┃ Make sure you read our docs on how to use a domain and SSL. \x1b[0m"
echo -e "\x1b[32;1m┃ We recommend you use cloudflare tunnels for this installation."
echo -e "\x1b[0m"
