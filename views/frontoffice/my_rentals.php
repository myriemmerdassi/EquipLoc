<?php
$pageTitle = "Mes Locations";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';

// Calculer les KPIs
$totalReservations = count($locations);
$totalEnCours = 0;
$totalDepense = 0;
foreach ($locations as $loc) {
    if (in_array($loc['statut'], ['En attente', 'Validée', 'En cours'])) {
        $totalEnCours++;
    }
    if ($loc['statut'] !== 'Annulée') {
        $totalDepense += ($loc['montant_total'] + $loc['frais_supplementaires']);
    }
}
$totalDocuments = $totalReservations * 3; // Contrat, facture, reçu
?>

<div style="background-color: #F8FAFC; min-height: calc(100vh - 70px);" class="pt-5 pb-5">
<div class="container">
    
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-primary text-uppercase fw-bold" style="font-size: 0.85rem; letter-spacing: 1px;">ESPACE CLIENT</span>
            <h1 class="fw-bold text-dark mt-1 mb-2" style="font-size: 2.5rem; letter-spacing: -0.5px; color: #0F172A !important;">Mes locations</h1>
            <p class="text-muted mb-0" style="font-size: 1.05rem;">Historique de vos réservations et téléchargement de vos documents officiels (contrat, facture, reçu).</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/index.php?action=catalogue" class="btn btn-primary fw-medium px-4 py-2" style="border-radius: 8px; background-color: #2563EB; border-color: #2563EB;">
                <i class="bi bi-plus-lg me-1"></i> Louer un équipement
            </a>
        </div>
    </div>

    <!-- KPIs Cards -->
    <div class="row g-4 mb-4">
        <!-- Réservations -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3 d-flex flex-row align-items-center bg-white">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-boxes text-primary fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark"><?= $totalReservations ?></h4>
                    <span class="text-muted small">Réservations</span>
                </div>
            </div>
        </div>
        <!-- En cours -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3 d-flex flex-row align-items-center bg-white">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-clock-history text-primary fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark"><?= $totalEnCours ?></h4>
                    <span class="text-muted small">En cours</span>
                </div>
            </div>
        </div>
        <!-- Total dépensé -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3 d-flex flex-row align-items-center bg-white">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-wallet2 text-primary fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark"><?= number_format($totalDepense, 2) ?> DT</h4>
                    <span class="text-muted small">Total dépensé</span>
                </div>
            </div>
        </div>
        <!-- Documents -->
        <div class="col-md-3">
            <div class="card border-0 rounded-4 shadow-sm h-100 p-3 d-flex flex-row align-items-center bg-white">
                <div class="rounded-circle bg-primary bg-opacity-10 d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark"><?= $totalDocuments ?></h4>
                    <span class="text-muted small">Documents</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styles pour la barre de recherche et les filtres */
        #rental-filters {
            border: 1px solid #E2E8F0 !important;
            transition: all 0.3s ease;
        }
        #rental-search {
            border: 1px solid #E2E8F0 !important;
            transition: all 0.2s ease-in-out;
            background-color: #FFFFFF;
        }
        #rental-search:focus {
            border-color: #3B82F6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
            outline: none;
        }
        /* Animation tactile et dynamique des boutons de filtre */
        .filter-btn {
            transition: all 0.2s ease-in-out;
            transform-origin: center;
        }
        .filter-btn:hover:not(.active) {
            background-color: #F8FAFC !important;
            color: #475569 !important;
            transform: translateY(-1px);
        }
        .filter-btn:active {
            transform: scale(0.92) !important;
        }
        @keyframes popEffect {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.03); }
            100% { transform: scale(1); }
        }
        .filter-btn.active {
            animation: popEffect 0.3s ease-out forwards;
        }
        /* Animation fluide pour les cartes */
        .equipement-item {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: top center;
        }
        .equipement-item.hide-item {
            opacity: 0;
            transform: scale(0.95);
            margin-bottom: -100px !important;
            pointer-events: none;
            z-index: -1;
            display: none !important;
        }
    </style>

    <!-- Barre de filtres et recherche -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="bg-white rounded-pill shadow-sm p-1 d-inline-flex" id="rental-filters">
            <button class="filter-btn btn btn-primary rounded-pill px-4 py-2 fw-medium border-0 active" data-filter="all" style="background-color: #0EA5E9; box-shadow: 0 2px 4px rgba(14,165,233,0.3);">Toutes</button>
            <button class="filter-btn btn btn-light rounded-pill px-4 py-2 fw-medium text-muted bg-transparent border-0" data-filter="En attente">En attente</button>
            <button class="filter-btn btn btn-light rounded-pill px-4 py-2 fw-medium text-muted bg-transparent border-0" data-filter="Confirmée">Confirmées</button>
            <button class="filter-btn btn btn-light rounded-pill px-4 py-2 fw-medium text-muted bg-transparent border-0" data-filter="Terminée">Terminées</button>
        </div>
        <div class="position-relative flex-grow-1 flex-md-grow-0" style="min-width: 300px; max-width: 400px;">
            <i class="bi bi-search position-absolute text-muted" style="left: 18px; top: 50%; transform: translateY(-50%);"></i>
            <input type="text" id="rental-search" class="form-control rounded-pill shadow-sm ps-5 py-2 text-dark" placeholder="Rechercher un équipement..." style="height: 45px; font-weight: 500;">
        </div>
    </div>

    <!-- Liste des réservations -->
    <?php if (empty($locations)): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <i class="bi bi-calendar-x display-1 text-muted"></i>
            <h4 class="mt-3 text-secondary">Aucune réservation pour le moment</h4>
            <p class="text-muted">Consultez notre catalogue pour réserver vos équipements en ligne.</p>
            <div>
                <a href="<?= BASE_URL ?>/index.php?action=catalogue" class="btn btn-primary rounded-pill px-4">Explorer le catalogue</a>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-3">
            <?php foreach ($locations as $loc): ?>
                <?php
                // Configuration des badges selon le statut
                $badgeStyle = '';
                $dotColor = '';
                $statutLabel = $loc['statut'];
                
                switch($loc['statut']) {
                    case 'En attente':
                        $badgeStyle = 'background-color: #FEF3C7; color: #D97706; border-color: #FDE68A;';
                        $dotColor = '#D97706';
                        break;
                    case 'Validée':
                    case 'En cours':
                        $badgeStyle = 'background-color: #E0F2FE; color: #0284C7; border-color: #BAE6FD;';
                        $dotColor = '#0284C7';
                        $statutLabel = 'Confirmée'; // Selon la maquette
                        break;
                    case 'Terminée':
                        $badgeStyle = 'background-color: #D1FAE5; color: #059669; border-color: #A7F3D0;';
                        $dotColor = '#059669';
                        break;
                    case 'Annulée':
                        $badgeStyle = 'background-color: #FEE2E2; color: #DC2626; border-color: #FECACA;';
                        $dotColor = '#DC2626';
                        break;
                    default:
                        $badgeStyle = 'background-color: #F3F4F6; color: #4B5563; border-color: #E5E7EB;';
                        $dotColor = '#4B5563';
                }
                
                // Préparation du texte pour la recherche
                $searchData = htmlspecialchars(strtolower($loc['nom_equipement'] . ' ' . $loc['nom_categorie'] . ' ' . $loc['id_location']));
                ?>
                <div class="card border-0 shadow-sm rounded-4 bg-white equipement-item mb-3" data-statut="<?= htmlspecialchars($statutLabel) ?>" data-search="<?= $searchData ?>">
                    <div class="card-body p-3">
                        <div class="row align-items-center g-3">
                            <!-- Info Équipement -->
                            <div class="col-12 col-xl-3 col-lg-4">
                                <div class="d-flex align-items-center">
                                    <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($loc['image']) ?>" 
                                         onerror="this.src='https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=60';"
                                         style="width: 70px; height: 70px; object-fit: cover;" class="rounded-4 me-3 border border-light">
                                    <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="text-muted fw-bold small me-2">#<?= $loc['id_location'] ?></span>
                                            <h6 class="fw-bold mb-0 text-dark text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($loc['nom_equipement']) ?>">
                                                <?= htmlspecialchars($loc['nom_equipement']) ?>
                                            </h6>
                                        </div>
                                        <div class="text-muted small">
                                            <?= htmlspecialchars($loc['nom_categorie']) ?> &middot; x<?= $loc['quantite'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="col-12 col-xl-2 col-lg-3 col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar4-week text-primary me-2 fs-5"></i>
                                    <div>
                                        <div class="text-muted small mb-1">
                                            <?= date('d/m/Y', strtotime($loc['date_debut'])) ?> &rarr; <?= date('d/m/Y', strtotime($loc['date_fin'])) ?>
                                        </div>
                                        <div class="text-primary fw-medium small bg-primary bg-opacity-10 rounded px-2 d-inline-block">
                                            <?= $loc['duree_jours'] ?> jour(s)
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Prix -->
                            <div class="col-6 col-xl-1 col-lg-2 col-md-3">
                                <div class="d-flex flex-column">
                                    <h5 class="fw-bold text-dark mb-0"><?= number_format($loc['montant_total'] + $loc['frais_supplementaires'], 2) ?></h5>
                                    <span class="fw-bold text-dark small">DT</span>
                                </div>
                            </div>

                            <!-- Statut -->
                            <div class="col-6 col-xl-2 col-lg-3 col-md-3 text-center">
                                <span class="badge rounded-pill px-3 py-2 fw-bold border w-100 d-inline-flex align-items-center justify-content-center text-truncate" style="<?= $badgeStyle ?>">
                                    <span class="d-inline-block rounded-circle me-2 flex-shrink-0" style="width: 8px; height: 8px; background-color: <?= $dotColor ?>;"></span>
                                    <?= $statutLabel ?>
                                </span>
                            </div>

                            <!-- Actions (PDFs) -->
                            <div class="col-12 col-xl-4 col-lg-12 d-flex gap-2 flex-wrap justify-content-xl-end justify-content-lg-start">
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_contrat&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm bg-white border rounded-pill text-muted fw-medium d-flex align-items-center px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                    <i class="bi bi-file-earmark-text me-1"></i> Contrat <span class="ms-1 text-black-50" style="font-size: 0.7rem;">PDF</span>
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_facture&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm bg-white border rounded-pill text-muted fw-medium d-flex align-items-center px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                    <i class="bi bi-receipt me-1"></i> Facture <span class="ms-1 text-black-50" style="font-size: 0.7rem;">PDF</span>
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_recu&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm bg-white border rounded-pill text-muted fw-medium d-flex align-items-center px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                    <i class="bi bi-currency-dollar me-1"></i> Reçu <span class="ms-1 text-black-50" style="font-size: 0.7rem;">PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const searchInput = document.getElementById('rental-search');
    const items = document.querySelectorAll('.equipement-item');

    let currentFilter = 'all';

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();

        items.forEach(item => {
            const status = item.getAttribute('data-statut');
            const searchData = item.getAttribute('data-search');

            const matchStatus = (currentFilter === 'all' || status === currentFilter);
            const matchSearch = (query === '' || searchData.includes(query));

            if (matchStatus && matchSearch) {
                item.classList.remove('hide-item');
            } else {
                item.classList.add('hide-item');
            }
        });
    }

    // Filtrage par boutons de statut
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Retirer le style actif de tous les boutons
            filterBtns.forEach(b => {
                b.classList.remove('btn-primary', 'active');
                b.classList.add('btn-light', 'text-muted', 'bg-transparent');
                b.style.cssText = '';
            });

            // Ajouter le style actif au bouton cliqué
            btn.classList.remove('btn-light', 'text-muted', 'bg-transparent');
            btn.classList.add('btn-primary', 'active');
            btn.style.cssText = 'background-color: #0EA5E9; box-shadow: 0 2px 4px rgba(14,165,233,0.3);';

            currentFilter = btn.getAttribute('data-filter');
            applyFilters();
        });
    });

    // Recherche textuelle (Instantanée 0ms delay)
    searchInput.addEventListener('input', applyFilters);
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
