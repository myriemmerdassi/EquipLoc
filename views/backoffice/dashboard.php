<?php
$pageTitle = "Tableau de Bord BackOffice — EquipLoc";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<div class="cat-page-wrapper py-5 animate-rise">
    <div class="container-fluid px-lg-5 px-3">
        <!-- 1. En-tête : Badge Back-office, Titre éditorial et Actions rapides -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-4 mb-4">
            <div>
                <div class="cat-dash-badge">BACK-OFFICE</div>
                <h1 class="cat-main-title">
                    Tableau de <span class="cat-accent-word">Bord</span>
                </h1>
                <p class="cat-subtitle mb-0">
                    Vue synthétique de l'activité, alertes critiques de stock, suivi financier et raccourcis d'administration.
                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap pt-md-2">
                <a href="<?= BASE_URL ?>/index.php?action=location_comptoir" class="btn btn-success fw-bold rounded-pill px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="bi bi-shop"></i>
                    <span>Créer location comptoir</span>
                </a>
                <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                    <a href="<?= BASE_URL ?>/index.php?action=equipement_create" class="btn btn-primary fw-bold rounded-pill px-4 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>Ajouter équipement</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- 2. Bannière Alerte Stock Faible -->
        <?php if (!empty($alertesStock)): ?>
            <div class="p-3 p-md-4 rounded-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" 
                 style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); backdrop-filter: blur(12px);">
                <div class="d-flex align-items-center gap-3">
                    <div class="cat-tile-icon" style="background: rgba(239, 68, 68, 0.2); color: #f87171; border-color: rgba(239, 68, 68, 0.4);">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-white mb-1">
                            Attention : <?= count($alertesStock) ?> équipement(s) ont atteint leur seuil critique de réapprovisionnement !
                        </h6>
                        <div class="text-white-50 small">
                            <?php foreach ($alertesStock as $idx => $ast): ?>
                                <span class="badge rounded-pill me-1 mb-1" style="background: rgba(0,0,0,0.25); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3);">
                                    <?= htmlspecialchars($ast['nom_equipement']) ?> (Stock: <?= $ast['stock'] ?>/Seuil: <?= $ast['seuil_alerte'] ?>)
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/index.php?action=equipements_admin" class="btn btn-sm btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm">
                    Gérer le stock
                </a>
            </div>
        <?php endif; ?>

        <!-- 3. Bandeau KPI segmenté en 4 colonnes -->
        <div class="cat-kpi-box" style="grid-template-columns: repeat(4, 1fr);">
            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum"><?= sprintf('%02d', $totalEquipements) ?></div>
                <div class="cat-kpi-tag">TOTAL ÉQUIPEMENTS</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-info"><?= sprintf('%02d', $totalLocations) ?></div>
                <div class="cat-kpi-tag">RÉSERVATIONS TOTALES</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum <?= $locationsAttente > 0 ? 'text-warning' : 'text-white' ?> d-flex align-items-center gap-2">
                    <span><?= sprintf('%02d', $locationsAttente) ?></span>
                    <?php if ($locationsAttente > 0): ?>
                        <span class="badge bg-warning text-dark rounded-pill px-2 py-0" style="font-size: 0.65rem;">À valider</span>
                    <?php endif; ?>
                </div>
                <div class="cat-kpi-tag">EN ATTENTE DE VALIDATION</div>
            </div>

            <div class="cat-kpi-col">
                <div class="cat-kpi-bignum text-success" style="font-size: 2rem;">
                    <?= number_format($chiffreAffaires, 2, ',', ' ') ?> <small style="font-size: 0.9rem;">DT</small>
                </div>
                <div class="cat-kpi-tag">CHIFFRE D'AFFAIRES GLOBAL</div>
            </div>
        </div>

        <!-- 4. Grille de Contenu : Fiches de réservations récentes & Raccourcis -->
        <div class="row g-4 mt-1">
            <!-- Colonne gauche : Réservations récentes en fiches modulaires (Cards) -->
            <div class="col-12 col-xl-8 col-lg-7">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-primary fs-5"></i>
                        <h5 class="fw-bold text-white mb-0" style="font-family: var(--font-display);">Réservations récentes</h5>
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?action=locations_admin" class="btn btn-sm btn-outline-light rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.8rem; border-color: rgba(255,255,255,0.2);">
                        Voir toutes les locations <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <?php if (empty($locations)): ?>
                    <div class="cat-luminous-card text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                        <h5 class="text-white fw-bold">Aucune réservation</h5>
                        <p class="text-muted small mb-0">Les nouvelles locations apparaîtront ici.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php 
                        $recentLocations = array_slice($locations, 0, 5);
                        foreach ($recentLocations as $loc): 
                            $initials = strtoupper(mb_substr($loc['client_prenom'] ?? '', 0, 1) . mb_substr($loc['client_nom'] ?? '', 0, 1)) ?: 'CL';
                            $etatPill = match($loc['statut']) {
                                'En attente' => 'status-pill--en-attente',
                                'Validée', 'En cours' => 'status-pill--validee',
                                'Terminée' => 'status-pill--terminee',
                                default => 'status-pill--annulee'
                            };
                            $locCode = sprintf('%02d', $loc['id_location']);
                        ?>
                            <div class="cat-row-card">
                                <div class="row align-items-center g-3">
                                    <div class="col-12 col-md-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="cat-code-badge">#<?= $locCode ?></span>
                                            <div class="cat-tile-icon" style="width: 36px; height: 36px; font-size: 0.82rem; font-weight: 700;">
                                                <?= htmlspecialchars($initials) ?>
                                            </div>
                                            <div class="overflow-hidden">
                                                <strong class="text-white d-block text-truncate" style="font-size: 0.92rem;">
                                                    <?= htmlspecialchars($loc['client_nom'] . ' ' . $loc['client_prenom']) ?>
                                                </strong>
                                                <small class="text-muted d-block text-truncate"><?= htmlspecialchars($loc['client_email']) ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <strong class="text-white d-block text-truncate" style="font-size: 0.9rem;"><?= htmlspecialchars($loc['nom_equipement']) ?></strong>
                                        <small class="text-muted">Qté : <?= $loc['quantite'] ?> &middot; <?= htmlspecialchars($loc['nom_categorie']) ?></small>
                                    </div>

                                    <div class="col-6 col-md-2">
                                        <div class="cat-title-text text-primary mb-0" style="font-size: 1.1rem;">
                                            <?= number_format($loc['montant_total'] + $loc['frais_supplementaires'], 2, ',', ' ') ?> DT
                                        </div>
                                    </div>

                                    <div class="col-6 col-md-3 d-flex align-items-center justify-content-end gap-2">
                                        <span class="status-pill <?= $etatPill ?>">
                                            <span class="dot"></span>
                                            <span><?= htmlspecialchars($loc['statut']) ?></span>
                                        </span>
                                        <?php if ($loc['statut'] === 'En attente'): ?>
                                            <a href="<?= BASE_URL ?>/index.php?action=location_status&id=<?= $loc['id_location'] ?>&statut=Validée" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                                                Valider
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>/index.php?action=pdf_contrat&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-2 py-1" style="font-size: 0.72rem; border-color: rgba(255,255,255,0.2);">
                                                PDF
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Colonne droite : Raccourcis d'administration & Modules -->
            <div class="col-12 col-xl-4 col-lg-5">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-grid text-primary fs-5"></i>
                        <h5 class="fw-bold text-white mb-0" style="font-family: var(--font-display);">Modules de gestion</h5>
                    </div>

                    <!-- Module 1 : Stock / Équipements -->
                    <a href="<?= BASE_URL ?>/index.php?action=equipements_admin" class="cat-luminous-card text-decoration-none p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="cat-tile-icon">
                                <i class="bi bi-tools"></i>
                            </div>
                            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: rgba(0, 145, 255, 0.15); color: #38bdf8; border: 1px solid rgba(0, 145, 255, 0.25);">
                                <?= $totalEquipements ?> matériels
                            </span>
                        </div>
                        <h5 class="cat-title-text mb-1">Inventaire &amp; Stocks</h5>
                        <p class="cat-desc-text mb-0">Gestion des équipements, stocks et seuils d'alertes.</p>
                    </a>

                    <!-- Module 2 : Catégories -->
                    <a href="<?= BASE_URL ?>/index.php?action=categories" class="cat-luminous-card text-decoration-none p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="cat-tile-icon" style="background: rgba(139, 92, 246, 0.15); color: #a78bfa; border-color: rgba(139, 92, 246, 0.3);">
                                <i class="bi bi-tags"></i>
                            </div>
                            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: rgba(139, 92, 246, 0.15); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.25);">
                                Domaines
                            </span>
                        </div>
                        <h5 class="cat-title-text mb-1">Gestion des Catégories</h5>
                        <p class="cat-desc-text mb-0">Organisation taxonomique et répartition globale du parc.</p>
                    </a>

                    <!-- Module 3 : Utilisateurs & Rôles -->
                    <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                        <a href="<?= BASE_URL ?>/index.php?action=users_admin" class="cat-luminous-card text-decoration-none p-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="cat-tile-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border-color: rgba(16, 185, 129, 0.3);">
                                    <i class="bi bi-people"></i>
                                </div>
                                <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.25);">
                                    <?= $totalUsers ?> comptes
                                </span>
                            </div>
                            <h5 class="cat-title-text mb-1">Comptes &amp; Utilisateurs</h5>
                            <p class="cat-desc-text mb-0">Contrôle d'accès des agents, responsables et clients.</p>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>