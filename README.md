# How to install
## Requirements
You will need to install Tailscale manually and make sure all your vpses have been connected in the tailscale network.

For the requirements below, you can install it yourself if you want to use this dashboard without docker, otherwise these requirements has been provided in the docker-compose.
* Web server (Apache2, nginx, or others)
* PHP 8.2
* Mongodb + Mongodb php driver
* Composer


## How to setup this dashboard
1. Add your current user to www-data group if you haven't
`sudo usermod -a -G www-data $USER`

2. Logout and login if you done step 2

3. Go to /var/www/ folder. Create it if it doesn't exist yet using this steps
```
cd /var
sudo mkdir www
sudo chown root:root /var/www
sudo chmod 755 /var/www/
cd www
```

4. Clone this project `sudo git clone https://github.com/stash86/ft-dashboard`

5. Give appropriate permissions
```
sudo chown -R $USER:www-data /var/www/ft-dashboard
sudo chmod -R 777 /var/www/ft-dashboard
```

6. Copy the bots.json.example into bots.json and put the talscale ip, username, and password for all the bots
`cp bots.json.example bots.json`

7. Copy the .env.example into .env and put change relevant info (if any)
`cp .env.example .env`

### Docker
```
docker-compose build
docker-compose run --rm php composer install
docker-compose up -d
```

### Non-docker
1. Install some required libraries through composer
`composer install`

2. Setup cronjob to fetch the data from APIs regularly `crontab -e`
`*/10 * * * * /usr/bin/php <address to the folder>/scripts/fetch_data.php`
The command above will fetch the data every 10 minutes. Change it to suit your preference.

3. Go to scripts/fetch_data.php, find this line `if ($interval >= 600)` and change the value (in seconds) to match the time you set at your cronjob

4. Customize the page yourself to suit your preference, or you can just use it as it is.


## Affiliate links
Vultr for server and bot hosting (you get $100 credit that expires in 14 days) https://www.vultr.com/?ref=8944192-8H

Binance.com (Non-US citizen) https://accounts.binance.com/en/register?ref=143744527


## Donation
Github sponsor - https://github.com/sponsors/stash86/

Patreon (for donation only, no extra perks) - https://patreon.com/stash86

## Crypto
BTC: 1FghqtgGLpD9F21BNDMje4iyj4cSzVPZPb
ERC20 : 0x1b7b65e64f3d944d29ba025c3ad0bb9389492370
TRC20 : TDqRvLXwbkCkBrhdsCm7aDNhfzeJqLRr94
BEP20 : 0x1b7b65e64f3d944d29ba025c3ad0bb9389492370