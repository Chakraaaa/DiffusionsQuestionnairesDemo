<div class="level-left">
    <h1 class="title is-5" style="font-weight: 600;">Paramètrage global</h1>
</div>
<form action="<?=$urlSubmitGeneralSettings?>" method="POST">
    <div style="margin-left: 20px;margin-right: 20px;" class="mt-3">
        <label style="font-weight: 600;" for="contact_nom"><?= htmlspecialchars($parameters[0]->label ?? '') ?></label>
        <input class="input" type="text" id="contact_nom" name="contact_nom" value="<?= htmlspecialchars($parameters[0]->value ?? '') ?>" />
    </div>
    <div style="margin-left: 20px;margin-right: 20px;">
        <label style="font-weight: 600;" for="contact_telephone"><?= htmlspecialchars($parameters[1]->label ?? '') ?></label>
        <input class="input" type="text" id="contact_telephone" name="contact_telephone" value="<?= htmlspecialchars($parameters[1]->value ?? '') ?>" />
    </div>
    <div class="has-text-right mt-5">
        <button class="button-valider" type="submit">Valider</button>
    </div>
</form>

