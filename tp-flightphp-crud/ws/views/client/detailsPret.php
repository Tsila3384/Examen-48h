    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8fafc;
            line-height: 1.6;
            color: #334155;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .detail-card {
            background: white;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .detail-item {
            padding: 1rem;
            border-left: 4px solid #3b82f6;
            background-color: #f8fafc;
            border-radius: 0 0.5rem 0.5rem 0;
        }

        .detail-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1.125rem;
            color: #1e293b;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .no-data {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Détails du Prêt</h2>
            <a href="<?= BASE_URL ?>/user/listePret" class="btn btn-secondary">
                ← Retour à la liste
            </a>
        </div>

        <?php if ($pretDetails): ?>
            <div class="detail-card">
                <h3 class="section-title">Informations générales</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">ID du Prêt</div>
                        <div class="detail-value">#<?= htmlspecialchars($pretDetails['pret_id'] ?? 'N/A') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Montant</div>
                        <div class="detail-value"><?= isset($pretDetails['montant_pret']) ? number_format($pretDetails['montant_pret'], 2, ',', ' ') : '0,00' ?> Ar</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Type de Prêt</div>
                        <div class="detail-value"><?= htmlspecialchars($pretDetails['type_pret'] ?? 'N/A') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Statut</div>
                        <div class="detail-value">
                            <span class="badge badge-info">
                                <?= htmlspecialchars($pretDetails['statut'] ?? 'N/A') ?>
                            </span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Date de demande</div>
                        <div class="detail-value"><?= isset($pretDetails['date_demande']) ? date('d/m/Y', strtotime($pretDetails['date_demande'])) : 'N/A' ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Durée</div>
                        <div class="detail-value"><?= htmlspecialchars($pretDetails['duree_mois'] ?? 'N/A') ?> mois</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Taux d'intérêt</div>
                        <div class="detail-value"><?= htmlspecialchars($pretDetails['taux_interet'] ?? 'N/A') ?>%</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Mensualité</div>
                        <div class="detail-value"><?= isset($pretDetails['montant']) ? number_format($pretDetails['montant'], 2, ',', ' ') : '0,00' ?> Ar</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Assurance mensuelle</div>
                        <div class="detail-value"><?= isset($pretDetails['montant_assurance']) ? number_format($pretDetails['montant_assurance'], 2, ',', ' ') : '0,00' ?> Ar</div>
                    </div>
                </div>
            </div>

            <div class="detail-card">
                <h3 class="section-title">Informations client</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Nom du client</div>
                        <div class="detail-value"><?= htmlspecialchars($pretDetails['client_nom'] ?? 'N/A') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Email</div>
                        <div class="detail-value"><?= htmlspecialchars($pretDetails['client_email'] ?? 'N/A') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Type de client</div>
                        <div class="detail-value"><?= htmlspecialchars($pretDetails['type_client'] ?? 'N/A') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Salaire</div>
                        <div class="detail-value"><?= isset($pretDetails['client_salaire']) ? number_format($pretDetails['client_salaire'], 2, ',', ' ') : '0,00' ?> Ar</div>
                    </div>
                </div>
            </div>

            <?php if (isset($pretDetails['date_mensualite'])): ?>
            <div class="detail-card">
                <h3 class="section-title">Informations de remboursement</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Date de mensualité</div>
                        <div class="detail-value"><?= isset($pretDetails['date_mensualite']) ? date('d/m/Y', strtotime($pretDetails['date_mensualite'])) : 'N/A' ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="detail-card">
                <div class="no-data">
                    <h3>Aucun détail trouvé</h3>
                    <p>Les détails de ce prêt ne sont pas disponibles.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

