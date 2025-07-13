// Fonctions pour la gestion des dossiers médicaux

function loadDossiersMedicaux() {
    fetch('lister_dossiers_medicaux.php')
        .then(response => response.json())
        .then(data => {
            const patientsList = document.getElementById('patients-list');
            
            if (data.success && data.dossiers.length > 0) {
                window._dossiersList = data.dossiers;
                patientsList.innerHTML = data.dossiers.map((dossier, index) => `
                    <div class="p-4 hover:bg-gray-50 cursor-pointer patient-item ${index === 0 ? 'border-l-4 border-indigo-500 bg-indigo-50' : ''}" 
                         data-medical-record-id="${dossier.medical_record_id}" 
                         onclick="selectDossier(${dossier.medical_record_id})">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" src="${dossier.patient_info.photo && dossier.patient_info.photo !== 'null' ? '/medicale/' + dossier.patient_info.photo : 'https://randomuser.me/api/portraits/lego/1.jpg'}" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 patient-name">${dossier.patient_name}</div>
                                <div class="text-sm text-gray-500">${dossier.patient_info.sexe ? dossier.patient_info.sexe : ''}${dossier.patient_info.date_naissance ? ', ' + getAge(dossier.patient_info.date_naissance) + ' ans' : ''}</div>
                                <div class="text-xs text-gray-400 mt-1">Dernière visite: ${dossier.statistiques.derniere_consultation ? formatDate(dossier.statistiques.derniere_consultation) : 'Aucune'}</div>
                            </div>
                        </div>
                    </div>
                `).join('');
                
                // Sélectionner le premier dossier par défaut
                if (data.dossiers.length > 0) {
                    selectDossier(data.dossiers[0].medical_record_id);
                }
            } else {
                patientsList.innerHTML = '<div class="p-4 text-center text-gray-400">Aucun dossier médical trouvé</div>';
            }
        })
        .catch(() => {
            const patientsList = document.getElementById('patients-list');
            patientsList.innerHTML = '<div class="p-4 text-center text-red-400">Erreur lors du chargement des dossiers</div>';
        });
}

