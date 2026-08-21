<?php
$isEdit = isset($equipement) && !empty($equipement['id_equipement']);
$pageTitle = $isEdit ? "Modifier Équipement" : "Ajouter Équipement";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4">
                    <h3 class="fw-bold mb-0"><?= $pageTitle ?></h3>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/index.php?action=<?= $isEdit ? 'equipement_edit&id=' . $equipement['id_equipement'] : 'equipement_create' ?>" 
                          method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="nom_equipement" class="form-label font-weight-bold">Nom de l'Équipement <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom_equipement" name="nom_equipement" required
                                       value="<?= htmlspecialchars($_POST['nom_equipement'] ?? ($equipement['nom_equipement'] ?? '')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="id_categorie" class="form-label font-weight-bold">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_categorie" name="id_categorie" required>
                                    <option value="">Sélectionner...</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id_categorie'] ?>" 
                                            <?= (($_POST['id_categorie'] ?? ($equipement['id_categorie'] ?? '')) == $cat['id_categorie']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nom_categorie']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label font-weight-bold">Description technique</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? ($equipement['description'] ?? '')) ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="prix_par_jour" class="form-label font-weight-bold">Prix par Jour (DT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="prix_par_jour" name="prix_par_jour" required
                                       value="<?= htmlspecialchars($_POST['prix_par_jour'] ?? ($equipement['prix_par_jour'] ?? '')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="stock" class="form-label font-weight-bold">Stock Disponible <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" id="stock" name="stock" required
                                       value="<?= htmlspecialchars($_POST['stock'] ?? ($equipement['stock'] ?? 1)) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="seuil_alerte" class="form-label font-weight-bold">Seuil d'Alerte <span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control" id="seuil_alerte" name="seuil_alerte" required
                                       value="<?= htmlspecialchars($_POST['seuil_alerte'] ?? ($equipement['seuil_alerte'] ?? 5)) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="etat" class="form-label font-weight-bold">État Imposé <span class="text-danger">*</span></label>
                                <select class="form-select" id="etat" name="etat" required>
                                    <?php foreach (['Disponible', 'En location', 'En maintenance', 'Endommagé'] as $et): ?>
                                        <option value="<?= $et ?>" <?= (($_POST['etat'] ?? ($equipement['etat'] ?? 'Disponible')) === $et) ? 'selected' : '' ?>>
                                            <?= $et ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label font-weight-bold">Image de l'équipement</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="<?= BASE_URL ?>/index.php?action=equipements_admin" class="btn btn-outline-secondary w-50 fw-bold">Retour</a>
                            <button type="submit" class="btn btn-primary w-50 fw-bold">
                                <i class="bi bi-save me-2"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Créer l\'équipement' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
