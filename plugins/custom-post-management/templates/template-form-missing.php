<div class="errorbox">
    <ul>
<?php
    echo CPM_Assets::get_label($post_type, 'required');
    foreach ($errors as $error) {
        echo '<li>' . $error . '</li>';
    }
?>
    </ul>
</div>

<?php
