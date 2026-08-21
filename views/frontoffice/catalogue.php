<?php
$pageTitle = "Catalogue des Équipements";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<!-- Hero Banner Header -->
<div class="bg-dark text-white py-5 mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);">
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-25 mb-3 px-3 py-2">
                    <i class="bi bi-stars me-1"></i> Matériel Professionnel & Garanti
                </span>
                <h1 class="display-4 fw-bold mb-3">Louez le meilleur matériel en quelques clics</h1>
                <p class="lead text-slate-300">Caméras, ordinateurs, projecteurs, drones et matériel de sonorisation prêts pour vos projets.</p>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Barre de Recherche & Filtres Multicritères Dynamiques Instantanés -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0"><i class="bi bi-funnel-fill text-primary me-2"></i> Recherche & Filtres Multicritères</h5>
        </div>
        
        <form id="filter-form" action="<?= BASE_URL ?>/index.php" method="GET" class="row g-3 align-items-end" onsubmit="return false;">
            <input type="hidden" name="action" value="catalogue">

            <!-- Recherche Mots-Clés -->
            <div class="col-md-3">
                <label for="q" class="form-label small text-muted font-weight-bold">Mots-clés / Nom</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="q" name="q" placeholder="Sony, MacBook, Drone..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" autocomplete="off">
                </div>
            </div>

            <!-- Catégorie -->
            <div class="col-md-3">
                <label for="categorie" class="form-label small text-muted font-weight-bold">Catégorie</label>
                <select class="form-select" id="categorie" name="categorie">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id_categorie'] ?>" <?= (($_GET['categorie'] ?? '') == $cat['id_categorie']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom_categorie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- État du matériel -->
            <div class="col-md-2">
                <label for="etat" class="form-label small text-muted font-weight-bold">État du matériel</label>
                <select class="form-select" id="etat" name="etat">
                    <option value="">Tous les états</option>
                    <option value="Disponible" <?= (($_GET['etat'] ?? '') === 'Disponible') ? 'selected' : '' ?>>Disponible</option>
                    <option value="En location" <?= (($_GET['etat'] ?? '') === 'En location') ? 'selected' : '' ?>>En location</option>
                    <option value="En maintenance" <?= (($_GET['etat'] ?? '') === 'En maintenance') ? 'selected' : '' ?>>En maintenance</option>
                    <option value="Endommagé" <?= (($_GET['etat'] ?? '') === 'Endommagé') ? 'selected' : '' ?>>Endommagé</option>
                </select>
            </div>

            <!-- Prix Min -->
            <div class="col-md-2">
                <label for="prix_min" class="form-label small text-muted font-weight-bold">Prix Min (DT/j)</label>
                <input type="number" step="0.01" class="form-control" id="prix_min" name="prix_min" placeholder="10" value="<?= htmlspecialchars($_GET['prix_min'] ?? '') ?>">
            </div>

            <!-- Prix Max -->
            <div class="col-md-1">
                <label for="prix_max" class="form-label small text-muted font-weight-bold">Prix Max</label>
                <input type="number" step="0.01" class="form-control" id="prix_max" name="prix_max" placeholder="300" value="<?= htmlspecialchars($_GET['prix_max'] ?? '') ?>">
            </div>

            <!-- Action Réinitialiser -->
            <div class="col-md-1 d-flex">
                <button type="button" id="reset-filters" class="btn btn-outline-secondary w-100" title="Réinitialiser tous les filtres">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Zone Dynamique des Résultats -->
    <div id="catalogue-results">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Équipements trouvés (<span id="results-count"><?= count($equipements) ?></span>)</h4>
        </div>

        <div id="no-results-box" class="text-center py-5 bg-white rounded-4 shadow-sm border d-none">
            <i class="bi bi-search display-1 text-muted"></i>
            <h4 class="mt-3 text-secondary">Aucun équipement ne correspond à vos critères.</h4>
            <p class="text-muted">Essayez de modifier vos filtres ou la plage de prix.</p>
            <button type="button" id="reset-btn-empty" class="btn btn-outline-primary">Voir tout le catalogue</button>
        </div>

        <div class="row g-4" id="cards-wrapper">
            <?php foreach ($equipements as $eq): ?>
                <div class="col-md-6 col-lg-4 equipement-card-col"
                     data-search="<?= htmlspecialchars(strtolower($eq['nom_equipement'] . ' ' . $eq['description'] . ' ' . $eq['nom_categorie'])) ?>"
                     data-categorie="<?= $eq['id_categorie'] ?>"
                     data-etat="<?= htmlspecialchars($eq['etat']) ?>"
                     data-prix="<?= (float)$eq['prix_par_jour'] ?>">
                    
                    <div class="equipement-card h-100 d-flex flex-column">
                        <div class="equipement-img-wrapper">
                            <span class="position-absolute top-0 start-0 m-3 badge bg-dark bg-opacity-75 text-white backdrop-blur">
                                <?= htmlspecialchars($eq['nom_categorie']) ?>
                            </span>
                            
                            <?php
                            $badgeClass = match($eq['etat']) {
                                'Disponible' => 'badge-disponible',
                                'En location' => 'badge-en-location',
                                'En maintenance' => 'badge-en-maintenance',
                                'Endommagé' => 'badge-endommage',
                                default => 'badge-disponible'
                            };
                            ?>
                            <span class="position-absolute top-0 end-0 m-3 badge-etat <?= $badgeClass ?>">
                                <?= htmlspecialchars($eq['etat']) ?>
                            </span>

                            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($eq['image']) ?>" 
                                 onerror="this.src='https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=60';"
                                 alt="<?= htmlspecialchars($eq['nom_equipement']) ?>">
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-2"><?= htmlspecialchars($eq['nom_equipement']) ?></h5>
                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                <?= htmlspecialchars(mb_strimwidth($eq['description'] ?? '', 0, 100, "...")) ?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-auto">
                                <div>
                                    <span class="small text-muted d-block">Prix par jour</span>
                                    <span class="prix-badge"><?= number_format($eq['prix_par_jour'], 2) ?> DT</span>
                                </div>
                                <div>
                                    <span class="small text-muted d-block text-end">Stock</span>
                                    <span class="fw-bold <?= $eq['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $eq['stock'] ?> dispo(s)
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3">
                                <?php if (isLoggedIn() && hasRole(ROLE_CLIENT)): ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=reserve&id_equipement=<?= $eq['id_equipement'] ?>" 
                                       class="btn btn-primary w-100 rounded-3 fw-bold <?= ($eq['stock'] <= 0 || $eq['etat'] !== 'Disponible') ? 'disabled' : '' ?>">
                                        <i class="bi bi-calendar-plus me-1"></i> Réserver maintenant
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=details&id=<?= $eq['id_equipement'] ?>" class="btn btn-outline-secondary w-100 rounded-3 mt-2 fw-bold">Voir les détails</a>
                                <?php elseif (isLoggedIn() && hasRole(ROLE_RESPONSABLE)): ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=equipement_edit&id=<?= $eq['id_equipement'] ?>" class="btn btn-outline-warning w-100 rounded-3 fw-bold">
                                        <i class="bi bi-pencil-square me-1"></i> Gérer dans l'inventaire
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=details&id=<?= $eq['id_equipement'] ?>" class="btn btn-outline-secondary w-100 rounded-3 mt-2 fw-bold">Voir les détails</a>
                                <?php elseif (isLoggedIn() && hasRole(ROLE_AGENT)): ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=location_comptoir&id_equipement=<?= $eq['id_equipement'] ?>" class="btn btn-success w-100 rounded-3 fw-bold <?= ($eq['stock'] <= 0 || $eq['etat'] !== 'Disponible') ? 'disabled' : '' ?>">
                                        <i class="bi bi-shop me-1"></i> Louer au comptoir
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=details&id=<?= $eq['id_equipement'] ?>" class="btn btn-outline-secondary w-100 rounded-3 mt-2 fw-bold">Voir les détails</a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=login" class="btn btn-outline-primary w-100 rounded-3 fw-bold">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Connectez-vous pour louer
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Script de Filtrage Dynamique Instantané en Temps Réel (0ms delay) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filter-form');
    const inputQ = document.getElementById('q');
    const selectCat = document.getElementById('categorie');
    const selectEtat = document.getElementById('etat');
    const inputPrixMin = document.getElementById('prix_min');
    const inputPrixMax = document.getElementById('prix_max');
    const resetBtn = document.getElementById('reset-filters');
    const resetBtnEmpty = document.getElementById('reset-btn-empty');

    const cardCols = document.querySelectorAll('.equipement-card-col');
    const resultsCount = document.getElementById('results-count');
    const noResultsBox = document.getElementById('no-results-box');

    // Récupérer l'URL de base du formulaire de manière sécurisée
    // (form.action renvoie l'input "action" à cause de son name="action")
    const formBaseUrl = form.getAttribute('action');

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

        if (resultsCount) resultsCount.textContent = count;
        if (noResultsBox) {
            if (count === 0) noResultsBox.classList.remove('d-none');
            else noResultsBox.classList.add('d-none');
        }

        // Mise à jour de l'URL pour conserver l'historique
        const params = new URLSearchParams();
        params.append('action', 'catalogue');
        if (inputQ.value.trim()) params.append('q', inputQ.value.trim());
        if (catVal) params.append('categorie', catVal);
        if (etatVal) params.append('etat', etatVal);
        if (inputPrixMin.value) params.append('prix_min', inputPrixMin.value);
        if (inputPrixMax.value) params.append('prix_max', inputPrixMax.value);

        const newUrl = formBaseUrl + '?' + params.toString();
        window.history.replaceState({}, '', newUrl);
    }

    // Événements INSTANTANÉS sur chaque touche tapée (0ms delay)
    inputQ.addEventListener('input', applyInstantFilter);
    inputPrixMin.addEventListener('input', applyInstantFilter);
    inputPrixMax.addEventListener('input', applyInstantFilter);

    // Événements sur les menus déroulants
    selectCat.addEventListener('change', applyInstantFilter);
    selectEtat.addEventListener('change', applyInstantFilter);

    // Bouton de réinitialisation
    function resetAll() {
        inputQ.value = '';
        selectCat.value = '';
        selectEtat.value = '';
        inputPrixMin.value = '';
        inputPrixMax.value = '';
        applyInstantFilter();
    }

    if (resetBtn) resetBtn.addEventListener('click', resetAll);
    if (resetBtnEmpty) resetBtnEmpty.addEventListener('click', resetAll);

    // Lancer une première fois pour appliquer les filtres présents dans l'URL
    applyInstantFilter();
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
