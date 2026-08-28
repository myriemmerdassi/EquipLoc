<?php
$pageTitle = "Inventaire des équipements — EquipLoc";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';

// Calcul des KPI statistiques intégrés
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
?>

<!-- 4. L'en-tête de page : bandeau sombre avec KPI intégrés -->
<section class="stock-hero-banner animate-rise">
    <div class="container-fluid px-lg-5 px-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="hero-badge mb-2">
                    <i class="bi bi-tools"></i>
                    <span>Stock / Équipements</span>
                </div>
                <h1 class="h2 fw-bold text-white mb-1" style="font-family: var(--font-display);">Inventaire des équipements</h1>
                <p class="text-white-50 mb-0" style="font-size: 0.95rem;">Gérez les stocks, définissez les seuils d'alerte et modifiez les états du matériel en temps réel.</p>
            </div>
            
            <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                <div class="flex-shrink-0">
                    <a href="<?= BASE_URL ?>/index.php?action=equipement_create" class="btn btn-primary d-inline-flex align-items-center gap-2 fw-bold px-4 py-2 rounded-pill shadow">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Ajouter un équipement</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Grille de 4 indicateurs KPI collés au bandeau sombre -->
        <div class="stock-indicators-grid">
            <!-- 1. Références -->
            <div class="stock-indicator-item">
                <div class="stock-indicator-icon" style="background: rgba(0, 145, 255, 0.18); color: #38bdf8;">
                    <i class="bi bi-collection"></i>
                </div>
                <div>
                    <div class="stock-indicator-val" id="kpiTotalRefs"><?= $totalReferences ?></div>
                    <div class="stock-indicator-lbl">Références</div>
                </div>
            </div>

            <!-- 2. Unités en stock -->
            <div class="stock-indicator-item">
                <div class="stock-indicator-icon" style="background: rgba(16, 185, 129, 0.18); color: #34d399;">
                    <i class="bi bi-boxes"></i>
                </div>
                <div>
                    <div class="stock-indicator-val"><?= $totalUnits ?></div>
                    <div class="stock-indicator-lbl">Unités en stock</div>
                </div>
            </div>

            <!-- 3. Sous le seuil -->
            <div class="stock-indicator-item">
                <div class="stock-indicator-icon" style="background: rgba(239, 68, 68, 0.18); color: #f87171;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <div class="stock-indicator-val text-danger d-flex align-items-center gap-2">
                        <span><?= $lowStockCount ?></span>
                        <?php if ($lowStockCount > 0): ?>
                            <span class="badge bg-danger text-white rounded-pill px-2 py-0" style="font-size: 0.65rem;">Alerte</span>
                        <?php endif; ?>
                    </div>
                    <div class="stock-indicator-lbl">Sous le seuil</div>
                </div>
            </div>

            <!-- 4. En maintenance -->
            <div class="stock-indicator-item">
                <div class="stock-indicator-icon" style="background: rgba(245, 158, 11, 0.18); color: #fbbf24;">
                    <i class="bi bi-wrench-adjustable"></i>
                </div>
                <div>
                    <div class="stock-indicator-val text-warning"><?= $maintenanceCount ?></div>
                    <div class="stock-indicator-lbl">En maintenance</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. Le tableau de bord d'inventaire : layout 2 colonnes (Sidebar + Grille) -->
