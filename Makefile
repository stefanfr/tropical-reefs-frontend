composer :
	composer install --no-dev --no-scripts

npm:
	npm install
	npm run build

permissions:
	chown -R www-data:www-data *
	composer dump-autoload --optimize --classmap-authoritative

translations:
	bin/console app:localize:vue

deploy: composer npm permissions translations