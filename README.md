
## INSTALLATION WINDOWS (WAMP)
##### Installer WAMP
Rendez-vous sur le site de WAMP et téléchargez le. (http://www.wampserver.com)
Il faut en suite ajouter PHP5 à votre PATH.
Tout d'abord, trouvez le binaire de PHP5, dans le dossier de WAMP il est dans bin/php/php5.*.
Faites un click droit sur le signe Windows (en bas à gauche), puis cliquez sur "Ordinateur". Dans la fenête qui s'ouvre, à gauche cliquez sur "Paramètres système avancés". Dans la nouvelle fenêtre, cliquez sur le bouton "Variables d'environnement". Cliquez sur PATH dans le tableau des variables système, puis sur le bouton "Modifier", puis dans la nouvelle fenêtre, sur le bouton "Ajouter". Collez le chemin du binaire PHP (quelque chose du style "C:/wamp/bin/php/php5.6.25".


## INSTALLATION UBUNTU (LAMP)
INFO : il faut avoir les doits sudo
##### Installer LAMP
```
sudo apt-get install apache2 php5 mysql-server libapache2-mod-php5 php5-mysql
```
