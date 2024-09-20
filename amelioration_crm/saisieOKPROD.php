<td>OK PROD</td>
<?php if (isset($okprodParTaille)) { ?>
    <?php foreach ($idcomdetParTaille as $taille => $couleurs) { ?>
        <td>
            <?php 
                // Vérifier si $okprodParTaille existe pour cette taille
                if (isset($okprodParTaille[$taille])) {
                    // Récupérer l'ID correspondant à cette taille
                    $idcomdet = $idcomdetParTaille[$taille];

                    // Afficher un champ input modifiable
                    echo '<input type="number" value="' . $okprodParTaille[$taille] . '" ';
                    
                    // Ajouter l'attribut pour AJAX avec seulement l'idcomdet
                    echo 'data-id="' . $idcomdet . '" ';
                    echo 'onchange="updateOkProd(this)">'; // Appel AJAX lors de la modification

                    // Afficher l'ID correspondant à cette taille
                    echo " ID: " . $idcomdet;
                }
            ?>
        </td>
    <?php } ?>
<?php } else { ?>
    <td>Aucune donnée</td>
<?php } ?>

<script>

  function updateOkProd(inputElement) {
    var newValue = inputElement.value; // La nouvelle valeur modifiée
    var idcomdet = inputElement.getAttribute('data-id'); // Récupérer l'ID associé

    // Création de la requête AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_okprod.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Envoyer les paramètres : l'ID et la nouvelle valeur
    var params = 'idcomdet=' + idcomdet + '&okprod=' + newValue;

    xhr.onload = function () {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                console.log('Mise à jour réussie');
            } else {
                console.error('Erreur : ' + response.message);
            }
        } else {
            console.error('Erreur AJAX');
        }
    };

    xhr.send(params); // Envoie les données au serveur
}


</script>