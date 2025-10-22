<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - Se connecter';
$this->css   = array(DIR_MODULE."/css/defaut");
?>

<h1 class="title is-5">Changer votre mot de passe</h1>
<?php include_once 'flash.tpl';?>

<div class="container">

    <form action=""  method="POST">

        <h2 class="title is-6">Changer mon mot de passe</h2>

        <div class="field">
            <label class="label">Mot de passe</label>
            <div class="control">
                <input class="input" type="password" name="password" placeholder="Nouveau mot de passe" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Confirmation du mot de passe</label>
            <div class="control">
                <input class="input" type="password" name="password_confirm" placeholder="Confirmez le mot de passe" required>
            </div>
        </div>

        <button type="submit" class="button is-button-highlight">Changer mon mot de passe</button>

    </form>

</div>

<!-- SCRIPTS -->
<?=\Appy\Src\Html::moduleJS("Modules/Membres/js/import.js")?>
