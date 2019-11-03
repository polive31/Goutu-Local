		<?php $current_user = wp_get_current_user(); ?>

		<form action="<?php the_permalink(); ?>" id="contactForm" method="post">

			<!-- <ul class="forms"> -->
			<p class="fieldset inline"><?php echo __('Fields marked with an asterisk (<span class="error">*</span>) are mandatory.', 'foodiepro'); ?></p>

			<div class="fieldset aligned">
				<label for="contactName" class="requiredField"><?php echo __('Name', 'foodiepro'); ?></label>
				<input type="text" name="contactName" id="contactName" value="<?php if (isset($_POST['contactName'])) echo $_POST['contactName'];
																				elseif (is_user_logged_in()) echo $current_user->user_firstname . ' ' . $current_user->user_lastname;  ?>" />
				<?php if ($nameMissing != '') { ?>
					<span class="error"><?= $nameMissing; ?></span>
				<?php } ?>
			</div>

			<div class="fieldset aligned">
				<label class="aligned requiredField" for="email">Email</label>
				<input type="text" name="email" id="email" value="<?php if (isset($_POST['email']))  echo $_POST['email'];
																	elseif (is_user_logged_in()) echo $current_user->user_email; ?>" class="requiredField email" />
				<?php if ($emailMissing != '') { ?>
					<span class="error"><?= $emailMissing; ?></span>
				<?php } ?>
			</div>

			<div class="fieldset aligned">
				<label class="aligned" for="ccfSubject"><?php echo __('Subject', 'foodiepro'); ?></label>
				<input type="text" name="ccfSubject" id="ccfSubject" value="<?php if (isset($_POST['ccfSubject'])) echo $_POST['ccfSubject']; ?>" />
				<?php if ($subjectMissing != '') { ?>
					<span class="error"><?= $subjectMissing; ?></span>
				<?php } ?>
			</div>

			<div class="fieldset textarea">
				<div class="clearfix">
					<label for="commentsText" class="requiredField"><?php echo __('Message', 'foodiepro'); ?></label>
					<?php if ($commentMissing != '') { ?>
						<span class="error"><?= $commentMissing; ?></span>
						<?php } ?>
					</div>
					<textarea name="comments" id="commentsText" rows="10" cols="20"><?php
						if (isset($_POST['comments'])) {
							if (function_exists('stripslashes')) {
								echo stripslashes($_POST['comments']);
							} else {
								echo $_POST['comments'];
							}
						}?></textarea>
			</div>

			<div class="fieldset inline">
				<?php if ($captchaMissing != '') { ?>
					<span class="error"><?= $captchaMissing; ?></span>
					<br>
					<?php }
				if (self::get_captcha() == 'gcaptcha') {
					CGR_Public::display('alignleft');
				} elseif (self::get_captcha()) {
					echo CGR_Public::pdscaptcha("ask");
				}
				?>
			</div>

			<div class="fieldset inline">
				<div class="clearfix">
					<?php if ($privacyMissing != '') { ?>
						<span class="error"> <?= $privacyMissing; ?></span>
						<br>
					<?php } ?>
						<input type="checkbox" name="privacyNotice" id="privacyNotice" value="true" <?php if (isset($_POST['privacyNotice']) && $_POST['privacyNotice'] == true) echo 'checked'; ?> />
						<label for="privacyNotice" class="requiredField">
							<?php echo __('I accept that the data entered on this form is stored on the present website.', 'foodiepro'); ?>
						</label>
					</div>
				</div>

			<div class="fieldset inline"><input type="checkbox" name="sendCopy" id="sendCopy" value="true" <?php if (isset($_POST['sendCopy']) && $_POST['sendCopy'] == true) echo 'checked'; ?> /><label for="sendCopy"><?php echo __('Send a copy of this email to yourself', 'foodiepro'); ?></label>
			</div>


			<div class="fieldset buttons"><input type="hidden" name="submitted" id="submitted" value="true" /><button type="submit"><?php echo __('Send Message', 'foodiepro'); ?></button>
			</div>


			<!-- </ul> -->
		</form>
