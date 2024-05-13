<!-- Display any errors accumulated from the password error array -->
<?php if (count($errors) > 0) : ?>
    <div>
        <?php foreach ($errors as $error) : ?>
            <span><?php echo $error ?></span>
        <?php endforeach ?>
    </div>
<?php endif ?>