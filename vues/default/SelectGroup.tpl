<select id="groupe_id" class="input is-small" type="text" name="groupe_id">
    <?php
        foreach ($groupes as $groupe) {
            if ($groupe->id == $id) {
    echo '<option value="' . $groupe->id . '" selected>' . $groupe->groupeName . '</option>';
    } else {
    echo '<option value="' . $groupe->id . '">' . $groupe->groupeName . '</option>';
    }
    }
    ?>
</select>
