<?php
$pageTitle = "Gestion des Catégories";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-tags-fill text-primary me-2"></i> Gestion des Catégories</h2>
            <p class="text-muted">Organisez les équipements par domaine d'activité</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?action=categorie_create" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Nouvelle Catégorie
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom de la Catégorie</th>
                        <th>Description</th>
                        <th>Équipements associés</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="fw-bold">#<?= $cat['id_categorie'] ?></td>
                            <td><strong class="text-dark fs-6"><?= htmlspecialchars($cat['nom_categorie']) ?></strong></td>
                            <td class="text-muted"><?= htmlspecialchars($cat['description'] ?? 'Aucune description') ?></td>
                            <td><span class="badge bg-info text-white rounded-pill"><?= $cat['nb_equipements'] ?> matériel(s)</span></td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/index.php?action=categorie_edit&id=<?= $cat['id_categorie'] ?>" class="btn btn-outline-warning btn-sm me-1">
                                    <i class="bi bi-pencil-fill"></i> Modifier
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?action=categorie_delete&id=<?= $cat['id_categorie'] ?>" 
                                   onclick="return confirm('Supprimer cette catégorie ?');" 
                                   class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
