# Guide de Déploiement - GitHub et Railway

Ce guide vous accompagne étape par étape pour déployer votre système de gestion médicale sur GitHub puis Railway.

## 📋 Prérequis

- Compte GitHub
- Compte Railway (gratuit)
- Git installé sur votre machine
- Accès à un terminal

## 🚀 Étape 1: Préparation du projet

### 1.1 Vérifier la structure
Assurez-vous que tous les fichiers de configuration sont présents :
- ✅ `.gitignore`
- ✅ `README.md`
- ✅ `railway.json`
- ✅ `nixpacks.toml`
- ✅ `composer.json`
- ✅ `config.example.php`
- ✅ `.htaccess`

### 1.2 Exécuter le script de déploiement
```bash
# Rendre le script exécutable
chmod +x deploy.sh

# Exécuter le script
./deploy.sh
```

## 📤 Étape 2: Déploiement sur GitHub

### 2.1 Créer un repository GitHub
1. Allez sur [GitHub](https://github.com)
2. Cliquez sur le bouton "+" puis "New repository"
3. Nommez le repository `medicale`
4. **Ne cochez PAS** "Initialize with README"
5. Cliquez sur "Create repository"

### 2.2 Connecter votre projet local
```bash
# Remplacer VOTRE_USERNAME par votre nom d'utilisateur GitHub
git remote add origin https://github.com/VOTRE_USERNAME/medicale.git
git branch -M main
git push -u origin main
```

### 2.3 Vérifier le déploiement
- Allez sur votre repository GitHub
- Vérifiez que tous les fichiers sont présents
- Le fichier `.gitignore` doit exclure les fichiers sensibles

## 🚂 Étape 3: Déploiement sur Railway

### 3.1 Créer un projet Railway
1. Allez sur [Railway](https://railway.app)
2. Connectez-vous avec votre compte GitHub
3. Cliquez sur "New Project"
4. Sélectionnez "Deploy from GitHub repo"
5. Choisissez votre repository `medicale`

### 3.2 Configurer la base de données
1. Dans votre projet Railway, cliquez sur "New"
2. Sélectionnez "Database" → "MySQL"
3. Notez les informations de connexion fournies

### 3.3 Configurer les variables d'environnement
Dans votre projet Railway, allez dans "Variables" et ajoutez :

```env
DB_HOST=your_mysql_host
DB_NAME=your_mysql_database
DB_USER=your_mysql_user
DB_PASS=your_mysql_password
APP_URL=https://your-app-url.railway.app
```

### 3.4 Importer la base de données
1. Connectez-vous à votre base de données MySQL Railway
2. Importez le fichier `medicale_db.sql`
3. Vérifiez que toutes les tables sont créées

### 3.5 Configurer l'application
1. Dans votre projet Railway, allez dans "Settings"
2. Vérifiez que le "Start Command" est : `php -S 0.0.0.0:$PORT -t .`
3. Le "Health Check Path" doit être : `/`

## 🔧 Étape 4: Configuration finale

### 4.1 Créer le fichier de configuration
```bash
# Copier le fichier d'exemple
cp config.example.php medicale/config.php
```

### 4.2 Vérifier les permissions
Railway gère automatiquement les permissions, mais vérifiez que :
- Les dossiers `uploads/` sont accessibles en écriture
- Le fichier `config.php` est protégé

### 4.3 Tester l'application
1. Allez sur l'URL de votre application Railway
2. Testez la connexion à la base de données
3. Vérifiez que toutes les fonctionnalités marchent

## 🛠️ Dépannage

### Problème de connexion à la base de données
- Vérifiez les variables d'environnement
- Assurez-vous que la base de données est bien créée
- Vérifiez que le fichier `config.php` utilise les bonnes variables

### Problème de déploiement
- Vérifiez les logs Railway dans l'onglet "Deployments"
- Assurez-vous que tous les fichiers sont commités sur GitHub
- Vérifiez que le fichier `railway.json` est présent

### Problème de permissions
- Railway gère automatiquement les permissions
- Si nécessaire, ajoutez des commandes dans `nixpacks.toml`

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifiez les logs Railway
2. Consultez la documentation Railway
3. Ouvrez une issue sur GitHub

## 🔄 Mises à jour

Pour mettre à jour votre application :
```bash
# Modifier vos fichiers
git add .
git commit -m "Description des changements"
git push origin main
```

Railway redéploiera automatiquement votre application.

---

**🎉 Félicitations ! Votre système de gestion médicale est maintenant en ligne !** 