<?php
$user = currentUser();
$action = sanitize($_GET['action'] ?? 'catalogue');
?>
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid px-lg-5 px-3">
        <!-- Logo EquipLoc -->
        <a class="navbar-brand d-flex align-items-center gap-2 me-4" href="<?= BASE_URL ?>/index.php">
            <div class="logo-icon-circle">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Top Cube -->
                    <polygon points="12,2.5 16.5,5 12,7.5 7.5,5" fill="#ffffff"/>
                    <polygon points="7.5,5 12,7.5 12,12 7.5,9.5" fill="#e6f2ff"/>
                    <polygon points="12,7.5 16.5,5 16.5,9.5 12,12" fill="#cce5ff"/>

                    <!-- Bottom Left Cube -->
                    <polygon points="6.5,10.5 11,13 6.5,15.5 2,13" fill="#ffffff"/>
                    <polygon points="2,13 6.5,15.5 6.5,20 2,17.5" fill="#e6f2ff"/>
                    <polygon points="6.5,15.5 11,13 11,17.5 6.5,20" fill="#cce5ff"/>

                    <!-- Bottom Right Cube -->
                    <polygon points="17.5,10.5 22,13 17.5,15.5 13,13" fill="#ffffff"/>
                    <polygon points="13,13 17.5,15.5 17.5,20 13,17.5" fill="#e6f2ff"/>
                    <polygon points="17.5,15.5 22,13 22,17.5 17.5,20" fill="#cce5ff"/>
                </svg>
            </div>
            <span class="brand-name">Equip<span class="brand-accent">Loc</span></span>
        </a>

        <!-- Toggle mobile -->
        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Navigation Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-links-wrapper">
                <li class="nav-item">
                    <a class="nav-link <?= ($action === 'catalogue') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=catalogue">
                        <i class="bi bi-grid nav-icon"></i>
                        <span>Catalogue</span>
                    </a>
                </li>

                <?php if (isLoggedIn()): ?>
                    <?php if (hasRole(ROLE_CLIENT)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= ($action === 'mes_locations') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=mes_locations">
                                <i class="bi bi-bag nav-icon"></i>
                                <span>Mes Locations</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (hasRole(ROLE_RESPONSABLE, ROLE_AGENT)): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-bold <?= ($action === 'dashboard') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=dashboard">
                                <i class="bi bi-speedometer2 nav-icon"></i>
                                <span>BackOffice</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($action === 'locations_admin') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=locations_admin">
                                <i class="bi bi-journal-text nav-icon"></i>
                                <span>Locations</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($action === 'equipements_admin') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=equipements_admin">
                                <i class="bi bi-tools nav-icon"></i>
                                <span>Stock / Équipements</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= ($action === 'categories') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=categories">
                                <i class="bi bi-tags nav-icon"></i>
                                <span>Catégories</span>
                            </a>
                        </li>
                        <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($action === 'users_admin') ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php?action=users_admin">
                                    <i class="bi bi-people nav-icon"></i>
                                    <span>Utilisateurs</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <!-- Zone utilisateur à droite -->
            <div class="d-flex align-items-center gap-3">
                <?php if (isLoggedIn()): 
                    $prenomInit = mb_substr($user['prenom'] ?? '', 0, 1);
                    $nomInit = mb_substr($user['nom'] ?? '', 0, 1);
                    $initials = strtoupper($prenomInit . $nomInit) ?: 'U';
                    $roleLabel = match($user['role'] ?? '') {
                        'agent' => 'Agent location',
                        'responsable' => 'Responsable admin',
                        'client' => 'Client',
                        default => ucfirst($user['role'] ?? 'Utilisateur')
                    };
                ?>
                    <div class="user-profile-pill d-flex align-items-center">
                        <div class="user-avatar-circle d-flex align-items-center justify-content-center fw-bold">
                            <span><?= htmlspecialchars($initials) ?></span>
                        </div>
                        <div class="user-info-text d-flex flex-column ms-2">
                            <span class="user-name"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></span>
                            <span class="user-role"><?= htmlspecialchars($roleLabel) ?></span>
                        </div>
                    </div>
                    <a href="<?= BASE_URL ?>/index.php?action=logout" class="btn btn-logout-outline d-flex align-items-center gap-2" title="Se déconnecter">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Déconnexion</span>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/index.php?action=login" class="btn btn-login-outline">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Connexion
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?action=register" class="btn btn-register-filled">
                        <i class="bi bi-person-plus-fill me-1"></i> S'inscrire
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?= displayFlash() ?>
</div>
