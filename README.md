# How to install
## Requirements
I won't provide instructions on how to install these.
* Web server (Apache2, nginx, or others)
* PHP 8.2
* Mongodb + Mongodb php driver
* Tailscale

## How to setup this web
1. Copy the config.json.example and put the talscale ip, username, and password for all the bots
`cp data/config.json.example data/config.json`

2. Install some required libraries through composer 
composer install

3. Setup cronjob to fetch the data from APIs regularly
`*/10 * * * * /usr/bin/php <address to the folder>/scripts/fetch_data.php`
The command above will fetch the data every 10 minutes. Change it to suit your preference.

4. Go to scripts/fetch_data.php, find this line `if ($interval >= 600)` and change the value (in seconds) to match the time you set at your cronjob

5. Customize the page yourself to suit your preference, or you can just use it as it is.