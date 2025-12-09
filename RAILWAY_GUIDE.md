# Guide de Déploiement sur Railway

Ce guide vous explique comment déployer votre site Arcadia sur Railway via GitHub.

## 1. Préparation du Code (Déjà fait)
J'ai déjà effectué les modifications suivantes pour rendre votre site compatible :
- **Découpage des jeux** : Les jeux trop volumineux (>100Mo) ont été découpés en plusieurs parties (ex: `.nds.001`, `.nds.002`) pour passer sur GitHub.
- **Script de lecture** : Un script (`backend/serve_rom.php`) recolle les morceaux automatiquement quand on joue.
- **Configuration** : `Dockerfile` et `docker-compose.yml` sont prêts.
- **Base de données** : Connexion adaptée pour Railway.

## 2. Pousser sur GitHub
Maintenant que les fichiers sont découpés, l'envoi devrait fonctionner.
```bash
git add .
git commit -m "Ajout des jeux découpés"
git push origin main
```

## 3. Créer le Projet sur Railway
1. Allez sur [Railway.app](https://railway.app/).
2. Cliquez sur **"New Project"** -> **"Deploy from GitHub repo"**.
3. Sélectionnez votre dépôt `Arcadia`.
4. Railway va détecter le `Dockerfile` et commencer le build.

## 4. Ajouter la Base de Données
1. Dans votre projet Railway, cliquez sur **"New"** (ou clic droit sur le canvas) -> **"Database"** -> **"MySQL"**.
2. Attendez que la base de données soit créée.

## 5. Configurer les Variables d'Environnement
Il faut maintenant relier votre site à la base de données.
1. Cliquez sur votre service **MySQL** -> onglet **"Variables"**.
2. Notez les valeurs (ou gardez l'onglet ouvert) : `MYSQLHOST`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`, `MYSQLPORT`.
3. Cliquez sur votre service **Arcadia** (le site web) -> onglet **"Variables"**.
4. Ajoutez les variables suivantes en copiant les valeurs du service MySQL :

| Variable | Valeur (depuis MySQL) |
|----------|-----------------------|
| `DB_HOST` | `${{MySQL.MYSQLHOST}}` (ou copiez la valeur) |
| `DB_USER` | `${{MySQL.MYSQLUSER}}` |
| `DB_PASS` | `${{MySQL.MYSQLPASSWORD}}` |
| `DB_NAME` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_PORT` | `${{MySQL.MYSQLPORT}}` |

*Astuce : Railway permet de référencer des variables d'autres services. Tapez `${{` pour voir l'autocomplétion.*

## 6. Importer les Données (SQL)
Votre base de données est vide pour l'instant. Il faut importer `projet_web.sql`.
1. Cliquez sur le service **MySQL** -> onglet **"Connect"**.
2. Copiez la commande "MySQL Client" ou utilisez un outil comme **DBeaver** ou **HeidiSQL** avec les identifiants fournis.
3. Exécutez le contenu du fichier `projet_web.sql` dans votre outil SQL connecté à Railway.

## 7. Vérification
Une fois le déploiement terminé (cercle vert sur Railway) et la base de données importée, cliquez sur l'URL fournie par Railway pour accéder à votre site !

---

## Note sur les Jeux
Les jeux volumineux (Pokemon, Inazuma Eleven, etc.) sont stockés en plusieurs morceaux sur GitHub mais apparaîtront comme un seul jeu sur le site grâce au script `serve_rom.php`.
