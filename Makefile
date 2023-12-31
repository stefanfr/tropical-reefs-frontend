composer :
	composer install --no-dev --no-scripts

npm:
	npm ci
	npm run build

permissions:
	chown -R www-data:www-data *
	composer dump-autoload --optimize --classmap-authoritative

translations:
	bin/console app:localize:vue

deploy: composer translations npm permissions