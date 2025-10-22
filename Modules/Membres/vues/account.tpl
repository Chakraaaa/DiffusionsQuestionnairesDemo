<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - Se connecter';
$this->css   = array(DIR_MODULE."/css/defaut");
?>

<h1 class="title is-5">Votre compte : <?=$user->email?></h1>
<?php include_once 'flash.tpl';?>

<div class="columns">

    <!--<div class="column" >
        
        <form action="" method="POST">

            <h2 class="title is-6">Changer mon email actuel : <?=$user->email?></h2>
            <div class="field">
                <label class="label">Email</label>
                <div class="control">
                    <input class="input" type="email" name="email" placeholder="Nouveau email" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Confirmation de l'email</label>
                <div class="control">
                    <input class="input" type="email" name="email_confirm" placeholder="Confirmez l'email" required>
                </div>
            </div>

            <button type="submit" class="button is-rspompes">Changer mon email</button>

        </form>

    </div>-->

    <div class="column is-half is-offset-3" >

        <form action=""  method="POST">

            <h2 class="title is-6">Définir / Modifier son mot de passe</h2>

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

            <button type="submit" class="button-valider ">Valider</button>

        </form>
    </div>
</div>

<!-- SCRIPTS -->
<?=\Appy\Src\Html::moduleJS("Modules/Membres/js/import.js")?>
