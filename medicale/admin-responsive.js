// Gestion de la responsivité pour l'interface admin

class AdminResponsive {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.hamburgerMenu = document.getElementById('hamburger-menu');
        this.mobileOverlay = document.getElementById('mobile-overlay');
        this.body = document.body;
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupResponsiveTables();
        this.setupResponsiveForms();
        this.setupResponsiveModals();
        this.setupResponsiveCards();
        this.handleResize();
    }

    setupEventListeners() {
        // Menu hamburger
        if (this.hamburgerMenu) {
            this.hamburgerMenu.addEventListener('click', () => this.toggleSidebar());
        }

        // Overlay mobile
        if (this.mobileOverlay) {
            this.mobileOverlay.addEventListener('click', () => this.closeSidebar());
        }

        // Fermer le menu en cliquant sur les liens (mobile)
        const sidebarLinks = document.querySelectorAll('#sidebar a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    this.closeSidebar();
                }
            });
        });

        // Gestion du redimensionnement
        window.addEventListener('resize', () => this.handleResize());

        // Fermer le menu avec la touche Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && window.innerWidth < 768) {
                this.closeSidebar();
            }
        });

        // Empêcher le scroll du body quand le menu est ouvert
        this.sidebar?.addEventListener('touchmove', (e) => {
            if (window.innerWidth < 768) {
                e.preventDefault();
            }
        }, { passive: false });
    }

    toggleSidebar() {
        if (!this.sidebar) return;
        
        const isOpen = !this.sidebar.classList.contains('-translate-x-full');
        
        if (isOpen) {
            this.closeSidebar();
        } else {
            this.openSidebar();
        }
    }

    openSidebar() {
        if (!this.sidebar || !this.mobileOverlay) return;
        
        this.sidebar.classList.remove('-translate-x-full');
        this.mobileOverlay.classList.remove('hidden');
        this.body.classList.add('overflow-hidden');
        
        // Focus sur le premier lien du menu pour l'accessibilité
        const firstLink = this.sidebar.querySelector('a');
        if (firstLink) {
            setTimeout(() => firstLink.focus(), 100);
        }
    }

    closeSidebar() {
        if (!this.sidebar || !this.mobileOverlay) return;
        
        this.sidebar.classList.add('-translate-x-full');
        this.mobileOverlay.classList.add('hidden');
        this.body.classList.remove('overflow-hidden');
        
        // Retourner le focus au bouton hamburger
        if (this.hamburgerMenu) {
            this.hamburgerMenu.focus();
        }
    }

    handleResize() {
        if (window.innerWidth >= 768) {
            // Desktop
            if (this.sidebar) {
                this.sidebar.classList.remove('-translate-x-full');
            }
            if (this.mobileOverlay) {
                this.mobileOverlay.classList.add('hidden');
            }
            this.body.classList.remove('overflow-hidden');
        } else {
            // Mobile
            if (this.sidebar) {
                this.sidebar.classList.add('-translate-x-full');
            }
        }
    }

    setupResponsiveTables() {
        const tables = document.querySelectorAll('table');
        
        tables.forEach(table => {
            // Ajouter la classe responsive
            table.classList.add('responsive-table');
            
            // Créer un wrapper pour le scroll horizontal sur mobile
            if (!table.parentElement.classList.contains('table-wrapper')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-wrapper overflow-x-auto';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
    }

    setupResponsiveForms() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.classList.add('responsive-form');
            
            // Ajouter des classes aux champs de formulaire
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    input.classList.add('form-input');
                }
            });
            
            // Wrapper les labels et inputs dans des groupes
            const labels = form.querySelectorAll('label');
            labels.forEach(label => {
                label.classList.add('form-label');
                
                // Trouver l'input associé
                const forAttr = label.getAttribute('for');
                let input = null;
                
                if (forAttr) {
                    input = document.getElementById(forAttr);
                } else {
                    // Chercher l'input suivant
                    input = label.nextElementSibling;
                }
                
                if (input && (input.tagName === 'INPUT' || input.tagName === 'SELECT' || input.tagName === 'TEXTAREA')) {
                    // Créer un groupe si nécessaire
                    if (!input.parentElement.classList.contains('form-group')) {
                        const group = document.createElement('div');
                        group.className = 'form-group';
                        input.parentNode.insertBefore(group, input);
                        group.appendChild(input);
                    }
                    
                    // Déplacer le label dans le groupe
                    const group = input.parentElement;
                    if (group.classList.contains('form-group') && group !== label.parentElement) {
                        group.insertBefore(label, input);
                    }
                }
            });
        });
    }

    setupResponsiveModals() {
        const modals = document.querySelectorAll('.modal, [id*="Modal"]');
        
        modals.forEach(modal => {
            modal.classList.add('modal');
            
            // Ajouter la classe au contenu de la modal
            const content = modal.querySelector('.modal-content, > div');
            if (content) {
                content.classList.add('modal-content');
            }
            
            // Gestion de la fermeture avec Escape
            modal.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeModal(modal);
                }
            });
            
            // Fermer en cliquant sur l'overlay
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal);
                }
            });
        });
    }

    setupResponsiveCards() {
        const cards = document.querySelectorAll('.card, .bg-white, .bg-gray-50');
        
        cards.forEach(card => {
            // Ajouter la classe responsive si ce n'est pas déjà fait
            if (!card.classList.contains('responsive-card') && 
                !card.classList.contains('stats-card') &&
                card.offsetWidth > 200) {
                card.classList.add('responsive-card');
            }
        });
    }

    closeModal(modal) {
        modal.classList.remove('active');
        this.body.classList.remove('overflow-hidden');
    }

    openModal(modal) {
        modal.classList.add('active');
        this.body.classList.add('overflow-hidden');
        
        // Focus sur le premier élément focusable
        const focusableElements = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
    }

    // Méthode pour rendre les grilles responsives
    setupResponsiveGrids() {
        const grids = document.querySelectorAll('.grid');
        
        grids.forEach(grid => {
            const columns = grid.children.length;
            
            if (columns <= 2) {
                grid.classList.add('responsive-grid-2');
            } else if (columns <= 3) {
                grid.classList.add('responsive-grid-3');
            } else {
                grid.classList.add('responsive-grid');
            }
        });
    }

    // Méthode pour optimiser les images
    setupResponsiveImages() {
        const images = document.querySelectorAll('img');
        
        images.forEach(img => {
            // Ajouter loading="lazy" pour les images
            if (!img.hasAttribute('loading')) {
                img.setAttribute('loading', 'lazy');
            }
            
            // Ajouter des classes responsives
            img.classList.add('max-w-full', 'h-auto');
        });
    }

    // Méthode pour gérer les boutons responsives
    setupResponsiveButtons() {
        const buttons = document.querySelectorAll('button, .btn, [role="button"]');
        
        buttons.forEach(button => {
            if (!button.classList.contains('btn')) {
                button.classList.add('btn');
            }
            
            // Ajouter des classes selon le contexte
            if (button.textContent.includes('Supprimer') || button.textContent.includes('Delete')) {
                button.classList.add('btn-danger');
            } else if (button.textContent.includes('Modifier') || button.textContent.includes('Edit')) {
                button.classList.add('btn-secondary');
            } else if (button.textContent.includes('Ajouter') || button.textContent.includes('Add')) {
                button.classList.add('btn-primary');
            }
        });
    }

    // Méthode pour optimiser les tableaux de données
    optimizeDataTables() {
        const tables = document.querySelectorAll('table');
        
        tables.forEach(table => {
            // Ajouter des attributs pour l'accessibilité
            table.setAttribute('role', 'table');
            
            const headers = table.querySelectorAll('th');
            headers.forEach(header => {
                header.setAttribute('scope', 'col');
            });
            
            // Ajouter des classes pour les statuts
            const cells = table.querySelectorAll('td');
            cells.forEach(cell => {
                const text = cell.textContent.toLowerCase();
                if (text.includes('confirmé') || text.includes('confirmed')) {
                    cell.classList.add('status-confirmed');
                } else if (text.includes('en attente') || text.includes('pending')) {
                    cell.classList.add('status-pending');
                } else if (text.includes('annulé') || text.includes('cancelled')) {
                    cell.classList.add('status-cancelled');
                } else if (text.includes('terminé') || text.includes('completed')) {
                    cell.classList.add('status-completed');
                }
            });
        });
    }

    // Méthode pour gérer les notifications
    setupNotifications() {
        // Créer un système de notifications toast
        this.createNotificationContainer();
    }

    createNotificationContainer() {
        if (!document.getElementById('notification-container')) {
            const container = document.createElement('div');
            container.id = 'notification-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
        }
    }

    showNotification(message, type = 'info', duration = 5000) {
        const container = document.getElementById('notification-container');
        if (!container) return;

        const notification = document.createElement('div');
        notification.className = `notification notification-${type} bg-white border-l-4 p-4 rounded shadow-lg transform translate-x-full transition-transform duration-300`;
        
        const borderColor = {
            'success': 'border-green-500',
            'error': 'border-red-500',
            'warning': 'border-yellow-500',
            'info': 'border-blue-500'
        }[type] || 'border-blue-500';

        notification.className = `notification notification-${type} bg-white border-l-4 ${borderColor} p-4 rounded shadow-lg transform translate-x-full transition-transform duration-300`;
        
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <p class="text-gray-800">${message}</p>
                <button class="ml-4 text-gray-400 hover:text-gray-600" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        container.appendChild(notification);

        // Animation d'entrée
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Auto-suppression
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, duration);
    }

    // Méthode pour optimiser les performances
    optimizePerformance() {
        // Debounce pour les événements de resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.handleResize();
            }, 250);
        });

        // Intersection Observer pour le lazy loading
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in');
                        observer.unobserve(entry.target);
                    }
                });
            });

            document.querySelectorAll('.responsive-card, .stats-card').forEach(card => {
                observer.observe(card);
            });
        }
    }
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', () => {
    window.adminResponsive = new AdminResponsive();
    
    // Initialiser les optimisations
    window.adminResponsive.setupResponsiveGrids();
    window.adminResponsive.setupResponsiveImages();
    window.adminResponsive.setupResponsiveButtons();
    window.adminResponsive.optimizeDataTables();
    window.adminResponsive.setupNotifications();
    window.adminResponsive.optimizePerformance();
});

// Exposer les méthodes utiles globalement
window.AdminResponsive = AdminResponsive; 