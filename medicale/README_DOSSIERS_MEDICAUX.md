# Gestion des Dossiers Médicaux - Admin

## Vue d'ensemble

La page des dossiers médicaux permet à l'administrateur de consulter et gérer tous les dossiers médicaux des patients de la clinique.

## Fonctionnalités

### 1. Liste des patients
- Affichage de tous les patients ayant un dossier médical
- Recherche par nom de patient
- Informations affichées : nom, âge, sexe, dernière visite
- Sélection d'un patient pour voir son dossier

### 2. Détails du dossier médical
- **En-tête** : Photo, nom, âge, téléphone, groupe sanguin
- **Onglets** :
  - **Résumé** : Informations médicales, dernières consultations, formulaire d'ajout
  - **Historique** : Toutes les consultations
  - **Ordonnances** : Toutes les prescriptions avec médicaments
  - **Examens** : (À implémenter)
  - **Allergies** : Allergies connues du patient

### 3. Ajout de consultations
- Formulaire pour ajouter une nouvelle consultation
- Sélection du type de consultation
- Sélection du médecin
- Saisie du motif et des observations
- Date automatique (aujourd'hui)

## Fichiers créés

### Backend (PHP)
- `lister_dossiers_medicaux.php` - Liste tous les dossiers médicaux
- `get_dossier_details.php` - Détails d'un dossier spécifique
- `ajouter_consultation_admin.php` - Ajoute une consultation
- `get_admin_doctors.php` - Liste des médecins pour l'admin

### Frontend (JavaScript)
- `dossiers_medicaux.js` - Fonctions JavaScript pour la gestion

### Tests et initialisation
- `test_dossiers_medicaux.php` - Test des APIs
- `test_dossiers_medicaux.html` - Page de test
- `init_dossiers_medicaux.php` - Initialisation de données de test

## Structure de la base de données

### Tables utilisées
- `medical_records` - Dossiers médicaux
- `patients` - Informations des patients
- `users` - Informations utilisateurs
- `doctors` - Informations des médecins
- `consultations` - Consultations
- `prescriptions` - Ordonnances
- `prescription_medications` - Médicaments des ordonnances
- `medications` - Catalogue des médicaments

### Relations
- Un patient peut avoir un dossier médical
- Un dossier médical peut avoir plusieurs consultations
- Une consultation peut avoir une ordonnance
- Une ordonnance peut avoir plusieurs médicaments

## Installation et configuration

### 1. Vérifier la base de données
```bash
# Accéder à la page de test
http://localhost/medicale/test_dossiers_medicaux.php
```

### 2. Initialiser des données de test
```bash
# Créer des dossiers médicaux de test
http://localhost/medicale/init_dossiers_medicaux.php
```

### 3. Tester l'interface
```bash
# Page de test
http://localhost/medicale/test_dossiers_medicaux.html
```

### 4. Intégrer dans l'admin
La fonction `loadMedicalRecords()` dans `admin.php` doit être mise à jour pour utiliser les nouvelles APIs.

## Utilisation

### Pour l'administrateur
1. Se connecter à l'interface admin
2. Cliquer sur "Dossiers médicaux" dans le menu
3. Sélectionner un patient dans la liste
4. Consulter les différents onglets du dossier
5. Ajouter de nouvelles consultations si nécessaire

### Fonctionnalités disponibles
- ✅ Consultation des dossiers médicaux
- ✅ Recherche de patients
- ✅ Affichage des consultations
- ✅ Affichage des ordonnances
- ✅ Ajout de nouvelles consultations
- ⏳ Impression des dossiers (à implémenter)
- ⏳ Gestion des examens (à implémenter)
- ⏳ Modification des consultations (à implémenter)

## API Endpoints

### GET /lister_dossiers_medicaux.php
Retourne la liste de tous les dossiers médicaux avec les informations des patients.

### GET /get_dossier_details.php?medical_record_id=X
Retourne les détails complets d'un dossier médical spécifique.

### POST /ajouter_consultation_admin.php
Ajoute une nouvelle consultation à un dossier médical.

### GET /get_admin_doctors.php
Retourne la liste des médecins pour l'interface admin.

## Sécurité

- Toutes les APIs vérifient le rôle 'admin'
- Validation des données d'entrée
- Protection contre les injections SQL
- Gestion des erreurs

## Maintenance

### Ajout de nouvelles fonctionnalités
1. Créer les APIs PHP nécessaires
2. Ajouter les fonctions JavaScript correspondantes
3. Mettre à jour l'interface utilisateur
4. Tester avec les données de test

### Debugging
- Utiliser `test_dossiers_medicaux.php` pour vérifier les APIs
- Vérifier les logs d'erreur PHP
- Utiliser la console du navigateur pour les erreurs JavaScript

## Prochaines étapes

1. Intégrer la fonction `loadMedicalRecords()` dans `admin.php`
2. Ajouter la fonctionnalité d'impression
3. Implémenter la gestion des examens
4. Ajouter la modification des consultations
5. Améliorer l'interface utilisateur 