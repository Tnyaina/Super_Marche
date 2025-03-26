<!-- Content to be inserted in the container div of index.php -->
<div class="row">
    <div class="col-lg-6 offset-lg-3 mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="text-center mb-0">Sélection de la Caisse</h4>
                <?php if(isset($_SESSION['client_nom'])): ?>
                    <p class="text-center mb-0 mt-2">Client: <?php echo htmlspecialchars($_SESSION['client_nom']); ?></p>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>/select-caisse" method="POST"></form>
                    <div class="form-group">
                        <label for="caisse_selectionnee">Choisissez votre caisse :</label>
                        <select class="form-control" id="caisse_selectionnee" name="caisse_id" required>
                            <?php foreach ($caisses as $caisse): ?>
                                <option value="<?php echo $caisse['id']; ?>">
                                    <?php echo htmlspecialchars($caisse['numero_caisse']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sélectionner</button>
                </form>
            </div>
        </div>
    </div>
</div>