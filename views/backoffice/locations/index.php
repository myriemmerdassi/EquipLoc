<?php
$pageTitle = "Suivi des Locations & Comptoir — EquipLoc";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';

// Calcul des KPI statistiques
$totalCount = count($locations);
$pendingCount = count(array_filter($locations, fn($l) => $l['statut'] === 'En attente'));
$activeCount = count(array_filter($locations, fn($l) => in_array($l['statut'], ['Validée', 'En cours'])));
$completedCount = count(array_filter($locations, fn($l) => $l['statut'] === 'Terminée'));
$totalRevenue = array_reduce($locations, fn($carry, $l) => $carry + (float)($l['montant_total'] ?? 0) + (float)($l['frais_supplementaires'] ?? 0), 0.0);
?>

<div class="cat-page-wrapper py-5 animate-rise">
    <div class="container-fluid px-lg-5 px-3">
        <!-- 1. En-tête : Badge Back-office, Titre avec "Locations" en bleu et Boutons d'action -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-4 mb-4">
            <div>
                <div class="cat-dash-badge">BACK-OFFICE</div>
                <h1 class="cat-main-title">
                    Suivi des <span class="cat-accent-word">Locations</span>
                </h1>
                <p class="cat-subtitle mb-0">
                    Supervision dynamique des réservations, validation comptoir et gestion des retours sans tableaux rigides.
                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap pt-md-2">
                <button type="button" id="btnExportCsv" class="btn btn-outline-light d-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-pill shadow-sm" style="border-color: rgba(255,255,255,0.2); font-size: 0.88rem;">
                    <i class="bi bi-download"></i>
                    <span>Exporter CSV</span>
                </button>
                <a href="<?= BASE_URL ?>/index.php?action=location_comptoir" class="btn btn-primary d-flex align-items-center gap-2 fw-bold px-4 py-2 rounded-pill shadow-sm">
                    <i class="bi bi-plus-lg"></i>
                    <span>Créer une location</span>
                </a>
            </div>
        </div>

        <!-- 2. Bandeau KPI segmenté en 4 colonnes -->
        <div class="cat-kpi-box" style="grid-template-columns: repeat(4, 1fr);">
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum"><?= sprintf('%02d', $totalCount) ?></div>
                <div class="cat-kpi-tag">LOCATIONS TOTALES</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum <?= $pendingCount > 0 ? 'text-warning' : 'text-white' ?> d-flex align-items-center gap-2">
                    <span><?= sprintf('%02d', $pendingCount) ?></span>
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge bg-warning text-dark rounded-pill px-2 py-0" style="font-size: 0.65rem;">Prioritaire</span>
                    <?php endif; ?>
                </div>
                <div class="cat-kpi-tag">EN ATTENTE DE VALIDATION</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-info"><?= sprintf('%02d', $activeCount) ?></div>
                <div class="cat-kpi-tag">EN COURS</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-success" style="font-size: 2rem;">
                    <?= number_format($totalRevenue, 2, ',', ' ') ?> <small style="font-size: 0.9rem;">DT</small>
                </div>
                <div class="cat-kpi-tag">CHIFFRE D'AFFAIRES</div>
            </div>
        </div>

        <!-- 3. Barre de commande & filtres par statut + recherche + commutateur de vue -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <!-- Filtres par statut (chips) -->
            <div class="d-flex align-items-center gap-2 flex-wrap" id="statusFilterGroup">
                <button type="button" class="filter-status-btn stock-state-chip-dark active" data-status="all">
                    Toutes (<?= $totalCount ?>)
                </button>
                <button type="button" class="filter-status-btn stock-state-chip-dark" data-status="En attente">
                    En attente (<?= $pendingCount ?>)
                </button>
                <button type="button" class="filter-status-btn stock-state-chip-dark" data-status="Validée">
                    En cours (<?= $activeCount ?>)
                </button>
                <button type="button" class="filter-status-btn stock-state-chip-dark" data-status="Terminée">
                    Terminées (<?= $completedCount ?>)
                </button>
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Recherche en direct -->
                <div class="position-relative" style="min-width: 260px; max-width: 320px;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" id="rentalSearchInput" class="cat-search-input-dark" placeholder="Rechercher client, matériel, ID..." autocomplete="off">
                </div>

                <!-- Commutateur Grille de Cartes / Rangées Modernes -->
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="cat-view-btn active" id="btnViewGrid" title="Vue en grille de cartes">
                        <i class="bi bi-grid-fill"></i>
                    </button>
                    <button type="button" class="cat-view-btn" id="btnViewRows" title="Vue en rangées aérées">
                        <i class="bi bi-view-stacked"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4. VUE 1 : Grille de Cartes Visuelles (Par défaut) -->
        <div class="rentals-cards-grid" id="rentalsGridView">
            <?php foreach ($locations as $loc): 
                $nomClient = trim($loc['client_nom'] . ' ' . $loc['client_prenom']);
                $initials = strtoupper(mb_substr($loc['client_prenom'] ?? '', 0, 1) . mb_substr($loc['client_nom'] ?? '', 0, 1)) ?: 'CL';
                $status = $loc['statut'];
                $totalAmount = (float)$loc['montant_total'] + (float)$loc['frais_supplementaires'];
                $locCode = sprintf('%02d', $loc['id_location']);
                
                $etatPill = match($status) {
                    'En attente' => 'status-pill--en-attente',
                    'Validée', 'En cours' => 'status-pill--validee',
                    'Terminée' => 'status-pill--terminee',
                    default => 'status-pill--annulee'
                };
            ?>
                <div class="rental-card-wrapper rental-item" 
                     data-id="<?= $loc['id_location'] ?>"
                     data-status="<?= htmlspecialchars($status) ?>"
                     data-search="<?= htmlspecialchars(strtolower($loc['id_location'] . ' ' . $nomClient . ' ' . $loc['client_email'] . ' ' . ($loc['client_telephone'] ?? '') . ' ' . $loc['nom_equipement'] . ' ' . $loc['nom_categorie'] . ' ' . $locCode)) ?>"
                     data-amount="<?= $totalAmount ?>"
                     data-client="<?= htmlspecialchars($nomClient) ?>"
                     data-equipement="<?= htmlspecialchars($loc['nom_equipement']) ?>">
                    
                    <div class="cat-luminous-card h-100 d-flex flex-column">
                        <!-- En-tête de carte -->
                        <div class="cat-card-header-row mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="cat-code-badge">#<?= $locCode ?></span>
                                <span class="badge rounded-pill" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-size: 0.72rem;">
                                    <?= htmlspecialchars($loc['nom_categorie']) ?>
                                </span>
                            </div>
                            <span class="status-pill <?= $etatPill ?>">
                                <span class="dot"></span>
                                <span><?= htmlspecialchars($status) ?></span>
                            </span>
                        </div>

                        <!-- Équipement & Quantité -->
                        <h4 class="cat-title-text mb-1 text-truncate" title="<?= htmlspecialchars($loc['nom_equipement']) ?>">
                            <?= htmlspecialchars($loc['nom_equipement']) ?>
                        </h4>
                        <div class="text-muted small mb-3">
                            <i class="bi bi-box-seam me-1 text-primary"></i> Quantité réservée : <strong><?= $loc['quantite'] ?> unité(s)</strong>
                        </div>

                        <!-- Section Client -->
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="cat-tile-icon" style="width: 38px; height: 38px; font-size: 0.85rem; font-weight: 700;">
                                <?= htmlspecialchars($initials) ?>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <strong class="text-dark d-block text-truncate" style="font-size: 0.9rem;"><?= htmlspecialchars($nomClient) ?></strong>
                                <small class="text-muted d-block text-truncate"><?= htmlspecialchars($loc['client_email']) ?></small>
                            </div>
                        </div>

                        <!-- Période & Montant -->
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <span class="text-muted small d-block" style="font-size: 0.72rem;">Période (<?= $loc['duree_jours'] ?> j)</span>
                                <span class="text-dark small fw-semibold">
                                    <?= date('d/m', strtotime($loc['date_debut'])) ?> ➔ <?= date('d/m/Y', strtotime($loc['date_fin'])) ?>
                                </span>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small d-block" style="font-size: 0.72rem;">Montant total</span>
                                <div class="cat-title-text text-primary mb-0" style="font-size: 1.25rem;">
                                    <?= number_format($totalAmount, 2, ',', ' ') ?> <small style="font-size: 0.72rem; color: #8fa0b5;">DT</small>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'actions et PDFs -->
                        <div class="mt-auto pt-3 border-top d-flex flex-column gap-2" style="border-color: #f1f5f9 !important;">
                            <!-- Actions directes de gestion -->
                            <?php if ($status === 'En attente'): ?>
                                <div class="d-flex gap-2">
                                    <a href="<?= BASE_URL ?>/index.php?action=location_status&id=<?= $loc['id_location'] ?>&statut=Validée" 
                                       class="btn btn-sm btn-success fw-bold rounded-pill w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                        <i class="bi bi-check-circle-fill"></i> Valider
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=location_status&id=<?= $loc['id_location'] ?>&statut=Annulée" 
                                       class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2" 
                                       onclick="return confirm('Confirmer l\'annulation ?');"
                                       title="Annuler">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                </div>
                            <?php elseif (in_array($status, ['Validée', 'En cours'])): ?>
                                <a href="<?= BASE_URL ?>/index.php?action=location_retour&id=<?= $loc['id_location'] ?>" 
                                   class="btn btn-sm btn-warning text-dark fw-bold rounded-pill w-100 py-2 shadow-sm d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-box-arrow-in-left"></i> Diagnostic retour
                                </a>
                            <?php endif; ?>

                            <!-- Trio PDF -->
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_contrat&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 flex-grow-1 text-center" style="font-size: 0.72rem;">
                                    <i class="bi bi-file-earmark-text"></i> Contrat
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_facture&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 flex-grow-1 text-center" style="font-size: 0.72rem;">
                                    <i class="bi bi-receipt"></i> Facture
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_recu&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 flex-grow-1 text-center" style="font-size: 0.72rem;">
                                    <i class="bi bi-check2-square"></i> Reçu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- 5. VUE 2 : Rangées de Fiches Aérées (Commutable) -->
        <div class="d-none" id="rentalsRowsView">
            <?php foreach ($locations as $loc): 
                $nomClient = trim($loc['client_nom'] . ' ' . $loc['client_prenom']);
                $initials = strtoupper(mb_substr($loc['client_prenom'] ?? '', 0, 1) . mb_substr($loc['client_nom'] ?? '', 0, 1)) ?: 'CL';
                $status = $loc['statut'];
                $totalAmount = (float)$loc['montant_total'] + (float)$loc['frais_supplementaires'];
                $locCode = sprintf('%02d', $loc['id_location']);
                
                $etatPill = match($status) {
                    'En attente' => 'status-pill--en-attente',
                    'Validée', 'En cours' => 'status-pill--validee',
                    'Terminée' => 'status-pill--terminee',
                    default => 'status-pill--annulee'
                };
            ?>
                <div class="cat-row-card rental-row-item"
                     data-status="<?= htmlspecialchars($status) ?>"
                     data-search="<?= htmlspecialchars(strtolower($loc['id_location'] . ' ' . $nomClient . ' ' . $loc['client_email'] . ' ' . ($loc['client_telephone'] ?? '') . ' ' . $loc['nom_equipement'] . ' ' . $loc['nom_categorie'] . ' ' . $locCode)) ?>"
                     data-amount="<?= $totalAmount ?>">
                    <div class="row align-items-center g-3">
                        <!-- ID + Client -->
                        <div class="col-12 col-xl-3 col-lg-4">
                            <div class="d-flex align-items-center gap-3">
                                <span class="cat-code-badge">#<?= $locCode ?></span>
                                <div class="cat-tile-icon" style="width: 36px; height: 36px; font-size: 0.82rem; font-weight: 700;">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                                <div class="overflow-hidden">
                                    <strong class="text-dark d-block text-truncate" style="font-size: 0.92rem;"><?= htmlspecialchars($nomClient) ?></strong>
                                    <small class="text-muted d-block text-truncate"><?= htmlspecialchars($loc['client_email']) ?></small>
                                </div>
                            </div>
                        </div>

                        <!-- Matériel -->
                        <div class="col-12 col-xl-3 col-lg-3">
                            <strong class="text-dark d-block text-truncate"><?= htmlspecialchars($loc['nom_equipement']) ?></strong>
                            <small class="text-muted">
                                <?= htmlspecialchars($loc['nom_categorie']) ?> &middot; Qté : <span class="text-primary fw-bold"><?= $loc['quantite'] ?></span>
                            </small>
                        </div>

                        <!-- Dates & Montant -->
                        <div class="col-6 col-xl-2 col-lg-2">
                            <div class="text-dark small fw-semibold">
                                <?= date('d/m', strtotime($loc['date_debut'])) ?> ➔ <?= date('d/m/Y', strtotime($loc['date_fin'])) ?>
                            </div>
                            <div class="cat-title-text text-primary mb-0" style="font-size: 1.1rem;">
                                <?= number_format($totalAmount, 2, ',', ' ') ?> DT
                            </div>
                        </div>

                        <!-- Statut -->
                        <div class="col-6 col-xl-1 col-lg-1 text-center">
                            <span class="status-pill <?= $etatPill ?>">
                                <span class="dot"></span>
                                <span><?= htmlspecialchars($status) ?></span>
                            </span>
                        </div>

                        <!-- Actions & PDFs -->
                        <div class="col-12 col-xl-3 col-lg-12 d-flex align-items-center justify-content-xl-end gap-2 flex-wrap">
                            <?php if ($status === 'En attente'): ?>
                                <a href="<?= BASE_URL ?>/index.php?action=location_status&id=<?= $loc['id_location'] ?>&statut=Validée" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold">
                                    Valider
                                </a>
                            <?php elseif (in_array($status, ['Validée', 'En cours'])): ?>
                                <a href="<?= BASE_URL ?>/index.php?action=location_retour&id=<?= $loc['id_location'] ?>" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 py-1">
                                    Retour
                                </a>
                            <?php endif; ?>

                            <div class="btn-group btn-group-sm">
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_contrat&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                    Contrat
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_facture&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                    Facture
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?action=pdf_recu&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-outline-secondary rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                    Reçu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- État vide si la recherche ne donne rien -->
        <div id="rentalsEmptyState" class="text-center py-5 cat-luminous-card d-none">
            <i class="bi bi-search fs-1 d-block mb-3 text-muted"></i>
            <h5 class="fw-bold text-white">Aucune location trouvée</h5>
            <p class="small text-muted mb-0">Essayez de modifier votre recherche ou de changer de filtre de statut.</p>
        </div>
    </div>
