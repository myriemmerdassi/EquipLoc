<?php
$isEdit = isset($categorie) && !empty($categorie['id_categorie']);
$pageTitle = $isEdit ? "Modifier Catégorie" : "Nouvelle Catégorie";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5 animate-rise">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 shadow-elevated rounded-4 overflow-hidden">
                <div class="p-4 bg-dark text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(155deg, var(--navy) 0%, var(--navy-mid) 100%) !important;">
                    <div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-2">Taxonomie</span>
                        <h4 class="fw-bold mb-0" style="font-family: var(--font-display);"><?= $pageTitle ?></h4>
                    </div>
                    <div class="cat-icon-tile" style="background: rgba(0, 145, 255, 0.15); color: #38bdf8;">
                        <i class="bi bi-tags-fill"></i>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger rounded-3" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/index.php?action=<?= $isEdit ? 'categorie_edit&id=' . $categorie['id_categorie'] : 'categorie_create' ?>" method="POST">
                        <div class="mb-4">
                            <label for="nom_categorie" class="form-label fw-bold text-dark">Nom de la Catégorie <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3 py-2" id="nom_categorie" name="nom_categorie" placeholder="Ex: Audiovisuel, Informatique..." required
                                   value="<?= htmlspecialchars($_POST['nom_categorie'] ?? ($categorie['nom_categorie'] ?? '')) ?>">
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-dark">Description</label>
                            <textarea class="form-control rounded-3" id="description" name="description" rows="3" placeholder="Description courte du domaine d'activité..."><?= htmlspecialchars($_POST['description'] ?? ($categorie['description'] ?? '')) ?></textarea>
                        </div>

                        <div class="d-flex gap-3 pt-2">
                            <a href="<?= BASE_URL ?>/index.php?action=categories" class="btn btn-outline-secondary w-50 fw-semibold rounded-pill py-2">Annuler</a>
                            <button type="submit" class="btn btn-primary w-50 fw-bold rounded-pill py-2 shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                <?= $isEdit ? 'Enregistrer les modifications' : 'Créer la catégorie' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
