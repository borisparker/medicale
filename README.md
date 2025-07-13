# Système de Gestion Médicale

Un système complet de gestion médicale développé en PHP avec interface responsive pour la gestion des patients, médecins, rendez-vous et dossiers médicaux.

## 🏥 Fonctionnalités

### Pour les Administrateurs
- Gestion complète des médecins et patients
- Tableau de bord avec statistiques
- Gestion des rendez-vous
- Suivi des consultations
- Gestion des médicaments et prescriptions
- Interface responsive et moderne

### Pour les Médecins
- Profil personnel avec statistiques
- Gestion des patients
- Création de consultations et prescriptions
- Suivi des rendez-vous
- Interface dédiée

### Pour les Patients
- Consultation du dossier médical
- Prise de rendez-vous
- Suivi des consultations
- Interface patient-friendly

## 🚀 Technologies Utilisées

- **Backend**: PHP 7.4+
- **Base de données**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **PDF**: FPDF pour les prescriptions
- **Interface**: Design responsive

## 📋 Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- Extensions PHP : mysqli, gd, mbstring

## 🔧 Installation

1. **Cloner le repository**
   ```bash
   git clone https://github.com/votre-username/medicale.git
   cd medicale
   ```

2. **Configurer la base de données**
   - Créer une base de données MySQL
   - Importer le fichier `medicale_db.sql`
   - Configurer les paramètres de connexion dans `db.php`

3. **Configurer l'environnement**
   - Copier `config.example.php` vers `config.php`
   - Modifier les paramètres selon votre environnement

4. **Permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 medicale/uploads/
   ```

## 🌐 Déploiement sur Railway

Ce projet est configuré pour être déployé sur Railway. Le fichier `railway.json` contient la configuration nécessaire.

### Variables d'environnement Railway
- `DB_HOST`: Hôte de la base de données
- `DB_NAME`: Nom de la base de données
- `DB_USER`: Utilisateur de la base de données
- `DB_PASS`: Mot de passe de la base de données
- `APP_URL`: URL de l'application

## 📁 Structure du Projet

```
medicale/
├── assets/              # Images et ressources
├── fpdf/               # Bibliothèque PDF
├── uploads/            # Fichiers uploadés
├── admin.php           # Interface administrateur
├── docteur.php         # Interface médecin
├── patient.php         # Interface patient
├── index.php           # Page d'accueil
├── login.php           # Authentification
├── db.php              # Configuration base de données
└── README.md           # Documentation
```

## 🔐 Sécurité

- Authentification sécurisée
- Validation des données
- Protection contre les injections SQL
- Gestion des sessions sécurisées

## 📞 Support

Pour toute question ou problème, veuillez ouvrir une issue sur GitHub.

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 🤝 Contribution

Les contributions sont les bienvenues ! Veuillez :

1. Fork le projet
2. Créer une branche pour votre fonctionnalité
3. Commiter vos changements
4. Pousser vers la branche
5. Ouvrir une Pull Request

---

**Développé avec ❤️ pour la gestion médicale** 