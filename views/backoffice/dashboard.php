<?php
$pageTitle = "Tableau de Bord BackOffice";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-speedometer2 text-primary me-2"></i> Tableau de Bord BackOffice</h2>
            <p class="text-muted">Espace de gestion pour Responsable Inventaire et Agent de Location</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/index.php?action=location_comptoir" class="btn btn-success fw-bold">
                <i class="bi bi-plus-circle me-1"></i> Créer Location au Comptoir
            </a>
            <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                <a href="<?= BASE_URL ?>/index.php?action=equipement_create" class="btn btn-primary fw-bold">
                    <i class="bi bi-box-seam me-1"></i> + Ajouter Équipement
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alertes Stock Faible (Exigence du sujet) -->
    <?php if (!empty($alertesStock)): ?>
        <div class="stock-alert-banner mb-4">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-exclamation-diamond-fill text-warning fs-2"></i>
                <div class="flex-grow-1">
                    <h5 class="fw-bold text-dark mb-1">Attention : Alertes de Stock Seuil Atteint !</h5>
                    <p class="mb-0 text-secondary">
                        <?= count($alertesStock) ?> équipement(s) ont atteint leur seuil critique de réapprovisionnement :
                        <strong>
                            <?php foreach ($alertesStock as $idx => $ast): ?>
                                <?= htmlspecialchars($ast['nom_equipement']) ?> (Stock: <?= $ast['stock'] ?>/Seuil: <?= $ast['seuil_alerte'] ?>)<?= $idx < count($alertesStock) - 1 ? ', ' : '' ?>
                            <?php endforeach; ?>
                        </strong>
                    </p>
                </div>
                <a href="<?= BASE_URL ?>/index.php?action=equipements_admin" class="btn btn-warning btn-sm fw-bold">Gérer le stock</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Cartes Métriques KPI -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="metric-card metric-card-purple">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-white-50 text-uppercase fw-bold small">Total Équipements</span>
                        <h2 class="display-6 fw-bold mb-0 mt-1"><?= $totalEquipements ?></h2>
                    </div>
                    <i class="bi bi-box-seam metric-icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="metric-card metric-card-cyan">