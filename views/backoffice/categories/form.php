<?php
$isEdit = isset($categorie) && !empty($categorie['id_categorie']);
$pageTitle = $isEdit ? "Modifier Catégorie" : "Nouvelle Catégorie";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
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

                    <form action="<?= BASE_URL ?>/index.php?action=<?= $isEdit ? 'categorie_edit&id=' . $categorie['id_categorie'] : 'categorie_create' ?>" method="POST">
                        <div class="mb-4">
                            <label for="nom_categorie" class="form-label font-weight-bold">Nom de la Catégorie <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="nom_categorie" name="nom_categorie" required
                                   value="<?= htmlspecialchars($_POST['nom_categorie'] ?? ($categorie['nom_categorie'] ?? '')) ?>">
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label font-weight-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($_POST['description'] ?? ($categorie['description'] ?? '')) ?></textarea>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="<?= BASE_URL ?>/index.php?action=categories" class="btn btn-outline-secondary w-50 fw-bold">Annuler</a>
                            <button type="submit" class="btn btn-primary w-50 fw-bold">
                                <?= $isEdit ? 'Enregistrer' : 'Créer Catégorie' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
