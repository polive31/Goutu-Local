<?php


class CSN_Notifications {

    const MODULE_ID = 1;// Blogposts module ID in Peepso

    public function send_notification_on_event($action, $from_id, $to_id, $post_id) {
        $peepsonot = PeepSoNotifications::get_instance();
        $post=get_post($post_id);
        $msg = sprintf(CPM_Assets::get_label($post->post_type, 'not_' . $action), $post->post_title);
        $peepsonot->add_notification($from_id, $to_id, $msg, 'foodiepro_' . $action, self::MODULE_ID, $post_id);
    }

}
