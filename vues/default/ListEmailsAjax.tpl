<h3 class="has-text-centered"><?= htmlspecialchars($title) ?></h3>

<?php if (!empty($users)): ?>
<ul>
    <?php foreach ($users as $user): ?>
    <li><?= htmlspecialchars($user->email) ?></li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<p>Aucun email à afficher.</p>
<?php endif; ?>

<hr class="divider">

<p class="has-text-right mt-5">
    <button type="button" class="button-fermer" id="close-popup-emails-details" name="" value="fermer">Fermer</button>
</p>
