<?php
$pageTitle = "Catalogue des Équipements — EquipLoc";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<div class="cat-page-wrapper py-5 animate-rise">
    <div class="container-fluid px-lg-5 px-3">
        <!-- 1. En-tête : Badge Catalogue, Titre et Sous-titre -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-4 mb-4">
            <div>
                <div class="cat-dash-badge">CATALOGUE OFFICIEL</div>
                <h1 class="cat-main-title">
                    Louez le meilleur <span class="cat-accent-word">matériel</span>
                </h1>
                <p class="cat-subtitle mb-0">
                    Caméras, ordinateurs, projecteurs, drones et sonorisation de qualité professionnelle prêts pour vos projets.
                </p>
            </div>
            
            <div class="flex-shrink-0 pt-md-2 d-none d-md-block">
                <span class="badge rounded-pill px-4 py-2 fw-semibold" style="background: rgba(0, 145, 255, 0.15); color: #38bdf8; border: 1px solid rgba(0, 145, 255, 0.25); font-size: 0.88rem;">
                    <i class="bi bi-shield-check me-1"></i> Matériel Garanti &amp; Révisé
                </span>
            </div>
        </div>

        <!-- 2. Panneau de Filtres & Recherche Luminous Slate -->
        <div class="cat-repartition-box mb-4">
            <div class="cat-rep-header mb-3">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-sliders text-primary fs-5"></i>
                    <span>FILTRES &amp; RECHERCHE EN DIRECT</span>
                </span>
                <span class="cat-rep-count" id="results-count-header"><?= count($equipements) ?> équipement(s) disponible(s)</span>
            </div>

            <form id="filter-form" action="<?= BASE_URL ?>/index.php" method="GET" onsubmit="return false;">
                <input type="hidden" name="action" value="catalogue">
                <div class="row g-3 align-items-end">
                    <!-- Recherche textuelle -->
                    <div class="col-12 col-lg-3 col-md-6">
                        <label for="q" class="cat-kpi-tag mb-2 d-block">Mots-clés / Nom</label>
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" class="cat-search-input-dark" id="q" name="q"
                                   placeholder="Sony, MacBook, Drone..."
                                   value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                                   autocomplete="off">
                        </div>
                    </div>

                    <!-- Catégorie -->
                    <div class="col-12 col-lg-3 col-md-6">
                        <label for="categorie" class="cat-kpi-tag mb-2 d-block">Catégorie</label>
                        <select class="cat-search-input-dark" id="categorie" name="categorie" style="padding-left: 18px; appearance: auto;">
                            <option value="" style="background: #172740; color: #fff;">Toutes les catégories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id_categorie'] ?>" style="background: #172740; color: #fff;"
                                    <?= (($_GET['categorie'] ?? '') == $cat['id_categorie']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom_categorie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- État -->
                    <div class="col-12 col-lg-2 col-md-4">
                        <label for="etat" class="cat-kpi-tag mb-2 d-block">État</label>
                        <select class="cat-search-input-dark" id="etat" name="etat" style="padding-left: 18px; appearance: auto;">
                            <option value="" style="background: #172740; color: #fff;">Tous les états</option>
                            <option value="Disponible" style="background: #172740; color: #fff;" <?= (($_GET['etat'] ?? '') === 'Disponible') ? 'selected' : '' ?>>Disponible</option>
                            <option value="En location" style="background: #172740; color: #fff;" <?= (($_GET['etat'] ?? '') === 'En location') ? 'selected' : '' ?>>En location</option>
                            <option value="En maintenance" style="background: #172740; color: #fff;" <?= (($_GET['etat'] ?? '') === 'En maintenance') ? 'selected' : '' ?>>En maintenance</option>
                        </select>
                    </div>

                    <!-- Prix Min & Max -->
                    <div class="col-6 col-lg-2 col-md-4">
                        <label for="prix_min" class="cat-kpi-tag mb-2 d-block">Prix Min (DT)</label>
                        <input type="number" step="0.01" class="cat-search-input-dark" style="padding-left: 18px;"
                               id="prix_min" name="prix_min" placeholder="0"
                               value="<?= htmlspecialchars($_GET['prix_min'] ?? '') ?>">
                    </div>

                    <div class="col-6 col-lg-2 col-md-4">
                        <label for="prix_max" class="cat-kpi-tag mb-2 d-block">Prix Max (DT)</label>
                        <input type="number" step="0.01" class="cat-search-input-dark" style="padding-left: 18px;"
                               id="prix_max" name="prix_max" placeholder="1000"
                               value="<?= htmlspecialchars($_GET['prix_max'] ?? '') ?>">
                    </div>
                </div>
            </form>
        </div>

        <!-- 3. Grille des fiches du catalogue -->
        <div id="catalogue-results">
            <!-- État vide -->
            <div id="no-results-box" class="text-center py-5 cat-luminous-card mb-4 d-none">
                <i class="bi bi-search fs-1 d-block mb-3 text-muted"></i>
                <h4 class="text-white fw-bold">Aucun équipement ne correspond à vos critères</h4>
                <p class="text-muted small mb-3">Essayez d'élargir votre recherche ou de réinitialiser vos filtres.</p>
                <button type="button" id="reset-btn-empty" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Réinitialiser les filtres
                </button>
            </div>

            <!-- Grille -->
            <div class="cat-cards-grid" id="cards-wrapper">
                <?php foreach ($equipements as $eq): 
                    $etatPill = match($eq['etat']) {
                        'Disponible' => 'status-pill--terminee',
                        'En location' => 'status-pill--validee',
                        'En maintenance', 'Endommagé' => 'status-pill--en-attente',
                        default => 'status-pill--annulee'
                    };
                    $eqCode = sprintf('%02d', $eq['id_equipement']);
                ?>
                    <div class="equipement-card-col"
                         data-search="<?= htmlspecialchars(strtolower($eq['nom_equipement'] . ' ' . $eq['description'] . ' ' . $eq['nom_categorie'] . ' ' . $eqCode)) ?>"
                         data-categorie="<?= $eq['id_categorie'] ?>"
                         data-etat="<?= htmlspecialchars($eq['etat']) ?>"
                         data-prix="<?= (float)$eq['prix_par_jour'] ?>">
                        
                        <div class="cat-luminous-card">
                            <!-- Image avec badge catégorie et état -->
                            <div class="position-relative mb-3 rounded-3 overflow-hidden" style="height: 190px; background: rgba(0,0,0,0.2);">
                                <span class="position-absolute top-0 start-0 m-3 badge rounded-pill" style="background: rgba(19, 31, 51, 0.85); backdrop-filter: blur(8px); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); font-size: 0.72rem;">
                                    <?= htmlspecialchars($eq['nom_categorie']) ?>
                                </span>
                                
                                <span class="position-absolute top-0 end-0 m-3 status-pill <?= $etatPill ?>" style="backdrop-filter: blur(8px);">
                                    <span class="dot"></span>
                                    <span><?= htmlspecialchars($eq['etat']) ?></span>
                                </span>

                                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($eq['image']) ?>" 
                                     onerror="this.src='https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=60';"
                                     alt="<?= htmlspecialchars($eq['nom_equipement']) ?>"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>

                            <!-- Titre & Description -->
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                <h4 class="cat-title-text mb-0 text-truncate" title="<?= htmlspecialchars($eq['nom_equipement']) ?>">
                                    <?= htmlspecialchars($eq['nom_equipement']) ?>
                                </h4>
                                <span class="cat-code-badge">#<?= $eqCode ?></span>
                            </div>
                            
                            <p class="cat-desc-text">
                                <?= htmlspecialchars(mb_strimwidth($eq['description'] ?? '', 0, 95, "...")) ?>
                            </p>

                            <!-- Tarif & Disponibilité -->
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                <div>
                                    <span class="text-muted small d-block" style="font-size: 0.72rem;">Tarif journalier</span>
                                    <div class="cat-title-text text-primary mb-0" style="font-size: 1.25rem;">
                                        <?= number_format($eq['prix_par_jour'], 2, ',', ' ') ?> <small style="font-size: 0.75rem; color: #8fa0b5;">DT/j</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted small d-block" style="font-size: 0.72rem;">Disponibilité</span>
                                    <span class="fw-bold <?= $eq['stock'] > 0 ? 'text-success' : 'text-danger' ?>" style="font-size: 0.88rem;">
                                        <?= $eq['stock'] > 0 ? $eq['stock'] . ' dispo(s)' : 'Rupture' ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-auto">
                                <?php if (isLoggedIn() && hasRole(ROLE_CLIENT)): ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=reserve&id_equipement=<?= $eq['id_equipement'] ?>" 
                                       class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm <?= ($eq['stock'] <= 0 || $eq['etat'] !== 'Disponible') ? 'disabled' : '' ?>">
                                        <i class="bi bi-calendar-plus me-1"></i> Réserver maintenant
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=details&id=<?= $eq['id_equipement'] ?>" class="btn btn-outline-secondary w-100 rounded-pill mt-2 py-2 fw-semibold" style="font-size: 0.85rem;">Voir les détails</a>
                                <?php elseif (isLoggedIn() && hasRole(ROLE_RESPONSABLE)): ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=equipement_edit&id=<?= $eq['id_equipement'] ?>" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                                        <i class="bi bi-pencil-square me-1"></i> Gérer dans l'inventaire
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=details&id=<?= $eq['id_equipement'] ?>" class="btn btn-outline-secondary w-100 rounded-pill mt-2 py-2 fw-semibold" style="font-size: 0.85rem;">Voir les détails</a>
                                <?php elseif (isLoggedIn() && hasRole(ROLE_AGENT)): ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=location_comptoir&id_equipement=<?= $eq['id_equipement'] ?>" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm <?= ($eq['stock'] <= 0 || $eq['etat'] !== 'Disponible') ? 'disabled' : '' ?>">
                                        <i class="bi bi-shop me-1"></i> Louer au comptoir
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=details&id=<?= $eq['id_equipement'] ?>" class="btn btn-outline-secondary w-100 rounded-pill mt-2 py-2 fw-semibold" style="font-size: 0.85rem;">Voir les détails</a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=login" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Connectez-vous pour louer
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=details&id=<?= $eq['id_equipement'] ?>" class="btn btn-outline-secondary w-100 rounded-pill mt-2 py-2 fw-semibold" style="font-size: 0.85rem;">Voir les détails</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Script de Filtrage Dynamique Instantané en Temps Réel -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputQ = document.getElementById('q');
    const selectCat = document.getElementById('categorie');
    const selectEtat = document.getElementById('etat');
    const inputPrixMin = document.getElementById('prix_min');
    const inputPrixMax = document.getElementById('prix_max');
    const resetBtnEmpty = document.getElementById('reset-btn-empty');

    const cardCols = document.querySelectorAll('.equipement-card-col');
    const resultsCountHeader = document.getElementById('results-count-header');
    const noResultsBox = document.getElementById('no-results-box');

    function applyInstantFilter() {
        const qVal = inputQ.value.toLowerCase().trim();
        const catVal = selectCat.value;
        const etatVal = selectEtat.value;
        const minVal = parseFloat(inputPrixMin.value) || 0;
        const maxVal = parseFloat(inputPrixMax.value) || Infinity;

        let count = 0;

        cardCols.forEach(col => {
            const text = col.getAttribute('data-search') || '';
            const catId = col.getAttribute('data-categorie') || '';
            const etat = col.getAttribute('data-etat') || '';
            const prix = parseFloat(col.getAttribute('data-prix')) || 0;

            const matchQ = (qVal === '' || text.includes(qVal));
            const matchCat = (catVal === '' || catId === catVal);
            const matchEtat = (etatVal === '' || etat === etatVal);
            const matchPrix = (prix >= minVal && prix <= maxVal);

            if (matchQ && matchCat && matchEtat && matchPrix) {
                col.style.display = '';
                count++;
            } else {
                col.style.display = 'none';
            }
        });

        if (resultsCountHeader) {
            resultsCountHeader.textContent = `${count} équipement(s) disponible(s)`;
        }

        if (noResultsBox) {
            if (count === 0) noResultsBox.classList.remove('d-none');
            else noResultsBox.classList.add('d-none');
        }
    }

    inputQ.addEventListener('input', applyInstantFilter);
    inputPrixMin.addEventListener('input', applyInstantFilter);
    inputPrixMax.addEventListener('input', applyInstantFilter);
    selectCat.addEventListener('change', applyInstantFilter);
    selectEtat.addEventListener('change', applyInstantFilter);

    function resetAll() {
        inputQ.value = '';
        selectCat.value = '';
        selectEtat.value = '';
        inputPrixMin.value = '';
        inputPrixMax.value = '';
        applyInstantFilter();
    }

    if (resetBtnEmpty) resetBtnEmpty.addEventListener('click', resetAll);
    applyInstantFilter();
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
