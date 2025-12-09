1. Description du projet :

Ce projet est une plateforme web d'émulation dédiée au retrogaming. Elle permet aux utilisateurs de charger et jouer à des jeux classiques via un navigateur, sans installation complexe. Pour renforcer l'aspect communautaire, une messagerie instantanée (tchat) est intégrée à l'interface, permettant aux joueurs de discuter en temps réel pendant leurs sessions.

2. Instruction d'installation : 

Mise en place des fichiers : Récupérer l'ensemble du dossier du projet et installer les dépendances requises.

Base de données (BDD) :

Le fichier de la base de données (au format .sql) est inclus directement dans le dossier du projet.

Il suffit d'importer ce fichier dans votre gestionnaire de base de données (ex: phpMyAdmin) pour créer automatiquement la structure et les tables nécessaires.

Assurez-vous simplement que votre projet est connecté à cette base une fois importée.

3. Avancement et Difficultés : 

État actuel : L'architecture globale est en place. L'émulateur parvient à charger et exécuter la majorité des jeux testés.

Points de blocage et difficultés techniques :

Persistance des contrôles (Keybinding) : Je rencontre un problème pour sauvegarder la configuration des touches. Actuellement, les touches ne sont pas mémorisées : l'utilisateur doit les redéfinir à chaque rechargement de la page (binding non conservé à l'arrivée).

Compatibilité ROM (Sonic) : Le jeu Sonic refuse spécifiquement de se lancer, alors que l'émulateur fonctionne pour d'autres titres. Je suspecte une corruption du fichier ROM ou une incompatibilité spécifique.

Interface de Messagerie (CSS) : L'intégration graphique du tchat est complexe. Je rencontre des difficultés majeures avec le CSS pour aligner et styliser correctement la zone de discussion par rapport au reste de l'interface de jeu.