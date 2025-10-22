<?php $this->titre = Appy\Src\Config::APPLI_NOM.' - En développement';?>


<div class="container">

    <h1 class="title is-5">En développement !</h1>

    <p class="notification is-warning">

        Cette partie de l'application est encore en développement.<br/>

        Merci de patientez...

    </p>

    <div id="webeo" class="column is-full">
        <span><?=Appy\Src\Config::APPLI_NOM?> - v1.2012 - Réalisation par&nbsp;</span>
        <a class="" href="https://www.webeosolution.fr/" title="www.webeosolution.fr">
            <?=Appy\Src\Html::img("logo-webeo.png", "Logo www.webeosolution.fr", ["height" => "35px;"])?>
        </a>
    </div>

</div>



