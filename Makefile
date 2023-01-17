composer :
	composer install --no-dev --no-scripts

npm:
	npm install
	npm run build

permissions:
	chown -R www-data:www-data *
	composer dump-autoload --optimize --classmap-authoritative

deploy: composer npm permissions