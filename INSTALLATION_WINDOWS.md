# Guide d'Installation Windows - GitHub et Railway

## 📋 Prérequis Windows

### 1. Installer Git pour Windows
1. Téléchargez Git depuis [https://git-scm.com/download/win](https://git-scm.com/download/win)
2. Exécutez l'installateur
3. **Important** : Pendant l'installation, choisissez "Git from the command line and also from 3rd-party software"
4. Redémarrez votre terminal PowerShell après l'installation

### 2. Vérifier l'installation
Ouvrez PowerShell et tapez :
```powershell
git --version
```
Vous devriez voir quelque chose comme : `git version 2.x.x.windows.x`

## 🚀 Déploiement sur GitHub

### Étape 1: Initialiser Git
```powershell
# Dans le dossier de votre projet
git init
```

### Étape 2: Configurer Git (première fois)
```powershell
git config --global user.name "Votre Nom"
git config --global user.email "votre.email@example.com"
```

### Étape 3: Ajouter les fichiers
```powershell
git add .
```

### Étape 4: Premier commit
```powershell
git commit -m "Initial commit: Système de gestion médicale"
```

### Étape 5: Créer un repository GitHub
1. Allez sur [GitHub](https://github.com)
2. Cliquez sur le bouton "+" puis "New repository"
3. Nommez le repository `medicale`
4. **Ne cochez PAS** "Initialize with README"
5. Cliquez sur "Create repository"

### Étape 6: Connecter à GitHub
```powershell
# Remplacez VOTRE_USERNAME par votre nom d'utilisateur GitHub
git remote add origin https://github.com/VOTRE_USERNAME/medicale.git
git branch -M main
git push -u origin main
```

## 🚂 Déploiement sur Railway

### Étape 1: Créer un compte Railway
1. Allez sur [Railway](https://railway.app)
2. Connectez-vous avec votre compte GitHub
3. Acceptez les autorisations

### Étape 2: Créer un projet
1. Cliquez sur "New Project"
2. Sélectionnez "Deploy from GitHub repo"
3. Choisissez votre repository `medicale`

### Étape 3: Configurer la base de données
1. Dans votre projet Railway, cliquez sur "New"
2. Sélectionnez "Database" → "MySQL"
3. Notez les informations de connexion

### Étape 4: Variables d'environnement
Dans votre projet Railway, allez dans "Variables" et ajoutez :
```env
DB_HOST=your_mysql_host
DB_NAME=your_mysql_database
DB_USER=your_mysql_user
DB_PASS=your_mysql_password
APP_URL=https://your-app-url.railway.app
```

### Étape 5: Importer la base de données
1. Connectez-vous à votre base de données MySQL Railway
2. Importez le fichier `medicale_db.sql`
3. Vérifiez que toutes les tables sont créées

## 🔧 Configuration finale

### Créer le fichier de configuration
```powershell
# Copier le fichier d'exemple
copy config.example.php medicale\config.php
```

### Vérifier le déploiement
1. Allez sur l'URL de votre application Railway
2. Testez la connexion à la base de données
3. Vérifiez que toutes les fonctionnalités marchent

## 🛠️ Dépannage Windows

### Git non reconnu
- Redémarrez PowerShell après l'installation de Git
- Vérifiez que Git est dans le PATH système
- Essayez d'utiliser Git Bash au lieu de PowerShell

### Problèmes de permissions
- Exécutez PowerShell en tant qu'administrateur si nécessaire
- Vérifiez les permissions des dossiers

### Problèmes de connexion
- Vérifiez votre connexion internet
- Assurez-vous que les ports ne sont pas bloqués par le pare-feu

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifiez les logs Railway
2. Consultez la documentation Railway
3. Ouvrez une issue sur GitHub

---

**🎉 Votre système de gestion médicale sera bientôt en ligne !** 