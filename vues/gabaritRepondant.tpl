<!DOCTYPE html>
<html lang="fr">
<head>
    <title><?=$titre?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?=Appy\Src\Html::css("assets/css/bulma.min.css")?>
    <?=Appy\Src\Html::css("assets/css/style.css")?>
    <?=Appy\Src\Html::css("Src/jquery-ui/jquery-ui.min.css")?>
    <?=Appy\Src\Core\Appy::loadCSS($css)?>
</head>
<body>

<?=$contenu?>

<?=Appy\Src\Html::scriptJS("assets/js/menu.js")?>
<?=Appy\Src\Html::scriptJS("assets/js/all.js")?>
<?=\Appy\Src\Html::moduleJS("./assets/js/interface.js")?>
<?=Appy\Src\Html::moduleJS("./assets/js/widgets.js");?>
</body>
</html>
