<?php
$pageTitle = "Créer un compte Client";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 text-center">
                    <h3 class="fw-bold mb-1">Inscription Client</h3>
                    <p class="text-muted small mb-0">Créez votre espace personnel pour louer du matériel</p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $err): ?>
                                    <li><?= htmlspecialchars($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/index.php?action=register" method="POST" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nom" name="nom" required value="<?= htmlspecialchars($data['nom'] ?? '') ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required value="<?= htmlspecialchars($data['prenom'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="telephone" name="telephone" placeholder="ex: 98123456" value="<?= htmlspecialchars($data['telephone'] ?? '') ?>">
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="mot_de_passe" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required minlength="6">
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_mdp" class="form-label">Confirmer mot de passe <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_mdp" name="confirm_mdp" required minlength="6">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                            <i class="bi bi-person-check-fill me-2"></i> S'inscrire
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-light p-3 text-center border-0">
                    <span class="text-muted">Déjà un compte ?</span>
                    <a href="<?= BASE_URL ?>/index.php?action=login" class="fw-bold text-primary text-decoration-none">Se connecter</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
