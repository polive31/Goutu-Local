<?php
$PeepSoProfile = PeepSoProfile::get_instance();
?>
<div class="peepso ps-page-profile">
    <section id="mainbody" class="ps-wrapper ps-clearfix">
        <section id="component" role="article" class="ps-clearfix">

            <div id="cProfileWrapper" class="ps-clearfix">

                <div class="ps-body">

                    <div class="ps-main ps-main-full'; ?>">
                        <!-- js_profile_feed_top -->
                        <div class="activity-stream-front">
                            <?php
                            PeepSoTemplate::exec_template('general', 'postbox-legacy', array('is_current_user' => $PeepSoProfile->is_current_user()));

                            // echo 'IN PROFILE OVERRIDE, for user : ' . $PeepSoProfile->user->get_fullname();
                            ?>

                            <div class="tab-pane active" id="stream">
                                <div id="ps-activitystream-recent" class="ps-stream-container" style="display:none"></div>
                                <div id="ps-activitystream" class="ps-stream-container" style="display:none"></div>

                                <div id="ps-activitystream-loading">
                                    <?php PeepSoTemplate::exec_template('activity', 'activity-placeholder'); ?>
                                </div>

                                <div id="ps-no-posts" class="ps-alert" style="display:none"><?php _e('No posts found.', 'peepso-core'); ?></div>
                                <div id="ps-no-posts-match" class="ps-alert" style="display:none"><?php _e('No posts found.', 'peepso-core'); ?></div>
                                <div id="ps-no-more-posts" class="ps-alert" style="display:none"><?php _e('Nothing more to show.', 'peepso-core'); ?></div>
                            </div>
                        </div><!-- end activity-stream-front -->

                        <?php PeepSoTemplate::exec_template('activity', 'dialogs'); ?>
                        <div id="apps-sortable" class="connectedSortable"></div>
                    </div><!-- cMain -->
                </div><!-- end row -->
            </div><!-- end cProfileWrapper -->
            <!-- js_bottom -->
            <div id="ps-dialogs" style="display:none">
                <?php do_action('peepso_profile_dialogs'); // give add-ons a chance to output some HTML
                ?>
            </div>
        </section>
        <!--end component-->
    </section>
    <!--end mainbody-->
</div>
<!--end row-->
