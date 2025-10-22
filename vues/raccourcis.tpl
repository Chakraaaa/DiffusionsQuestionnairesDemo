<?php $this->titre = Appy\Src\Config::APPLI_NOM.' - Aide';?>


<div class="container">

    <h1 class="title is-5">Liste des raccourcis clavier</h1>

    <table class="table is-fullwidth">
        <tr>
            <td><kbd>F11</kbd></td>
            <td>Plein écran</td>
        </tr>
        <tr>
            <td><kbd>Ctrl</kbd>+<kbd>F</kbd></td>
            <td>Trouver dans la page</td>
        </tr>
        <tr>
            <td><kbd>Ctrl</kbd>+<kbd>-</kbd></td>
            <td>Réduire la taille</td>
        </tr>
        <tr>
            <td><kbd>Ctrl</kbd>+<kbd>+</kbd></td>
            <td>Augmente la taille</td>
        </tr>
        <tr>
            <td><kbd>Ctrl</kbd>+<kbd>0</kbd></td>
            <td>Restaurer la taille par d&eacute;faut</td>
        </tr>
        <tr>
            <td><kbd>Ctrl</kbd>+<kbd>A</kbd></td>
            <td>S&eacute;lectionner tout</td>
        </tr>
        <tr>
            <td><kbd>Ctrl</kbd>+<kbd>C</kbd></td>
            <td>Copier</td>
        </tr>
        <tr>
            <td><kbd>Ctrl</kbd>+<kbd>V</kbd></td>
            <td>Coller</td>
        </tr>
    </table>

    <div id="webeo" class="column is-full">
        <span><?=Appy\Src\Config::APPLI_NOM?> - v1.001 - Réalisation par&nbsp;</span>
        <a class="" href="https://www.webeosolution.fr/" title="www.webeosolution.fr">
            <?=Appy\Src\Html::img("logo-webeo.png", "Logo www.webeosolution.fr", ["height" => "35px;"])?>
        </a>
    </div>

</div>



