[INSTALLATION WINDOWS (WAMP)](https://github.com/alexdu98/TERM1/blob/master/README.md#installation-windows-wamp)
[INSTALLATION UBUNTU (LAMP)](https://github.com/alexdu98/TERM1/blob/master/README.md#installation-ubuntu-lamp)

## INSTALLATION WINDOWS (WAMP)
#### Installer WAMP
Téléchargez et installez [WAMP](http://www.wampserver.com/).

#### Configurer WAMP
Dans le fichier C:\wamp\bin\apache\apache2.4.23\conf\extra\httpd-vhosts.conf, ajoutez
```
<VirtualHost *:80>
	ServerName term1.calyxe.dev

	DocumentRoot C:/wamp/www/TERM1/web

	<Directory  "C:/wamp/www/TERM1/web">
		AllowOverride All
		Order Allow,Deny
        Allow from All
	</Directory>

	ErrorLog C:/wamp/www/TERM1/error.log
  CustomLog C:/wamp/www/TERM1/access.log combined
</VirtualHost>
```
Redémarrer WAMP.

#### Configurer le host
Copiez le fichier C:\Windows\System32\drivers\etc\hosts sur votre bureau, puis ajoutez 
```
127.0.0.1 term1.calyxe.dev
```

#### Ajouter PHP à votre PATH
Trouvez le répertoire de PHP dans WAMP. (Ca doit ressembler à "C:\wamp\bin\php\php5.6.25")
Ensuite ajoutez le chemin de ce répertoire dans le PATH. [Modifier le PATH](http://sametmax.com/ajouter-un-chemin-a-la-variable-denvironnement-path-sous-windows/)
Pour tester, ouvrez un terminal (windows + r, puis tapez "cmd", et Entrée), et tapez "php -v". (normalement ça vous renvoit la version de PHP)

#### Installer GIT
Téléchargez et installez [GIT](https://git-scm.com/).

#### Cloner le projet
Avec le terminal, rendez-vous dans C:\wamp\www, et clonez le projet
```
cd C:\wamp\www
git clone https://github.com/alexdu98/IMNA.git
```

#### Installer Composer
Téléchargez et installez [Composer](https://getcomposer.org/download/). (Windows Installer)
Dans un terminal
```
cd C:\wamp\www\TERM1
composer update
```
PS : il faudra peut être ajouter "C:\ProgramData\ComposerSetup\bin" dans votre PATH s'il n'y ai pas déjà.

#### Utilisation
Démarrer WAMP, et ouvrir le navigateur sur la page : [http://term1.calyxe.dev](http://term1.calyxe.dev/) ou [http://term1.calyxe.dev/app_dev.php](http://term1.calyxe.dev/app_dev.php/)

## INSTALLATION UBUNTU (LAMP)
##### Installer LAMP
```
sudo apt-get install apache2 php5 mysql-server libapache2-mod-php5 php5-mysql
```

#### Configurer LAMP
```
vim /etc/apache2/sites-available/term1.calyxe.dev.conf
# Collez-y 
<VirtualHost *:80>
	ServerName term1.calyxe.dev

	DocumentRoot C:/wamp/www/TERM1/web

	<Directory  "C:/wamp/www/TERM1/web">
		AllowOverride All
		Order Allow,Deny
        Allow from All
	</Directory>

	ErrorLog C:/wamp/www/TERM1/error.log
  CustomLog C:/wamp/www/TERM1/access.log combined
</VirtualHost>
```
```
vim /etc/php5/cli/php.ini
# Recherchez "date.timezone"
?date.timezone
# Puis remplacez par
date.timezone = Europe/Paris
```
Redémarrer Apache2
```
service apache2 restart
```
```
vim /etc/hosts
# Collez-y
127.0.0.1 term1.calyxe.dev
```
Redémarrer les configurations réseaux
```
service networking restart
```

#### Installer GIT
```
sudo apt-get install git
```

#### Cloner le projet
```
cd /var/www
git clone https://github.com/alexdu98/IMNA.git
```

#### Installer Composer
```
cd /var/www/TERM1
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php composer.phar update
```

#### Utilisation
Démarrer WAMP, et ouvrir le navigateur sur la page : [http://term1.calyxe.dev](http://term1.calyxe.dev)
