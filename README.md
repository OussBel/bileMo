Créez un web service exposant une API.

BileMo est une API REST développé avec le framework Symfony.

Description du projet:

BileMo est une entreprise proposant une selection de téléphones mobiles premium. Elle fonctionne sur modèle Business-to-Business (B2B). Au lieu de vendre directement des produits sur le site web, l'entreprise fournit un accès à son catalogue via une Interface de Programmation d'Application (API) destinée à d'autres plates-formes. L'objectif principal est de servir exclusivement les clients professionnels.

Les fonctionnalités de l'app BileMo:

L'app offre la possibilié à un client inscrit de:
  - Consulter de la liste des produits BileMo.
  - Consulter les détails d’un produit BileMo.
  - Consulter la liste des utilisateurs inscrits liés au client connecté sur le site web.
  - Consulter le détail d’un utilisateur inscrit lié au client connecté.
  - Ajouter un nouvel utilisateur lié  au client connecté.
  - Supprimer un utilisateur ajouté par le client connecté.


Prérequis:

  - PHP 8.2.4
  - MySQL ou Mariadb
  - Composer version  >=2.3.10
  - Symfony CLI 5.6.1
  - Symfony 6.4
  - Extension OpenSSL


Installation:

  - Clonez le projet.
  - Installez les dépendances du projet en lancant la commande: composer install
  - Créez une copié du fichier .env et nommez le .env.local
  - Dans le fichier .env.local, renseignez les données de la base de données DATABASE_URL="mysql://user:password@127.0.0.1.3306/dbname?charset=utf8mb4"
  - Créez la base de données avec la commande: symfony console doctrine:database:create
  - Générez la migration avec la commande: symfony console doctrine:migrations:migrate
  - Générez le jeux de données (fixtures) avec la commande: symfony console doctrine:fixtures:load
  - Générer les clés SSL de JWT pour l'authentification : symfony console lexik:jwt:generate-keypair
