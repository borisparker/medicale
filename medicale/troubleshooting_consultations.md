# Guide de dépannage - Système de consultations

## Problème : "Unexpected token '<', "<!DOCTYPE "... is not valid JSON"

### Cause
Cette erreur indique que le fichier PHP retourne du HTML au lieu du JSON attendu. Cela arrive généralement quand :
1. Il y a une erreur PHP qui affiche une page d'erreur HTML
2. La session n'est pas correctement configurée
3. Les permissions d'accès ne sont pas correctes

### Solutions

#### 1. Vérifier la session
Accédez à `debug_session.php` dans votre navigateur pour voir la structure de session :
```
http://localhost/medicale/debug_session.php
```

#### 2. Vérifier que vous êtes connecté en tant que médecin
- Assurez-vous d'être connecté avec un compte médecin
- Vérifiez que le rôle dans la session est 'docteur'

#### 3. Tester les fichiers individuellement
Testez chaque fichier pour voir lequel pose problème :

- `test_consultations_simple.php` - Test de base de la session
- `get_consultations.php` - Récupération des consultations
- `get_doctor_appointments.php` - Récupération des rendez-vous

#### 4. Vérifier les erreurs PHP
Activez l'affichage des erreurs PHP en ajoutant au début de vos fichiers :
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

#### 5. Vérifier la base de données
Exécutez `test_consultation_system.php` pour vérifier que toutes les tables existent.

### Fichiers corrigés
Les fichiers suivants ont été corrigés pour utiliser la bonne structure de session :

- `get_consultations.php`
- `create_consultation.php`
- `create_prescription.php`
- `get_patient_medical_record.php`
- `get_doctor_appointments.php`
- `update_appointment_status.php`

### Structure de session attendue
```php
$_SESSION['user'] = [
    'id' => 123,
    'role' => 'docteur', // ou 'patient', 'admin'
    'nom' => 'Nom',
    'prenom' => 'Prénom'
];
```

### Test rapide
1. Connectez-vous en tant que médecin
2. Allez sur `debug_session.php`
3. Vérifiez que la structure de session est correcte
4. Testez `get_consultations.php` directement
5. Si tout fonctionne, testez l'interface complète

### Si le problème persiste
1. Vérifiez les logs d'erreur PHP
2. Testez avec un navigateur en mode incognito
3. Videz le cache du navigateur
4. Vérifiez que tous les fichiers sont bien sauvegardés 