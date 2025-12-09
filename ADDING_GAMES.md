# Comment ajouter un nouveau jeu sur Arcadia

Voici la procédure pour ajouter un jeu sans casser le déploiement sur Railway.

## 1. Ajouter le fichier du jeu
Copie ton fichier `.nds` dans le dossier `roms/nds/` de ton projet.

## 2. Lancer le script de découpage
Ce script va vérifier si le jeu est trop gros (> 90 Mo).
- S'il est trop gros : il le découpe automatiquement et supprime l'original.
- S'il est petit : il ne touche à rien.

Ouvre ton terminal dans le dossier du projet et lance :
```powershell
powershell -ExecutionPolicy Bypass -File split_roms.ps1
```

## 3. Ajouter l'image et la base de données
1.  Ajoute l'image de couverture dans `roms/cover/`.
2.  Ajoute le jeu dans ta base de données (table `games`) :
    - Soit via ton interface d'admin si tu en as une.
    - Soit via PhpMyAdmin/DBeaver.
    - **Important** : Dans le champ `file_path`, mets le chemin du fichier `.nds` original (ex: `/roms/nds/MonJeu.nds`).
      *Même si le fichier est découpé en .001, .002, garde le nom original dans la base de données, le site se débrouille tout seul.*

## 4. Envoyer sur GitHub
Une fois que le script est passé, tu peux envoyer les modifications :

```bash
git add .
git commit -m "Ajout du jeu MonJeu"
git push origin main
```

Railway détectera le changement et mettra à jour le site automatiquement !
