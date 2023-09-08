# How to install
## Requirements
You will need to install Tailscale manually and make sure all your vpses have been connected in the tailscale network.

For the requirements below, you can install it yourself if you want to use this dashboard without docker, otherwise these requirements has been provided in the docker-compose.
* Web server (Apache2, nginx, or others)
* PHP 8.2
* Mongodb + Mongodb php driver


## How to setup this dashboard
1. Copy the bots.json.example and put the talscale ip, username, and password for all the bots
`cp bots.json.example bots.json`

2. Copy the .env.example 
`cp .env.example .env`

2. Install some required libraries through composer 
composer install

3. Setup cronjob to fetch the data from APIs regularly
`*/10 * * * * /usr/bin/php <address to the folder>/scripts/fetch_data.php`
The command above will fetch the data every 10 minutes. Change it to suit your preference.

4. Go to scripts/fetch_data.php, find this line `if ($interval >= 600)` and change the value (in seconds) to match the time you set at your cronjob

5. Customize the page yourself to suit your preference, or you can just use it as it is.