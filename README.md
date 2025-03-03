# TaskLinker

TaskLinker est une plateforme permettant de gérer les projets de l'entreprise BeWise.

## Installation

1. Télécharger le projet
2. Modifier le fichier _.env_ et renseigner vos informations de connexion à la base de données
3. Créer la base de données avec `php bin/console doctrine:database:create`
4. Appliquer les migrations avec `php bin/console doctirne:migrations:migrate`
5. Insérer les fixtures avec `php bin/console doctrine:fixtures:load`
6. Lancer le serveur

# Afin de tester l'envoi de mail, mon mentor m'a conseilé d'utiliser MailTrap, il faut aller sur mailtrap et dans email testing => inboxes
# Puis dans Code Sample sélectionner dans l'onglet "php" "symfony5+" et enfin de copier le DSN
# Attention à bien utiliser l'onglet "copy" afin de récupérer le DSN avec les valeurs cachées par les étoiles et de l'insérer dans MAILER_DSN

# Pour envoyer d'une adresse gmail à une autre adresse, j'ai aussi activé un mot de passe d'application sur mon adresse gmail 
# Ainsi il suffit de remplacer dans la ligne suivante "gmail" par l'adresse gmail ainsi que "mdpgmail" par le mot de passe d'application gmail
# MAILER_DSN=smtp://gmail:mdpgmail@smtp.gmail.com:587?encryption=tls&auth_mode=login