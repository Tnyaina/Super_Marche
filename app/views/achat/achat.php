    <form action="<?= BASE_URL ?>/achat/liste" method="get">
        <select name="idproduit" id="idproduit">
            <?php foreach ($produits as $produit) { ?>
                <option value="<?= $produit['id'] ?>"><?= $produit['designation'] ?></option>
           <?php } ?>
        </select>
    </form>