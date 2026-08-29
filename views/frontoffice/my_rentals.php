<?php
$pageTitle = "Mes Locations — EquipLoc";
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

<div class="cat-page-wrapper py-5 animate-rise">
    <div class="container-fluid px-lg-5 px-3">
        <!-- 1. En-tête : Badge Espace Client, Titre et Bouton d'action -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-4 mb-4">
            <div>
                <div class="cat-dash-badge">ESPACE CLIENT</div>
                <h1 class="cat-main-title">
                    Mes <span class="cat-accent-word">Locations</span>
                </h1>
                <p class="cat-subtitle mb-0">
                    Historique de vos réservations et téléchargement direct de vos documents officiels (contrat, facture, reçu).
                </p>
            </div>
            
            <div class="flex-shrink-0 pt-md-2">
                <a href="<?= BASE_URL ?>/index.php?action=catalogue" class="btn btn-primary fw-bold rounded-pill px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>Louer un équipement</span>
                </a>
            </div>
        </div>

        <!-- 2. Bandeau KPI segmenté en 4 colonnes -->
        <div class="cat-kpi-box" style="grid-template-columns: repeat(4, 1fr);">
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum"><?= sprintf('%02d', $totalReservations) ?></div>
                <div class="cat-kpi-tag">RÉSERVATIONS</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-info"><?= sprintf('%02d', $totalEnCours) ?></div>
                <div class="cat-kpi-tag">EN COURS</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-success" style="font-size: 2rem;">
                    <?= number_format($totalDepense, 2, ',', ' ') ?> <small style="font-size: 0.9rem;">DT</small>
                </div>
                <div class="cat-kpi-tag">TOTAL DÉPENSÉ</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-primary"><?= sprintf('%02d', $totalDocuments) ?></div>
                <div class="cat-kpi-tag">DOCUMENTS PDF</div>
            </div>
        </div>

        <!-- 3. Barre de filtres & recherche -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap" id="rental-filters">
                <button class="filter-btn stock-state-chip-dark active" data-filter="all">Toutes</button>
                <button class="filter-btn stock-state-chip-dark" data-filter="En attente">En attente</button>
                <button class="filter-btn stock-state-chip-dark" data-filter="Confirmée">Confirmées</button>
                <button class="filter-btn stock-state-chip-dark" data-filter="Terminée">Terminées</button>
            </div>
            
            <div class="position-relative" style="min-width: 280px; max-width: 380px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="rental-search" class="cat-search-input-dark" placeholder="Rechercher un équipement...">
            </div>
        </div>

        <!-- 4. Liste des cartes de réservations -->
        <?php if (empty($locations)): ?>
            <div class="cat-luminous-card text-center py-5">
                <i class="bi bi-calendar-x fs-1 d-block mb-3 text-muted"></i>
                <h4 class="text-white fw-bold">Aucune réservation pour le moment</h4>
                <p class="text-muted small mb-3">Consultez notre catalogue pour réserver vos équipements en ligne.</p>
                <div>
                    <a href="<?= BASE_URL ?>/index.php?action=catalogue" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">Explorer le catalogue</a>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($locations as $loc): 
                    $statutLabel = $loc['statut'];
                    if (in_array($loc['statut'], ['Validée', 'En cours'])) {
                        $statutLabel = 'Confirmée';
                    }

                    $etatPill = match($loc['statut']) {
                        'En attente' => 'status-pill--en-attente',
                        'Validée', 'En cours' => 'status-pill--validee',
                        'Terminée' => 'status-pill--terminee',
                        default => 'status-pill--annulee'
                    };
                    $searchData = htmlspecialchars(strtolower($loc['nom_equipement'] . ' ' . $loc['nom_categorie'] . ' ' . $loc['id_location']));
                    $locCode = sprintf('%02d', $loc['id_location']);
                ?>
                    <div class="cat-luminous-card equipement-item" data-statut="<?= htmlspecialchars($statutLabel) ?>" data-search="<?= $searchData ?>">
                        <div class="row align-items-center g-3">
                            <!-- Info Équipement -->
                            <div class="col-12 col-xl-4 col-lg-4">
                                <div class="d-flex align-items-center">
                                    <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($loc['image']) ?>" 
                                         onerror="this.src='https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=60';"
                                         style="width: 64px; height: 64px; object-fit: cover;" class="rounded-3 me-3 border border-secondary border-opacity-25">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="cat-code-badge">#<?= $locCode ?></span>
                                            <h5 class="cat-title-text mb-0 text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($loc['nom_equipement']) ?>">
                                                <?= htmlspecialchars($loc['nom_equipement']) ?>
                                            </h5>
                                        </div>
                                        <div class="text-muted small">
                                            <?= htmlspecialchars($loc['nom_categorie']) ?> &middot; x<?= $loc['quantite'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Période -->
                            <div class="col-12 col-xl-2 col-lg-3 col-md-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-event text-primary me-2 fs-5"></i>
                                    <div>
                                        <div class="text-white small fw-semibold mb-1">
                                            <?= date('d/m/Y', strtotime($loc['date_debut'])) ?> &rarr; <?= date('d/m/Y', strtotime($loc['date_fin'])) ?>
                                        </div>
                                        <span class="badge rounded-pill" style="background: rgba(0, 145, 255, 0.15); color: #38bdf8; font-size: 0.72rem;">
                                            <?= $loc['duree_jours'] ?> jour(s)
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Montant -->
                            <div class="col-6 col-xl-2 col-lg-2 col-md-3">
                                <div class="d-flex flex-column">
                                    <div class="cat-price-value mb-0">
                                        <?= number_format($loc['montant_total'] + $loc['frais_supplementaires'], 2, ',', ' ') ?> <small style="font-size: 0.72rem; color: #8fa0b5;">DT</small>
                                    </div>
                                    <span class="text-muted small">Montant total</span>
                                </div>
                            </div>

                            <!-- Statut -->
                            <div class="col-6 col-xl-1 col-lg-3 col-md-2 text-center">
                                <span class="status-pill <?= $etatPill ?>">
                                    <span class="dot"></span>
                                    <span><?= $statutLabel ?></span>
                                </span>
                            </div>

                            <!-- Actions (PDFs) -->
                            <div class="col-12 col-xl-3 col-lg-12 d-flex gap-2 flex-wrap justify-content-xl-end justify-content-lg-start">
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_contrat&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                                    <i class="bi bi-file-earmark-text me-1"></i> Contrat
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_facture&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                                    <i class="bi bi-receipt me-1"></i> Facture
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_recu&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                                    <i class="bi bi-check2-square me-1"></i> Reçu
                                </a>
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
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.getAttribute('data-filter');
            applyFilters();
        });
    });

    if (searchInput) searchInput.addEventListener('input', applyFilters);
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
