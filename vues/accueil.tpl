<?php
$this->titre       = Appy\Src\Config::APPLI_NOM.' - Accueil';
$this->description = "Services RS-POMPES";
?>

<div class="container">

    <h1 class="title is-5">Accueil</h1>

    <?php include BASE_PATH."Modules/Membres/vues/flash.tpl"?>

    <article class="message">
        <div class="message-header">
            <p>NOTE IMPORTANTE</p>
        </div>
        <div class="message-body">
            <ul>
                <li class="list-group-item">Il est recommandé d'utiliser le navigateur Chrome pour naviguer sur le site.</li>
            </ul>
        </div>
    </article>

    <article class="message">
        <div class="message-header">
            <p>13/01/2021</p>
        </div>
        <div class="message-body">
            <ul>
                <li class="list-group-item">- Ajout de l'écran de gestion des utilisateurs</li>
                <li class="list-group-item">- Création du module de visualisation des commandes</li>
            </ul>
        </div>
    </article>

    <article class="message">
        <div class="message-header">
            <p>21/12/2020</p>
        </div>
        <div class="message-body">
            <ul>
                <li class="list-group-item">- Création de la structure général du site (url, sécurité, menu).</li>
            </ul>
        </div>
    </article>

</div>
