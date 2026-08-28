<?php
$pageTitle = "Gestion des Utilisateurs — EquipLoc";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';

$totalUsers = count($users);
$responsablesCount = count(array_filter($users, fn($u) => $u['role'] === 'responsable_inventaire'));
$agentsCount = count(array_filter($users, fn($u) => $u['role'] === 'agent_location'));
$clientsCount = count(array_filter($users, fn($u) => $u['role'] === 'client'));
?>

<div class="cat-page-wrapper py-5 animate-rise">
    <div class="container-fluid px-lg-5 px-3">
        <!-- 1. En-tête : Badge Back-office, Titre et Bouton d'action -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-4 mb-4">
            <div>
                <div class="cat-dash-badge">BACK-OFFICE</div>
                <h1 class="cat-main-title">
                    Gestion des <span class="cat-accent-word">Utilisateurs</span>
                </h1>
                <p class="cat-subtitle mb-0">
                    Administration visuelle des comptes, attribution des rôles et contrôle d'accès.
                </p>
            </div>
            
            <div class="flex-shrink-0 pt-md-2">
                <a href="<?= BASE_URL ?>/index.php?action=user_create" class="btn btn-primary fw-bold rounded-pill px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-person-plus-fill"></i>
                    <span>Nouvel Utilisateur</span>
                </a>
            </div>
        </div>

        <!-- 2. Bandeau KPI segmenté en 4 colonnes -->
        <div class="cat-kpi-box" style="grid-template-columns: repeat(4, 1fr);">
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum"><?= sprintf('%02d', $totalUsers) ?></div>
                <div class="cat-kpi-tag">COMPTES TOTAUX</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-danger"><?= sprintf('%02d', $responsablesCount) ?></div>
                <div class="cat-kpi-tag">RESPONSABLES</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-warning"><?= sprintf('%02d', $agentsCount) ?></div>
                <div class="cat-kpi-tag">AGENTS DE LOCATION</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-info"><?= sprintf('%02d', $clientsCount) ?></div>
                <div class="cat-kpi-tag">CLIENTS INSCRITS</div>
            </div>
        </div>

        <!-- 3. Barre d'outils : Recherche & Commutateur de vue -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
            <div class="position-relative flex-grow-1" style="max-width: 380px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="userSearchInput" class="cat-search-input-dark" placeholder="Rechercher par nom, email, rôle..." autocomplete="off">
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="cat-view-btn active" id="btnUserGrid" title="Vue en grille de cartes">
                    <i class="bi bi-grid-fill"></i>
                </button>
                <button type="button" class="cat-view-btn" id="btnUserRows" title="Vue en rangées">
                    <i class="bi bi-view-stacked"></i>
                </button>
            </div>
        </div>

        <!-- 4. VUE 1 : Grille de Cartes Utilisateurs -->
        <div class="cat-cards-grid" id="usersGridView">
            <?php foreach ($users as $u): 
                $initials = strtoupper(mb_substr($u['prenom'] ?? '', 0, 1) . mb_substr($u['nom'] ?? '', 0, 1)) ?: 'US';
                $userCode = sprintf('%02d', $u['id_utilisateur']);
                $roleLabel = match($u['role']) {
                    'responsable_inventaire' => 'Responsable Admin',
                    'agent_location' => 'Agent Location',
                    'client' => 'Client',
                    default => $u['role']
                };
                $roleBadgeStyle = match($u['role']) {
                    'responsable_inventaire' => 'background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25);',
                    'agent_location' => 'background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.25);',
                    'client' => 'background: rgba(0, 145, 255, 0.15); color: #38bdf8; border: 1px solid rgba(0, 145, 255, 0.25);',
                    default => 'background: rgba(255,255,255,0.06); color: #cbd5e1;'
                };
            ?>
                <div class="user-card-item" data-search="<?= htmlspecialchars(strtolower($u['nom'] . ' ' . $u['prenom'] . ' ' . $u['email'] . ' ' . ($u['telephone'] ?? '') . ' ' . $u['role'] . ' ' . $userCode)) ?>">
                    <div class="cat-luminous-card h-100 d-flex flex-column">
                        <div class="cat-card-header-row mb-3">
                            <div class="cat-tile-icon" style="width: 44px; height: 44px; font-size: 1rem; font-weight: 700; background: rgba(0, 145, 255, 0.15); color: #38bdf8;">
                                <?= htmlspecialchars($initials) ?>
                            </div>
                            <span class="cat-code-badge">#<?= $userCode ?></span>
                        </div>

                        <h4 class="cat-title-text mb-1"><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></h4>
                        <p class="cat-desc-text mb-3">
                            <i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($u['email']) ?><br>
                            <i class="bi bi-telephone me-1"></i> <?= htmlspecialchars($u['telephone'] ?: 'Non renseigné') ?>
                        </p>

                        <div class="d-flex align-items-center justify-content-between mb-4 mt-auto">
                            <span class="badge rounded-pill px-3 py-1 fw-semibold" style="<?= $roleBadgeStyle ?>">
                                <?= htmlspecialchars($roleLabel) ?>
                            </span>
                            <small class="text-muted">Inscrit le <?= date('d/m/Y', strtotime($u['date_creation'])) ?></small>
                        </div>

                        <div class="pt-3 border-top d-flex align-items-center justify-content-between" style="border-color: rgba(255,255,255,0.08) !important;">
                            <a href="<?= BASE_URL ?>/index.php?action=user_edit&id=<?= $u['id_utilisateur'] ?>" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
                                <i class="bi bi-pencil-fill me-1"></i> Modifier
                            </a>
                            <?php if ($u['id_utilisateur'] !== $_SESSION['user']['id_utilisateur']): ?>
                                <a href="<?= BASE_URL ?>/index.php?action=user_delete&id=<?= $u['id_utilisateur'] ?>" 
                                   onclick="return confirm('Supprimer cet utilisateur ?');" 
                                   class="btn btn-sm btn-outline-danger rounded-circle px-2 py-1"
                                   title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Carte + Nouvel utilisateur -->
            <a href="<?= BASE_URL ?>/index.php?action=user_create" class="cat-create-dashed-card">
                <div class="cat-plus-circle">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h5 class="fw-bold text-white mb-1" style="font-family: var(--font-display);">Nouvel Utilisateur</h5>
                <p class="text-muted small mb-0">Créer un profil responsable, agent ou client</p>
            </a>
        </div>

        <!-- 5. VUE 2 : Rangées de Fiches Aérées -->
        <div class="d-none" id="usersRowsView">
            <?php foreach ($users as $u): 
                $initials = strtoupper(mb_substr($u['prenom'] ?? '', 0, 1) . mb_substr($u['nom'] ?? '', 0, 1)) ?: 'US';
                $userCode = sprintf('%02d', $u['id_utilisateur']);
                $roleLabel = match($u['role']) {
                    'responsable_inventaire' => 'Responsable Admin',
                    'agent_location' => 'Agent Location',
                    'client' => 'Client',
                    default => $u['role']
                };
                $roleBadgeStyle = match($u['role']) {
                    'responsable_inventaire' => 'background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25);',
                    'agent_location' => 'background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.25);',
                    'client' => 'background: rgba(0, 145, 255, 0.15); color: #38bdf8; border: 1px solid rgba(0, 145, 255, 0.25);',
                    default => 'background: rgba(255,255,255,0.06); color: #cbd5e1;'
                };
            ?>
                <div class="cat-row-card user-row-item" data-search="<?= htmlspecialchars(strtolower($u['nom'] . ' ' . $u['prenom'] . ' ' . $u['email'] . ' ' . ($u['telephone'] ?? '') . ' ' . $u['role'] . ' ' . $userCode)) ?>">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-md-4">
                            <div class="d-flex align-items-center gap-3">
                                <span class="cat-code-badge">#<?= $userCode ?></span>
                                <div class="cat-tile-icon" style="width: 38px; height: 38px; font-size: 0.85rem; font-weight: 700;">
                                    <?= htmlspecialchars($initials) ?>
                                </div>
                                <div>
                                    <strong class="text-white d-block" style="font-size: 0.95rem;"><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></strong>
                                    <small class="text-muted"><?= htmlspecialchars($u['email']) ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <small class="text-muted d-block">Téléphone :</small>
                            <span class="text-white small"><?= htmlspecialchars($u['telephone'] ?: 'N/A') ?></span>
                        </div>

                        <div class="col-6 col-md-2">
                            <span class="badge rounded-pill px-3 py-1 fw-semibold" style="<?= $roleBadgeStyle ?>">
                                <?= htmlspecialchars($roleLabel) ?>
                            </span>
                        </div>

                        <div class="col-12 col-md-3 d-flex align-items-center justify-content-md-end gap-2">
                            <a href="<?= BASE_URL ?>/index.php?action=user_edit&id=<?= $u['id_utilisateur'] ?>" class="btn btn-primary btn-sm rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                                <i class="bi bi-pencil-fill me-1"></i> Modifier
                            </a>
                            <?php if ($u['id_utilisateur'] !== $_SESSION['user']['id_utilisateur']): ?>
                                <a href="<?= BASE_URL ?>/index.php?action=user_delete&id=<?= $u['id_utilisateur'] ?>" 
                                   onclick="return confirm('Supprimer cet utilisateur ?');" 
                                   class="btn btn-outline-danger btn-sm rounded-circle px-2 py-1"
                                   title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- État vide -->
        <div id="usersEmptyState" class="text-center py-5 cat-luminous-card d-none">
            <i class="bi bi-search fs-1 d-block mb-3 text-muted"></i>
            <h5 class="fw-bold text-white">Aucun utilisateur trouvé</h5>
            <p class="small text-muted mb-0">Essayez de modifier votre recherche.</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('userSearchInput');
    const cardItems = document.querySelectorAll('.user-card-item');
    const rowItems = document.querySelectorAll('.user-row-item');
    const emptyState = document.getElementById('usersEmptyState');

    const btnGrid = document.getElementById('btnUserGrid');
    const btnRows = document.getElementById('btnUserRows');
    const gridView = document.getElementById('usersGridView');
    const rowsView = document.getElementById('usersRowsView');

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

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.trim().toLowerCase();
            let count = 0;

            cardItems.forEach(item => {
                const searchData = item.getAttribute('data-search') || '';
                if (!term || searchData.includes(term)) {
                    item.style.display = '';
                    count++;
                } else {
                    item.style.display = 'none';
                }
            });

            rowItems.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                if (!term || searchData.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            if (count === 0 && cardItems.length > 0) {
                emptyState.classList.remove('d-none');
            } else {
                emptyState.classList.add('d-none');
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
