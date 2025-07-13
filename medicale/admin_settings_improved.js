// Fonction améliorée pour charger la page paramètres avec un design moderne
function loadSettingsImproved() {
    document.getElementById('page-content').innerHTML = `
        <div class="mb-8 animate-fadeIn">
            <div class="flex items-center gap-4 mb-4">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-3 rounded-2xl shadow-lg">
                    <i class="fas fa-cog text-white text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Paramètres</h2>
                    <p class="text-gray-600 text-lg">Configuration et personnalisation de votre compte</p>
                </div>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-6 border border-blue-200 shadow-dynamic hover-lift animate-slideInLeft">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-600 font-semibold text-xs uppercase tracking-wide">Dernière connexion</p>
                            <p class="text-2xl font-bold text-blue-800 mt-2">Aujourd'hui</p>
                        </div>
                        <div class="bg-blue-500 rounded-full p-3">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl p-6 border border-green-200 shadow-dynamic hover-lift animate-slideInLeft" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-600 font-semibold text-xs uppercase tracking-wide">Sécurité</p>
                            <p class="text-2xl font-bold text-green-800 mt-2">Active</p>
                        </div>
                        <div class="bg-green-500 rounded-full p-3">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-2xl p-6 border border-purple-200 shadow-dynamic hover-lift animate-slideInLeft" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-600 font-semibold text-xs uppercase tracking-wide">Notifications</p>
                            <p class="text-2xl font-bold text-purple-800 mt-2">Activées</p>
                        </div>
                        <div class="bg-purple-500 rounded-full p-3">
                            <i class="fas fa-bell text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-amber-100 rounded-2xl p-6 border border-orange-200 shadow-dynamic hover-lift animate-slideInLeft" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-600 font-semibold text-xs uppercase tracking-wide">Sauvegarde</p>
                            <p class="text-2xl font-bold text-orange-800 mt-2">Auto</p>
                        </div>
                        <div class="bg-orange-500 rounded-full p-3">
                            <i class="fas fa-cloud text-white text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Menu de navigation des paramètres -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden animate-slideInLeft">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
                        <h3 class="text-white font-bold text-lg mb-2">Navigation</h3>
                        <p class="text-indigo-200 text-sm">Sélectionnez une section</p>
                    </div>
                    <nav class="p-4 space-y-2">
                        <a href="#" class="settings-nav-item active bg-indigo-50 border-indigo-500 text-indigo-700 group flex items-center px-4 py-3 rounded-xl border-l-4 text-sm font-medium transition-all duration-300 hover:shadow-md" data-section="profile">
                            <div class="bg-indigo-100 text-indigo-600 rounded-lg p-2 mr-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Compte utilisateur</div>
                                <div class="text-xs text-indigo-500">Informations personnelles</div>
                            </div>
                        </a>
                        <a href="#" class="settings-nav-item border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 rounded-xl border-l-4 text-sm font-medium transition-all duration-300 hover:shadow-md" data-section="clinic">
                            <div class="bg-gray-100 text-gray-600 rounded-lg p-2 mr-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Informations clinique</div>
                                <div class="text-xs text-gray-500">Données de l'établissement</div>
                            </div>
                        </a>
                        <a href="#" class="settings-nav-item border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 rounded-xl border-l-4 text-sm font-medium transition-all duration-300 hover:shadow-md" data-section="appointments">
                            <div class="bg-gray-100 text-gray-600 rounded-lg p-2 mr-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Paramètres RDV</div>
                                <div class="text-xs text-gray-500">Configuration agenda</div>
                            </div>
                        </a>
                        <a href="#" class="settings-nav-item border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 rounded-xl border-l-4 text-sm font-medium transition-all duration-300 hover:shadow-md" data-section="notifications">
                            <div class="bg-gray-100 text-gray-600 rounded-lg p-2 mr-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Notifications</div>
                                <div class="text-xs text-gray-500">Préférences alertes</div>
                            </div>
                        </a>
                        <a href="#" class="settings-nav-item border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 rounded-xl border-l-4 text-sm font-medium transition-all duration-300 hover:shadow-md" data-section="security">
                            <div class="bg-gray-100 text-gray-600 rounded-lg p-2 mr-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Sécurité</div>
                                <div class="text-xs text-gray-500">Protection du compte</div>
                            </div>
                        </a>
                        <a href="#" class="settings-nav-item border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-4 py-3 rounded-xl border-l-4 text-sm font-medium transition-all duration-300 hover:shadow-md" data-section="access">
                            <div class="bg-gray-100 text-gray-600 rounded-lg p-2 mr-3 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Gestion accès</div>
                                <div class="text-xs text-gray-500">Permissions utilisateurs</div>
                            </div>
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Contenu des paramètres -->
            <div class="lg:col-span-3">
                <!-- Section Compte utilisateur -->
                <div id="settings-content" class="space-y-6">
                    <!-- Profil utilisateur -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden animate-slideInRight">
                        <div class="bg-gradient-to-r from-gray-50 to-indigo-50 p-6 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-3 rounded-xl">
                                    <i class="fas fa-user-cog text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">Compte utilisateur</h3>
                                    <p class="text-gray-600">Gérez vos informations personnelles et votre profil</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <form id="form-admin-settings" enctype="multipart/form-data" class="space-y-8">
                                <!-- Photo de profil -->
                                <div class="flex items-center gap-8 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl border border-indigo-100">
                                    <div class="relative">
                                        <img id="admin-photo-preview" class="h-24 w-24 rounded-2xl object-cover border-4 border-white shadow-lg" src="${window.adminUserInfo.photo_url}" alt="Photo profil">
                                        <div class="absolute -bottom-2 -right-2 bg-green-500 rounded-full p-1 border-2 border-white">
                                            <i class="fas fa-check text-white text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-lg font-semibold text-gray-800 mb-2">Photo de profil</h4>
                                        <p class="text-gray-600 text-sm mb-4">Ajoutez une photo pour personnaliser votre profil</p>
                                        <label class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl text-sm font-semibold cursor-pointer transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-flex items-center gap-2">
                                            <i class="fas fa-camera"></i>
                                            Changer la photo
                                            <input type="file" name="photo" id="admin-photo-input" class="hidden" accept="image/*">
                                        </label>
                                        <p class="text-xs text-gray-500 mt-2">JPG, GIF ou PNG. Taille max: 2MB</p>
                                    </div>
                                </div>
                                
                                <!-- Informations personnelles -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fas fa-user text-indigo-500"></i>
                                            Prénom
                                        </label>
                                        <div class="relative">
                                            <input type="text" name="prenom" id="admin-prenom" value="${window.adminUserInfo.prenom}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-300" placeholder="Votre prénom">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fas fa-user text-indigo-500"></i>
                                            Nom
                                        </label>
                                        <div class="relative">
                                            <input type="text" name="nom" id="admin-nom" value="${window.adminUserInfo.nom}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-300" placeholder="Votre nom">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <i class="fas fa-envelope text-indigo-500"></i>
                                        Adresse email
                                    </label>
                                    <div class="relative">
                                        <input type="email" name="email" id="admin-email" value="${window.adminUserInfo.email}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-300" placeholder="votre.email@exemple.com">
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <i class="fas fa-phone text-indigo-500"></i>
                                        Numéro de téléphone
                                    </label>
                                    <div class="relative">
                                        <input type="tel" name="telephone" id="admin-telephone" value="${window.adminUserInfo.telephone}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all duration-300" placeholder="+33 6 12 34 56 78">
                                    </div>
                                </div>
                                
                                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                                    <button type="button" class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300 shadow-sm">
                                        <i class="fas fa-times mr-2"></i>Annuler
                                    </button>
                                    <button type="submit" class="bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                        <i class="fas fa-save mr-2"></i>Enregistrer
                                    </button>
                                </div>
                            </form>
                            <div id="admin-settings-feedback" class="mt-6"></div>
                        </div>
                    </div>
                    
                    <!-- Formulaire de changement de mot de passe -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden animate-slideInRight" style="animation-delay: 0.2s;">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 border-b border-gray-200">
                            <div class="flex items-center gap-4">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-xl">
                                    <i class="fas fa-lock text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">Sécurité du compte</h3>
                                    <p class="text-gray-600">Modifiez votre mot de passe pour sécuriser votre compte</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8">
                            <form id="form-admin-password" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fas fa-key text-green-500"></i>
                                            Mot de passe actuel
                                        </label>
                                        <div class="relative">
                                            <input type="password" name="current_password" id="admin-current-password" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all duration-300" required placeholder="••••••••">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                <i class="fas fa-eye text-gray-400 cursor-pointer" onclick="togglePasswordVisibility('admin-current-password')"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                            <i class="fas fa-shield-alt text-green-500"></i>
                                            Nouveau mot de passe
                                        </label>
                                        <div class="relative">
                                            <input type="password" name="new_password" id="admin-new-password" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all duration-300" required placeholder="••••••••">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                <i class="fas fa-eye text-gray-400 cursor-pointer" onclick="togglePasswordVisibility('admin-new-password')"></i>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <i class="fas fa-info-circle text-blue-500"></i>
                                            Le mot de passe doit contenir au moins 8 caractères
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        Confirmer le nouveau mot de passe
                                    </label>
                                    <div class="relative">
                                        <input type="password" name="confirm_password" id="admin-confirm-password" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all duration-300" required placeholder="••••••••">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <i class="fas fa-eye text-gray-400 cursor-pointer" onclick="togglePasswordVisibility('admin-confirm-password')"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                                    <button type="button" class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-50 transition-all duration-300 shadow-sm" onclick="resetPasswordForm()">
                                        <i class="fas fa-times mr-2"></i>Annuler
                                    </button>
                                    <button type="submit" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                        <i class="fas fa-lock mr-2"></i>Changer le mot de passe
                                    </button>
                                </div>
                            </form>
                            <div id="admin-password-feedback" class="mt-6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Initialiser les événements
    initializeSettingsEvents();
}

// Fonction pour initialiser tous les événements de la page paramètres
function initializeSettingsEvents() {
    // Gestion de la navigation des paramètres
    document.querySelectorAll('.settings-nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Retirer la classe active de tous les éléments
            document.querySelectorAll('.settings-nav-item').forEach(el => {
                el.classList.remove('active', 'bg-indigo-50', 'border-indigo-500', 'text-indigo-700');
                el.classList.add('border-transparent', 'text-gray-600');
            });
            
            // Ajouter la classe active à l'élément cliqué
            this.classList.add('active', 'bg-indigo-50', 'border-indigo-500', 'text-indigo-700');
            this.classList.remove('border-transparent', 'text-gray-600');
            
            // Ici on pourrait charger le contenu correspondant à la section
            const section = this.getAttribute('data-section');
            console.log('Section sélectionnée:', section);
        });
    });

    // Preview de la photo
    document.getElementById('admin-photo-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('admin-photo-preview').src = evt.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Soumission AJAX du formulaire principal
    document.getElementById('form-admin-settings').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Afficher un indicateur de chargement
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enregistrement...';
        submitBtn.disabled = true;
        
        fetch('update_admin_user.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            const feedback = document.getElementById('admin-settings-feedback');
            if(data.success) {
                feedback.innerHTML = '<div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl"><i class="fas fa-check-circle text-green-500 text-xl"></i><div><div class="font-semibold text-green-800">Succès !</div><div class="text-green-600 text-sm">Vos modifications ont été enregistrées avec succès.</div></div></div>';
            } else {
                feedback.innerHTML = '<div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl"><i class="fas fa-exclamation-triangle text-red-500 text-xl"></i><div><div class="font-semibold text-red-800">Erreur</div><div class="text-red-600 text-sm">' + (data.message || 'Impossible de sauvegarder les modifications.') + '</div></div></div>';
            }
        })
        .catch(() => {
            const feedback = document.getElementById('admin-settings-feedback');
            feedback.innerHTML = '<div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl"><i class="fas fa-exclamation-triangle text-red-500 text-xl"></i><div><div class="font-semibold text-red-800">Erreur</div><div class="text-red-600 text-sm">Erreur lors de la communication avec le serveur.</div></div></div>';
        })
        .finally(() => {
            // Restaurer le bouton
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Soumission AJAX du formulaire de changement de mot de passe
    document.getElementById('form-admin-password').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('admin-current-password').value;
        const newPassword = document.getElementById('admin-new-password').value;
        const confirmPassword = document.getElementById('admin-confirm-password').value;
        
        // Validation côté client
        if (newPassword.length < 8) {
            const feedback = document.getElementById('admin-password-feedback');
            feedback.innerHTML = '<div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl"><i class="fas fa-exclamation-triangle text-red-500 text-xl"></i><div><div class="font-semibold text-red-800">Erreur</div><div class="text-red-600 text-sm">Le nouveau mot de passe doit contenir au moins 8 caractères.</div></div></div>';
            return;
        }
        
        if (newPassword !== confirmPassword) {
            const feedback = document.getElementById('admin-password-feedback');
            feedback.innerHTML = '<div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl"><i class="fas fa-exclamation-triangle text-red-500 text-xl"></i><div><div class="font-semibold text-red-800">Erreur</div><div class="text-red-600 text-sm">Les mots de passe ne correspondent pas.</div></div></div>';
            return;
        }
        
        const formData = new FormData();
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);
        
        // Afficher un indicateur de chargement
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Modification...';
        submitBtn.disabled = true;
        
        fetch('change_admin_password.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            const feedback = document.getElementById('admin-password-feedback');
            if(data.success) {
                feedback.innerHTML = '<div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl"><i class="fas fa-check-circle text-green-500 text-xl"></i><div><div class="font-semibold text-green-800">Succès !</div><div class="text-green-600 text-sm">Votre mot de passe a été modifié avec succès.</div></div></div>';
                resetPasswordForm();
            } else {
                feedback.innerHTML = '<div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl"><i class="fas fa-exclamation-triangle text-red-500 text-xl"></i><div><div class="font-semibold text-red-800">Erreur</div><div class="text-red-600 text-sm">' + (data.message || 'Impossible de modifier le mot de passe.') + '</div></div></div>';
            }
        })
        .catch(() => {
            const feedback = document.getElementById('admin-password-feedback');
            feedback.innerHTML = '<div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl"><i class="fas fa-exclamation-triangle text-red-500 text-xl"></i><div><div class="font-semibold text-red-800">Erreur</div><div class="text-red-600 text-sm">Erreur lors de la communication avec le serveur.</div></div></div>';
        })
        .finally(() => {
            // Restaurer le bouton
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
}

// Fonction pour basculer la visibilité du mot de passe
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Fonction pour réinitialiser le formulaire de mot de passe
function resetPasswordForm() {
    document.getElementById('form-admin-password').reset();
    document.getElementById('admin-password-feedback').innerHTML = '';
} 