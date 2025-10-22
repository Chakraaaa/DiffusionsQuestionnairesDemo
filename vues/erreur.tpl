<?php $this->titre = Appy\Src\Config::APPLI_NOM.' - Erreur de l\'application';?>

<div class="container">

    <h1 class="title is-4">Une erreur est survenue !</h1>

    <p class="content">Merci de faire une <u>capture d'écran du message</u> ci dessous et de l'envoyer à <a href="mailto:vdepeyre@webeosolution.fr">vdepeyre@webeosolution.fr</a></p>

    <div class="notification is-warning"><?=$msg_erreur?></div>

</div>