# FT Dashboard

## Link to live websites

* Free version <http://ft-dashboard.ddns.net/free.php>
* Premium version <http://ft-dashboard.ddns.net/>

## How to get the premium version

Being a sponsor of my [github](#donation) will give you access to the private premium repo and ft-discord repo.
You have to choose monthly payment instead of one-time payment. Choosing monthly payment will give you access to the repo, while choosing one-time payment won't. Don't ask me why, it's how github sponsor is right now

## What's available on premium version

* Ability to filter chart based of date
* Open Trade table update in background automatically every 1 minute
* Performance Summary table and profit chart update in background automatically every 5 minute

## How to install

### Initial setup

Skip this step if you think you have done them previously

* [Create sudo-enabled non-root user](https://botacademy.ddns.net/2023/09/13/how-to-create-new-sudo-enabled-user/)
* [Install PHP8.2](https://botacademy.ddns.net/2023/09/13/install-php8-2-and-modules-on-ubuntu/).
* [Install docker-compose](https://botacademy.ddns.net/2023/09/13/how-to-install-docker-compose-on-ubuntu/)
* Install Tailscale

### Setup the dashboard

1. Clone this project `sudo git clone https://github.com/stash86/ft-dashboard`

2. Copy the bots.json.example into bots.json and put the talscale ip, username, and password for all the bots
`cp bots.json.example bots.json`

3. Run `id` to get the uid and gid to be used in the next step

4. Copy the .env.example into .env and put change relevant info (especially uid and gid)
`cp .env.example .env`

#### Docker installation

```bash
docker-compose build php_ft cron_ft
docker-compose run --rm php composer install
docker-compose up -d php_ft cron_ft nginx_ft pg_db
```

#### Create SSH certificate

Replace `[domain-name]` and `[email]` at the command below with the real domain of your dashboard and your email address, for example `example.org`

```bash
docker-compose run --rm certbot certonly --webroot --webroot-path /var/www/certbot/ --force-renewal -d [domain-name] --email [email] --agree-tos
cp nginx/conf.d/default.conf.ssh nginx/conf.d/default.conf
vi nginx/conf.d/default.conf
```

Replace all [domain-name] with your actual domain name. After that, run

```bash
docker-compose restart nginx_ft
```

#### Non-docker

1 install PostgreSQL

```bash
sudo apt update
sudo apt install postgresql postgresql-contrib
```

For more details, see the official docs: <https://www.postgresql.org/download/linux/ubuntu/>

2 Install some required libraries through composer

```bash
composer install
bash init_db.sh
```

3 Setup cronjob to fetch the data from APIs regularly `crontab -e`
`*/10 * * * * /usr/bin/php <address to the folder>/scripts/fetch_data.php`
The command above will fetch the data every 10 minutes. Change it to suit your preference.

4 Customize the page yourself to suit your preference, or you can just use it as it is.

### Troubleshooting

* `Error response from daemon: driver failed programming external connectivity on endpoint ft-dashboard-nginx-1 (5715ad4646ad5d120e65509981a1c790b0a7c98eded19a3f6752d759ae9c67e2): Error starting userland proxy: listen tcp4 0.0.0.0:80: bind: address already in use`
Means port 80 is being used right now. Try `sudo lsof -i:80` to see which services using port 80. Tailscale will be one of them, but see whether apache2 is active as well. If it does and you still want to use the docker route (which will use nginx), either stop or remove apache2.

## Available configurations

This settings can be set via `.env` file

### CRON_MINUTES

How often the dashboard fetch data from freqtrade bots. Default is 10

### API

Whether [API endpoints](#api-endpoints) being enabled or not. Default is false

### DEBUG

Whether [debug mode](#debug) being enabled or not. Default is false

### START_0

Whether the profit chart's first point start from zero point. Default is false

### CHART_MIN_TRADES

How many closed trades required before a strategy's profit being plotted in the chart. Default is 2

### BOT_NAME_AS_INDEX

Instead as using strategy's class name, use `bot_name` as index in databae

### MAX_CLOSED_TRADES_SHOWN

How many latest closed trades are shown for each bot. `-1` means all closed trades are shown.

## API Endpoints

By default, the API isn't enabled. You can activate API endpoints by setting `API=true` in your `.env` file. The supported responses for now are `strategy` for the strategy class name, `strategy_version` for the strategy version, `profit` for performance summary, `trades` for closed trades, `status` for open trades, and `chart_profit_data` for the data needed to plot the profit chart.
The link to access the api is `http://<your ip or domain>/api.php?response=status&bot_id=<bot_id>`
Note that `bot_id` start from 1, and it follow the order of your input inside `bots.json`

## Debug

There are several scripts that are used to debug several aspects of the dashboard. To use it, you need to set `DEBUG=true` in your `.env` file. The debug scripts are located at `/public/debug` folder.

**WARNING!!!**

When you enable this, this means anyone can access those scripts as well. While the scripts won't be able to alter the dashboard, it can be subjected to some DDoS attacks. It's advisable to only enable this mode when you need it, and turn it off after you are done.

## How to update

`git pull` followed by

### Docker

```
docker-compose down
docker-compose run --rm php composer update
docker-compose up -d --build
```

### Non-docker

`composer update`

## Links

### Affiliate Links

* Vultr for server and bot hosting (you get $100 credit that expires in 14 days) <https://www.vultr.com/?ref=8944192-8H>
* Binance.com (Non-US citizen) <https://accounts.binance.com/en/register?ref=143744527>

### Donation

* Github sponsor - <https://github.com/sponsors/stash86/>

### Crypto

* BTC: 1FghqtgGLpD9F21BNDMje4iyj4cSzVPZPb
* ERC20 : 0x1b7b65e64f3d944d29ba025c3ad0bb9389492370
* TRC20 : TDqRvLXwbkCkBrhdsCm7aDNhfzeJqLRr94
* BEP20 : 0x1b7b65e64f3d944d29ba025c3ad0bb9389492370
