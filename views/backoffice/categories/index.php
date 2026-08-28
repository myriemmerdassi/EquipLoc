<?php
$pageTitle = "Gestion des Catégories — EquipLoc";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';

// Calcul des KPI statistiques
$totalCategories = count($categories);
$totalClassifiedEquipments = array_sum(array_column($categories, 'nb_equipements'));
$emptyCategories = count(array_filter($categories, fn($c) => (int)$c['nb_equipements'] === 0));

// Palette harmonieuse de tons bleus/cyans pour la répartition du parc (comme sur l'image)
$blueTones = [
    '#0091ff',
    '#0077d4',
    '#0ea5e9',
    '#38bdf8',
    '#2563eb',
    '#6366f1'
];

function getCategoryIcon(string $name): string {
    $n = mb_strtolower($name);
    if (str_contains($n, 'audio') || str_contains($n, 'cam') || str_contains($n, 'vid') || str_contains($n, 'photo')) {
        return 'bi-camera-video';
    } elseif (str_contains($n, 'info') || str_contains($n, 'pc') || str_contains($n, 'ordi') || str_contains($n, 'mac')) {
        return 'bi-cpu';
    } elseif (str_contains($n, 'sono') || str_contains($n, 'event') || str_contains($n, 'son') || str_contains($n, 'jbl')) {
        return 'bi-speaker';
    } elseif (str_contains($n, 'jardin') || str_contains($n, 'outil') || str_contains($n, 'mat')) {
        return 'bi-tools';
    }
    return 'bi-box-seam';
}
?>

