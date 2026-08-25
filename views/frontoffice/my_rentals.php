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
$totalDocuments = $totalReservations * 3;
?>

<!-- ══════════════════════════════════════════════════════
     HERO — Mes Locations
═══════════════════════════════════════════════════════ -->
<section class="rentals-hero" aria-label="Espace client mes locations">
    <!-- Halos décoratifs -->
    <span class="hero-halo hero-halo--left"  aria-hidden="true"></span>
    <span class="hero-halo hero-halo--right" aria-hidden="true"></span>

    <div class="container hero-content rentals-hero__inner">
        <!-- Partie gauche : badge + titre -->
        <div>
            <div class="hero-badge">
                <i class="bi bi-person-check-fill" aria-hidden="true"></i>
                Espace Client
            </div>
            <h1 class="hero-title" style="margin-bottom:16px;">
                Mes <span class="hero-title-accent">locations</span>
            </h1>
            <p class="hero-subtitle">
                Historique de vos réservations et téléchargement de vos documents officiels (contrat, facture, reçu).
            </p>
        </div>

        <!-- Bouton CTA -->
        <div class="rentals-hero__cta">
            <a href="<?= BASE_URL ?>/index.php?action=catalogue"
               class="btn rentals-cta-btn">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                Louer un équipement
            </a>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════════
     CARTES KPI — chevauchent le hero par en bas
═══════════════════════════════════════════════════════ -->
<div class="container rentals-kpi-wrapper">
    <div class="rentals-kpi-grid">

        <!-- Réservations -->
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon--blue">
                <i class="bi bi-boxes" aria-hidden="true"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-value"><?= $totalReservations ?></span>
                <span class="kpi-label">Réservations</span>
            </div>
        </div>

        <!-- En cours -->
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon--cyan">
                <i class="bi bi-clock-history" aria-hidden="true"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-value"><?= $totalEnCours ?></span>
                <span class="kpi-label">En cours</span>
            </div>
        </div>

        <!-- Total dépensé -->
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon--green">
                <i class="bi bi-wallet2" aria-hidden="true"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-value"><?= number_format($totalDepense, 2) ?> <small>DT</small></span>
                <span class="kpi-label">Total dépensé</span>
            </div>
        </div>

        <!-- Documents -->
        <div class="kpi-card">
            <div class="kpi-icon kpi-icon--purple">
                <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
            </div>
            <div class="kpi-body">
                <span class="kpi-value"><?= $totalDocuments ?></span>
                <span class="kpi-label">Documents</span>
            </div>
        </div>

    </div>
</div>
<!-- ══════════════════════════════════════════════════════
     FILTRES + LISTE DES LOCATIONS
═══════════════════════════════════════════════════════ -->
<div class="container rentals-content-section pb-5">

    <!-- Barre de filtres et recherche -->
    <div class="rentals-filter-bar">
        <div class="bg-white rounded-pill shadow-sm p-1 d-inline-flex" id="rental-filters">
            <button class="filter-btn btn btn-primary rounded-pill px-4 py-2 fw-medium border-0 active"
                    data-filter="all"
                    style="background-color: #0091ff; box-shadow: 0 2px 8px rgba(0,145,255,0.3);">
                Toutes
            </button>
            <button class="filter-btn btn btn-light rounded-pill px-4 py-2 fw-medium text-muted bg-transparent border-0" data-filter="En attente">En attente</button>
            <button class="filter-btn btn btn-light rounded-pill px-4 py-2 fw-medium text-muted bg-transparent border-0" data-filter="Confirmée">Confirmées</button>
            <button class="filter-btn btn btn-light rounded-pill px-4 py-2 fw-medium text-muted bg-transparent border-0" data-filter="Terminée">Terminées</button>
        </div>
        <div class="rentals-search-wrapper">
            <i class="bi bi-search rentals-search-icon" aria-hidden="true"></i>
            <input type="text" id="rental-search" class="rentals-search-input"
                   placeholder="Rechercher un équipement…">
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

</div><!-- /rentals-content-section -->

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
