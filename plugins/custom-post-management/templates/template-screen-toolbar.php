<!-- Function buttons  -->
<div class="post-top">


    <div class="toolbar-buttons">

        <!-- Like Button -->
        <div class="toolbar-button tooltip-onhover alignleft" id="like">
            <?php
            $post_like = new CPM_Like('post');
            $post_like->display();
            ?>
        </div>

        <!-- Post Print Button -->
        <div class="toolbar-button tooltip-onhover alignright" id="print">
            <a class="post-print-button" href="javascript:window.print()" target="_blank">
                <?= foodiepro_get_icon('print'); ?>
                <span class="button-caption"><?php echo __('Print', 'foodiepro'); ?></span>
            </a>
            <?php
            $args = array(
                'content' =>  __('Print this post', 'foodiepro'),
                'valign' => 'above',
                'halign' => 'right',
            );
            Tooltip::display($args);
            ?>
        </div>

    </div>

</div>

<?php