<div class="container-fluid px-lg-5 px-3 py-5">
    <div class="row g-4">
        <!-- a) Barre latérale de filtres (gauche - sticky) -->
        <div class="col-12 col-lg-3 col-xl-3">
            <aside class="stock-sidebar" id="stockSidebar">
                <!-- Champ de recherche -->
                <div class="mb-4">
                    <label class="filter-label mb-2">Recherche</label>
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="stockSearchInput" class="form-control rounded-pill ps-5 py-2" placeholder="Nom, mot-clé, ID..." style="font-size: 0.88rem;">
                    </div>
                </div>

                <!-- Filtre par Catégories -->
                <div class="mb-4">
                    <div class="stock-sidebar-title">
                        <i class="bi bi-tags text-primary"></i>
                        <span>Catégories</span>
                    </div>
                    <div class="stock-category-list" id="categoryFilterList">
                        <button type="button" class="stock-category-item active" data-cat="all">
                            <span>Toutes les catégories</span>
                            <span class="stock-category-badge"><?= $totalReferences ?></span>
                        </button>
                        <?php foreach ($categories as $cat): 
                            $count = $categoryCounts[$cat['id_categorie']] ?? 0;
                        ?>
                            <button type="button" class="stock-category-item" data-cat="<?= $cat['id_categorie'] ?>">
                                <span><?= htmlspecialchars($cat['nom_categorie']) ?></span>
                                <span class="stock-category-badge"><?= $count ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Filtre par État (chips arrondis) -->
                <div class="mb-4">
                    <div class="stock-sidebar-title">
                        <i class="bi bi-activity text-info"></i>
                        <span>État matériel</span>
                    </div>
                    <div class="stock-state-chips" id="stateChipGroup">
                        <button type="button" class="stock-state-chip active" data-state="all">Tous</button>
                        <button type="button" class="stock-state-chip" data-state="Disponible">Disponible</button>
                        <button type="button" class="stock-state-chip" data-state="En location">En location</button>
                        <button type="button" class="stock-state-chip" data-state="En maintenance">En maintenance</button>
                    </div>
                </div>

                <!-- Bascule d'alerte « Stock sous le seuil » -->
                <div class="stock-alert-toggle-box" id="alertToggleContainer">
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
                <button type="button" id="resetFiltersBtn" class="btn btn-sm btn-outline-secondary w-100 mt-4 rounded-pill py-2 d-none">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser les filtres
                </button>
            </aside>
        </div>

        <!-- b) Grille de fiches équipement (droite) -->
        <div class="col-12 col-lg-9 col-xl-9">
            <!-- Barre supérieure de résultats -->
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold text-dark mb-0" style="font-family: var(--font-display);">Équipements répertoriés</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold" id="matchingCountBadge">
                        <?= $totalReferences ?> fiche(s)
                    </span>
                </div>
            </div>

            <!-- Grille des fiches -->
            <div class="stock-grid" id="stockGrid">
                <?php foreach ($equipements as $eq): 
                    $isLowStock = ((int)$eq['stock'] <= (int)$eq['seuil_alerte']);
                    $catNomLower = mb_strtolower($eq['nom_categorie'] ?? '');
                    
                    // Icône et couleur selon la catégorie
                    if (str_contains($catNomLower, 'audio') || str_contains($catNomLower, 'cam') || str_contains($catNomLower, 'vid')) {
                        $catIconClass = 'bi-camera-video';
                        $catStyleClass = 'stock-cat-icon--audiovisuel';
                    } elseif (str_contains($catNomLower, 'info') || str_contains($catNomLower, 'pc') || str_contains($catNomLower, 'ordi')) {
                        $catIconClass = 'bi-cpu';
                        $catStyleClass = 'stock-cat-icon--informatique';
                    } elseif (str_contains($catNomLower, 'sono') || str_contains($catNomLower, 'event') || str_contains($catNomLower, 'son')) {
                        $catIconClass = 'bi-speaker';
                        $catStyleClass = 'stock-cat-icon--sonorisation';
                    } else {
                        $catIconClass = 'bi-tools';
                        $catStyleClass = 'stock-cat-icon--outillage';
                    }

                    // Calcul de la jauge de progression
                    $gaugeMax = max((int)$eq['stock'] + 5, (int)$eq['seuil_alerte'] * 2, 10);
                    $gaugePercent = min(100, max(0, round(((int)$eq['stock'] / $gaugeMax) * 100)));
                    
                    $barColorClass = '';
                    if ((int)$eq['stock'] === 0) {
                        $barColorClass = 'bar-danger';
                        $gaugePercent = 5;
                    } elseif ($isLowStock) {
                        $barColorClass = 'bar-warning';
                    }

                    // Badge d'état
                    $etatStatusPill = match($eq['etat']) {
                        'Disponible' => 'status-pill--terminee',
                        'En location' => 'status-pill--validee',
                        'En maintenance', 'Endommagé' => 'status-pill--en-attente',
                        default => 'status-pill--annulee'
                    };
                ?>
                    <div class="stock-card-item" 
                         data-id="<?= $eq['id_equipement'] ?>"
                         data-cat="<?= $eq['id_categorie'] ?>"
                         data-state="<?= htmlspecialchars($eq['etat']) ?>"
                         data-lowstock="<?= $isLowStock ? '1' : '0' ?>"
                         data-search="<?= htmlspecialchars(mb_strtolower($eq['id_equipement'] . ' ' . $eq['nom_equipement'] . ' ' . ($eq['description'] ?? '') . ' ' . $eq['nom_categorie'])) ?>">
                        
                        <div class="stock-card <?= $isLowStock ? 'is-low-stock' : '' ?>">
                            <!-- Barre de couleur supérieure (rouge si seuil dépassé, bleue sinon) -->
                            <div class="stock-card-top-bar"></div>

                            <!-- En-tête de carte -->
                            <div class="stock-card-header">
                                <div class="stock-cat-icon <?= $catStyleClass ?>" title="<?= htmlspecialchars($eq['nom_categorie']) ?>">
                                    <i class="bi <?= $catIconClass ?>"></i>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <span class="text-muted small fw-bold">#<?= $eq['id_equipement'] ?></span>
                                    <span class="status-pill <?= $etatStatusPill ?>">
                                        <span class="dot"></span>
                                        <span><?= htmlspecialchars($eq['etat']) ?></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Corps de carte -->
                            <div class="stock-card-body">
                                <h6 class="stock-card-name"><?= htmlspecialchars($eq['nom_equipement']) ?></h6>
                                <p class="stock-card-desc"><?= htmlspecialchars($eq['description'] ?: 'Aucune description fournie pour cet équipement.') ?></p>
                                
                                <div class="mb-3">
                                    <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                                        <i class="bi bi-tag-fill me-1 text-primary"></i><?= htmlspecialchars($eq['nom_categorie']) ?>
                                    </span>
                                </div>

                                <!-- Jauge de stock visuelle -->
                                <div class="stock-gauge-box">
                                    <div class="stock-gauge-header">
                                        <span class="fw-bold <?= $isLowStock ? 'text-danger' : 'text-dark' ?>">
                                            <i class="bi <?= $isLowStock ? 'bi-exclamation-circle-fill text-danger' : 'bi-check-circle-fill text-success' ?> me-1"></i>
                                            <?= $eq['stock'] ?> unité(s)
                                        </span>
                                        <span class="text-muted small">Seuil : <strong><?= $eq['seuil_alerte'] ?></strong></span>
                                    </div>
                                    <div class="stock-gauge-bar-wrapper">
                                        <div class="stock-gauge-bar <?= $barColorClass ?>" style="width: <?= $gaugePercent ?>%;"></div>
                                    </div>
                                    <div class="stock-gauge-footer">
                                        <span><?= $eq['stock'] > 0 ? 'Stock opérationnel' : 'Rupture immédiate' ?></span>
                                        <?php if ($isLowStock): ?>
                                            <span class="text-danger fw-bold">Réappro urgent</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Pied de la fiche équipement -->
                            <div class="stock-card-footer">
                                <div>
                                    <div class="stock-card-price">
                                        <?= number_format($eq['prix_par_jour'], 2, ',', ' ') ?> <small>DT/j</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                                        <a href="<?= BASE_URL ?>/index.php?action=equipement_edit&id=<?= $eq['id_equipement'] ?>" class="btn btn-sm btn-outline-primary px-3 py-1 rounded-pill fw-semibold" title="Modifier l'équipement">
                                            <i class="bi bi-pencil-fill me-1"></i> Modifier
                                        </a>
                                        <a href="<?= BASE_URL ?>/index.php?action=equipement_delete&id=<?= $eq['id_equipement'] ?>" 
                                           onclick="return confirm('Confirmer la suppression définitive de cet équipement ?');" 
                                           class="btn btn-sm btn-outline-danger px-2 py-1 rounded-circle" 
                                           title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">Mode consultation</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- État vide si la recherche/filtre ne retourne rien -->
            <div id="stockEmptyState" class="text-center py-5 bg-white rounded-4 shadow-sm border mt-3 d-none">
                <i class="bi bi-search fs-1 d-block mb-3 text-secondary opacity-50"></i>
                <h5 class="fw-bold text-dark">Aucun équipement correspondant</h5>
                <p class="text-muted small mb-3">Aucun matériel ne correspond à votre combinaison de critères de recherche ou de filtre.</p>
                <button type="button" id="emptyResetBtn" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser les filtres
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script d'interaction & filtrage temps réel multicritère -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('stockSearchInput');
    const categoryButtons = document.querySelectorAll('.stock-category-item');
    const stateChips = document.querySelectorAll('.stock-state-chip');
    const lowStockToggle = document.getElementById('toggleLowStockOnly');
    const alertToggleContainer = document.getElementById('alertToggleContainer');
    const stockSidebar = document.getElementById('stockSidebar');
    const items = document.querySelectorAll('.stock-card-item');
    const emptyState = document.getElementById('stockEmptyState');
    const matchingBadge = document.getElementById('matchingCountBadge');
    const resetBtn = document.getElementById('resetFiltersBtn');
    const emptyResetBtn = document.getElementById('emptyResetBtn');

    let currentCategory = 'all';
    let currentState = 'all';
    let currentSearch = '';
    let isLowStockOnly = false;

    function applyFilters() {
        let visibleCount = 0;

        items.forEach(item => {
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

        // Gestion affichage état vide
        if (visibleCount === 0 && items.length > 0) {
            emptyState.classList.remove('d-none');
        } else {
            emptyState.classList.add('d-none');
        }

        // Mettre à jour le badge de comptage
        if (matchingBadge) {
            matchingBadge.textContent = `${visibleCount} fiche(s)`;
        }

        // Afficher ou masquer le bouton réinitialiser si filtres actifs
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

    // Filtre recherche temps réel
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearch = e.target.value.trim().toLowerCase();
            applyFilters();
        });
    }

    // Filtre par catégorie
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCategory = btn.getAttribute('data-cat');
            applyFilters();
        });
    });

    // Filtre par état (chips)
    stateChips.forEach(chip => {
        chip.addEventListener('click', () => {
            stateChips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            currentState = chip.getAttribute('data-state');
            applyFilters();
        });
    });

    // Toggle stock sous le seuil
    if (lowStockToggle) {
        lowStockToggle.addEventListener('change', (e) => {
            isLowStockOnly = e.target.checked;
            alertToggleContainer.classList.toggle('active', isLowStockOnly);
            stockSidebar.classList.toggle('alert-active', isLowStockOnly);
            applyFilters();
        });
    }

    // Boutons de reset
    if (resetBtn) resetBtn.addEventListener('click', resetAll);
    if (emptyResetBtn) emptyResetBtn.addEventListener('click', resetAll);
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
