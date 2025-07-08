<div class="simulation-list">
    <h1>Mes simulations</h1>
    <form id="compareSimulationsForm" action="<?= BASE_URL ?>/client/simulations/compare" method="POST">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll" title="Sélectionner tout"></th>
                    <th>Montant</th>
                    <th>Durée (mois)</th>
                    <th>Taux d'intérêt</th>
                    <th>Taux d'assurance</th>
                    <th>Type de prêt</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($simulations as $simulation): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="simulation_ids[]" value="<?= $simulation['id'] ?>" class="simulation-checkbox">
                        </td>
                        <td><?= number_format($simulation['montant'], 2) ?> Ar</td>
                        <td><?= $simulation['duree_mois'] ?></td>
                        <td><?= $simulation['taux_interet'] ?>%</td>
                        <td><?= $simulation['taux_assurance'] ?>%</td>
                        <td><?= $simulation['type_pret_nom'] ?? 'N/A' ?></td>
                        <td><?= $simulation['date_simulation'] ?></td>
                        <td>
                            <form method="POST" action="<?= BASE_URL ?>/client/pret/convertirSimulation/<?= $simulation['id'] ?>" style="display: inline;">
                                <button type="submit" class="btn-primary">Convertir en prêt</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="form-actions">
            <button type="submit" class="btn-primary" id="compareButton" disabled>
                <span class="btn-icon"><i class="fas fa-balance-scale"></i></span>
                Comparer les simulations
            </button>
        </div>
    </form>
</div>

<style>
    .simulation-list {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .simulation-list h1 {
        color: #333;
        text-align: center;
        margin-bottom: 30px;
        font-size: 2.2em;
        font-weight: 600;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    thead {
        background-color: #273267;
        color: white;
    }

    th,
    td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9em;
        letter-spacing: 0.5px;
    }

    tr:hover {
        background-color: #f5f5f5;
    }

    tr:nth-child(even) {
        background-color: #fafafa;
    }

    td {
        font-size: 0.95em;
        color: #555;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
        padding: 8px 16px;
        text-decoration: none;
        border-radius: 4px;
        font-size: 0.9em;
        font-weight: 500;
        transition: background-color 0.3s ease;
        display: inline-block;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        transform: translateY(-1px);
    }

    .btn-primary:disabled {
        background-color: #6c757d;
        cursor: not-allowed;
    }

    .form-actions {
        margin-top: 20px;
        text-align: right;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .simulation-list {
            padding: 10px;
            margin: 10px;
        }

        table {
            font-size: 0.85em;
        }

        th,
        td {
            padding: 8px 6px;
        }

        .btn-primary {
            padding: 6px 12px;
            font-size: 0.8em;
        }
    }

    @media (max-width: 600px) {
        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.simulation-checkbox');
        const compareButton = document.getElementById('compareButton');
        const selectAllCheckbox = document.getElementById('selectAll');

        function updateCompareButton() {
            const checkedCount = document.querySelectorAll('.simulation-checkbox:checked').length;
            compareButton.disabled = checkedCount !== 2;
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                updateCompareButton();
                selectAllCheckbox.checked = checkboxes.length === document.querySelectorAll('.simulation-checkbox:checked').length;
            });
        });

        selectAllCheckbox.addEventListener('change', () => {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateCompareButton();
        });

        document.getElementById('compareSimulationsForm').addEventListener('submit', (e) => {
            const checkedCount = document.querySelectorAll('.simulation-checkbox:checked').length;
            if (checkedCount !== 2) {
                e.preventDefault();
                alert('Veuillez sélectionner exactement deux simulations pour comparer.');
            }
        });
    });
</script>