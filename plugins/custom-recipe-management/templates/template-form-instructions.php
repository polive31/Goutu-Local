<table class="recipe-form" id="recipe-instructions">
    <thead>
        <tr class="instruction-group instruction-group-first">
            <td colspan="4" class="group">
                <span class="header"><?php _e('Instructions Group', 'crm'); ?></span>
                <!-- <span class="name instruction-groups-disabled"><?php echo __('Main Instructions', 'crm') . ' ' . __('(this label is not shown)', 'crm'); ?></span> -->
                <?php
                $previous_group = '';
                if (isset($instructions[0]) && isset($instructions[0]['group'])) {
                    $previous_group = $instructions[0]['group'];
                }
                ?>
                <span class="name"><input type="text" placeholder="<?php echo __('eg. Preparation of the dough', 'crm'); ?>" class="instruction-group-label" value="<?php echo esc_attr($previous_group); ?>" /></span>
            </td>
        </tr>
    </thead>
    <tbody>
        <tr class="instruction-group-stub">
            <td class="group center-column delete-button">
                <?= foodiepro_get_icon('delete', 'instruction-group-delete', '', __('Remove this instruction group headline', 'crm')); ?>
            </td>
            <td colspan="3" class="group">
                <div class="group-container">
                    <span class="header"><?php _e('Instructions Group', 'crm'); ?></span>
                    <span class="name"><input type="text" class="instruction-group-label" /></span>
                </div>
            </td>
        </tr>
        <?php

        // Add one visible instruction field at the end
        $instructions[]=CRM_Recipe::get_instruction_item();
        // Add one more hidden instruction at the end of the section, to be used as a hidden stub
        $instructions[]=CRM_Recipe::get_instruction_item(false);
        $i = 0;
        foreach ($instructions as $instruction) {

            $instruction['group']= isset($instruction['group'])?$instruction['group']:'';
            $instruction['visible']= isset($instruction['visible'])?$instruction['visible']:true;
            if ($instruction['group'] != $previous_group) {
                $previous_group = $instruction['group'];
                $newgroup = true;
            } else
                $newgroup = false;

            $args=compact('i','instruction','newgroup');
            CRM_Assets::echo_template_part( 'form-instructions', 'instruction', $args );
            $i++;
        }

    ?>

    </tbody>
</table>
<?php
