<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - Mot de passe oublié';
$this->css   = array(DIR_MODULE."/css/defaut");
?>

<h3 class="title is-5">Mot de passe oublié</h3>
<?php include_once 'flash.tpl';?>

<form action="" method="POST">

    <div class="field">
        <label class="label">Votre email</label>
        <div class="control">
            <input class="input" type="email" name="email" placeholder="Email" title="Préciser votre email afin de réinitialiser votre mot de passe" required>
        </div>
    </div>

    <button type="submit" class="button is-success">Envoyer la demande</button>

</form>

<!-- SCRIPTS -->
<?=\Appy\Src\Html::moduleJS("Modules/Membres/js/import.js")?>