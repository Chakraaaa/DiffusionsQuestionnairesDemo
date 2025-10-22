<?php $session = Appy\Src\Core\Session::getInstance()->getFlashes();?>
<?php if (!empty($session)) {?>
    <div class="columns is-centered">
        <?php foreach ($session as $type => $message) {?>
            <div class="column is-half notification is-<?=$type?>">
                <button class="delete" aria-label="Fermer">X</button>
                <?=$message?>
            </div>
        <?php }?>
    </div>
<?php }?>
