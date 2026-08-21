<?php
$pageTitle = htmlspecialchars($equipement['nom_equipement']);
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<div class="container py-5">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/index.php?action=catalogue" class="text-decoration-none text-primary">Catalogue</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($equipement['nom_equipement']) ?></li>
        </ol>
    </nav>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        <div class="row g-0">
            <!-- Image de l'équipement -->
            <div class="col-lg-6 bg-light d-flex align-items-center justify-content-center p-4">
                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($equipement['image']) ?>" 
                     onerror="this.src='https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=60';"
                     alt="<?= htmlspecialchars($equipement['nom_equipement']) ?>"
                     class="img-fluid rounded-4 shadow-sm" style="max-height: 400px; object-fit: cover;">
            </div>

            <!-- Détails de l'équipement -->
            <div class="col-lg-6 p-4 p-md-5 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-secondary px-3 py-2 fs-6"><?= htmlspecialchars($equipement['nom_categorie']) ?></span>
                        <?php
                        $badgeClass = match($equipement['etat']) {
                            'Disponible' => 'badge-disponible',
                            'En location' => 'badge-en-location',
                            'En maintenance' => 'badge-en-maintenance',
                            'Endommagé' => 'badge-endommage',
                            default => 'badge-disponible'
                        };
                        ?>
                        <span class="badge-etat <?= $badgeClass ?>"><?= htmlspecialchars($equipement['etat']) ?></span>
                    </div>

                    <h2 class="display-6 fw-bold mb-3"><?= htmlspecialchars($equipement['nom_equipement']) ?></h2>
                    
                    <p class="text-muted lead fs-6 mb-4">
                        <?= nl2br(htmlspecialchars($equipement['description'] ?? 'Aucune description fournie.')) ?>
                    </p>

                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <span class="small text-muted d-block">Prix de la location</span>
                                <span class="fs-4 fw-bold text-primary"><?= number_format($equipement['prix_par_jour'], 2) ?> DT / jour</span>
                            </div>
                            <div class="col-6">
                                <span class="small text-muted d-block">Stock disponible</span>
                                <span class="fs-4 fw-bold <?= $equipement['stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $equipement['stock'] ?> unité(s)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions contextuelles -->
                <div>
                    <?php if (isLoggedIn() && hasRole(ROLE_CLIENT)): ?>
                        <?php if ($equipement['stock'] > 0 && $equipement['etat'] === 'Disponible'): ?>
                            <a href="<?= BASE_URL ?>/index.php?action=reserve&id_equipement=<?= $equipement['id_equipement'] ?>"
                               class="btn btn-primary btn-lg w-100 fw-bold rounded-3 py-3 shadow">
                                <i class="bi bi-calendar-plus-fill me-2"></i> Réserver cet équipement
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg w-100 fw-bold rounded-3 py-3" disabled>
                                <i class="bi bi-calendar-x me-2"></i> Non disponible actuellement
                            </button>
                        <?php endif; ?>

                    <?php elseif (isLoggedIn() && hasRole(ROLE_RESPONSABLE)): ?>
                        <a href="<?= BASE_URL ?>/index.php?action=equipement_edit&id=<?= $equipement['id_equipement'] ?>"
                           class="btn btn-warning btn-lg w-100 fw-bold rounded-3 py-3 text-dark">
                            <i class="bi bi-pencil-square me-2"></i> Gérer dans l'inventaire
                        </a>

                    <?php elseif (isLoggedIn() && hasRole(ROLE_AGENT)): ?>
                        <a href="<?= BASE_URL ?>/index.php?action=location_comptoir&id_equipement=<?= $equipement['id_equipement'] ?>"
                           class="btn btn-success btn-lg w-100 fw-bold rounded-3 py-3 <?= ($equipement['stock'] <= 0 || $equipement['etat'] !== 'Disponible') ? 'disabled' : '' ?>">
                            <i class="bi bi-shop me-2"></i> Louer au comptoir
                        </a>

                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/index.php?action=login"
                           class="btn btn-outline-primary btn-lg w-100 fw-bold rounded-3 py-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Connectez-vous pour réserver
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
