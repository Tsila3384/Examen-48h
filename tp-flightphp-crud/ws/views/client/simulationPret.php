
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 700px;
            margin: 40px auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #1a3c6d;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 16px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #1a3c6d;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 5px rgba(37, 99, 235, 0.2);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn:hover {
            background: linear-gradient(90deg, #1e40af 0%, #1e3a8a 100%);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            font-size: 14px;
        }

        .alert.error {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert.success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .simulation-result {
            margin-top: 20px;
            display: none;
        }

        .result-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .result-card .title {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .result-card .amount {
            color: #f59e0b;
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .result-card .details {
            text-align: left;
            margin-top: 20px;
        }

        .result-card .details div {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 16px;
            color: #1a3c6d;
        }

        .result-card .details div span:last-child {
            font-weight: 600;
            color: #2563eb;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 20px;
            }

            .header h1 {
                font-size: 24px;
            }

            .form-group input {
                padding: 10px;
            }

            .btn {
                padding: 12px;
            }

            .result-card .amount {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Simulateur de Prêt</h1>
            <p>Estimez vos mensualités en quelques clics</p>
        </div>

        <div id="alert" class="alert"></div>

        <form id="simulationForm">
            <div class="form-group">
                <label for="montant">Montant du prêt (€)</label>
                <input type="number" id="montant" name="montant" min="1000" step="100" required placeholder="Ex: 10000">
            </div>
            <div class="form-group">
                <label for="duree">Durée (en mois)</label>
                <input type="number" id="duree" name="duree" min="6" max="360" step="1" required placeholder="Ex: 60">
            </div>
            <div class="form-group">
                <label for="taux_interet">Taux d'intérêt (%)</label>
                <input type="number" id="taux_interet" name="taux_interet" min="0" step="0.01" required placeholder="Ex: 3.5">
            </div>
            <div class="form-group">
                <label for="taux_assurance">Taux d'assurance (%)</label>
                <input type="number" id="taux_assurance" name="taux_assurance" min="0" step="0.01" required placeholder="Ex: 0.3">
            </div>
            <button type="submit" class="btn">Simuler le prêt</button>
        </form>

        <div id="simulationResult" class="simulation-result">
            <div class="result-card">
                <div class="title">Votre mensualité sera de</div>
                <div class="amount" id="mensualiteTotale"></div>
                <div class="details">
                    <div><span>Montant du prêt :</span><span id="montantPret"></span></div>
                    <div><span>Mensualité sans assurance :</span><span id="mensualiteSansAssurance"></span></div>
                    <div><span>Assurance :</span><span id="assuranceMensuelle"></span></div>
                    <div><span>Mensualité totale :</span><span id="mensualiteTotaleDetails"></span></div>
                    <div><span>Coût total du crédit :</span><span id="coutTotal"></span></div>
                    <div><span>Coût total de l'assurance :</span><span id="coutAssurance"></span></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('simulationForm');
        const alert = document.getElementById('alert');
        const simulationResult = document.getElementById('simulationResult');
        const mensualiteTotale = document.getElementById('mensualiteTotale');
        const montantPret = document.getElementById('montantPret');
        const mensualiteSansAssurance = document.getElementById('mensualiteSansAssurance');
        const assuranceMensuelle = document.getElementById('assuranceMensuelle');
        const mensualiteTotaleDetails = document.getElementById('mensualiteTotaleDetails');
        const coutTotal = document.getElementById('coutTotal');
        const coutAssurance = document.getElementById('coutAssurance');

        function showAlert(message, type) {
            alert.textContent = message;
            alert.className = `alert ${type}`;
            alert.style.display = 'block';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const data = Object.fromEntries(formData);

            if (data.montant < 1000) {
                showAlert('Le montant doit être d\'au moins 1000€', 'error');
                return;
            }
            if (data.duree < 6 || data.duree > 360) {
                showAlert('La durée doit être entre 6 et 360 mois', 'error');
                return;
            }
            if (data.taux_interet < 0) {
                showAlert('Le taux d\'intérêt ne peut pas être négatif', 'error');
                return;
            }
            if (data.taux_assurance < 0) {
                showAlert('Le taux d\'assurance ne peut pas être négatif', 'error');
                return;
            }

            try {
                const response = await fetch('<?= BASE_URL ?>/client/pret/simuler', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    const amortissement = result.data;
                    const firstMonth = amortissement[0];
                    const lastMonth = amortissement[amortissement.length - 1];
                    const totalInterets = amortissement.reduce((sum, month) => sum + month.interets, 0);
                    const totalAssurance = amortissement.reduce((sum, month) => sum + month.assurance, 0);
                    const totalPret = amortissement.reduce((sum, month) => sum + month.mensualite, 0);
                    const mensualiteBase = firstMonth.mensualite - firstMonth.assurance;

                    montantPret.textContent = `${data.montant} €`;
                    mensualiteSansAssurance.textContent = `${mensualiteBase.toFixed(2)} €/mois`;
                    assuranceMensuelle.textContent = `${firstMonth.assurance.toFixed(2)} €/mois`;
                    mensualiteTotale.textContent = `${firstMonth.mensualite.toFixed(2)} €`;
                    mensualiteTotaleDetails.textContent = `${firstMonth.mensualite.toFixed(2)} €/mois`;
                    coutTotal.textContent = `${(totalPret + totalAssurance).toFixed(2)} €`;
                    coutAssurance.textContent = `${totalAssurance.toFixed(2)} €`;

                    simulationResult.style.display = 'block';
                    showAlert('Simulation effectuée avec succès', 'success');
                } else {
                    showAlert(result.message, 'error');
                    simulationResult.style.display = 'none';
                }
            } catch (error) {
                showAlert('Erreur de connexion au serveur', 'error');
                simulationResult.style.display = 'none';
            }
        });
    </script>
