<?php
$isEdit = isset($equipement) && !empty($equipement['id_equipement']);
$pageTitle = $isEdit ? "Modifier Équipement" : "Ajouter Équipement";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5 animate-rise">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-elevated rounded-4 overflow-hidden">
                <div class="p-4 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(155deg, #131f33 0%, #172740 100%) !important;">
                    <div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-2">Inventaire & Stock</span>
                        <h4 class="fw-bold mb-0 text-white" style="font-family: var(--font-display);"><?= $pageTitle ?></h4>
                    </div>
                    <div class="cat-tile-icon" style="background: rgba(0, 145, 255, 0.15); color: #38bdf8;">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger rounded-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/index.php?action=<?= $isEdit ? 'equipement_edit&id=' . $equipement['id_equipement'] : 'equipement_create' ?>" 
                          method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="nom_equipement" class="form-label fw-bold text-dark">Nom de l'Équipement <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3 py-2" id="nom_equipement" name="nom_equipement" required
                                       value="<?= htmlspecialchars($_POST['nom_equipement'] ?? ($equipement['nom_equipement'] ?? '')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="id_categorie" class="form-label fw-bold text-dark">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 py-2" id="id_categorie" name="id_categorie" required>
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
                            <label for="description" class="form-label fw-bold text-dark">Description technique</label>
                            <textarea class="form-control rounded-3" id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? ($equipement['description'] ?? '')) ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="prix_par_jour" class="form-label fw-bold text-dark">Prix par Jour (DT) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" class="form-control rounded-3 py-2" id="prix_par_jour" name="prix_par_jour" required
                                       value="<?= htmlspecialchars($_POST['prix_par_jour'] ?? ($equipement['prix_par_jour'] ?? '')) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="stock" class="form-label fw-bold text-dark">Stock Disponible <span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control rounded-3 py-2" id="stock" name="stock" required
                                       value="<?= htmlspecialchars($_POST['stock'] ?? ($equipement['stock'] ?? 1)) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="seuil_alerte" class="form-label fw-bold text-dark">Seuil d'Alerte <span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control rounded-3 py-2" id="seuil_alerte" name="seuil_alerte" required
                                       value="<?= htmlspecialchars($_POST['seuil_alerte'] ?? ($equipement['seuil_alerte'] ?? 5)) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="etat" class="form-label fw-bold text-dark">État Imposé <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3 py-2" id="etat" name="etat" required>
                                    <?php foreach (['Disponible', 'En location', 'En maintenance', 'Endommagé'] as $et): ?>
                                        <option value="<?= $et ?>" <?= (($_POST['etat'] ?? ($equipement['etat'] ?? 'Disponible')) === $et) ? 'selected' : '' ?>>
                                            <?= $et ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label fw-bold text-dark">Image de l'équipement</label>
                                <input type="file" class="form-control rounded-3 py-2" id="image" name="image" accept="image/*">
                            </div>
                        </div>

                        <div class="d-flex gap-3 pt-2">
                            <a href="<?= BASE_URL ?>/index.php?action=equipements_admin" class="btn btn-outline-secondary w-50 fw-semibold rounded-pill py-2">Retour</a>
                            <button type="submit" class="btn btn-primary w-50 fw-bold rounded-pill py-2 shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> <?= $isEdit ? 'Enregistrer les modifications' : 'Créer l\'équipement' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
