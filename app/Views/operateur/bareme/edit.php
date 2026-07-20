<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>


<?php
function formatMontant($value): string
{
    return $value !== null
        ? number_format($value, 0, ',', ' ') . ' Ar'
        : 'Illimité';
}


$avant = $contexte['avant'] ?? null;
$courant = $contexte['courant'];
$apres = $contexte['apres'] ?? null;

?>


<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2>Modifier un barème</h2>
        <p class="text-muted">
            Les tranches voisines seront ajustées automatiquement.
        </p>
    </div>

    <a href="<?= base_url('operateur/baremes') ?>"
       class="btn btn-outline-secondary">
        Retour
    </a>

</div>



<form method="post"
      action="<?= base_url('operateur/baremes/update/'.$courant['id']) ?>">



<div class="row justify-content-center">

<div class="col-lg-8">



<?php if ($avant): ?>

<div class="card mb-3">

<div class="card-header bg-light">
    Barème précédent
</div>

<div class="card-body">


<div class="row">

<div class="col">
<small>Montant min</small>
<div>
<?= formatMontant($avant['montant_min']) ?>
</div>
</div>


<div class="col">

<small>Montant max</small>

<div id="previewAvantMax">
<?= formatMontant($avant['montant_max']) ?>
</div>

</div>


<div class="col">
<small>Frais</small>
<div>
<?= formatMontant($avant['frais']) ?>
</div>
</div>

</div>


</div>
</div>

<?php endif; ?>




<!-- Ligne à modifier -->

<div class="card mb-3 border-primary">


<div class="card-header bg-primary text-white">
    Barème à modifier
</div>



<div class="card-body">


<div class="row">


<div class="col-md-4">

<label class="form-label">
Montant minimum
</label>


<input 
type="number"
class="form-control"
id="montant_min"
name="montant_min"
value="<?= $courant['montant_min'] ?>">


</div>



<div class="col-md-4">

<label class="form-label">
Montant maximum
</label>


<input 
type="number"
class="form-control"
id="montant_max"
name="montant_max"
value="<?= $courant['montant_max'] ?>">


</div>



<div class="col-md-4">

<label class="form-label">
Frais
</label>


<input 
type="number"
class="form-control"
name="frais"
value="<?= $courant['frais'] ?>">


</div>


</div>


</div>

</div>





<?php if ($apres): ?>

<div class="card mb-3">

<div class="card-header bg-light">
    Barème suivant
</div>


<div class="card-body">


<div class="row">


<div class="col">

<small>
Montant min
</small>


<div id="previewApresMin">

<?= formatMontant($apres['montant_min']) ?>

</div>


</div>


<div class="col">

<small>
Montant max
</small>

<div>
<?= formatMontant($apres['montant_max']) ?>
</div>


</div>



<div class="col">

<small>
Frais
</small>

<div>
<?= formatMontant($apres['frais']) ?>
</div>

</div>


</div>


</div>

</div>


<?php endif; ?>




<button class="btn btn-primary">
Enregistrer les modifications
</button>



</div>

</div>


</form>




<script>

const inputMin = document.getElementById('montant_min');
const inputMax = document.getElementById('montant_max');


const previewAvantMax =
    document.getElementById('previewAvantMax');


const previewApresMin =
    document.getElementById('previewApresMin');



function formatAr(value)
{
    return Number(value)
        .toLocaleString('fr-FR') + ' Ar';
}



function updatePreview()
{

    let min = parseInt(inputMin.value);
    let max = parseInt(inputMax.value);



    if(previewAvantMax)
    {
        previewAvantMax.innerHTML =
            formatAr(min - 1);
    }



    if(previewApresMin)
    {
        previewApresMin.innerHTML =
            formatAr(max + 1);
    }

}



inputMin.addEventListener(
    'input',
    updatePreview
);


inputMax.addEventListener(
    'input',
    updatePreview
);


</script>


<?= $this->endSection() ?>