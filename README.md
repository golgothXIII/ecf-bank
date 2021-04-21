#ecf-bank
Application qui simule un portail de banque en ligne.

I - For deploy application :
- git clone https://github.com/golgothXIII/ecf-bank.git ecf-bank
- composer update
- yarn install --force
- yarn encore prod
- cp -r assets/images public/build

II - Modify .env file
 - APP_ENV=dev => APP_ENV=prod
 - Modify DATABASE_URL
 
 III - Databse initialize
 - php bin/console doctrine:database:create
 - php bin/console doctrine:migration:migrate
 
 
