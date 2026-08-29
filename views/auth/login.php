<?php
$pageTitle = "Connexion — EquipLoc";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<div class="cat-page-wrapper py-5 animate-rise d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-5 col-xl-4">
                <!-- Carte de Connexion Luminous -->
                <div class="cat-luminous-card p-4 p-md-5">
                    <!-- En-tête de la carte -->
                    <div class="text-center mb-4">
                        <div class="cat-tile-icon mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.4rem;">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div class="cat-dash-badge mb-1">AUTHENTIFICATION</div>
                        <h2 class="cat-main-title fs-3 mb-1">
                            Espace <span class="cat-accent-word">Connexion</span>
                        </h2>
                        <p class="cat-subtitle small mb-0">
                            Accédez à vos locations, inventaires et services EquipLoc
                        </p>
                    </div>

                    <!-- Affichage de l'erreur si présente -->
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger rounded-3 py-2 px-3 small d-flex align-items-center gap-2 mb-4" role="alert">
                            <i class="bi bi-exclamation-circle-fill text-danger fs-5 flex-shrink-0"></i>
                            <div><?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Formulaire de Connexion -->
                    <form action="<?= BASE_URL ?>/index.php?action=login" method="POST" class="needs-validation" novalidate>
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="cat-kpi-tag mb-2 d-block">Adresse Email</label>
                            <div class="position-relative">
                                <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="email" class="cat-search-input-dark" id="email" name="email" 
                                       placeholder="ex: client@gmail.com" required 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       autocomplete="email">
                            </div>
                        </div>

                        <!-- Mot de Passe -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label for="mot_de_passe" class="cat-kpi-tag d-block mb-0">Mot de Passe</label>
                            </div>
                            <div class="position-relative">
                                <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="password" class="cat-search-input-dark" id="mot_de_passe" name="mot_de_passe" 
                                       placeholder="••••••••" required
                                       style="padding-right: 46px;"
                                       autocomplete="current-password">
                                <button type="button" id="togglePasswordBtn" class="btn btn-link position-absolute top-50 end-0 translate-middle-y me-2 text-muted text-decoration-none p-1" title="Afficher/Masquer le mot de passe">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Bouton de Soumission -->
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Se Connecter</span>
                        </button>
                    </form>

                    <!-- Pied de carte -->
                    <div class="text-center pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
                        <span class="text-muted small">Pas encore de compte ?</span>
                        <a href="<?= BASE_URL ?>/index.php?action=register" class="fw-bold text-primary text-decoration-none small ms-1">
                            Créer un compte Client
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script pour basculer la visibilité du mot de passe -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const pwdInput = document.getElementById('mot_de_passe');
    const pwdIcon = document.getElementById('togglePasswordIcon');

    if (toggleBtn && pwdInput && pwdIcon) {
        toggleBtn.addEventListener('click', () => {
            const isPassword = pwdInput.type === 'password';
            pwdInput.type = isPassword ? 'text' : 'password';
            pwdIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
