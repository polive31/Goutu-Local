<div class="peepso ps-page-profile">
	<section id="mainbody" class="ps-page-unstyled">
		<section id="component" role="article" class="ps-clearfix">
            <?php if(get_current_user_id()) { ?>
                <div class="ps-clearfix ps-groups ps-js-groups ps-js-groups--<?php echo apply_filters('peepso_user_profile_id', 0); ?>"></div>
                <div class="ps-scroll ps-groups-scroll ps-js-groups-triggerscroll ps-js-groups-triggerscroll--<?php echo apply_filters('peepso_user_profile_id', 0); ?>">
                    <img class="post-ajax-loader ps-js-groups-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
                </div>
            <?php
            } else {
                PeepSoTemplate::exec_template('general','login-profile-tab');
            }?>
		</section><!--end component-->
	</section><!--end mainbody-->
</div><!--end row-->
<?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
