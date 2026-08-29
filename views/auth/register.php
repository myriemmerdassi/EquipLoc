<?php
$pageTitle = "Créer un compte Client — EquipLoc";
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/navbar.php';
?>

<div class="cat-page-wrapper py-5 animate-rise d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-7 col-xl-6">
                <!-- Carte d'Inscription Luminous -->
                <div class="cat-luminous-card p-4 p-md-5">
                    <!-- En-tête de la carte -->
                    <div class="text-center mb-4">
                        <div class="cat-tile-icon mx-auto mb-3" style="width: 54px; height: 54px; font-size: 1.4rem;">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div class="cat-dash-badge mb-1">NOUVEAU COMPTE</div>
                        <h2 class="cat-main-title fs-3 mb-1">
                            Inscription <span class="cat-accent-word">Client</span>
                        </h2>
                        <p class="cat-subtitle small mb-0">
                            Créez votre profil personnel pour louer du matériel professionnel en ligne
                        </p>
                    </div>

                    <!-- Affichage des erreurs si présentes -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger rounded-3 py-2 px-3 small mb-4" role="alert">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $err): ?>
                                    <li><?= htmlspecialchars($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Formulaire d'Inscription -->
                    <form action="<?= BASE_URL ?>/index.php?action=register" method="POST" class="needs-validation" novalidate>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label for="nom" class="cat-kpi-tag mb-2 d-block">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="cat-search-input-dark" id="nom" name="nom" 
                                       placeholder="ex: Merdassi" required 
                                       value="<?= htmlspecialchars($data['nom'] ?? '') ?>"
                                       style="padding-left: 20px;">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="prenom" class="cat-kpi-tag mb-2 d-block">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="cat-search-input-dark" id="prenom" name="prenom" 
                                       placeholder="ex: Myriem" required 
                                       value="<?= htmlspecialchars($data['prenom'] ?? '') ?>"
                                       style="padding-left: 20px;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="cat-kpi-tag mb-2 d-block">Adresse Email <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i class="bi bi-envelope position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="email" class="cat-search-input-dark" id="email" name="email" 
                                       placeholder="ex: client@gmail.com" required 
                                       value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="telephone" class="cat-kpi-tag mb-2 d-block">Téléphone</label>
                            <div class="position-relative">
                                <i class="bi bi-telephone position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" class="cat-search-input-dark" id="telephone" name="telephone" 
                                       placeholder="ex: +216 98 123 456" 
                                       value="<?= htmlspecialchars($data['telephone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12 col-sm-6">
                                <label for="mot_de_passe" class="cat-kpi-tag mb-2 d-block">Mot de passe <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <i class="bi bi-lock position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                    <input type="password" class="cat-search-input-dark" id="mot_de_passe" name="mot_de_passe" 
                                           placeholder="••••••••" required minlength="6">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label for="confirm_mdp" class="cat-kpi-tag mb-2 d-block">Confirmer <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <i class="bi bi-shield-check position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                    <input type="password" class="cat-search-input-dark" id="confirm_mdp" name="confirm_mdp" 
                                           placeholder="••••••••" required minlength="6">
                                </div>
                            </div>
                        </div>

                        <!-- Bouton d'Inscription -->
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2 mb-3">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Créer mon compte Client</span>
                        </button>
                    </form>

                    <!-- Pied de carte -->
                    <div class="text-center pt-3 border-top" style="border-color: rgba(226, 232, 240, 0.8) !important;">
                        <span class="text-muted small">Vous avez déjà un compte ?</span>
                        <a href="<?= BASE_URL ?>/index.php?action=login" class="fw-bold text-primary text-decoration-none small ms-1">
                            Se connecter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
