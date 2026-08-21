<?php
$pageTitle = "Gestion des Équipements & Stocks";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tools text-primary me-2"></i> Inventaire des Équipements</h2>
            <p class="text-muted">Gérez les stocks, définissez les seuils d'alerte et modifiez les états du matériel</p>
        </div>
        <?php if (hasRole(ROLE_RESPONSABLE)): ?>
            <a href="<?= BASE_URL ?>/index.php?action=equipement_create" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Nouveau Matériel
            </a>
        <?php endif; ?>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom & Description</th>
                        <th>Catégorie</th>
                        <th>Prix/Jour</th>
                        <th>Stock & Seuil Alerte</th>
                        <th>État Imposé</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($equipements as $eq): ?>
                        <tr class="<?= $eq['stock'] <= $eq['seuil_alerte'] ? 'table-warning' : '' ?>">
                            <td class="fw-bold">#<?= $eq['id_equipement'] ?></td>
                            <td>
                                <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($eq['image']) ?>" 
                                     onerror="this.src='https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=600&auto=format&fit=crop&q=60';"
                                     style="width: 50px; height: 50px; object-fit: cover;" class="rounded-3">
                            </td>
                            <td>
                                <strong class="d-block text-dark"><?= htmlspecialchars($eq['nom_equipement']) ?></strong>
                                <small class="text-muted"><?= htmlspecialchars(mb_strimwidth($eq['description'] ?? '', 0, 50, "...")) ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($eq['nom_categorie']) ?></span></td>
                            <td class="fw-bold text-primary"><?= number_format($eq['prix_par_jour'], 2) ?> DT</td>
                            <td>
                                <span class="fw-bold <?= $eq['stock'] <= $eq['seuil_alerte'] ? 'text-danger' : 'text-success' ?>">
                                    Stock : <?= $eq['stock'] ?>
                                </span>
                                <small class="d-block text-muted">Seuil d'alerte : <?= $eq['seuil_alerte'] ?></small>
                            </td>
                            <td>
                                <?php
                                $badgeClass = match($eq['etat']) {
                                    'Disponible' => 'bg-success',
                                    'En location' => 'bg-primary',
                                    'En maintenance' => 'bg-warning text-dark',
                                    'Endommagé' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?> px-3 py-2"><?= htmlspecialchars($eq['etat']) ?></span>
                            </td>
                            <td class="text-end">
                                <?php if (hasRole(ROLE_RESPONSABLE)): ?>
                                    <a href="<?= BASE_URL ?>/index.php?action=equipement_edit&id=<?= $eq['id_equipement'] ?>" class="btn btn-outline-warning btn-sm me-1">
                                        <i class="bi bi-pencil-fill"></i> Modifier
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=equipement_delete&id=<?= $eq['id_equipement'] ?>" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet équipement ?');" 
                                       class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Lecture seule</span>
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
