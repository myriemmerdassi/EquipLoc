<?php
$user = currentUser();
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>/index.php">
            <i class="bi bi-box-seam-fill fs-3 text-primary"></i>
            <span class="fs-4 fw-bold">EquipLoc</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= (!isset($_GET['action']) || $_GET['action'] === 'catalogue') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=catalogue">
                        <i class="bi bi-grid-fill me-1"></i> Catalogue
                    </a>
                </li>

                <?php if (isLoggedIn()): ?>
                    <?php if (hasRole(ROLE_CLIENT)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= (($_GET['action'] ?? '') === 'mes_locations') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=mes_locations">
                                <i class="bi bi-bag-check-fill me-1"></i> Mes Locations
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (hasRole(ROLE_RESPONSABLE, ROLE_AGENT)): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-bold <?= (($_GET['action'] ?? '') === 'dashboard') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=dashboard">
                                <i class="bi bi-speedometer2 me-1"></i> BackOffice
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (($_GET['action'] ?? '') === 'locations_admin') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=locations_admin">
                                <i class="bi bi-journal-text me-1"></i> Locations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (($_GET['action'] ?? '') === 'equipements_admin') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=equipements_admin">
                                <i class="bi bi-tools me-1"></i> Stock / Équipements
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (($_GET['action'] ?? '') === 'categories') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=categories">
                                <i class="bi bi-tags-fill me-1"></i> Catégories
                            </a>
                        </li>
                        <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= (($_GET['action'] ?? '') === 'users_admin') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=users_admin">
                                    <i class="bi bi-people-fill me-1"></i> Utilisateurs
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if (isLoggedIn()): ?>
                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                        <small class="d-block text-warning" style="font-size: 0.7rem;">
                            <?= str_replace('_', ' ', strtoupper($user['role'])) ?>
                        </small>
                    </span>
                    <a href="<?= BASE_URL ?>/index.php?action=logout" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/index.php?action=login" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="bi bi-box-arrow-in-right"></i> Connexion
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?action=register" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-person-plus-fill"></i> S'inscrire
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?= displayFlash() ?>
</div>
