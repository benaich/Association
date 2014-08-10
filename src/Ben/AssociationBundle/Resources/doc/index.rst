==============================================================================
                    I.  Annexe I : Installation sous Linux
==============================================================================

On va voir dans ce chapitre les points à vérifier pour déployer l’application sur un serveur linux. La méthodologie est la suivante :

1) Vérifier et préparer le serveur de production
------------------------------------------------

Évidemment, pour déployer une application Symfony2 sur votre serveur, encore faut-il que celui-ci soit compatible avec les besoins de Symfony2 ! Pour vérifier cela, on peut distinguer deux cas.
Symfony2 intègre un petit fichier PHP qui fait toutes les vérifications de compatibilité nécessaires, Il s'agit du fichier web/config.php, envoyez le sur votre serveur. Ouvrez la page web qui lui correspond, par exemple www.votre-serveur.com/config.php. Vous devriez obtenir soit des alertes "Major Problems" que vous devez corriger ou juste des Recommandations essayez de les respecter si cela est possible.

2) Pré-requis au bon fonctionnement de Symfony2
-----------------------------------------------

Voici les points obligatoires qu'il faut que votre serveur respecte pour pouvoir faire tourner Symfony2 :
- PHP doit être au minimum à la version PHP 5.3.3
- JSON doit être activé
- ctype doit être activé
- Votre PHP.ini doit avoir le paramètre date.timezone défini
- installer le driver PDO

3) Installer wkhtmltopdf
-----------------------------------------------

Wkhtmltopdf un outil qui permet de générer des PDF, il est utilisé dans l'application pour imprimer les badges des adhérents.
# apt-get install wkhtmltopdf
Âpres l'installation modifier le fichier de configuration app/config/config.yml afin de spécifier le chemin absolue de wkhtmltopdf.

configurer selinux
yum -y install policycoreutils-python
grep httpd_t /var/log/audit/audit.log | audit2allow -m httpdlocal > httpd.te
checkmodule -M -m -o httpdlocal.mod httpd.te
semodule_package -o httpdlocal.pp -m httpdlocal.mod
semodule -i httpdlocal.pp

4) Envoyer les fichiers sur le serveur
-----------------------------------------------


Dans un premier temps, il faut bien évidemment envoyer les fichiers sur le serveur. Pour éviter d'envoyer des fichiers inutiles et lourds, videz dans un premier temps le cache de votre application en supprimant tout le contenu du repertoire app/cache. Ensuite, envoyez tous vos fichiers et dossiers à la racine de votre hébergement, dans www/
Important:
Les fichiers dans le répertoire vendors/ sont assez lourds et prennent beaucoup de temps lors de l'uploada. Pour remédier à ce problème, sur votre serveur, exécutez la commande
 # php composer.phar install 
Cette commande qui va installer les mêmes versions des dépendances que vous avez en local. Cela se fait grâce au fichier composer.lock qui contient tous les numéros des versions installées justement.
Si vous n'avez pas accès à Composer sur votre serveur, alors contentez-vous d'envoyer le dossier vendor en même temps que le reste de votre application.

5) Régler les droits sur les dossiers app/cache et app/logs
-----------------------------------------------------------


Vous le savez maintenant, Symfony2 a besoin de pouvoir écrire dans deux répertoires : app/cache pour y mettre le cache de l'application et ainsi améliorer les performances, et app/logs pour y mettre l'historiques des informations et erreurs rencontrées lors de l'exécution des pages. Sur votre serveur, exécutez la commande suivante:
# chmod 775 -R app/cache app/logs


6) Mettre en place la base de données
-------------------------------------

Il ne manque pas grand-chose avant que votre site ne soit opérationnel. Il faut notamment s'attaquer à la base de données. Pour cela, modifiez le fichier app/config/parameters.yml de votre serveur afin d'adapter les valeurs des paramètres database_*.
Ensuite connecter a MySQL et importer la base de donner à l'aide du ficher app/Ressources/database.sql

Ça y est, l'application devrait être opérationnel dès maintenant ! Vérifiez que tout fonctionne bien dans www.votre-serveur.com/app.php.
Important:
Les erreurs ne sont certes pas affichées à l'écran, mais elles sont heureusement répertoriées dans le fichier app/logs/prod. Si l'un de vos visiteurs vous rapporte une erreur, c'est dans ce fichier qu'il faut aller regarder pour avoir le détail, les informations nécessaires à la résolution de l'erreur.

==============================================================================
                    II : Installation sous Windows
==============================================================================

2) Installation de WAMP
-----------------------

Pour commencer, il faut télécharger l’installeur wamp disponible gratuitement sur son site officiel. (Notez qu’il est disponible en version 32 et 64 bits veillez donc à choisir la bonne version en regard de votre système d’exploitation afin d’en tirer pleinement satisfaction)
Dès lors que le téléchargement est terminé vous pouvez procéder à son installation. L’installation est très simple je ne m’attarderai pas dessus afin de rester centré uniquement sur l’essentiel dans ce tutoriel.


2) Déploiement des différents modules Apache et PHP 
---------------------------------------------------

Pour apache :
Faites clic-gauche sur l’icône de Wamp dans la barre des tâches > Apache > Apache Modules > sélectionnez « Rewrite Module »
Pour les modules php :
clic-gauche sur l’icône de Wamp > PHP > PHP Extensions > cochez « php_intl », « php_xmlrpc », « php_pdo_mysql », « php_sqlite3 », « php_mbstring »
Symfony2 recommande aussi l’utilisation du module php_apc pour accélérer le rendu des pages.


3) Installer wkhtmltopdf
------------------------

Pour télécharger Wkhtmltopdf, rendez-vous à cette adresse : http://wkhtmltopdf.org/downloads.html Dès lors que le téléchargement est terminé vous pouvez procéder à son installation.
Ensuit vous devez modifier le fichier de configuration app/config/config.yml afin de spécifier le chemin absolue de wkhtmltopdf. 


4) Test de Symfony2
-------------------

Placez ensuite le répertoire de l’application dans votre répertoire web (par défaut C:/WAMP/www/ ) et rendez-vous à l’adresse : http://localhost/association/app/check.php