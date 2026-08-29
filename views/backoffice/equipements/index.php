<?php
$pageTitle = "Inventaire des équipements — EquipLoc";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';

// Calcul des KPI statistiques
$totalReferences = count($equipements);
$totalUnits = array_sum(array_column($equipements, 'stock'));
$lowStockCount = count(array_filter($equipements, fn($e) => (int)$e['stock'] <= (int)$e['seuil_alerte']));
$maintenanceCount = count(array_filter($equipements, fn($e) => in_array($e['etat'], ['En maintenance', 'Endommagé'])));

// Comptage par catégorie
$categoryCounts = [];
foreach ($equipements as $eq) {
    $catId = $eq['id_categorie'];
    $categoryCounts[$catId] = ($categoryCounts[$catId] ?? 0) + 1;
}

// Palette harmonieuse de bleus pour les jauges et badges
$blueTones = [
    '#0091ff',
    '#0077d4',
    '#0ea5e9',
    '#38bdf8',
    '#2563eb',
    '#6366f1'
];
?>

<div class="cat-page-wrapper py-5 animate-rise">
    <div class="container-fluid px-lg-5 px-3">
        <!-- 1. En-tête : Badge Back-office, Titre avec "Équipements" en bleu et Bouton d'action -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-4 mb-4">
            <div>
                <div class="cat-dash-badge">BACK-OFFICE</div>
                <h1 class="cat-main-title">
                    Inventaire des <span class="cat-accent-word">Équipements</span>
                </h1>
                <p class="cat-subtitle mb-0">
                    Gérez les stocks, définissez les seuils d'alerte et modifiez les états du matériel en temps réel.
                </p>
            </div>
            
            <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                <div class="flex-shrink-0 pt-md-2">
                    <a href="<?= BASE_URL ?>/index.php?action=equipement_create" class="btn btn-primary fw-bold rounded-pill px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>Ajouter un équipement</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. Bandeau KPI segmenté en 4 colonnes -->
        <div class="cat-kpi-box" style="grid-template-columns: repeat(4, 1fr);">
            <!-- Colonne 1 : Références -->
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum"><?= sprintf('%02d', $totalReferences) ?></div>
                <div class="cat-kpi-tag">RÉFÉRENCES</div>
            </div>

            <!-- Colonne 2 : Unités en stock -->
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-info"><?= sprintf('%02d', $totalUnits) ?></div>
                <div class="cat-kpi-tag">UNITÉS EN STOCK</div>
            </div>

            <!-- Colonne 3 : Sous le seuil -->
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum <?= $lowStockCount > 0 ? 'text-danger' : 'text-success' ?> d-flex align-items-center gap-2">
                    <span><?= sprintf('%02d', $lowStockCount) ?></span>
                    <?php if ($lowStockCount > 0): ?>
                        <span class="badge bg-danger text-white rounded-pill px-2 py-0" style="font-size: 0.65rem;">Alerte</span>
                    <?php endif; ?>
                </div>
                <div class="cat-kpi-tag">SOUS LE SEUIL</div>
            </div>

            <!-- Colonne 4 : En maintenance -->
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-warning"><?= sprintf('%02d', $maintenanceCount) ?></div>
                <div class="cat-kpi-tag">EN MAINTENANCE</div>
            </div>
        </div>

        <!-- 3. Disposition 2 colonnes (Sidebar gauche + Grille droite) -->
        <div class="row g-4 mt-1">
            <!-- a) Barre latérale de filtres (gauche - sticky) -->
            <div class="col-12 col-lg-3 col-xl-3">
                <aside class="stock-sidebar-luminous" id="stockSidebar">
                    <!-- Champ de recherche -->
                    <div class="mb-4">
                        <label class="cat-kpi-tag mb-2 d-block">Recherche</label>
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" id="stockSearchInput" class="cat-search-input-dark" placeholder="Nom, mot-clé, ID..." style="font-size: 0.88rem;">
                        </div>
                    </div>

                    <!-- Filtre par Catégories -->
                    <div class="mb-4">
                        <div class="cat-kpi-tag mb-2 d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-tags text-primary"></i>
                            <span>Catégories</span>
                        </div>
                        <div class="d-flex flex-column gap-1" id="categoryFilterList">
                            <button type="button" class="stock-cat-item-dark active" data-cat="all">
                                <span>Toutes les catégories</span>
                                <span class="stock-cat-badge-dark"><?= $totalReferences ?></span>
                            </button>
                            <?php foreach ($categories as $cat): 
                                $count = $categoryCounts[$cat['id_categorie']] ?? 0;
                            ?>
                                <button type="button" class="stock-cat-item-dark" data-cat="<?= $cat['id_categorie'] ?>">
                                    <span><?= htmlspecialchars($cat['nom_categorie']) ?></span>
                                    <span class="stock-cat-badge-dark"><?= $count ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Filtre par État -->
                    <div class="mb-4">
                        <div class="cat-kpi-tag mb-2 d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-activity text-info"></i>
                            <span>État matériel</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2" id="stateChipGroup">
                            <button type="button" class="stock-state-chip-dark active" data-state="all">Tous</button>
                            <button type="button" class="stock-state-chip-dark" data-state="Disponible">Disponible</button>
                            <button type="button" class="stock-state-chip-dark" data-state="En location">En location</button>
                            <button type="button" class="stock-state-chip-dark" data-state="En maintenance">En maintenance</button>
                        </div>
                    </div>

                    <!-- Bascule d'alerte « Stock sous le seuil » -->
                    <div class="stock-alert-toggle-dark" id="alertToggleContainer">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-exclamation text-danger fs-5"></i>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.82rem;">Stock sous le seuil</div>
                                <small class="text-muted" style="font-size: 0.72rem;">Alertes réappro</small>
                            </div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="toggleLowStockOnly" style="cursor: pointer; width: 2.2em; height: 1.2em;">
                        </div>
                    </div>

                    <!-- Bouton de réinitialisation -->
                    <button type="button" id="resetFiltersBtn" class="btn btn-sm btn-outline-secondary w-100 mt-4 rounded-pill py-2 d-none" style="font-size: 0.82rem;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser les filtres
                    </button>
                </aside>
            </div>

            <!-- b) Grille de fiches équipement (droite) -->
            <div class="col-12 col-lg-9 col-xl-9">
                <!-- Barre d'outils droite : Compteur & Commutateur de vue -->
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="fw-bold text-dark mb-0" style="font-family: var(--font-display);">Équipements répertoriés</h5>
                        <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: rgba(0, 145, 255, 0.12); color: #0077d4; border: 1px solid rgba(0, 145, 255, 0.25);" id="matchingCountBadge">
                            <?= $totalReferences ?> fiche(s)
                        </span>
                    </div>

                    <!-- Commutateur Grille / Liste -->
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="cat-view-btn active" id="btnStockViewGrid" title="Vue en grille">
                            <i class="bi bi-grid-fill"></i>
                        </button>
                        <button type="button" class="cat-view-btn" id="btnStockViewList" title="Vue en liste">
                            <i class="bi bi-list-ul"></i>
                        </button>
                    </div>
                </div>

                <!-- VUE 1 : Grille des fiches -->
                <div class="cat-cards-grid" id="stockGridView">
                    <?php foreach ($equipements as $idx => $eq): 
                        $isLowStock = ((int)$eq['stock'] <= (int)$eq['seuil_alerte']);
                        $catNomLower = mb_strtolower($eq['nom_categorie'] ?? '');
                        
                        // Icône selon la catégorie
                        if (str_contains($catNomLower, 'audio') || str_contains($catNomLower, 'cam') || str_contains($catNomLower, 'vid') || str_contains($catNomLower, 'photo')) {
                            $catIconClass = 'bi-camera-video';
                        } elseif (str_contains($catNomLower, 'info') || str_contains($catNomLower, 'pc') || str_contains($catNomLower, 'ordi') || str_contains($catNomLower, 'mac')) {
                            $catIconClass = 'bi-cpu';
                        } elseif (str_contains($catNomLower, 'sono') || str_contains($catNomLower, 'event') || str_contains($catNomLower, 'son') || str_contains($catNomLower, 'jbl')) {
                            $catIconClass = 'bi-speaker';
                        } else {
                            $catIconClass = 'bi-tools';
                        }

                        // Jauge de progression
                        $gaugeMax = max((int)$eq['stock'] + 5, (int)$eq['seuil_alerte'] * 2, 10);
                        $gaugePercent = min(100, max(0, round(((int)$eq['stock'] / $gaugeMax) * 100)));
                        
                        $gaugeColor = '#10b981';
                        if ((int)$eq['stock'] === 0) {
                            $gaugeColor = '#e5484d';
                            $gaugePercent = 5;
                        } elseif ($isLowStock) {
                            $gaugeColor = '#f59e0b';
                        }

                        // Badge d'état
                        $etatPill = match($eq['etat']) {
                            'Disponible' => 'status-pill--terminee',
                            'En location' => 'status-pill--validee',
                            'En maintenance', 'Endommagé' => 'status-pill--en-attente',
                            default => 'status-pill--annulee'
                        };
                        $eqCode = sprintf('%02d', $eq['id_equipement']);
                    ?>
                        <div class="stock-card-item" 
                             data-id="<?= $eq['id_equipement'] ?>"
                             data-cat="<?= $eq['id_categorie'] ?>"
                             data-state="<?= htmlspecialchars($eq['etat']) ?>"
                             data-lowstock="<?= $isLowStock ? '1' : '0' ?>"
                             data-search="<?= htmlspecialchars(mb_strtolower($eq['id_equipement'] . ' ' . $eq['nom_equipement'] . ' ' . ($eq['description'] ?? '') . ' ' . $eq['nom_categorie'] . ' ' . $eqCode)) ?>">
                            
                            <div class="cat-luminous-card <?= $isLowStock ? 'is-low-stock' : '' ?>">
                                <!-- En-tête de carte : Icône + #ID + Statut -->
                                <div class="cat-card-header-row">
                                    <div class="cat-tile-icon">
                                        <i class="bi <?= $catIconClass ?>"></i>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="cat-code-badge">#<?= $eqCode ?></span>
                                        <span class="status-pill <?= $etatPill ?>">
                                            <span class="dot"></span>
                                            <span><?= htmlspecialchars($eq['etat']) ?></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Titre & Description -->
                                <h4 class="cat-title-text"><?= htmlspecialchars($eq['nom_equipement']) ?></h4>
                                <p class="cat-desc-text"><?= htmlspecialchars($eq['description'] ?: 'Aucune description fournie pour cet équipement.') ?></p>
                                
                                <div class="mb-3">
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-size: 0.72rem;">
                                        <i class="bi bi-tag-fill me-1 text-primary"></i><?= htmlspecialchars($eq['nom_categorie']) ?>
                                    </span>
                                </div>

                                <!-- Jauge de stock visuelle -->
                                <div class="cat-card-gauge">
                                    <div class="cat-card-gauge-header">
                                        <span class="fw-bold <?= $isLowStock ? 'text-danger' : 'text-dark' ?>">
                                            <i class="bi <?= $isLowStock ? 'bi-exclamation-circle-fill text-danger' : 'bi-check-circle-fill text-success' ?> me-1"></i>
                                            <?= $eq['stock'] ?> unité(s)
                                        </span>
                                        <span class="text-muted small">Seuil : <strong><?= $eq['seuil_alerte'] ?></strong></span>
                                    </div>
                                    <div class="cat-card-gauge-track">
                                        <div class="cat-card-gauge-fill" style="width: <?= $gaugePercent ?>%; background: <?= $gaugeColor ?>;"></div>
                                    </div>
                                </div>

                                <!-- Pied de carte : Tarif & Actions -->
                                <div class="cat-card-bottom-actions">
                                    <div class="cat-price-value mb-0">
                                        <?= number_format($eq['prix_par_jour'], 2, ',', ' ') ?> <small style="font-size: 0.72rem; color: #8fa0b5;">DT/j</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                                            <a href="<?= BASE_URL ?>/index.php?action=equipement_edit&id=<?= $eq['id_equipement'] ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;" title="Modifier l'équipement">
                                                <i class="bi bi-pencil-fill me-1"></i> Modifier
                                            </a>
                                            <a href="<?= BASE_URL ?>/index.php?action=equipement_delete&id=<?= $eq['id_equipement'] ?>" 
                                               onclick="return confirm('Confirmer la suppression définitive de cet équipement ?');" 
                                               class="btn btn-sm btn-outline-danger rounded-circle px-2 py-1" 
                                               title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">Consultation</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Carte interactive "+ Ajouter un équipement" -->
                    <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                        <a href="<?= BASE_URL ?>/index.php?action=equipement_create" class="cat-create-dashed-card">
                            <div class="cat-plus-circle">
                                <i class="bi bi-plus-lg"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1" style="font-family: var(--font-display);">Nouveau Matériel</h5>
                            <p class="text-muted small mb-0">Enregistrer un équipement dans le stock</p>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- VUE 2 : Tableau Liste (Commutable) -->
                <div class="cat-table-box d-none" id="stockListView">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Nom du matériel</th>
                                    <th>Catégorie</th>
                                    <th>Tarif / jour</th>
                                    <th>Stock & Jauge</th>
                                    <th>État</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="stockTableBody">
                                <?php foreach ($equipements as $idx => $eq): 
                                    $isLowStock = ((int)$eq['stock'] <= (int)$eq['seuil_alerte']);
                                    $eqCode = sprintf('%02d', $eq['id_equipement']);
                                    $etatPill = match($eq['etat']) {
                                        'Disponible' => 'status-pill--terminee',
                                        'En location' => 'status-pill--validee',
                                        'En maintenance', 'Endommagé' => 'status-pill--en-attente',
                                        default => 'status-pill--annulee'
                                    };
                                ?>
                                    <tr class="stock-table-row" 
                                        data-cat="<?= $eq['id_categorie'] ?>"
                                        data-state="<?= htmlspecialchars($eq['etat']) ?>"
                                        data-lowstock="<?= $isLowStock ? '1' : '0' ?>"
                                        data-search="<?= htmlspecialchars(mb_strtolower($eq['id_equipement'] . ' ' . $eq['nom_equipement'] . ' ' . ($eq['description'] ?? '') . ' ' . $eq['nom_categorie'] . ' ' . $eqCode)) ?>">
                                        <td><span class="cat-code-badge">#<?= $eqCode ?></span></td>
                                        <td>
                                            <strong class="text-white"><?= htmlspecialchars($eq['nom_equipement']) ?></strong>
                                            <small class="d-block text-muted"><?= htmlspecialchars(mb_strimwidth($eq['description'] ?? '', 0, 45, '...')) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-2 py-1" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.08);">
                                                <?= htmlspecialchars($eq['nom_categorie']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold text-primary"><?= number_format($eq['prix_par_jour'], 2, ',', ' ') ?> DT</td>
                                        <td>
                                            <span class="fw-bold <?= $isLowStock ? 'text-danger' : 'text-success' ?>">
                                                <?= $eq['stock'] ?> unité(s)
                                            </span>
                                            <small class="d-block text-muted">Seuil : <?= $eq['seuil_alerte'] ?></small>
                                        </td>
                                        <td>
                                            <span class="status-pill <?= $etatPill ?>">
                                                <span class="dot"></span>
                                                <span><?= htmlspecialchars($eq['etat']) ?></span>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                                                <a href="<?= BASE_URL ?>/index.php?action=equipement_edit&id=<?= $eq['id_equipement'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 py-1 me-1">
                                                    <i class="bi bi-pencil-fill me-1"></i> Modifier
                                                </a>
                                                <a href="<?= BASE_URL ?>/index.php?action=equipement_delete&id=<?= $eq['id_equipement'] ?>" 
                                                   onclick="return confirm('Confirmer la suppression ?');" 
                                                   class="btn btn-outline-danger btn-sm rounded-circle px-2 py-1">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- État vide -->
                <div id="stockEmptyState" class="text-center py-5 cat-luminous-card mt-3 d-none">
                    <i class="bi bi-search fs-1 d-block mb-3 text-muted"></i>
                    <h5 class="fw-bold text-white">Aucun équipement correspondant</h5>
                    <p class="text-muted small mb-3">Aucun matériel ne correspond à vos critères de recherche ou filtres actifs.</p>
                    <button type="button" id="emptyResetBtn" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser les filtres
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script interactif de filtrage multicritère, recherche et commutation Grille/Liste -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('stockSearchInput');
    const categoryButtons = document.querySelectorAll('.stock-cat-item-dark');
    const stateChips = document.querySelectorAll('.stock-state-chip-dark');
    const lowStockToggle = document.getElementById('toggleLowStockOnly');
    const alertToggleContainer = document.getElementById('alertToggleContainer');
    const stockSidebar = document.getElementById('stockSidebar');
    const cardItems = document.querySelectorAll('.stock-card-item');
    const tableRows = document.querySelectorAll('.stock-table-row');
    const emptyState = document.getElementById('stockEmptyState');
    const matchingBadge = document.getElementById('matchingCountBadge');
    const resetBtn = document.getElementById('resetFiltersBtn');
    const emptyResetBtn = document.getElementById('emptyResetBtn');

    const btnGrid = document.getElementById('btnStockViewGrid');
    const btnList = document.getElementById('btnStockViewList');
    const gridView = document.getElementById('stockGridView');
    const listView = document.getElementById('stockListView');

    let currentCategory = 'all';
    let currentState = 'all';
    let currentSearch = '';
    let isLowStockOnly = false;

    // Commutateur Grille / Liste
    if (btnGrid && btnList) {
        btnGrid.addEventListener('click', () => {
            btnGrid.classList.add('active');
            btnList.classList.remove('active');
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
        });

        btnList.addEventListener('click', () => {
            btnList.classList.add('active');
            btnGrid.classList.remove('active');
            listView.classList.remove('d-none');
            gridView.classList.add('d-none');
        });
    }

    function applyFilters() {
        let visibleCount = 0;

        cardItems.forEach(item => {
            const itemCat = item.getAttribute('data-cat');
            const itemState = item.getAttribute('data-state');
            const itemLowStock = item.getAttribute('data-lowstock') === '1';
            const itemSearch = item.getAttribute('data-search') || '';

            const matchesCat = (currentCategory === 'all' || itemCat === currentCategory);
            const matchesState = (currentState === 'all' || itemState === currentState);
            const matchesLowStock = (!isLowStockOnly || itemLowStock);
            const matchesSearch = (!currentSearch || itemSearch.includes(currentSearch));

            if (matchesCat && matchesState && matchesLowStock && matchesSearch) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        tableRows.forEach(row => {
            const itemCat = row.getAttribute('data-cat');
            const itemState = row.getAttribute('data-state');
            const itemLowStock = row.getAttribute('data-lowstock') === '1';
            const itemSearch = row.getAttribute('data-search') || '';

            const matchesCat = (currentCategory === 'all' || itemCat === currentCategory);
            const matchesState = (currentState === 'all' || itemState === currentState);
            const matchesLowStock = (!isLowStockOnly || itemLowStock);
            const matchesSearch = (!currentSearch || itemSearch.includes(currentSearch));

            if (matchesCat && matchesState && matchesLowStock && matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Affichage état vide
        if (visibleCount === 0 && cardItems.length > 0) {
            emptyState.classList.remove('d-none');
        } else {
            emptyState.classList.add('d-none');
        }

        // Compteur
        if (matchingBadge) {
            matchingBadge.textContent = `${visibleCount} fiche(s)`;
        }

        // Bouton reset
        const hasActiveFilters = (currentCategory !== 'all' || currentState !== 'all' || isLowStockOnly || currentSearch !== '');
        if (resetBtn) {
            if (hasActiveFilters) {
                resetBtn.classList.remove('d-none');
            } else {
                resetBtn.classList.add('d-none');
            }
        }
    }

    function resetAll() {
        currentCategory = 'all';
        currentState = 'all';
        currentSearch = '';
        isLowStockOnly = false;

        if (searchInput) searchInput.value = '';
        if (lowStockToggle) lowStockToggle.checked = false;

        categoryButtons.forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-cat') === 'all');
        });

        stateChips.forEach(chip => {
            chip.classList.toggle('active', chip.getAttribute('data-state') === 'all');
        });

        alertToggleContainer.classList.remove('active');
        stockSidebar.classList.remove('alert-active');

        applyFilters();
    }

    // Recherche instantanée
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearch = e.target.value.trim().toLowerCase();
            applyFilters();
        });
    }

    // Filtre Catégorie
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCategory = btn.getAttribute('data-cat');
            applyFilters();
        });
    });

    // Filtre État
    stateChips.forEach(chip => {
        chip.addEventListener('click', () => {
            stateChips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            currentState = chip.getAttribute('data-state');
            applyFilters();
        });
    });

    // Toggle Seuil Alerte
    if (lowStockToggle) {
        lowStockToggle.addEventListener('change', (e) => {
            isLowStockOnly = e.target.checked;
            alertToggleContainer.classList.toggle('active', isLowStockOnly);
            stockSidebar.classList.toggle('alert-active', isLowStockOnly);
            applyFilters();
        });
    }

    if (resetBtn) resetBtn.addEventListener('click', resetAll);
    if (emptyResetBtn) emptyResetBtn.addEventListener('click', resetAll);
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
