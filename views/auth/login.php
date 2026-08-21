<?php
$pageTitle = "Connexion";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 text-center position-relative">
                    <h3 class="fw-bold mb-1">Espace Connexion</h3>
                    <p class="text-muted small mb-0">Accédez à votre espace Client, Agent ou Responsable</p>
                </div>
                <div class="card-body p-4 p-md-5 bg-white">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/index.php?action=login" method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="email" class="form-label font-weight-bold">Adresse Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-envelope-fill text-muted"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="ex: client@gmail.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="mot_de_passe" class="form-label font-weight-bold">Mot de Passe</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" placeholder="Votre mot de passe" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Se Connecter
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-light p-3 text-center border-0">
                    <span class="text-muted">Pas encore de compte ?</span>
                    <a href="<?= BASE_URL ?>/index.php?action=register" class="fw-bold text-primary text-decoration-none">Créer un compte Client</a>
                </div>
            </div>

            <!-- Comptes de démonstration pour le professeur / correcteur -->
            <div class="card border-0 bg-info bg-opacity-10 mt-4 rounded-3 p-3">
                <div class="d-flex align-items-center gap-2 mb-2 text-info-emphasis fw-bold">
                    <i class="bi bi-info-circle-fill"></i> Comptes de démo (Mot de passe: <code>password123</code>)
                </div>
                <ul class="list-unstyled mb-0 small text-dark">
                    <li>🔹 <strong>Responsable :</strong> <code>responsable@equiploc.tn</code></li>
                    <li>🔹 <strong>Agent Location :</strong> <code>agent@equiploc.tn</code></li>
                    <li>🔹 <strong>Client :</strong> <code>client@gmail.com</code></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
