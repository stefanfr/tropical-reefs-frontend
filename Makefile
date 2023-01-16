composer :
	composer install

npm:
	npm install
	npm run build

database:
	php bin/console doctrine:schema:update --force --complete
	php bin/console doctrine:schema:update --force --em=manager --complete

permissions:
	chown -R www-data:www-data *
	composer dump-autoload --optimize --classmap-authoritative

deploy: composer database npm permissions