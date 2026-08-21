<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #FAC-<?= $location['id_location'] ?> - EquipLoc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; color: #1e293b; }
        .document-container { max-width: 850px; margin: 30px auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        .header-logo { border-bottom: 3px solid #10b981; padding-bottom: 20px; margin-bottom: 30px; }
        @media print {
            .no-print { display: none !important; }
            .document-container { box-shadow: none; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

<div class="container no-print text-end pt-3">
    <button onclick="window.print()" class="btn btn-success fw-bold">
        <i class="bi bi-printer"></i> Imprimer / Enregistrer en PDF
    </button>
</div>

<div class="document-container">
    <div class="header-logo d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-success mb-0">EquipLoc SARL</h2>
            <small class="text-muted">Facturation Officielle</small>
        </div>
        <div class="text-end">
            <h3 class="fw-bold text-dark mb-0">FACTURE</h3>
            <span class="badge bg-success">N° FAC-<?= sprintf('%05d', $location['id_location']) ?></span>
            <small class="d-block text-muted">Date d'émission : <?= date('d/m/Y') ?></small>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold text-muted text-uppercase small">Émetteur</h6>
            <strong>EquipLoc SARL</strong><br>
            MF : 1234567/A/M/000<br>
            Avenue Habib Bourguiba, Tunis
        </div>
        <div class="col-6 text-end">
            <h6 class="fw-bold text-muted text-uppercase small">Facturé à</h6>
            <strong><?= htmlspecialchars($location['client_nom'] . ' ' . $location['client_prenom']) ?></strong><br>
            Email : <?= htmlspecialchars($location['client_email']) ?><br>
            Tél : <?= htmlspecialchars($location['client_telephone'] ?? 'N/A') ?>
        </div>
    </div>

    <!-- Tableau détaillé -->
    <table class="table table-bordered mb-4">
        <thead class="table-dark">
            <tr>
                <th>Description</th>
                <th class="text-center">Qté</th>
                <th class="text-center">Prix Unitaire / Jour</th>
                <th class="text-center">Durée</th>
                <th class="text-end">Total HT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Location <?= htmlspecialchars($location['nom_equipement']) ?></strong>
                    <small class="d-block text-muted">Du <?= date('d/m/Y', strtotime($location['date_debut'])) ?> au <?= date('d/m/Y', strtotime($location['date_fin'])) ?></small>
                </td>
                <td class="text-center"><?= $location['quantite'] ?></td>
                <td class="text-center"><?= number_format($location['prix_par_jour'], 2) ?> DT</td>
                <td class="text-center"><?= $location['duree_jours'] ?> jour(s)</td>
                <td class="text-end fw-bold"><?= number_format($location['montant_total'], 2) ?> DT</td>
            </tr>

            <?php if ($location['frais_supplementaires'] > 0): ?>
                <tr>
                    <td colspan="4" class="text-danger fw-bold">
                        Frais supplémentaires (Rendu endommagé / retard)
                        <small class="d-block text-muted"><?= htmlspecialchars($location['motif_frais'] ?? '') ?></small>
                    </td>
                    <td class="text-end text-danger fw-bold">+<?= number_format($location['frais_supplementaires'], 2) ?> DT</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="row">
        <div class="col-6">
            <div class="p-3 bg-light rounded-3 border">
                <h6 class="fw-bold text-dark mb-1">Mode de Paiement :</h6>
                <p class="mb-0 small text-muted">Paiement au comptoir / Espèces ou Carte Bancaire</p>
                <small class="text-success fw-bold">Statut du dossier : <?= htmlspecialchars($location['statut']) ?></small>
            </div>
        </div>
        <div class="col-6">
            <table class="table table-borderless text-end">
                <tr>
                    <td class="fw-bold">Sous-total HT :</td>
                    <td><?= number_format($location['montant_total'], 2) ?> DT</td>
                </tr>
                <tr>
                    <td class="fw-bold">Frais Annexes :</td>
                    <td><?= number_format($location['frais_supplementaires'], 2) ?> DT</td>
                </tr>
                <tr class="border-top border-2">
                    <td class="fs-5 fw-bold text-success">TOTAL À PAYER :</td>
                    <td class="fs-5 fw-bold text-success"><?= number_format($location['montant_total'] + $location['frais_supplementaires'], 2) ?> DT</td>
                </tr>
            </table>
        </div>
    </div>
</div>

</body>
</html>
