<?php
$pageTitle = "Gestion des Utilisateurs";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary me-2"></i> Gestion des Comptes & Rôles</h2>
            <p class="text-muted">Gérez les Responsables Inventaire, Agents de Location et Clients</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?action=user_create" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Nouvel Utilisateur
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom & Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle Imposé</th>
                        <th>Date de création</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td class="fw-bold">#<?= $u['id_utilisateur'] ?></td>
                            <td><strong class="text-dark"><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></strong></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['telephone'] ?? 'N/A') ?></td>
                            <td>
                                <?php
                                $badgeRole = match($u['role']) {
                                    'responsable_inventaire' => 'bg-danger text-white',
                                    'agent_location' => 'bg-warning text-dark',
                                    'client' => 'bg-primary text-white',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeRole ?> px-3 py-2">
                                    <?= htmlspecialchars(str_replace('_', ' ', strtoupper($u['role']))) ?>
                                </span>
                            </td>
                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($u['date_creation'])) ?></small></td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/index.php?action=user_edit&id=<?= $u['id_utilisateur'] ?>" class="btn btn-outline-warning btn-sm me-1">
                                    <i class="bi bi-pencil-fill"></i> Modifier
                                </a>
                                <?php if ($u['id_utilisateur'] !== $_SESSION['user']['id_utilisateur']): ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=user_delete&id=<?= $u['id_utilisateur'] ?>" 
                                       onclick="return confirm('Supprimer cet utilisateur ?');" 
                                       class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
