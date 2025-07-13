<?php
require_once 'auth.php';
require_role('admin');
require_once 'db.php';

// Configuration de la page
$page_title = 'Gestion des Patients';
$current_page = 'patients';

// Récupération des patients avec pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where_clause = '';
$params = [];

if (!empty($search)) {
    $where_clause = "WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR telephone LIKE ?";
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param, $search_param];
}

// Compter le total des patients
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM patients $where_clause");
$count_stmt->execute($params);
$total_patients = $count_stmt->fetchColumn();
$total_pages = ceil($total_patients / $limit);

// Récupérer les patients
$stmt = $pdo->prepare("
    SELECT p.*, 
           COUNT(d.id) as dossier_count,
           COUNT(r.id) as rdv_count
    FROM patients p 
    LEFT JOIN dossiers_medicaux d ON p.id = d.patient_id 
    LEFT JOIN rendezvous r ON p.id = r.patient_id 
    $where_clause
    GROUP BY p.id 
    ORDER BY p.nom, p.prenom 
    LIMIT ? OFFSET ?
");

$params[] = $limit;
$params[] = $offset;
$stmt->execute($params);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les infos de l'admin connecté
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare('SELECT nom, prenom, photo, email, telephone FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);
$nom = $user_info['nom'] ?? '';
$prenom = $user_info['prenom'] ?? '';
$email = $user_info['email'] ?? '';
$telephone = $user_info['telephone'] ?? '';

// Gestion de la photo
$photo_url = 'https://ui-avatars.com/api/?name=' . urlencode($prenom . '+' . $nom) . '&background=6366f1&color=fff&size=128&font-size=0.4&bold=true';
if (!empty($user_info['photo'])) {
    $photo_file = basename($user_info['photo']);
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/medicale/medicale/uploads/' . $photo_file)) {
        $photo_url = '/medicale/medicale/uploads/' . $photo_file;
    } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . '/medicale/uploads/' . $photo_file)) {
        $photo_url = '/medicale/uploads/' . $photo_file;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Gestion Clinique</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dossiers_medicaux.css">
    <link rel="stylesheet" href="admin-responsive.css">
    <script src="admin_settings_improved.js"></script>
    <script src="admin-responsive.js"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 md:static md:flex md:flex-shrink-0 transition-transform duration-300 ease-in-out">
            <div class="flex flex-col w-64 min-h-screen bg-gradient-to-b from-indigo-800 via-indigo-700 to-indigo-900 shadow-2xl transition-all duration-500 ease-in-out">
                <div class="flex items-center justify-center h-20 px-4 bg-indigo-900 shadow-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-heartbeat text-3xl animate-pulse text-pink-400 drop-shadow-lg"></i>
                        <span class="text-2xl font-extrabold tracking-wide text-white">Vaidya Mitra</span>
                    </div>
                </div>
                <div class="flex flex-col flex-grow px-4 py-4 overflow-y-auto">
                    <nav class="flex-1 space-y-2">
                        <a href="admin.php" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-green-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-tachometer-alt mr-3 text-xl group-hover:scale-125 group-hover:text-pink-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Tableau de bord</span>
                        </a>
                        <a href="lister_patients_responsive.php" class="group flex items-center px-4 py-3 rounded-xl bg-indigo-600 text-white shadow-lg transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-green-400 rounded-r-full transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-user-injured mr-3 text-xl group-hover:scale-125 group-hover:text-green-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Patients</span>
                        </a>
                        <a href="lister_dossiers_medicaux.php" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-purple-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-file-medical mr-3 text-xl group-hover:scale-125 group-hover:text-purple-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Dossiers médicaux</span>
                        </a>
                        <a href="lister_medicaments.php" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-yellow-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-pills mr-3 text-xl group-hover:scale-125 group-hover:text-yellow-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Médicaments</span>
                        </a>
                        <a href="lister_docteurs.php" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-pink-300 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-user-md mr-3 text-xl group-hover:scale-125 group-hover:text-pink-300 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Médecins</span>
                        </a>
                        <a href="lister_rendezvous.php" class="group flex items-center px-4 py-3 rounded-xl text-indigo-200 hover:bg-indigo-600 hover:text-white transition-all duration-300 ease-in-out relative sidebar-link">
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-blue-400 rounded-r-full opacity-0 group-hover:opacity-100 transition-all duration-300 sidebar-indicator"></span>
                            <i class="fas fa-calendar-alt mr-3 text-xl group-hover:scale-125 group-hover:text-blue-400 transition-transform duration-300"></i>
                            <span class="font-semibold text-base">Rendez-vous</span>
                        </a>
                    </nav>
                </div>
                <div class="p-4 border-t border-indigo-700 bg-indigo-900 shadow-inner animate-fadeIn">
                    <div class="flex items-center">
                        <div class="relative group">
                            <img class="w-12 h-12 rounded-full border-2 border-pink-400 shadow-lg transition-all duration-300 hover:scale-110 hover:border-pink-300" 
                                 src="<?php echo htmlspecialchars($photo_url); ?>" 
                                 alt="Photo de profil"
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($prenom . '+' . $nom); ?>&background=6366f1&color=fff&size=128&font-size=0.4&bold=true'">
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white shadow-sm animate-pulse"></div>
                        </div>
                        <div class="ml-3">
                            <p class="text-base font-bold text-white"><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></p>
                            <p class="text-xs text-indigo-200">Administrateur</p>
                            <a href="logout.php" class="block mt-2 text-xs text-pink-300 hover:text-pink-500 font-semibold transition-all duration-300 hover:scale-105"><i class="fas fa-sign-out-alt mr-1"></i>Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top Navigation -->
            <div class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200">
                <div class="flex items-center md:hidden">
                    <button id="hamburger-menu" class="text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors duration-200">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
                <div class="flex-1 max-w-md ml-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" class="w-full py-2 pl-10 pr-4 text-sm bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Rechercher...">
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <button class="text-gray-500 focus:outline-none">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="text-gray-500 focus:outline-none">
                        <i class="fas fa-envelope"></i>
                    </button>
                    <div class="relative">
                        <button class="flex items-center focus:outline-none">
                            <img class="w-8 h-8 rounded-full border-2 border-indigo-200 shadow-sm" 
                                 src="<?php echo htmlspecialchars($photo_url); ?>" 
                                 alt="Profile"
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($prenom . '+' . $nom); ?>&background=6366f1&color=fff&size=128&font-size=0.4'">
                        </button>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-6">
                <div class="animate-fadeIn">
                    <!-- Header de la page -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestion des Patients</h1>
                            <p class="text-gray-600">Gérez les informations de tous les patients de la clinique</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <a href="ajouter_patient.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Ajouter un patient
                            </a>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="responsive-grid-3 mb-6">
                        <div class="responsive-card">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-users text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Total Patients</p>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo $total_patients; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="responsive-card">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-file-medical text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Dossiers Médicaux</p>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo array_sum(array_column($patients, 'dossier_count')); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="responsive-card">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                    <i class="fas fa-calendar-check text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Rendez-vous</p>
                                    <p class="text-2xl font-bold text-gray-900"><?php echo array_sum(array_column($patients, 'rdv_count')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barre de recherche et filtres -->
                    <div class="responsive-card mb-6">
                        <form method="GET" class="responsive-form">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="form-group">
                                    <label for="search" class="form-label">Rechercher</label>
                                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                           class="form-input" placeholder="Nom, prénom, email...">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Actions</label>
                                    <div class="flex space-x-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                            Rechercher
                                        </button>
                                        <?php if (!empty($search)): ?>
                                            <a href="lister_patients_responsive.php" class="btn btn-secondary">
                                                <i class="fas fa-times"></i>
                                                Effacer
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tableau des patients -->
                    <div class="responsive-card">
                        <div class="overflow-x-auto">
                            <table class="responsive-table">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Contact</th>
                                        <th>Informations</th>
                                        <th>Statistiques</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($patients)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-8 text-gray-500">
                                                <i class="fas fa-users text-4xl mb-4"></i>
                                                <p>Aucun patient trouvé</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($patients as $patient): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td>
                                                    <div class="flex items-center">
                                                        <img class="w-10 h-10 rounded-full mr-3" 
                                                             src="https://ui-avatars.com/api/?name=<?php echo urlencode($patient['nom'] . '+' . $patient['prenom']); ?>&background=6366f1&color=fff&size=40" 
                                                             alt="Photo patient">
                                                        <div>
                                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']); ?></p>
                                                            <p class="text-sm text-gray-500">ID: <?php echo $patient['id']; ?></p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <p class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['email']); ?></p>
                                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($patient['telephone']); ?></p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <p class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['date_naissance']); ?></p>
                                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($patient['sexe']); ?></p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex space-x-2">
                                                        <span class="status-badge status-confirmed">
                                                            <i class="fas fa-file-medical mr-1"></i>
                                                            <?php echo $patient['dossier_count']; ?>
                                                        </span>
                                                        <span class="status-badge status-pending">
                                                            <i class="fas fa-calendar mr-1"></i>
                                                            <?php echo $patient['rdv_count']; ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex space-x-2">
                                                        <a href="dossier_medical.php?patient_id=<?php echo $patient['id']; ?>" 
                                                           class="btn btn-secondary btn-sm" title="Voir dossier">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="editer_patient.php?id=<?php echo $patient['id']; ?>" 
                                                           class="btn btn-warning btn-sm" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button onclick="deletePatient(<?php echo $patient['id']; ?>)" 
                                                                class="btn btn-danger btn-sm" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination mt-6">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="pagination-btn">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="pagination-btn <?php echo $i === $page ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" 
                                       class="pagination-btn">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

    <script>
        // Fonction pour supprimer un patient
        function deletePatient(patientId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce patient ? Cette action est irréversible.')) {
                // Ici vous pouvez ajouter la logique de suppression
                console.log('Suppression du patient:', patientId);
                // Redirection vers la page de suppression
                window.location.href = `supprimer_patient.php?id=${patientId}`;
            }
        }

        // Injection des variables PHP dans JavaScript
        window.adminUserInfo = {
            nom: '<?php echo addslashes($nom); ?>',
            prenom: '<?php echo addslashes($prenom); ?>',
            email: '<?php echo addslashes($email); ?>',
            telephone: '<?php echo addslashes($telephone); ?>',
            photo_url: '<?php echo addslashes($photo_url); ?>'
        };
    </script>
</body>
</html> 