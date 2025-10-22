<?php
$utilisateur  = \Appy\Src\Core\Session::getInstance()->read('utilisateur');
?>

<nav class="navbar is-black" role="navigation" aria-label="main navigation" style="font-family: Work Sans;font-size: 14px">

    <div class="navbar-brand">
        <a class="navbar-item is-brand" href="<?=WEB_PATH?>accueil.html">
        </a>
        <span class="navbar-item" style="margin-right: 1.5rem; font-weight: bolder;" href="<?=WEB_PATH?>"><?=Appy\Src\Config::APPLI_NOM?></span>
        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>      
    </div>

    <div class="navbar-menu" id="navMenu">

        <div class="navbar-start">
            <?php if (isset($utilisateur)) { ?>
                <a class="navbar-item" href="<?=WEB_PATH?>users.html">Répondants</a>
                <a class="navbar-item" href="<?=WEB_PATH?>groups.html">Groupes</a>
                <a class="navbar-item" href="<?=WEB_PATH?>quiz.html">Questionnaires</a>

                <?php if ((isset($utilisateur->role) && ($utilisateur->role == 1)) || ($utilisateur->role == 2)) { ?>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Paramètrage</a>
                        <div class="navbar-dropdown">
                            <a style="display: none" class="navbar-item" href="<?=WEB_PATH?>quiz.html/GeneralSettings" title="Paramétrage global">Paramétrage global</a>
                            <a class="navbar-item" href="<?=WEB_PATH?>quiz.html/OptionsModeles?quizType=360" title="Modèle 360 - Options">Modèle 360 - Options</a>
                            <a class="navbar-item" href="<?=WEB_PATH?>quiz.html/edit360Questions" title="Modèle 360 - Chapitres et questions">Modèle 360 - Chapitres et questions</a>
                            <a class="navbar-item" href="<?=WEB_PATH?>quiz.html/OptionsModeles?quizType=PRCC" title="Modèle PRCC - Options">Modèle PRCC - Options</a>
                            <a class="navbar-item" href="<?=WEB_PATH?>quiz.html/editPRCCQuestions" title="Modèle PRCC - Catégories et questions">Modèle PRCC - Catégories et questions</a>
                            <a class="navbar-item" href="<?=WEB_PATH?>quiz.html/OptionsModeles?quizType=BAROM" title="Modèle BAROMETRE - Options">Modèle BAROMETRE - Options</a>
                            <a class="navbar-item" href="<?=WEB_PATH?>quiz.html/editBaromQuestions" title="Modèle BAROMETRE - Chapitres et questions">Modèle BAROMETRE - Chapitres et questions</a>
                        </div>
                    </div>
                <?php }?>

                <?php if (isset($utilisateur->role) && ($utilisateur->role == 1 || $utilisateur->role == 2)) { ?>
                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Administration</a>
                        <div class="navbar-dropdown">
                            <a class="navbar-item" href="<?=WEB_PATH?>utilisateurs.html" title="Paramètrage des utilisateurs">Utilisateurs Relais-Managers</a>
                        </div>
                    </div>
                <?php }?>
            <?php }?>

        </div>

        <div class="navbar-end">

            <?php if ($utilisateur) {?>
                <a class="navbar-item ml-1" href="<?=WEB_PATH?>membres.html/account" title="Votre compte"> <i class="fas fa-user"></i>&nbsp;<?=ucfirst($utilisateur->email)?></a>
                <a class="navbar-item" href="<?=WEB_PATH?>membres.html/logout" title="Quitter la session"> <i class="fas fa-sign-out-alt"></i>&nbsp;Quitter</a>
            <?php }?>

        </div>

    </div>
</nav>

