# Instalacja projektu
git clone https://github.com/Saneczka/projekt_si_new .
composer install
chmod -R 777 ./var
chmod -R 777 ./public/upload
php bin/console doctrine:schema:create
php bin/console doctrine:fixtures:load -n
php bin/console cache:clear

# PHP Code Sniffer instalacja
composer require --dev squizlabs/php_codesniffer
composer require --dev escapestudios/symfony2-coding-standard
vendor/bin/phpcs --config-set installed_paths vendor/escapestudios/symfony2-coding-standard
# PHP Code Sniffer uruchomienie
./vendor/bin/phpcs --standard=Symfony ./src
# PHP Code Sniffer automatyczna naprawa błędów
./vendor/bin/phpcbf --standard=Symfony ./src

# Generowanie dokumentacji instalacja 
cd ~
mkdir phpdoc
cd phpdoc
wget https://phpdoc.org/phpDocumentor.phar
chmod +x phpDocumentor.phar
cd project_dir
# Generowanie dokumentacji uruchomienie
~/phpdoc/phpDocumentor.phar -d src/ -t doc/
