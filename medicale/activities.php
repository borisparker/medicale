<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

// Récupérer les informations de l'admin connecté
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT nom, prenom, photo, email, telephone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Générer l'URL de la photo de profil
$photo_url = '';
if ($admin['photo']) {
    $photo_paths = [
        'medicale/uploads/' . $admin['photo'],
        'uploads/' . $admin['photo'],
        '../uploads/' . $admin['photo']
    ];
    
    foreach ($photo_paths as $path) {
        if (file_exists($path)) {
            $photo_url = $path;
            break;
        }
    }
}

if (!$photo_url) {
    $photo_url = 'https://ui-avatars.com/api/?name=' . urlencode($admin['prenom'] . '+' . $admin['nom']) . '&background=3B82F6&color=fff&size=128';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activités - Administration Médicale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Activités du Système</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3">
                        <img src="<?php echo htmlspecialchars($photo_url); ?>" alt="Photo de profil" 
                             class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($admin['prenom'] . ' ' . $admin['nom']); ?></p>
                            <p class="text-xs text-gray-500">Administrateur</p>
                        </div>
                    </div>
                    <a href="admin.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Retour au Dashboard
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <h2 class="text-lg font-semibold text-gray-900">Filtres</h2>
                    <select id="typeFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les types</option>
                        <option value="patient">Patients</option>
                        <option value="doctor">Médecins</option>
                        <option value="appointment">Rendez-vous</option>
                        <option value="consultation">Consultations</option>
                        <option value="prescription">Ordonnances</option>
                        <option value="medical_record">Dossiers médicaux</option>
                    </select>
                    <select id="periodFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Toutes les périodes</option>
                        <option value="today">Aujourd'hui</option>
                        <option value="week">Cette semaine</option>
                        <option value="month">Ce mois</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="refreshBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Actualiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des activités -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Toutes les activités</h3>
                <p class="text-sm text-gray-500 mt-1">Historique complet des activités du système</p>
            </div>
            
            <div id="activitiesContainer" class="divide-y divide-gray-200">
                <!-- Les activités seront chargées ici -->
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        <p class="text-gray-500 mt-2">Chargement des activités...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        let allActivities = [];
        let filteredActivities = [];

        // Charger toutes les activités
        function loadAllActivities() {
            fetch('get_all_activities.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allActivities = data.activities;
                        filteredActivities = [...allActivities];
                        displayActivities();
                    } else {
                        showError('Erreur lors du chargement des activités: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showError('Erreur de connexion au serveur');
                });
        }

        // Afficher les activités
        function displayActivities() {
            const container = document.getElementById('activitiesContainer');
            
            if (filteredActivities.length === 0) {
                container.innerHTML = `
                    <div class="flex items-center justify-center py-12">
                        <div class="text-center">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Aucune activité trouvée</p>
                        </div>
                    </div>
                `;
                return;
            }

            container.innerHTML = filteredActivities.map(activity => `
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center bg-${activity.color}-100">
                                <i class="${activity.icon} text-${activity.color}-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">${activity.action}</p>
                                <p class="text-xs text-gray-500">${activity.time_ago}</p>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">${activity.description}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Filtrer les activités
        function filterActivities() {
            const typeFilter = document.getElementById('typeFilter').value;
            const periodFilter = document.getElementById('periodFilter').value;

            filteredActivities = allActivities.filter(activity => {
                // Filtre par type
                if (typeFilter && activity.type !== typeFilter) {
                    return false;
                }

                // Filtre par période
                if (periodFilter) {
                    const activityDate = new Date(activity.time);
                    const now = new Date();
                    
                    if (periodFilter === 'today') {
                        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                        return activityDate >= today;
                    } else if (periodFilter === 'week') {
                        const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                        return activityDate >= weekAgo;
                    } else if (periodFilter === 'month') {
                        const monthAgo = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
                        return activityDate >= monthAgo;
                    }
                }

                return true;
            });

            displayActivities();
        }

        // Afficher une erreur
        function showError(message) {
            const container = document.getElementById('activitiesContainer');
            container.innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-300 mb-4"></i>
                        <p class="text-red-500">${message}</p>
                    </div>
                </div>
            `;
        }

        // Event listeners
        document.getElementById('typeFilter').addEventListener('change', filterActivities);
        document.getElementById('periodFilter').addEventListener('change', filterActivities);
        document.getElementById('refreshBtn').addEventListener('click', loadAllActivities);

        // Charger les activités au chargement de la page
        document.addEventListener('DOMContentLoaded', loadAllActivities);
    </script>
</body>
</html> 