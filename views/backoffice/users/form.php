<?php
$isEdit = isset($user) && !empty($user['id_utilisateur']);
$pageTitle = $isEdit ? "Modifier Utilisateur" : "Nouvel Utilisateur";
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

                    <form action="<?= BASE_URL ?>/index.php?action=<?= $isEdit ? 'user_edit&id=' . $user['id_utilisateur'] : 'user_create' ?>" method="POST" class="needs-validation" novalidate>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nom" class="form-label font-weight-bold">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? ($user['nom'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="prenom" class="form-label font-weight-bold">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required value="<?= htmlspecialchars($_POST['prenom'] ?? ($user['prenom'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label font-weight-bold">Adresse Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? ($user['email'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="telephone" class="form-label font-weight-bold">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? ($user['telephone'] ?? '')) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="role" class="form-label font-weight-bold">Rôle Imposé <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="client" <?= (($_POST['role'] ?? ($user['role'] ?? '')) === 'client') ? 'selected' : '' ?>>Client</option>
                                    <option value="agent_location" <?= (($_POST['role'] ?? ($user['role'] ?? '')) === 'agent_location') ? 'selected' : '' ?>>Agent de Location</option>
                                    <option value="responsable_inventaire" <?= (($_POST['role'] ?? ($user['role'] ?? '')) === 'responsable_inventaire') ? 'selected' : '' ?>>Responsable Inventaire</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="mot_de_passe" class="form-label font-weight-bold">Mot de passe <?= $isEdit ? '(Laisser vide pour conserver)' : '<span class="text-danger">*</span>' ?></label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" <?= $isEdit ? '' : 'required' ?>>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <a href="<?= BASE_URL ?>/index.php?action=users_admin" class="btn btn-outline-secondary w-50 fw-bold">Annuler</a>
                            <button type="submit" class="btn btn-primary w-50 fw-bold">
                                <?= $isEdit ? 'Enregistrer les modifications' : 'Créer l\'utilisateur' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
