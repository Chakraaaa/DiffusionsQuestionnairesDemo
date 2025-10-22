<?php if (!empty($errors)) {?>
    <div class="alert--danger center " style="width: 40%">
        Vous n'avez pas rempli le formulaire correctement :
        <ul>
            <?php foreach ($errors as $error) {?>
                <li><?=$error?></li>
            <?php }?>
        </ul>
    </div>
<?php }?>
