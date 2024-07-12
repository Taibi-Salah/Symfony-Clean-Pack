<p>1- Cloner le projet grâce au git clone </p>
<p></p>2- Ouvrir le dossier dans un IDE, Ouvrir un terminal dans l'IDE</p>
3- Ouvrir un terminal et se positionner sur le dossier ERN24_SYMFONY-main
4- Faire la commande docker compose up --build (pour construire le container)
5- docker exec -it phpimmo composer install (installer le dossier vendor, Appuyer sur n quand il vous demander une autre recette docker)
6- Se connecter à la bdd graĉe au données sur docker-compose.yml
7- docker exec -it phpimmo bin/console d:m:m
8- docker exec -it phpimmo bin/console d:f:l 


<h1> Pour apporter des modifications</h1>

Se connecter a sa BRANCH

git checkout -b feature-login


Faire un git fetch ORIGIN pour etre a JOUR de la PROD 

Apporter des Modifications
Apportez vos modifications au code, par exemple, ajoutez une nouvelle fonctionnalité de connexion.
Assurez-vous que votre code respecte les normes de codage du projet.

Testez vos modifications de manière approfondie.

Valider les Modifications
git add .

Copier le code
git commit -m "Ajout de la fonctionnalité de connexion"

Pousser les Modifications

git push origin 


