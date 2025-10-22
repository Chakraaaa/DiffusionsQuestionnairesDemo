<!DOCTYPE html>
<html lang="fr">

<head>
    <title>RELAIS MANAGERS - Identification</title>
    <meta name="author" content="WEBEO SOLUTION">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?=Appy\Src\Html::css("assets/css/bulma.min.css")?>
    <?=Appy\Src\Html::css("assets/css/style.css")?>
    <?=Appy\Src\Html::css("Src/jquery-ui/jquery-ui.min.css")?>
    <link rel="icon" type="image/png" sizes="96x96" href="<?=WEB_PATH?>assets/images/favicon.png?v=<?=filemtime(BASE_PATH."assets".DS."images".DS."favicon.png")?>">

    <!-- Ajout des scripts de JQUERY-UI -->
    <?=Appy\Src\Html::scriptJS("assets/js/jquery.min.js")?>
    <?=Appy\Src\Html::scriptJS("assets/js/jquery-ui/jquery-ui.min.js")?>

</head>

<body>

<main style="padding-top: 40px">
    <?php if (isset($error)) { ?>
        <div class="columns is-centered">
            <div class="column is-half notification is-danger">
                <button class="delete" aria-label="Fermer">X</button>
                <?=$error?>
            </div>
        </div>
    <?php } ?>
    <div id="login" class="container">
        <div class="container has-text-centered">
            <img src="<?=WEB_PATH?>assets/images/RELAISMANAGERS-logo.jpg" style="height: 142px" />
            <div class="column is-4 is-offset-4 has-text-centered" >
                <form action="<?=$urlAction?>" method="POST" class="box">

                    <div class="field">
                        <p class="control has-icons-left">
                            <input class="input" name="identifier" type="text" placeholder="Saisir votre identifiant" required autofocus="">
                            <span class="icon is-small is-left">
                            <i class="fas fa-user"></i>
                        </span>
                        </p>
                    </div>
                    <input id="quizIdentifier" name="quizIdentifier" type="hidden" value="<?=$quiz->identifier?>">
                    <button class="button-valider">
                        Se connecter
                    </button>
                </form>
            </div>
        </div>

    </div>
</main>

<!-- SCRIPTS -->
<?=Appy\Src\Html::scriptJS("assets/js/menu.js")?>
<?=Appy\Src\Html::scriptJS("assets/js/all.js")?>
<?=\Appy\Src\Html::moduleJS("./assets/js/interface.js")?>
<?=Appy\Src\Html::moduleJS("./assets/js/widgets.js");?>

</body>
</html>