function selectDossier(medicalRecordId) {
    // Mettre à jour la sélection visuelle
    document.querySelectorAll('.patient-item').forEach(item => {
        item.classList.remove('border-l-4', 'border-indigo-500', 'bg-indigo-50');
    });
    document.querySelector(`[data-medical-record-id="${medicalRecordId}"]`).classList.add('border-l-4', 'border-indigo-500', 'bg-indigo-50');
    
    // Charger les détails du dossier
    fetch(`get_dossier_details.php?medical_record_id=${medicalRecordId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayDossierDetails(data);
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(() => {
            alert('Erreur lors du chargement du dossier');
        });
}

function displayDossierDetails(data) {
    const dossier = data.dossier;
    
    // Mettre à jour l'en-tête
    document.getElementById('dossier-header').innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0 h-14 w-14">
                <img class="h-14 w-14 rounded-full" src="${dossier.patient_info.photo && dossier.patient_info.photo !== 'null' ? '/medicale/' + dossier.patient_info.photo : 'https://randomuser.me/api/portraits/lego/1.jpg'}" alt="">
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-bold text-gray-900">${dossier.patient_info.nom} ${dossier.patient_info.prenom}</h3>
                <div class="flex items-center space-x-4 mt-1">
                    <span class="text-sm text-gray-600">${dossier.patient_info.sexe ? dossier.patient_info.sexe : ''}${dossier.patient_info.date_naissance ? ', ' + getAge(dossier.patient_info.date_naissance) + ' ans (' + formatDate(dossier.patient_info.date_naissance) + ')' : ''}</span>
                    <span class="text-sm text-gray-600">Tél: ${dossier.patient_info.telephone || 'Non renseigné'}</span>
                    <span class="text-sm px-2 py-1 bg-blue-100 text-blue-800 rounded-full">Groupe sanguin: ${dossier.patient_info.groupe_sanguin || 'Non renseigné'}</span>
                </div>
            </div>
        </div>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center" onclick="printDossier(${dossier.medical_record_id})">
            <i class="fas fa-print mr-2"></i> Imprimer
        </button>
    `;
    
    // Mettre à jour les onglets
    document.getElementById('dossier-tabs').innerHTML = `
        <a href="#" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" onclick="showTab('resume')">Résumé</a>
        <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" onclick="showTab('historique')">Historique</a>
        <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" onclick="showTab('ordonnances')">Ordonnances</a>
        <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" onclick="showTab('examens')">Examens</a>
        <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" onclick="showTab('allergies')">Allergies</a>
    `;
    
    // Afficher le contenu par défaut (résumé)
    showTabContent('resume', data);
}

function showTab(tabName) {
    // Mettre à jour les onglets
    document.querySelectorAll('#dossier-tabs a').forEach(tab => {
        tab.classList.remove('border-indigo-500', 'text-indigo-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    event.target.classList.remove('border-transparent', 'text-gray-500');
    event.target.classList.add('border-indigo-500', 'text-indigo-600');
    
    // Charger le contenu de l'onglet
    const currentDossierId = document.querySelector('.patient-item.border-l-4').getAttribute('data-medical-record-id');
    fetch(`get_dossier_details.php?medical_record_id=${currentDossierId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showTabContent(tabName, data);
            }
        });
}

function showTabContent(tabName, data) {
    const content = document.getElementById('dossier-content');
    
    switch(tabName) {
        case 'resume':
            content.innerHTML = generateResumeContent(data);
            break;
        case 'historique':
            content.innerHTML = generateHistoriqueContent(data);
            break;
        case 'ordonnances':
            content.innerHTML = generateOrdonnancesContent(data);
            break;
        case 'examens':
            content.innerHTML = generateExamensContent(data);
            break;
        case 'allergies':
            content.innerHTML = generateAllergiesContent(data);
            break;
    }
}

function generateResumeContent(data) {
    const dossier = data.dossier;
    return `
        <div class="mb-8">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Informations médicales</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h5 class="text-sm font-medium text-gray-500 mb-2">ANTÉCÉDENTS MÉDICAUX</h5>
                    <p class="text-sm text-gray-700">Informations à compléter par le médecin</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h5 class="text-sm font-medium text-gray-500 mb-2">ALLERGIES</h5>
                    <p class="text-sm text-gray-700">${dossier.patient_info.allergies || 'Aucune allergie connue'}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h5 class="text-sm font-medium text-gray-500 mb-2">TRAITEMENTS EN COURS</h5>
                    <p class="text-sm text-gray-700">Aucun traitement en cours</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h5 class="text-sm font-medium text-gray-500 mb-2">HABITUDES DE VIE</h5>
                    <p class="text-sm text-gray-700">Informations à compléter</p>
                </div>
            </div>
        </div>
        
        <div class="mb-8">
            <h4 class="text-lg font-medium text-gray-900 mb-4">Dernières consultations</h4>
            <div class="space-y-4">
                ${data.consultations.length > 0 ? data.consultations.slice(0, 3).map(consultation => `
                    <div class="border-l-4 border-indigo-500 pl-4 py-2 bg-gray-50 rounded-r-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="font-medium text-gray-900">${consultation.type}</h5>
                                <p class="text-sm text-gray-600">${consultation.doctor_name} - ${formatDate(consultation.date_consultation)}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Terminé</span>
                        </div>
                        <div class="mt-2 text-sm text-gray-700">
                            <p class="font-medium">Motif:</p>
                            <p>${consultation.motif}</p>
                            <p class="font-medium mt-2">Observations:</p>
                            <p>${consultation.observations || 'Aucune observation'}</p>
                        </div>
                    </div>
                `).join('') : '<p class="text-gray-500">Aucune consultation enregistrée</p>'}
            </div>
        </div>
        
        <div>
            <h4 class="text-lg font-medium text-gray-900 mb-4">Ajouter une nouvelle entrée</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
                <form id="form-nouvelle-consultation">
                    <input type="hidden" name="medical_record_id" value="${dossier.medical_record_id}">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de consultation</label>
                            <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="Consultation générale">Consultation générale</option>
                                <option value="Consultation spécialisée">Consultation spécialisée</option>
                                <option value="Urgence">Urgence</option>
                                <option value="Bilan de santé">Bilan de santé</option>
                                <option value="Suivi traitement">Suivi traitement</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                            <select name="doctor_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">-- Sélectionner --</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motif de la consultation</label>
                            <textarea name="motif" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observations et diagnostic</label>
                            <textarea name="observations" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-3 hover:bg-gray-50">Annuler</button>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    // Charger la liste des médecins
    loadDoctorsForConsultation();
}

function generateHistoriqueContent(data) {
    return `
        <div class="space-y-4">
            ${data.consultations.length > 0 ? data.consultations.map(consultation => `
                <div class="border-l-4 border-indigo-500 pl-4 py-2 bg-gray-50 rounded-r-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h5 class="font-medium text-gray-900">${consultation.type}</h5>
                            <p class="text-sm text-gray-600">${consultation.doctor_name} - ${formatDate(consultation.date_consultation)}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Terminé</span>
                    </div>
                    <div class="mt-2 text-sm text-gray-700">
                        <p class="font-medium">Motif:</p>
                        <p>${consultation.motif}</p>
                        <p class="font-medium mt-2">Observations:</p>
                        <p>${consultation.observations || 'Aucune observation'}</p>
                    </div>
                </div>
            `).join('') : '<p class="text-gray-500">Aucune consultation enregistrée</p>'}
        </div>
    `;
}

function generateOrdonnancesContent(data) {
    return `
        <div class="space-y-4">
            ${data.prescriptions.length > 0 ? data.prescriptions.map(prescription => `
                <div class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50 rounded-r-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h5 class="font-medium text-gray-900">Ordonnance du ${formatDate(prescription.date_prescription)}</h5>
                            <p class="text-sm text-gray-600">${prescription.doctor_name} - ${formatDate(prescription.consultation_date)}</p>
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-700">
                        <p class="font-medium">Médicaments:</p>
                        ${prescription.medications.length > 0 ? prescription.medications.map(med => `
                            <div class="ml-4 mt-1">
                                <p><strong>${med.nom}</strong> ${med.dosage ? '(' + med.dosage + ')' : ''}</p>
                                <p class="text-gray-600">${med.quantite} - ${med.instructions || 'Sans instruction particulière'}</p>
                            </div>
                        `).join('') : '<p class="ml-4 text-gray-500">Aucun médicament prescrit</p>'}
                        ${prescription.details ? `<p class="font-medium mt-2">Notes:</p><p>${prescription.details}</p>` : ''}
                    </div>
                </div>
            `).join('') : '<p class="text-gray-500">Aucune ordonnance enregistrée</p>'}
        </div>
    `;
}

function generateExamensContent(data) {
    return `
        <div class="text-center text-gray-500 py-8">
            <i class="fas fa-microscope text-4xl mb-4"></i>
            <p>Fonctionnalité des examens à implémenter</p>
        </div>
    `;
}

function generateAllergiesContent(data) {
    const dossier = data.dossier;
    return `
        <div class="bg-gray-50 p-4 rounded-lg">
            <h5 class="text-sm font-medium text-gray-500 mb-2">ALLERGIES CONNUES</h5>
            <p class="text-sm text-gray-700">${dossier.patient_info.allergies || 'Aucune allergie connue'}</p>
        </div>
    `;
}

function loadDoctorsForConsultation() {
    fetch('get_admin_doctors.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.querySelector('select[name="doctor_id"]');
                if (select) {
                    data.doctors.forEach(doctor => {
                        const option = document.createElement('option');
                        option.value = doctor.id;
                        option.textContent = doctor.name + (doctor.specialite ? ' (' + doctor.specialite + ')' : '');
                        select.appendChild(option);
                    });
                }
            }
        });
}

function printDossier(medicalRecordId) {
    alert('Fonctionnalité d\'impression à implémenter pour le dossier ' + medicalRecordId);
}

// Fonctions utilitaires
function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('fr-FR');
}

function getAge(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    const diff = Date.now() - d.getTime();
    const age = new Date(diff).getUTCFullYear() - 1970;
    return age;
} 