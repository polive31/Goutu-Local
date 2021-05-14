<?php

echo $args['before_widget'];

$PeepSoProfile=PeepSoProfile::get_instance();
$PeepSoUser = $PeepSoProfile->user;
$position = $instance['content_position'];

?>

	<div class="psw-userbar psw-userbar--<?php echo $position; ?> ps-js-widget-userbar">

		<?php
			if($instance['user_id'] > 0) {

			$user  = $instance['user'];
			?>

				<div class="psw-userbar__user">

					<div class="ps-notifs psw-notifs--userbar ps-js-widget-userbar-notifications">
							<?php
								do_action('peepso_action_userbar_notifications_before', $user->get_id());

								// Notifications
								echo $instance['toolbar'];

								do_action('peepso_action_userbar_notifications_after', $user->get_id());
							?>
					</div>


					<!-- <?php //if(isset($instance['show_vip']) && 1 == intval($instance['show_vip'])) { ?>
					<div class="ps-vip__icons"><?php //do_action('peepso_action_userbar_user_name_before', $user->get_id()); ?></div>
					<?php //} ?> -->

					<?php
								$name = $user->get_firstname();
                            ?>

					        <div class="psw-userbar__name"><a href="<?php echo $user->get_profileurl();?>"><?php echo $name; ?></a></div>

					        <?php


					// if(isset($instance['show_badges']) && 1 == intval($instance['show_badges'])) {
					// 	do_action('peepso_action_userbar_user_name_after', $user->get_id());
					// }
					?>

					<?php
						// Profile Submenu extra links
						$instance['links']['peepso-core-preferences'] = array(
							'href' => $user->get_profileurl() . 'about/preferences/',
							'icon' => 'gcis gci-user-edit',
							'label' => __('Preferences', 'peepso-core'),
						);

						$instance['links']['peepso-core-logout'] = array(
							'href' => PeepSo::get_page('logout'),
							'icon' => 'gcis gci-power-off',
							'label' => __('Log Out', 'peepso-core'),
							'widget'=>TRUE,
						);
					?>

					<?php if(isset($instance['show_usermenu']) && 1 == intval($instance['show_usermenu'])) { ?>
					<div class="psw-userbar__menu ps-dropdown ps-dropdown--menu ps-dropdown--left ps-js-dropdown">
						<a href="javascript:" class="ps-dropdown__toggle psw-userbar__menu-toggle ps-js-dropdown-toggle">
							<i class="gcis gci-angle-down"></i>
						</a>
						<div class="ps-dropdown__menu ps-js-dropdown-menu">
							<?php
								foreach($instance['links'] as $id => $link)
								{
									if(!isset($link['label']) || !isset($link['href']) || !isset($link['icon'])) {
										var_dump($link);
									}

									$class = isset($link['class']) ? $link['class'] : '' ;

									$href = $user->get_profileurl(). $link['href'];
									if('http' == substr(strtolower($link['href']), 0,4)) {
										$href = $link['href'];
									}

									echo '<a href="' . $href . '" class="' . $class . '"><i class="' . $link['icon'] . '"></i> ' . $link['label'] . '</a>';
								}
							?>
						</div>
					</div>
					<?php } ?>

					<?php
					// if(isset($instance['show_avatar']) && 1 == intval($instance['show_avatar'])) { ?>
					<div class="ps-avatar psw-avatar--userbar">
						<a href="<?php echo $user->get_profileurl();?>">
							<img src="<?php echo $user->get_avatar();?>" alt="<?php echo $user->get_fullname();?> avatar" title="<?php echo $user->get_profileurl();?>">
						</a>
					</div>
					<?php //} ?>


					<?php if(isset($instance['show_logout']) && 1 == intval($instance['show_logout'])) { ?>
					<a class="psw-userbar__logout" href="<?php echo PeepSo::get_page('logout'); ?>" title="<?php echo __('Log Out', 'peepso-core'); ?>" arialabel="<?php echo __('Log Out', 'peepso-core'); ?>">
						<i class="gcis gci-power-off"></i>
					</a>
					<?php } ?>
				</div>
			<?php
		} else {
			?>
			<a href="<?php echo PeepSo::get_page('activity'); ?>"><?php echo __('Log in', 'peepso-core'); ?></a>
		<?php
		}
		?>

	</div>

<?php
echo $args['after_widget'];
// EOF