</div>

<!-- Script interactif de filtrage, recherche, commutation de vue et export CSV -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.filter-status-btn');
    const searchInput = document.getElementById('rentalSearchInput');
    const gridItems = document.querySelectorAll('.rental-item');
    const rowItems = document.querySelectorAll('.rental-row-item');
    const emptyState = document.getElementById('rentalsEmptyState');
    const exportBtn = document.getElementById('btnExportCsv');

    const btnGrid = document.getElementById('btnViewGrid');
    const btnRows = document.getElementById('btnViewRows');
    const gridView = document.getElementById('rentalsGridView');
    const rowsView = document.getElementById('rentalsRowsView');

    let currentStatusFilter = 'all';
    let currentSearchTerm = '';

    // Commutateur Grille / Rangées
    if (btnGrid && btnRows) {
        btnGrid.addEventListener('click', () => {
            btnGrid.classList.add('active');
            btnRows.classList.remove('active');
            gridView.classList.remove('d-none');
            rowsView.classList.add('d-none');
        });

        btnRows.addEventListener('click', () => {
            btnRows.classList.add('active');
            btnGrid.classList.remove('active');
            rowsView.classList.remove('d-none');
            gridView.classList.add('d-none');
        });
    }

    function applyFilters() {
        let visibleCount = 0;

        gridItems.forEach(item => {
            const rowStatus = item.getAttribute('data-status');
            const rowSearch = item.getAttribute('data-search') || '';

            let matchesStatus = false;
            if (currentStatusFilter === 'all') {
                matchesStatus = true;
            } else if (currentStatusFilter === 'Validée') {
                matchesStatus = (rowStatus === 'Validée' || rowStatus === 'En cours');
            } else {
                matchesStatus = (rowStatus === currentStatusFilter);
            }

            const matchesSearch = !currentSearchTerm || rowSearch.includes(currentSearchTerm);

            if (matchesStatus && matchesSearch) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        rowItems.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowSearch = row.getAttribute('data-search') || '';

            let matchesStatus = false;
            if (currentStatusFilter === 'all') {
                matchesStatus = true;
            } else if (currentStatusFilter === 'Validée') {
                matchesStatus = (rowStatus === 'Validée' || rowStatus === 'En cours');
            } else {
                matchesStatus = (rowStatus === currentStatusFilter);
            }

            const matchesSearch = !currentSearchTerm || rowSearch.includes(currentSearchTerm);

            if (matchesStatus && matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        if (visibleCount === 0 && gridItems.length > 0) {
            emptyState.classList.remove('d-none');
        } else {
            emptyState.classList.add('d-none');
        }
    }

    filterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentStatusFilter = btn.getAttribute('data-status');
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearchTerm = e.target.value.trim().toLowerCase();
            applyFilters();
        });
    }

    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            let csv = [];
            csv.push(['ID', 'Client', 'Équipement', 'Statut', 'Montant (DT)'].join(';'));

            gridItems.forEach(item => {
                if (item.style.display !== 'none') {
                    const id = item.getAttribute('data-id') || '';
                    const client = item.getAttribute('data-client') || '';
                    const equipement = item.getAttribute('data-equipement') || '';
                    const status = item.getAttribute('data-status') || '';
                    const amount = item.getAttribute('data-amount') || '0';

                    csv.push([`"${id}"`, `"${client}"`, `"${equipement}"`, `"${status}"`, `"${amount}"`].join(';'));
                }
            });

            const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + encodeURIComponent(csv.join('\n'));
            const downloadAnchor = document.createElement('a');
            downloadAnchor.setAttribute('href', csvContent);
            downloadAnchor.setAttribute('download', `suivi_locations_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(downloadAnchor);
            downloadAnchor.click();
            document.body.removeChild(downloadAnchor);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
