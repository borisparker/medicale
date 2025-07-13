# Guide de test - Workflow de consultation

## Problème résolu
Le problème "Rendez-vous non trouvé" était dû à une incohérence dans la gestion des IDs :
- Le formulaire utilisait l'ID utilisateur du médecin
- La base de données stocke l'ID du docteur depuis la table `doctors`

## Corrections apportées

### 1. `create_consultation.php`
- ✅ Récupère maintenant le bon `doctor_id` depuis la table `doctors`
- ✅ Ajoute des informations de debug en cas d'erreur
- ✅ Vérifie que le docteur existe avant de procéder

### 2. `get_doctor_appointments.php`
- ✅ Supporte maintenant le paramètre `?statut=confirmé`
- ✅ Filtre correctement les rendez-vous confirmés pour le formulaire

## Tests à effectuer

### Test 1 : Vérifier les IDs
```
http://localhost/medicale/test_consultation_ids.php
```
Cela doit afficher :
- Votre `user_id`
- Votre `doctor_id` (différent de user_id)
- Les rendez-vous confirmés disponibles

### Test 2 : Vérifier les rendez-vous confirmés
```
http://localhost/medicale/get_doctor_appointments.php?statut=confirmé
```
Cela doit retourner uniquement les rendez-vous avec statut "confirmé"

### Test 3 : Test complet du workflow
1. Connectez-vous en tant que médecin
2. Allez dans l'interface médecin
3. Cliquez sur "Consultations"
4. Cliquez sur "Nouvelle consultation"
5. Sélectionnez un rendez-vous confirmé
6. Remplissez le formulaire
7. Validez

## Structure des données attendue

### Session utilisateur
```json
{
  "user": {
    "id": 123,        // ID utilisateur
    "role": "docteur",
    "nom": "Nom",
    "prenom": "Prénom"
  }
}
```

### Table doctors
```sql
id | user_id | specialite
1  | 123     | Cardiologie
```

### Table appointments
```sql
id | patient_id | doctor_id | date_heure | statut
1  | 5          | 1         | 2024-01-15 | confirmé
```

## Si le problème persiste

1. **Vérifiez que vous avez des rendez-vous confirmés**
   - Testez `get_doctor_appointments.php?statut=confirmé`
   - Assurez-vous qu'il y a des résultats

2. **Vérifiez les IDs**
   - Testez `test_consultation_ids.php`
   - Vérifiez que `doctor_id` correspond dans les tables

3. **Vérifiez la session**
   - Testez `debug_session.php`
   - Assurez-vous d'être connecté en tant que docteur

4. **Vérifiez les logs d'erreur**
   - Regardez la console du navigateur
   - Vérifiez les logs PHP

## Messages d'erreur possibles

- **"Docteur introuvable"** : L'utilisateur n'est pas dans la table `doctors`
- **"Rendez-vous non trouvé"** : L'ID du rendez-vous ne correspond pas au docteur
- **"Accès non autorisé"** : L'utilisateur n'est pas connecté ou n'est pas docteur 