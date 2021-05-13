<div class="peepso ps-page-profile">

    <section id="mainbody" class="ps-page-unstyled">
        <section id="component" role="article" class="ps-clearfix">

            <?php if(get_current_user_id()) { ?>
            <?php
                if(get_current_user_id() == $view_user_id && apply_filters('peepso_permissions_photos_upload', TRUE)) {
                ?>
                <div class="ps-page__actions-wrapper">
                    <div class="ps-page__actions">
                        <a class="ps-btn ps-btn-small" href="#" onclick="peepso.photos.show_dialog_album(<?php echo get_current_user_id();?>, this); return false;"><i class="ps-icon-plus"></i><?php echo __('Create Album', 'picso'); ?></a>
                    </div>
                </div>
                <?php
                }
            ?>

            <div class="ps-tabs__wrapper">
                <div class="ps-tabs ps-tabs--arrows">
                    <div class="ps-tabs__item <?php if('latest' === $current) echo 'current' ?>"><a href="<?php echo PeepSoSharePhotos::get_url($view_user_id, 'latest'); ?>"><?php _e('Photos', 'picso'); ?></a></div>
                    <div class="ps-tabs__item <?php if('album' === $current) echo 'current' ?>"><a href="<?php echo PeepSoSharePhotos::get_url($view_user_id, 'album'); ?>"><?php _e('Albums', 'picso'); ?></a></div>
                </div>
            </div>

            <!--<div class="ps-page-filters">
                <select class="ps-select ps-full ps-js-photos-submenu" onchange="peepso.photos.select_menu(this);">
                    <option value="latest" <?php if('latest' === $current) echo 'selected' ?> data-url="<?php echo PeepSoSharePhotos::get_url($view_user_id, 'latest'); ?>">
                        <?php _e('Recently added photos', 'picso'); ?>
                    </option>
                    <option value="album"<?php if('album' === $current) echo 'selected' ?> data-url="<?php echo PeepSoSharePhotos::get_url($view_user_id, 'album'); ?>"><?php _e('Photo Albums', 'picso'); ?></option>
                </select>

                <?php
                if(get_current_user_id() == $view_user_id) {
                ?>
                <a class="ps-btn" href="#" onclick="peepso.photos.show_dialog_album(<?php echo get_current_user_id();?>, this); return false;"><i class="ps-icon-plus"></i><?php echo __('Create Album', 'picso'); ?></a>
                <?php
                }
                ?>
            </div>-->

            <div class="ps-clearfix mb-20"></div>

            <div class="ps-page-filters" style="display:none;">
                <select class="ps-select ps-full ps-js-<?php echo $type?>-sortby ps-js-<?php echo $type?>-sortby--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>">
                    <option value="desc"><?php _e('Newest first', 'picso');?></option>
                    <option value="asc"><?php _e('Oldest first', 'picso');?></option>
                </select>
            </div>

            <div class="ps-clearfix mb-20"></div>
            <div class="ps-<?php echo $type?> ps-js-<?php echo $type?> ps-js-<?php echo $type?>--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>"></div>
            <div class="ps-scroll ps-js-<?php echo $type?>-triggerscroll ps-js-<?php echo $type?>-triggerscroll--<?php echo  apply_filters('peepso_user_profile_id', 0); ?>">
                <img class="post-ajax-loader ps-js-<?php echo $type?>-loading" src="<?php echo PeepSo::get_asset('images/ajax-loader.gif'); ?>" alt="" style="display:none" />
            </div>
            <div class="ps-clearfix mb-20"></div>
            <?php } else {
                PeepSoTemplate::exec_template('general','login-profile-tab');
            }?>
        </section><!--end component-->
    </section><!--end mainbody-->
</div><!--end row-->

<?php PeepSoTemplate::exec_template('activity','dialogs'); ?>
