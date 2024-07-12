<h2>Installer le projet</h2>

<p>1- Cloner le projet grâce au git clone </p>
<p></p>2- Ouvrir le dossier dans un IDE, Ouvrir un terminal dans l'IDE</p>
<p>3- Ouvrir un terminal et se positionner sur le dossier ERN24_SYMFONY-main</p>
<p>4- Faire la commande docker compose up --build (pour construire le container)</p>
<p>5- docker exec -it phpimmo composer install (installer le dossier vendor, Appuyer sur n quand il vous demander une autre recette docker)</p>
<p>6- Se connecter à la bdd graĉe au données sur docker-compose.yml </p>
<p>7- docker exec -it phpimmo bin/console d:m:m</p>
<p></p>8- docker exec -it phpimmo bin/console d:f:l </p>


<h2> Pour apporter des modifications</h2>

Se connecter à sa BRANCH

git checkout -b feature-login

Faire un git fetch ORIGIN pour etre a JOUR de la PROD 

Apporter des Modifications
Apportez vos modifications au code, par exemple, ajoutez une nouvelle fonctionnalité de connexion.
Assurez-vous que votre code respecte les normes de codage du projet.

Testez vos modifications de manière approfondie.

<b>Valider les Modifications</b><br>
git add .

<b>Copier le code</b><br>
git commit -m "Ajout de la fonctionnalité de connexion"

<b>Pousser les Modifications</b><br>
git push origin 


