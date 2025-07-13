#!/bin/bash

# Script de déploiement pour GitHub et Railway
# Usage: ./deploy.sh

echo "🚀 Déploiement du projet médical sur GitHub et Railway"
echo "=================================================="

# Vérifier si Git est installé
if ! command -v git &> /dev/null; then
    echo "❌ Git n'est pas installé. Veuillez l'installer d'abord."
    exit 1
fi

# Initialiser Git si ce n'est pas déjà fait
if [ ! -d ".git" ]; then
    echo "📁 Initialisation du repository Git..."
    git init
    echo "✅ Repository Git initialisé"
else
    echo "✅ Repository Git déjà initialisé"
fi

# Ajouter tous les fichiers
echo "📝 Ajout des fichiers au staging..."
git add .

# Faire le premier commit
echo "💾 Création du premier commit..."
git commit -m "Initial commit: Système de gestion médicale"

echo ""
echo "🎯 Étapes suivantes pour déployer sur GitHub et Railway:"
echo ""
echo "1. 📤 Créer un repository sur GitHub:"
echo "   - Allez sur https://github.com"
echo "   - Cliquez sur 'New repository'"
echo "   - Nommez-le 'medicale'"
echo "   - Ne cochez PAS 'Initialize with README'"
echo ""
echo "2. 🔗 Connecter votre repository local à GitHub:"
echo "   git remote add origin https://github.com/VOTRE_USERNAME/medicale.git"
echo "   git branch -M main"
echo "   git push -u origin main"
echo ""
echo "3. 🚂 Déployer sur Railway:"
echo "   - Allez sur https://railway.app"
echo "   - Connectez-vous avec votre compte GitHub"
echo "   - Cliquez sur 'New Project'"
echo "   - Sélectionnez 'Deploy from GitHub repo'"
echo "   - Choisissez votre repository 'medicale'"
echo ""
echo "4. ⚙️ Configurer les variables d'environnement sur Railway:"
echo "   - DB_HOST: (fourni par Railway)"
echo "   - DB_NAME: (fourni par Railway)"
echo "   - DB_USER: (fourni par Railway)"
echo "   - DB_PASS: (fourni par Railway)"
echo "   - APP_URL: (URL de votre app Railway)"
echo ""
echo "5. 🗄️ Configurer la base de données:"
echo "   - Créez une base de données MySQL sur Railway"
echo "   - Importez le fichier medicale_db.sql"
echo "   - Copiez config.example.php vers config.php"
echo ""
echo "✅ Votre projet est prêt pour le déploiement !"
echo ""
echo "📚 Documentation complète dans README.md" 