<?php
$this->titre = Appy\Src\Config::APPLI_NOM." - S'inscrire";
$this->css   = array(DIR_MODULE."/css/defaut");
?>

<!-- Ajouter les required quand fini -->

<h1 class="title is-5">Inscription</h1>
<?php include_once 'erreurs.tpl';?>

<form action="" method="POST">

    <div class="container">

        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email" placeholder="email" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Mot de passe</label>
            <div class="control">
                <input class="input" type="password" name="password" placeholder="Mot de passe" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Confirmation du mot de passe</label>
            <div class="control">
                <input class="input" type="password" name="password_confirm" placeholder="Confirmez votre mot de passe" required>
            </div>
        </div>

        <button type="submit" class="button is-button-highlight">S'enregistrer</button>

    </div>
</form>

<!-- SCRIPTS -->
<?=\Appy\Src\Html::moduleJS("Modules/Membres/js/import.js")?>