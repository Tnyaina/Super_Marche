<!-- achat/achat.php -->
<h1>Caisse No: <?= $_SESSION['numCaisse'] ?></h1>

<div class="purchase-container">
    <!-- Product selection form -->
    <form id="addProductForm">
        <input type="hidden" name="caisse_id" value="<?= $_SESSION['idCaisse'] ?>">
        <select name="idproduit" id="idproduit">
            <?php foreach ($produits as $produit) { ?>
                <option value="<?= $produit['id'] ?>" data-price="<?= $produit['prix'] ?>"
                    data-designation="<?= $produit['designation'] ?>">
                    <?= $produit['designation'] ?>
                </option>
            <?php } ?>
        </select>
        <input type="number" name="quantite" id="quantite" min="1" required>
        <button type="submit">Ajouter</button>
    </form>

    <!-- Purchase table -->
    <table id="purchaseTable">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix Unit</th>
                <th>Qté</th>
                <th>Montant</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be added dynamically by JavaScript -->
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total</td>
                <td id="totalAmount">0</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Finalize purchase button -->
    <button id="finalizePurchase">Clôturer Achat</button>
</div>

<script>
    class PurchaseManager {
        constructor() {
            this.purchases = [];
            this.tableBody = document.querySelector('#purchaseTable tbody');
            this.totalElement = document.querySelector('#totalAmount');
            this.form = document.querySelector('#addProductForm');
            this.finalizeBtn = document.querySelector('#finalizePurchase');
            this.baseUrl = '<?= BASE_URL ?>'; // Use PHP-defined base URL

            this.setupEventListeners();
        }

        setupEventListeners() {
            this.form.addEventListener('submit', (e) => this.handleAddProduct(e));
            this.finalizeBtn.addEventListener('click', () => this.handleFinalizePurchase());
        }

        handleAddProduct(e) {
            e.preventDefault();

            const select = document.querySelector('#idproduit');
            const quantityInput = document.querySelector('#quantite');

            const productId = select.value;
            const quantity = parseInt(quantityInput.value);
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price);
            const designation = selectedOption.dataset.designation;

            if (quantity > 0) {
                this.addPurchase(productId, designation, price, quantity);
                quantityInput.value = ''; // Reset quantity input
            }
        }

        addPurchase(productId, designation, price, quantity) {
            const existingIndex = this.purchases.findIndex(p => p.id_produit === productId);

            if (existingIndex >= 0) {
                this.purchases[existingIndex].quantite += quantity;
            } else {
                this.purchases.push({
                    id_produit: productId,
                    designation: designation,
                    prix: price,
                    quantite: quantity
                });
            }

            this.renderTable();
        }

        removePurchase(index) {
            this.purchases.splice(index, 1);
            this.renderTable();
        }

        renderTable() {
            this.tableBody.innerHTML = '';
            let total = 0;

            this.purchases.forEach((purchase, index) => {
                const amount = purchase.prix * purchase.quantite;
                total += amount;

                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${purchase.designation}</td>
                <td>${purchase.prix.toFixed(2)}</td>
                <td>${purchase.quantite}</td>
                <td>${amount.toFixed(2)}</td>
                <td><button class="remove-btn" data-index="${index}">Supprimer</button></td>
            `;
                this.tableBody.appendChild(row);
            });

            this.totalElement.textContent = total.toFixed(2);

            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const index = parseInt(e.target.dataset.index);
                    this.removePurchase(index);
                });
            });
        }

        async handleFinalizePurchase() {
            if (this.purchases.length === 0) {
                alert('Aucun produit dans le panier');
                return;
            }

            const data = {
                purchases: this.purchases,
                caisse_id: document.querySelector('input[name="caisse_id"]').value
            };

            console.log('Sending data to server:', JSON.stringify(data, null, 2));

            try {
                const response = await fetch(`${this.baseUrl}/achat/finaliser`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${errorText}`);
                }

                const result = await response.json();

                if (result.success) {
                    alert('Achat finalisé avec succès! ID: ' + result.achat_id);
                    this.purchases = [];
                    this.renderTable();
                } else {
                    alert('Erreur: ' + (result.message || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('Erreur lors de la communication avec le serveur: ' + error.message);
            }
        }
    }

    // Initialize the purchase manager
    const purchaseManager = new PurchaseManager();
</script>

<style>
    .purchase-container {
        max-width: 800px;
        margin: 20px auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
    }

    th,
    td {
        padding: 8px;
        border: 1px solid #ddd;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .remove-btn {
        background-color: #ff4444;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }

    #finalizePurchase {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }
</style>