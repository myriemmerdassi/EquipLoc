<?php
$pageTitle = "Créer une Location au Comptoir";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4">
                    <h3 class="fw-bold mb-1"><i class="bi bi-shop me-2 text-success"></i> Espace Agent : Location au Comptoir</h3>
                    <p class="text-muted small mb-0">Recevez le client au comptoir, vérifiez la disponibilité et créez directement la location</p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/index.php?action=location_comptoir" method="POST" class="needs-validation" novalidate>
                        
                        <div class="mb-4">
                            <label for="id_client" class="form-label font-weight-bold">Sélectionner le Client <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="id_client" name="id_client" required>
                                <option value="">Choisir un client enregistré...</option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id_utilisateur'] ?>" <?= (($_POST['id_client'] ?? '') == $c['id_utilisateur']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nom'] . ' ' . $c['prenom']) ?> (<?= htmlspecialchars($c['email']) ?> - Tél: <?= htmlspecialchars($c['telephone'] ?? 'N/A') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="id_equipement" class="form-label font-weight-bold">Sélectionner l'Équipement <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="id_equipement" name="id_equipement" required>
                                <option value="">Choisir un équipement...</option>
                                <?php foreach ($equipements as $eq): ?>
                                    <option value="<?= $eq['id_equipement'] ?>" 
                                            data-prix="<?= $eq['prix_par_jour'] ?>" 
                                            data-stock="<?= $eq['stock'] ?>"
                                            <?= ($eq['stock'] <= 0 || $eq['etat'] !== 'Disponible') ? 'disabled' : '' ?>
                                            <?= (($_POST['id_equipement'] ?? '') == $eq['id_equipement']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($eq['nom_equipement']) ?> - <?= number_format($eq['prix_par_jour'], 2) ?> DT/j (Stock: <?= $eq['stock'] ?> - State: <?= $eq['etat'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label font-weight-bold">Date Début <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                       value="<?= htmlspecialchars($_POST['date_debut'] ?? date('Y-m-d')) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="date_fin" class="form-label font-weight-bold">Date Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                       value="<?= htmlspecialchars($_POST['date_fin'] ?? date('Y-m-d', strtotime('+1 day'))) ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="quantite" class="form-label font-weight-bold">Quantité <span class="text-danger">*</span></label>
                            <input type="number" min="1" class="form-control" id="quantite" name="quantite" value="1" required>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="<?= BASE_URL ?>/index.php?action=locations_admin" class="btn btn-outline-secondary w-50 fw-bold">Annuler</a>
                            <button type="submit" class="btn btn-success w-50 fw-bold">
                                <i class="bi bi-check-circle-fill me-2"></i> Créer et Valider la Location
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
