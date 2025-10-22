<option value="" disabled <?= empty($quiz->autoUserId) ? 'selected' : '' ?>>-- Choisir la personne évaluée --</option>
<?php foreach ($respondants as $respondant): ?>
<option value="<?= $respondant->id ?>" <?= ($respondant->id == $quiz->autoUserId) ? 'selected' : '' ?>>
<?= $respondant->lastname ?> <?= $respondant->firstname ?> <?= $respondant->email ?>
</option>
<?php endforeach; ?>

