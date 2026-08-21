<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement #REC-<?= $location['id_location'] ?> - EquipLoc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; color: #1e293b; }
        .document-container { max-width: 850px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .header-logo { border-bottom: 3px solid #06b6d4; padding-bottom: 20px; margin-bottom: 30px; }
        @media print {
            .no-print { display: none !important; }
            .document-container { box-shadow: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container no-print text-end pt-3">
    <button onclick="window.print()" class="btn btn-info text-white fw-bold">
        <i class="bi bi-printer"></i> Imprimer / Enregistrer en PDF
    </button>
</div>

<div class="document-container">
    <div class="header-logo d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-info mb-0">EquipLoc SARL</h2>
            <small class="text-muted">Reçu Officiel de Règlement et Restitution</small>
        </div>
        <div class="text-end">
            <h3 class="fw-bold text-dark mb-0">REÇU DE PAIEMENT</h3>
            <span class="badge bg-info text-white">N° REC-<?= sprintf('%05d', $location['id_location']) ?></span>
            <small class="d-block text-muted">Date : <?= date('d/m/Y H:i') ?></small>
        </div>
    </div>

    <div class="alert alert-success d-flex align-items-center gap-3 mb-4">
        <i class="bi bi-check-circle-fill display-5"></i>
        <div>
            <h5 class="fw-bold mb-1">Paiement intégralement reçu</h5>
            <p class="mb-0 small">Nous vous remercions pour votre confiance. La somme ci-dessous a été réglée avec succès.</p>
        </div>
    </div>

    <table class="table table-bordered mb-4">
        <tbody>
            <tr>
                <th class="bg-light" style="width: 30%;">Reçu de M. / Mme :</th>
                <td><strong><?= htmlspecialchars($location['client_nom'] . ' ' . $location['client_prenom']) ?></strong></td>
            </tr>
            <tr>
                <th class="bg-light">Pour la location de :</th>
                <td><?= htmlspecialchars($location['nom_equipement']) ?> (x<?= $location['quantite'] ?>)</td>
            </tr>
            <tr>
                <th class="bg-light">Période effectuée :</th>
                <td>Du <?= date('d/m/Y', strtotime($location['date_debut'])) ?> au <?= date('d/m/Y', strtotime($location['date_fin'])) ?> (<?= $location['duree_jours'] ?> jours)</td>
            </tr>
            <tr>
                <th class="bg-light">Montant de la location :</th>
                <td><?= number_format($location['montant_total'], 2) ?> DT</td>
            </tr>
            <tr>
                <th class="bg-light">Frais de dommages / retard :</th>
                <td><?= number_format($location['frais_supplementaires'], 2) ?> DT</td>
            </tr>
            <tr class="table-active">
                <th class="fs-5 text-dark">Total Réglé :</th>
                <td class="fs-5 fw-bold text-primary"><?= number_format($location['montant_total'] + $location['frais_supplementaires'], 2) ?> DT</td>
            </tr>
            <?php if ($location['etat_retour']): ?>
                <tr>
                    <th class="bg-light">État du matériel au retour :</th>
                    <td>
                        <span class="badge bg-secondary"><?= htmlspecialchars($location['etat_retour']) ?></span>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="row pt-4 mt-4 border-top">
        <div class="col-6">
            <small class="text-muted d-block">Agent de comptoir :</small>
            <strong><?= htmlspecialchars(($location['agent_nom'] ?? 'EquipLoc') . ' ' . ($location['agent_prenom'] ?? 'Comptoir')) ?></strong>
        </div>
        <div class="col-6 text-end">
            <small class="text-muted d-block">Tampon & Cachet de l'entreprise :</small>
            <div class="border d-inline-block p-3 rounded-circle text-muted mt-2" style="width:100px; height:100px; line-height: 70px;">
                EquipLoc
            </div>
        </div>
    </div>
</div>

</body>
</html>