<div class="cat-page-wrapper py-5 animate-rise">
    <div class="container-fluid px-lg-5 px-3">
        <!-- 1. En-tête : Badge Back-office, Titre avec "Catégories" en bleu et Bouton d'action -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-4 mb-4">
            <div>
                <div class="cat-dash-badge">BACK-OFFICE</div>
                <h1 class="cat-main-title">
                    Gestion des <span class="cat-accent-word">Catégories</span>
                </h1>
                <p class="cat-subtitle mb-0">
                    Organisez vos équipements par domaine d'activité et visualisez la répartition de votre parc en un coup d'œil.
                </p>
            </div>
            
            <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                <div class="flex-shrink-0 pt-md-2">
                    <a href="<?= BASE_URL ?>/index.php?action=categorie_create" class="btn btn-primary fw-bold rounded-pill px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>Nouvelle Catégorie</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. Bandeau KPI segmenté en 3 colonnes -->
        <div class="cat-kpi-box">
            <!-- Colonne 1 : Catégories actives -->
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum"><?= sprintf('%02d', $totalCategories) ?></div>
                <div class="cat-kpi-tag">CATÉGORIES ACTIVES</div>
            </div>

            <!-- Colonne 2 : Équipements classés -->
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum"><?= sprintf('%02d', $totalClassifiedEquipments) ?></div>
                <div class="cat-kpi-tag">ÉQUIPEMENTS CLASSÉS</div>
            </div>

            <!-- Colonne 3 : Catégories vides -->
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum"><?= sprintf('%02d', $emptyCategories) ?></div>
                <div class="cat-kpi-tag">CATÉGORIES VIDES</div>
            </div>
        </div>

        <!-- 3. Carte Répartition du parc -->
        <div class="cat-repartition-box">
            <div class="cat-rep-header">
                <span>RÉPARTITION DU PARC</span>
                <span class="cat-rep-count"><?= $totalClassifiedEquipments ?> équipement<?= $totalClassifiedEquipments > 1 ? 's' : '' ?></span>
            </div>

            <!-- Ruban multi-segments dégradé de bleus -->
            <div class="cat-rep-ribbon">
                <?php if ($totalClassifiedEquipments == 0): ?>
                    <div class="cat-rep-segment" style="width: 100%; background: rgba(255,255,255,0.15);" title="Aucun équipement"></div>
                <?php else: ?>
                    <?php foreach ($categories as $idx => $cat): 
                        $color = $blueTones[$idx % count($blueTones)];
                        $pct = round(($cat['nb_equipements'] / $totalClassifiedEquipments) * 100, 1);
                        if ($pct > 0):
                    ?>
                        <div class="cat-rep-segment" 
                             style="width: <?= $pct ?>%; background: <?= $color ?>;" 
                             title="<?= htmlspecialchars($cat['nom_categorie']) ?> : <?= $cat['nb_equipements'] ?> (<?= $pct ?>%)"></div>
                    <?php 
                        endif;
                    endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Légende sous le ruban -->
            <div class="cat-rep-legend">
                <?php foreach ($categories as $idx => $cat): 
                    $color = $blueTones[$idx % count($blueTones)];
                ?>
                    <div class="cat-rep-legend-item">
                        <span class="cat-rep-dot" style="background: <?= $color ?>;"></span>
                        <span><?= htmlspecialchars($cat['nom_categorie']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4. Barre d'outils : Recherche arrondie & Commutateur de vue -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <!-- Recherche instantanée -->
            <div class="position-relative flex-grow-1" style="max-width: 440px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="categorySearchInput" class="cat-search-input-dark" placeholder="Rechercher une catégorie..." autocomplete="off">
            </div>

            <!-- Commutateur Grille / Liste -->
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="cat-view-btn active" id="btnViewGrid" title="Vue en grille">
                    <i class="bi bi-grid-fill"></i>
                </button>
                <button type="button" class="cat-view-btn" id="btnViewList" title="Vue en liste">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
        </div>

        <!-- 5. Vue Grille de cartes (Vue par défaut) -->
        <div class="cat-cards-grid" id="categoriesGridView">
            <?php foreach ($categories as $idx => $cat): 
                $icon = getCategoryIcon($cat['nom_categorie']);
                $color = $blueTones[$idx % count($blueTones)];
                $pct = ($totalClassifiedEquipments > 0) ? round(($cat['nb_equipements'] / $totalClassifiedEquipments) * 100, 1) : 0;
                $catCode = sprintf('%02d', $cat['id_categorie']);
            ?>
                <div class="cat-card-item" data-search="<?= htmlspecialchars(mb_strtolower($cat['nom_categorie'] . ' ' . ($cat['description'] ?? '') . ' ' . $catCode)) ?>">
                    <div class="cat-luminous-card">
                        <!-- En-tête de carte : Icône domaine + #Code mono -->
                        <div class="cat-card-header-row">
                            <div class="cat-tile-icon">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                            <span class="cat-code-badge">#<?= $catCode ?></span>
                        </div>

                        <!-- Titre & Description -->
                        <h4 class="cat-title-text"><?= htmlspecialchars($cat['nom_categorie']) ?></h4>
                        <p class="cat-desc-text"><?= htmlspecialchars($cat['description'] ?: 'Organisez et gérez les matériels de cette catégorie.') ?></p>

                        <!-- Jauge de répartition -->
                        <div class="cat-card-gauge">
                            <div class="cat-card-gauge-header">
                                <span><i class="bi bi-box-seam text-primary me-1"></i> <?= $cat['nb_equipements'] ?> matériel(s)</span>
                                <span class="text-muted"><?= $pct ?>% du parc</span>
                            </div>
                            <div class="cat-card-gauge-track">
                                <div class="cat-card-gauge-fill" style="width: <?= max($pct, 5) ?>%; background: <?= $color ?>;"></div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="cat-card-bottom-actions">
                            <a href="<?= BASE_URL ?>/index.php?action=equipements_admin" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem; border-color: rgba(255,255,255,0.2);">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Voir stock
                            </a>
                            
                            <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="<?= BASE_URL ?>/index.php?action=categorie_edit&id=<?= $cat['id_categorie'] ?>" 
                                       class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold" 
                                       style="font-size: 0.8rem;"
                                       title="Modifier la catégorie">
                                        <i class="bi bi-pencil-fill me-1"></i> Modifier
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=categorie_delete&id=<?= $cat['id_categorie'] ?>" 
                                       onclick="return confirm('Supprimer cette catégorie ?');" 
                                       class="btn btn-sm btn-outline-danger rounded-circle px-2 py-1" 
                                       title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Carte interactive "+ Créer une catégorie" en pointillés -->
            <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                <a href="<?= BASE_URL ?>/index.php?action=categorie_create" class="cat-create-dashed-card">
                    <div class="cat-plus-circle">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-1" style="font-family: var(--font-display);">Nouvelle Catégorie</h5>
                    <p class="text-muted small mb-0">Ajouter un nouveau domaine d'équipements</p>
                </a>
            <?php endif; ?>
        </div>

        <!-- 6. Vue Liste (Commutable) -->
        <div class="cat-table-box d-none" id="categoriesListView">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 90px;">ID</th>
                            <th>Domaine & Catégorie</th>
                            <th>Description</th>
                            <th>Part du parc</th>
                            <th>Équipements associés</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        <?php foreach ($categories as $idx => $cat): 
                            $icon = getCategoryIcon($cat['nom_categorie']);
                            $color = $blueTones[$idx % count($blueTones)];
                            $pct = ($totalClassifiedEquipments > 0) ? round(($cat['nb_equipements'] / $totalClassifiedEquipments) * 100, 1) : 0;
                            $catCode = sprintf('%02d', $cat['id_categorie']);
                        ?>
                            <tr class="cat-table-row" data-search="<?= htmlspecialchars(mb_strtolower($cat['nom_categorie'] . ' ' . ($cat['description'] ?? '') . ' ' . $catCode)) ?>">
                                <td><span class="cat-code-badge">#<?= $catCode ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="cat-tile-icon" style="width: 34px; height: 34px; font-size: 1rem;">
                                            <i class="bi <?= $icon ?>"></i>
                                        </div>
                                        <strong class="text-white"><?= htmlspecialchars($cat['nom_categorie']) ?></strong>
                                    </div>
                                </td>
                                <td class="text-muted small"><?= htmlspecialchars($cat['description'] ?: 'Aucune description') ?></td>
                                <td style="min-width: 140px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="cat-card-gauge-track flex-grow-1" style="height: 6px;">
                                            <div class="cat-card-gauge-fill" style="width: <?= max($pct, 4) ?>%; background: <?= $color ?>;"></div>
                                        </div>
                                        <span class="small text-muted"><?= $pct ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background: rgba(0, 145, 255, 0.15); color: #38bdf8; border: 1px solid rgba(0, 145, 255, 0.25);">
                                        <?= $cat['nb_equipements'] ?> matériel(s)
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                                        <a href="<?= BASE_URL ?>/index.php?action=categorie_edit&id=<?= $cat['id_categorie'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 py-1 me-1">
                                            <i class="bi bi-pencil-fill me-1"></i> Modifier
                                        </a>
                                        <a href="<?= BASE_URL ?>/index.php?action=categorie_delete&id=<?= $cat['id_categorie'] ?>" 
                                           onclick="return confirm('Supprimer cette catégorie ?');" 
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
        <div id="categoryEmptyState" class="text-center py-5 cat-luminous-card mt-3 d-none">
            <i class="bi bi-search fs-1 d-block mb-3 text-muted"></i>
            <h5 class="fw-bold text-white">Aucune catégorie trouvée</h5>
            <p class="text-muted small mb-0">Essayez de modifier vos termes de recherche.</p>
        </div>
    </div>
</div>

<!-- Script interactif de commutation et de recherche instantanée -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnGrid = document.getElementById('btnViewGrid');
    const btnList = document.getElementById('btnViewList');
    const gridView = document.getElementById('categoriesGridView');
    const listView = document.getElementById('categoriesListView');
    const searchInput = document.getElementById('categorySearchInput');
    const cardItems = document.querySelectorAll('.cat-card-item');
    const tableRows = document.querySelectorAll('.cat-table-row');
    const emptyState = document.getElementById('categoryEmptyState');

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

    // Recherche instantanée en direct
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.trim().toLowerCase();
            let visibleCount = 0;

            cardItems.forEach(item => {
                const searchData = item.getAttribute('data-search') || '';
                if (!term || searchData.includes(term)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            tableRows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                if (!term || searchData.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCount === 0 && cardItems.length > 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
