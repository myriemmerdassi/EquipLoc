<?php
$pageTitle = "Réservation de Matériel";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4">
                    <h3 class="fw-bold mb-1">Demande de Location</h3>
                    <p class="text-muted small mb-0">Réservez votre matériel en choisissant vos dates</p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-4 border">
                        <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($equipement['image']) ?>" 
                             onerror="this.src='https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=60';"
                             alt="Équipement" style="width: 90px; height: 90px; object-fit: cover;" class="rounded-3">
                        <div>
                            <span class="badge bg-primary mb-1"><?= htmlspecialchars($equipement['nom_categorie']) ?></span>
                            <h4 class="fw-bold mb-1"><?= htmlspecialchars($equipement['nom_equipement']) ?></h4>
                            <span class="text-primary fw-bold fs-5" id="prix_par_jour" data-prix="<?= $equipement['prix_par_jour'] ?>">
                                <?= number_format($equipement['prix_par_jour'], 2) ?> DT / jour
                            </span>
                            <span class="ms-3 badge bg-success">Stock disponible: <?= $equipement['stock'] ?></span>
                        </div>
                    </div>

                    <form action="<?= BASE_URL ?>/index.php?action=reserve" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="id_equipement" value="<?= $equipement['id_equipement'] ?>">

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label font-weight-bold">Date de début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-lg" id="date_debut" name="date_debut" 
                                       min="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($_POST['date_debut'] ?? date('Y-m-d')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_fin" class="form-label font-weight-bold">Date de fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control form-control-lg" id="date_fin" name="date_fin" 
                                       min="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($_POST['date_fin'] ?? date('Y-m-d', strtotime('+1 day'))) ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="quantite" class="form-label font-weight-bold">Quantité souhaitée <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-lg" id="quantite" name="quantite" 
                                   min="1" max="<?= $equipement['stock'] ?>" value="1" required>
                        </div>

                        <!-- Calculateur automatique de montant -->
                        <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 p-4 rounded-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">Montant total estimé</h5>
                                    <small class="text-muted">Calculé en fonction de la durée sélectionnée</small>
                                </div>
                                <div class="text-end">
                                    <span class="display-6 fw-bold text-primary" id="montant_total_display">0.00 DT</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="<?= BASE_URL ?>/index.php?action=catalogue" class="btn btn-outline-secondary btn-lg w-50 fw-bold">
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg w-50 fw-bold">
                                <i class="bi bi-check-circle-fill me-2"></i> Valider la réservation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
