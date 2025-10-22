<!DOCTYPE html>
<html lang="fr">

    <head>
        <title><?=$titre?></title>
        <meta name="author" content="WEBEO SOLUTION">
        <meta name="description" content="<?=$description?>">
        <meta name="keywords" content="<?=$keywords?>">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?=Appy\Src\Html::css("assets/css/bulma.min.css")?>
        <?=Appy\Src\Html::css("assets/css/style.css")?>
        <?=Appy\Src\Html::css("Src/jquery-ui/jquery-ui.min.css")?>
        <?=Appy\Src\Core\Appy::loadCSS($css)?>
        <link rel="icon" type="image/png" sizes="96x96" href="<?=WEB_PATH?>assets/images/favicon.png?v=<?=filemtime(BASE_PATH."assets".DS."images".DS."favicon.png")?>">

        <!-- Ajout des scripts de JQUERY-UI -->
        <?=Appy\Src\Html::scriptJS("assets/js/jquery.min.js")?>
        <?=Appy\Src\Html::scriptJS("assets/js/jquery-ui/jquery-ui.min.js")?>

    </head>

    <body>

        <header class="hero">
            <div class="hero-head">
                <?php require 'menu.tpl';?>
            </div>
        </header>

        <main><?=$contenu?></main>

        <?php if (\Appy\Src\Config::DEBUG) {?>
            <div class="container is-fluid">
                <?=require_once 'debug.tpl';?>
            </div>
        <?php }?>

        <!-- SCRIPTS -->
        <?=Appy\Src\Html::scriptJS("assets/js/menu.js")?>
        <?=Appy\Src\Html::scriptJS("assets/js/all.js")?>
        <?=\Appy\Src\Html::moduleJS("./assets/js/interface.js")?>
        <?=Appy\Src\Html::moduleJS("./assets/js/widgets.js");?>

    </body>
</html>
