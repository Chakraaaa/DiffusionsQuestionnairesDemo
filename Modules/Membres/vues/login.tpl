<?php
$this->titre = Appy\Src\Config::APPLI_NOM.' - Se connecter';
$this->css   = array(DIR_MODULE."/css/defaut");
?>

<h1 class="title is-5"></h1>

<?php include_once 'flash.tpl';?>

<div id="login" class="container">
    <div class="container has-text-centered">
        <img src="<?=WEB_PATH?>assets/images/logoDemo.png" style="height: 142px" />
        <div class="column is-4 is-offset-4 has-text-centered" >
            <form action="" method="POST" class="box">

                <div class="field">
                    <p class="control has-icons-left">
                        <input class="input" name="email" type="text" placeholder="Votre email" required autofocus="">
                        <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                        </span>
                    </p>
                </div>
                <div class="field">
                    <p class="control has-icons-left">
                        <input class="input" type="password" name="password" placeholder="Mot de passe" required>
                        <span class="icon is-small is-left">
                            <i class="fas fa-unlock"></i>
                        </span>
                    </p>
                </div>
                <div class="field is-narrow">
                    <div class="control">
                        <label class="checkbox">
                            <input class="checkbox" type="checkbox" name="remember" value="1"/> Se souvenir de moi
                        </label>
                    </div>
                </div>
                <button class="button-valider">
                    Se connecter
                </button>

                <p style="margin-top: 20px; font-size: 13px"><a href="<?=WEB_PATH?>membres.html/forget" class="text-link">J'ai oublié mon mot de passe</a></p>
            </form>
        </div>
    </div>

</div>

<!-- SCRIPTS -->
<?=\Appy\Src\Html::moduleJS("./assets/js/interface.js")?>
