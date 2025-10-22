<?php

function convert($size)
{
    $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
    return @round($size / pow(1024, ($i    = floor(log($size, 1024)))), 2).' '.$unit[$i];
}
?>

<div class="container">

    <h1 class="title is-5">DEBUG</h1>

    <div class=" message">
        <div class="message-header">
            Informations script
        </div>
        <div class="message-body">
            <ul>
                <li><strong>Dur&eacute;e du script :&nbsp;</strong><?=round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 3)?> sec.</li>
                <li><strong>M&eacute;moire maximale allouée au script :&nbsp;</strong><?=convert(memory_get_peak_usage(true))?></li>
                <li><strong>m&eacute;moire limite :&nbsp;</strong><?=ini_get('memory_limit')?></li>
                <li><strong>post max size :&nbsp;</strong><?=ini_get('post_max_size')?></li>
                <li><strong>upload max file size :&nbsp;</strong><?=ini_get('upload_max_filesize')?></li>
            </ul>
        </div>
    </div>

    <?php
    if (isset($_GET)) {
        $globales[] = array('$_GET', $_GET);
    }
    if (isset($_POST)) {
        $globales[] = array('$_POST', $_POST);
    }
    if (isset($_SESSION)) {
        $globales[] = array('$_SESSION', $_SESSION);
    }
    if (isset($_FILES)) {
        $globales[] = array('$_FILES', $_FILES);
    }
    ?>
    <?php foreach ($globales as $globale) {?>
        <div class=" message">
            <div class="message-header">
                <?="Variables ".$globale[0]?>
            </div>
            <div class="message-body">
                <?php foreach ($globale[1] as $k => $v) {?>
                    <pre><strong><?=$k;?> :</strong><?=print_r($v, true)?></pre>
                <?php }?>
            </div>
        </div>
    <?php }?>
</div>

<div class="container">
    <div class=" message">
        <div class="message-header">
            Variables $_SERVEUR
        </div>
        <div class="message-body">
            <ul>
                <?php foreach ($_SERVER AS $cle => $valeur) {?>
                    <li><strong><?=$cle;?> :</strong><pre><?=print_r($valeur, true)?></pre></li>
                <?php }?>
            </ul>

        </div>
    </div>
</div>
