<?php
$pageTitle = "Gestion des Locations & Comptoir";
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/navbar.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-journal-text text-primary me-2"></i> Suivi des Locations</h2>
            <p class="text-muted">Gestion des réservations en ligne, validation au comptoir et retours d'équipements</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?action=location_comptoir" class="btn btn-success fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Créer Location Comptoir
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Client (Locataire)</th>
                        <th>Équipement & Qté</th>
                        <th>Période (Début -> Fin)</th>
                        <th>Montant & Frais</th>
                        <th>Statut</th>
                        <th class="text-end">Actions & Impression PDF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $loc): ?>
                        <tr>
                            <td class="fw-bold">#<?= $loc['id_location'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($loc['client_nom'] . ' ' . $loc['client_prenom']) ?></strong>
                                <small class="d-block text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($loc['client_email']) ?></small>
                                <small class="d-block text-muted"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($loc['client_telephone'] ?? 'N/A') ?></small>
                            </td>
                            <td>
                                <strong class="d-block text-dark"><?= htmlspecialchars($loc['nom_equipement']) ?></strong>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($loc['nom_categorie']) ?> (Qté: <?= $loc['quantite'] ?>)</span>
                            </td>
                            <td>
                                <span class="d-block"><i class="bi bi-calendar-event me-1 text-primary"></i> <?= date('d/m/Y', strtotime($loc['date_debut'])) ?></span>
                                <span class="d-block"><i class="bi bi-calendar-check me-1 text-success"></i> <?= date('d/m/Y', strtotime($loc['date_fin'])) ?></span>
                                <small class="text-muted"><?= $loc['duree_jours'] ?> jour(s)</small>
                            </td>
                            <td>
                                <strong class="text-primary fs-6"><?= number_format($loc['montant_total'] + $loc['frais_supplementaires'], 2) ?> DT</strong>
                                <?php if ($loc['frais_supplementaires'] > 0): ?>
                                    <small class="d-block text-danger font-weight-bold">+<?= number_format($loc['frais_supplementaires'], 2) ?> DT (Frais)</small>
                                    <small class="d-block text-muted" style="font-size:0.75rem;"><?= htmlspecialchars($loc['motif_frais'] ?? '') ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $badge = match($loc['statut']) {
                                    'En attente' => 'bg-warning text-dark',
                                    'Validée', 'En cours' => 'bg-info text-white',
                                    'Terminée' => 'bg-success text-white',
                                    'Annulée' => 'bg-danger text-white',
                                    default => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badge ?> px-3 py-2 rounded-pill"><?= htmlspecialchars($loc['statut']) ?></span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm mb-1">
                                    <?php if ($loc['statut'] === 'En attente'): ?>
                                        <a href="<?= BASE_URL ?>/index.php?action=location_status&id=<?= $loc['id_location'] ?>&statut=Validée" class="btn btn-success" title="Valider la location">
                                            <i class="bi bi-check-lg me-1"></i> Valider
                                        </a>
                                        <a href="<?= BASE_URL ?>/index.php?action=location_status&id=<?= $loc['id_location'] ?>&statut=Annulée" class="btn btn-outline-danger" title="Annuler">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    <?php elseif (in_array($loc['statut'], ['Validée', 'En cours'])): ?>
                                        <a href="<?= BASE_URL ?>/index.php?action=location_retour&id=<?= $loc['id_location'] ?>" class="btn btn-warning fw-bold text-dark" title="Diagnostiquer le retour matériel">
                                            <i class="bi bi-box-arrow-in-left me-1"></i> Diagnostic Retour
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Trio PDF imposé (Contrat, Facture, Reçu) -->
                                <div class="btn-group btn-group-sm d-block">
                                    <a href="<?= BASE_URL ?>/index.php?action=pdf_contrat&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Contrat PDF">
                                        Contrat PDF
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=pdf_facture&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-success" title="Facture PDF">
                                        Facture PDF
                                    </a>
                                    <a href="<?= BASE_URL ?>/index.php?action=pdf_recu&id=<?= $loc['id_location'] ?>" target="_blank" class="btn btn-sm btn-outline-info" title="Reçu PDF">
                                        Reçu PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
