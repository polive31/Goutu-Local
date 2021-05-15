<div class="peepso">
  <div class="ps-page ps-page--messages">
    <?php // PeepSoTemplate::exec_template('general','navbar'); ?>

    <?php wp_nonce_field('load-messages', '_messages_nonce'); ?>
    <div class="ps-messages" id="inbox"></div>
  </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        ps_messages.load_messages('inbox', 1);
    });
</script>
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
