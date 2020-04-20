<?php 

$theme_directory = get_template_directory();

include("{$theme_directory}/countries.php");
include("{$theme_directory}/languages.php");
$subscribed = get_user_meta($user->ID, 'newsletter', true);

?>

<?php if($complete === true && $edit === false): ?>
    <div class="profile__container">
        <section class="profile__success-message-container"> 
            <h1 class="profile__title"><?php _e('CONGRATULATIONS!', 'community-portal'); ?></h1>
            <p class="profile__success-message">
                <?php 
                    _e('Your Account has been created! You can keep adding to your profile or dive right in. You are now ready to connect with other users, participate in events and projects, and get involved in the Mozilla community.', 'community-portal');
                ?>
            </p>
			<?php if(isset($subscribed) && intval($subscribed) !== 1): ?>	
				<p class="profile__error-message">
					<?php 
					    _e('Notice: We had a problem registering you for our newsletter. Please try signing up again later. To try again ', 'community-portal');
					?>
						<a class="newsletter__link" href="/newsletter">
							<?php _e('Click here', 'community-portal') ?>
						</a> 
				</p>
			<?php endif;?>
            <div class="profile__button-container">
                <a href="/people/<?php print $updated_username ? $updated_username : $user->user_nicename; ?>/profile/edit/group/1/" class="profile__button"><?php _e('Complete your profile', 'community-portal'); ?></a><a href="" class="profile__button profile__button--secondary"><?php _e('Go back to browsing', 'community-portal'); ?></a>
            </div>
        </section>
    </div>
<?php else: ?>
    <div class="profile__hero">
        <div class="profile__hero-container">
            <div class="profile__hero-content">
                <h1 class="profile__title"><?php (isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree') ? _e('Edit Profile', 'community-portal') : _e('Complete Profile', 'community-portal'); ?></h1>
                <p class="profile__hero-copy profile__hero-copy--green">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 16V12" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="8" r="1" fill="black"/>
                    </svg>
                    <span>
                        <?php _e('We\'ve pre-populated some of your information via your connected account with ', 'community-portal'); ?>
                        <a href="#" class="profile__hero-link"><?php _e('Mozilla SSO.', 'community-portal'); ?></a>
                    </span>
                </p>
            </div>
        </div>
    </div>
    <form class="profile__form" id="complete-profile-form" method="post" novalidate>
        <?php print wp_nonce_field('protect_content', 'my_nonce_field'); ?>
        <section class="profile__form-container profile__form-container--first">
            <div class="profile__form-primary">
                <h2 class="profile__form-title"><?php _e('Primary Information', 'community-portal'); ?></h2>
                <div class="profile__select-container">
                    <label class="profile__label"><?php _e('Visibility Settings', 'community-portal'); ?></label>
                    <select id="profile-visibility" name="profile_visibility" class="profile__select">
                        <option><?php _e('Custom', 'community-portal'); ?></option>
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php if(isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree'): ?>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container profile__input-container--profile">
                    <label class="profile__label" for="image-url"><?php _e('Profile Photo (optional)', 'community-portal'); ?></label>
                        <?php 
                            if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
                                if(isset($form['image_url']) && strlen($form['image_url']) > 0) {
                                    $avatar_url = preg_replace("/^http:/i", "https:", $form['image_url']);
                                } else {
                                    if(is_array($community_fields) && isset($community_fields['image_url']) && strlen($community_fields['image_url']) > 0) {
                                        $avatar_url = preg_replace("/^http:/i", "https:", $community_fields['image_url']);
                                    }
                                }
                            } else {
                                if(isset($form['image_url']) && strlen($form['image_url']) > 0) {
                                    $avatar_url = $form['image_url'];
                                } else {
                                    if(is_array($community_fields) && isset($community_fields['image_url']) && strlen($community_fields['image_url']) > 0) {
                                        $avatar_url = $community_fields['image_url'];
                                    }
                                }
                            }
                            
                        ?>
                        <div id="dropzone-photo-uploader" class="profile__image-upload"<?php if($form && isset($form['image_url']) && strlen($form['image_url']) > 0): ?> style="background: url('<?php print $avatar_url; ?>') cover;"<?php else: ?><?php if(is_array($community_fields) && isset($community_fields['image_url']) && strlen($community_fields['image_url']) > 0): ?> style="background: url('<?php print $avatar_url; ?>'); background-size: cover;"<?php endif; ?><?php endif; ?>>
							<div class="dz-message" data-dz-message="">
								<div class="profile__image-instructions">
									<div class="form__error-container">
										<div class="form__error form__error--image"></div>
									</div>
									<button id="dropzone-trigger" type="button" class="dropzone__image-instructions profile__image-instructions <?php if(isset($community_fields['image_url']) || strlen($community_fields['image_url']) !== 0):?> dropzone__image-instructions--hidden <?php endif; ?>">
										<?php _e('Click or drag a photo above', 'community-portal'); ?>
										<span><?php _e('minimum dimensions 175px by 175px', 'community-portal'); ?></span>
									</button>
								</div>
								<button class="dz-remove<?php if(!isset($community_fields['image_url']) || strlen($community_fields['image_url']) === 0): ?> dz-remove--hide<?php endif; ?>" type="button" data-dz-remove="" ><?php _e('Remove file', 'community-portal'); ?></button>
							</div>
                    </div>
                    <input type="hidden" name="image_url" id="image-url" value="<?php if($form && isset($form['image_url'])): ?><?php $form['image_url']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['image_url'])): ?><?php print $community_fields['image_url']; ?><?php endif; ?><?php endif; ?>" />
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-image-visibility" name="profile_image_url_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_image_url_visibility']) && $form['profile_image_url_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_image_url_visibility']) && $community_fields['profile_image_url_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="username"><?php _e('Username (required)', 'community-portal'); ?></label>
                    <input type="text" name="username" id="username" class="profile__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['username']) || (isset($form['username']) && empty(trim($form['username'])) || isset($form['username_error_message']) )): ?> profile__input--error<?php endif; ?>" placeholder="<?php _e('Username', 'community-portal'); ?>" value="<?php print isset($form['username']) ? $form['username'] : $user->user_nicename; ?>"  required/>
                    <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['username']) || (isset($form['username']) && empty(trim($form['username'])) || isset($form['username_error_message']))): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php if(isset($form['username_error_message'])): ?><?php print $form['username_error_message']; ?><?php else: ?><?php _e('This field is required', 'community-portal'); ?><?php endif; ?></div>
                    </div>
                    <span class="profile__input-desc"><?php _e('Usernames are public', 'community-portal'); ?></span>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="username-visibility" name="username_visibility" class="profile__select select--disabled" disabled>
                        <option value="<?php print PrivacySettings::PUBLIC_USERS; ?>"><?php _e('Public (Everyone)', 'community-portal'); ?></option>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="first-name"><?php _e('First Name (required)', 'community-portal'); ?></label>
                    <input type="text" name="first_name" id="first-name" class="profile__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['first_name']) || (isset($form['first_name']) && empty(trim($form['first_name'])) )): ?> profile__input--error<?php endif; ?>" placeholder="<?php _e('First Name', 'community-portal'); ?>" value="<?php print isset($form['first_name']) ? $form['first_name'] : $meta['first_name'][0]; ?>" required />
                    <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['first_name']) || (isset($form['first_name']) && empty(trim($form['first_name'])) )): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php _e('This field is required', 'community-portal'); ?></div>
                    </div>
                    <span class="profile__input-desc"><?php _e('Your first name is always visible to registered users', 'community-portal'); ?></span>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="firstname-visibility" name="first_name_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <?php if($value != 'Private (Only Me)'): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($meta['first_name_visibility'][0]) && $meta['first_name_visibility'][0] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="last-name"><?php _e('Last Name (required)', 'community-portal'); ?></label>
                    <input type="text" name="last_name" id="first-name" class="profile__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['last_name']) || (isset($form['last_name']) && empty(trim($form['last_name'])) )): ?> profile__input--error<?php endif; ?>" placeholder="<?php _e('Last Name', 'community-portal'); ?>" value="<?php print isset($form['last_name']) ? $form['last_name'] : $meta['last_name'][0]; ?>" required />
                    <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['last_name']) || (isset($form['last_name']) && empty(trim($form['last_name'])) )): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php _e('This field is required', 'community-portal'); ?></div>
                    </div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="lastname-visibility" name="last_name_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($meta['last_name_visibility'][0]) && $meta['last_name_visibility'][0] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php if(isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree'): ?>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__select-container profile__select-container--full">
                    <label class="profile__label" for="pronoun"><?php _e('Preferred Pronouns (optional)', 'community-portal'); ?></label>
                    <select id="pronoun" name="pronoun" class="profile__select">
                        <option value=""><?php _e('Preferred Pronoun', 'community-portal'); ?></option> 
                        <?php foreach($pronouns AS $p): ?>
                        <option value="<?php print $p; ?>"<?php if($form && isset($form['pronoun']) && $form['pronoun'] == $p): ?> selected<?php else: ?><?php if(isset($community_fields['pronoun']) && $community_fields['pronoun'] == $p): ?> selected<?php endif; ?><?php endif; ?>><?php print $p; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-pronoun-visibility" name="profile_pronoun_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_pronoun_visibility']) && $form['profile_pronoun_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_pronoun_visibility']) && $community_fields['profile_pronoun_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="bio"><?php _e('Bio (optional)', 'community-portal'); ?></label>
                    <textarea name="bio" id="bio" class="profile__textarea" maxlength="3000"><?php if($form && isset($form['bio'])): ?><?php $form['bio']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['bio'])): ?><?php print $community_fields['bio']; ?><?php endif; ?><?php endif; ?></textarea>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php _e('Can be viewed by', 'community-portal'); ?></label>

                    <select id="profile-bio-visibility" name="profile_bio_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_bio_visibility']) && $form['profile_bio_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_bio_visibility']) && $community_fields['profile_bio_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__select-container profile__select-container--inline profile__select-container--half">
                    <label class="profile__label" for="country"><?php _e('Country (optional)', 'community-portal'); ?></label>
                    <select id="country" name="country" class="profile__select">
                        <option value="0"><?php _e('Country', 'community-portal'); ?></option>
                        <?php foreach($countries AS $key    =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['country']) && $form['country'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['country']) && $community_fields['country'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="profile__input-container">
                    <label class="profile__label" for="city"><?php _e('City (optional)', 'community-portal'); ?></label>
                    <input type="text" name="city" id="city" class="profile__input" placeholder="<?php _e('City', 'community-portal'); ?>" value="<?php print isset($form['city']) ? $form['city'] : $community_fields['city']; ?>" maxlength="180" />
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for=""><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-location-visibility" name="profile_location_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_location_visibility']) && $form['profile_location_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($meta['profile_location_visibility'][0]) && $meta['profile_location_visibility'][0] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="email"><?php _e('Email contact (required)', 'community-portal'); ?></label>
                    <input type="email" name="email" id="email" class="profile__input<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['email']) || (isset($form['email']) && empty(trim($form['email'])) || isset($form['email_error_message']))): ?> profile__input--error<?php endif; ?>" placeholder="<?php _e('Email', 'community-portal'); ?>" value="<?php print isset($form['email']) ? $form['email'] : $user->user_email; ?>" required/>
                    <div class="form__error-container<?php if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($form['email']) || (isset($form['email']) && empty(trim($form['email'])) || isset($form['email_error_message']))): ?> form__error-container--visible<?php endif; ?>">
                        <div class="form__error"><?php if(isset($form['email_error_message'])): ?><?php print $form['email_error_message']; ?><?php else: ?><?php _e('This field is required', 'community-portal'); ?><?php endif; ?></div>
                    </div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="email-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="email-visibility" name="email_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($meta['email_visibility'][0]) && $meta['email_visibility'][0] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php if(isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree'): ?>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="phone"><?php _e('Phone contact (optional)', 'community-portal'); ?></label>
                    <input type="text" name="phone" id="phone" class="profile__input" value="<?php if($form && isset($form['phone'])): ?><?php $form['phone']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['phone'])): ?><?php print $community_fields['phone']; ?><?php endif; ?><?php endif; ?>"/>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-phone-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-phone-visibility" name="profile_phone_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($community_fields['profile_phone_visibility']) && $community_fields['profile_phone_visibility'] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php endif; ?>
        </section>
        <?php if(isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree'): ?>
        <section class="profile__form-container">
            <div class="profile__form-primary">
                <h2 class="profile__form-title"><?php _e('Social Links', 'community-portal'); ?></h2>
                <div class="profile__select-container">
                    <label class="profile__label"><?php _e('Visibility Settings', 'community-portal'); ?></label>
                    <select id="social-visibility" name="social_visibility" class="profile__select">
                        <option><?php _e('Custom', 'community-portal'); ?></option>
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="discourse"><?php _e('Mozilla Discourse username (optional)', 'community-portal'); ?></label>
                    <input type="text" name="discourse" id="discourse" class="profile__input" value="<?php if($form && isset($form['discourse'])): ?><?php $form['discourse']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['discourse'])): ?><?php print $community_fields['discourse']; ?><?php endif; ?><?php endif; ?>"/>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-discourse-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-discourse-visibility" name="profile_discourse_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($community_fields['profile_discourse_visibility']) && $community_fields['profile_discourse_visibility'] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="facebook"><?php _e('Facebook username (optional)', 'community-portal'); ?></label>
                    <input type="text" name="facebook" id="facebook" class="profile__input" value="<?php if($form && isset($form['facebook'])): ?><?php $form['facebook']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['facebook'])): ?><?php print $community_fields['facebook']; ?><?php endif; ?><?php endif; ?>"/>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-facebook-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-facebook-visibility" name="profile_facebook_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($community_fields['profile_facebook_visibility']) && $community_fields['profile_facebook_visibility'] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="twitter"><?php _e('Twitter username (optional)', 'community-portal'); ?></label>
                    <input type="text" name="twitter" id="twitter" class="profile__input" value="<?php if($form && isset($form['facebook'])): ?><?php $form['twitter']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['twitter'])): ?><?php print $community_fields['twitter']; ?><?php endif; ?><?php endif; ?>"/>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-twitter-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-twitter-visibility" name="profile_twitter_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($community_fields['profile_twitter_visibility']) && $community_fields['profile_twitter_visibility'] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="linkedin"><?php _e('LinkedIn username (optional)', 'community-portal'); ?></label>
                    <input type="text" name="linkedin" id="linkedin" class="profile__input" value="<?php if($form && isset($form['linkedin'])): ?><?php $form['linkedin']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['linkedin'])): ?><?php print $community_fields['linkedin']; ?><?php endif; ?><?php endif; ?>"/>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-linkedin-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-linkedin-visibility" name="profile_linkedin_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($community_fields['profile_linkedin_visibility']) && $community_fields['profile_linkedin_visibility'] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="github"><?php _e('Github username (optional)', 'community-portal'); ?></label>
                    <input type="text" name="github" id="github" class="profile__input" value="<?php if($form && isset($form['github'])): ?><?php $form['github']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['github'])): ?><?php print $community_fields['github']; ?><?php endif; ?><?php endif; ?>"/>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-github-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-github-visibility" name="profile_github_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($community_fields['profile_github_visibility']) && $community_fields['profile_github_visibility'] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="telegram"><?php _e('Telegram username (optional)', 'community-portal'); ?></label>
                    <input type="text" name="telegram" id="telegram" class="profile__input" value="<?php if($form && isset($form['telegram'])): ?><?php $form['telegram']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['telegram'])): ?><?php print $community_fields['telegram']; ?><?php endif; ?><?php endif; ?>"/>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-telegram-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-telegram-visibility" name="profile_telegram_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($community_fields['profile_telegram_visibility']) && $community_fields['profile_telegram_visibility'] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
			<hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <label class="profile__label" for="matrix"><?php _e('Matrix username (optional)', 'community-portal'); ?></label>
                    <input placeholder="username:domain" type="text" name="matrix" id="matrix" class="profile__input" value="<?php if($form && isset($form['matrix'])): ?><?php $form['matrix']; ?><?php else: ?><?php if(is_array($community_fields) && isset($community_fields['matrix'])): ?><?php print $community_fields['matrix']; ?><?php endif; ?><?php endif; ?>"/>
					<div class="form__error-container form__error-container--checkbox">
						<div class="form__error"><?php _e('Please format as username:domain', 'community-portal'); ?></div>
					</div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-matrix-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-matrix-visibility" name="profile_matrix_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if(isset($community_fields['profile_matrix_visibility']) && $community_fields['profile_matrix_visibility'] == $key): ?> selected<?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </section>
        <section class="profile__form-container">
            <div class="profile__form-primary">
                <h2 class="profile__form-title"><?php _e('Communication & Interests', 'community-portal'); ?></h2>
                <div class="profile__select-container">
                    <label class="profile__label"><?php _e('Visibility Settings', 'community-portal'); ?></label>
                    <select id="communication-visibility" name="communication_visibility" class="profile__select">
                        <option><?php _e('Custom', 'community-portal'); ?></option>   
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php 
        
                if($form && isset($form['languages']) && is_array($form['languages'])) {
                    $languages_spoken = $form['languages'];
                } else {
                    if(is_array($community_fields) && isset($community_fields['languages']) && is_array($community_fields['languages'])) {
                        $languages_spoken = array_filter($community_fields['languages']);
                    } else {
                        $languages_spoken = Array();  
                    }
                }
            ?>
    

            <?php if(sizeof($languages_spoken) < 2 ): ?>
                <hr class="profile__keyline" />
                <div class="profile__form-field profile__form-field--tight">
                    <div class="profile__select-container profile__select-container--full profile__select-container--first">
                        <label class="profile__label" for="pronoun"><?php _e('Languages spoken (optional)', 'community-portal'); ?></label>
                        <select id="languages-1" name="languages[]" class="profile__select">
                            <option value=""><?php _e('Make Selection', 'community-portal'); ?>
                            <?php foreach($languages AS $key    =>  $language): ?>
                            <option value="<?php print $key; ?>"<?php if($form && isset($form['langauges'][0]) && $form['languages'][0] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['languages'][0]) && $community_fields['languages'][0] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $language; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="profile__select-container profile__select-container--hide-mobile profile__select-container--flex">
                        <label class="profile__label profile__label--full profile__label--max" for="profile-languages-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                        <select id="profile-languages-visibility" name="profile_languages_visibility" class="profile__select profile__select--flex">
                            <?php foreach($visibility_options AS $key   =>  $value): ?>
                            <option value="<?php print $key; ?>"<?php if(isset($community_fields['profile_languages_visibility']) && $community_fields['profile_languages_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_languages_visibility']) && $community_fields['profile_languages_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="profile__form-field profile__form-field--tight profile__form-field--hidden">
                    <div class="profile__select-container profile__select-container--full profile__select-container--no-label profile__select-container--languages">
                        <select id="languages-<?php print $index; ?>" name="languages[]" class="profile__select profile__select--short profile__select--hide">
                            <option value=""><?php _e('Make Selection (optional)', 'community-portal'); ?>
                            <?php foreach($languages AS $key    =>  $language): ?>
                            <option value="<?php print $key; ?>"><?php print $language; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="profile__remove-language">&mdash;</button>
                    </div>
                    <div class="profile__select-container profile__select-container--empty">
          
                    </div>                      
                </div>
                <div class="profile__add-language-container"> 
                    <a href="#" class="profile__add-language"><?php _e('Add Another Language', 'community-portal'); ?></a>
                </div>
            <?php else: ?>
                <hr class="profile__keyline" />
                <?php foreach($languages_spoken AS $index =>  $value): ?>
                    <div class="profile__form-field profile__form-field--tight">
                        <div class="profile__select-container profile__select-container--full<?php if($index > 0): ?> profile__select-container--no-label<?php endif; ?><?php if($index === 0): ?> profile__select-container--first<?php endif; ?>">
                        <?php if($index === 0): ?><label class="profile__label" for="languages"><?php _e('Languages spoken (optional)', 'community-portal'); ?></label><?php endif; ?>
                            <select id="languages-<?php print $index; ?>" name="languages[]" class="profile__select<?php if($index > 0): ?> profile__select--short<?php endif; ?>">
                                <option value=""><?php _e('Make Selection', 'community-portal'); ?>
                                <?php foreach($languages AS $key    =>  $language): ?>
                                <option value="<?php print $key; ?>"<?php if($form && isset($form['languages'][$index]) && $form['languages'][$index] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['languages'][$index]) && $community_fields['languages'][$index] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $language; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if($index > 0): ?>
                            <button type="button" class="profile__remove-language">&mdash;</button>
                            <?php endif; ?>
                        </div>
                        <?php if($index === 0 ): ?>
                        <div class="profile__select-container profile__select-container--hide-mobile profile__select-container--flex">
                            <label class="profile__label profile__label--full profile__label--max" for="profile-languages-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                            <select id="profile-languages-visibility" class="profile__select profile__select--flex">
                                <option value=""><?php _e('Make Selection', 'community-portal'); ?>
                                <?php foreach($visibility_options AS $key   =>  $value): ?>
                                <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_languages_visibility']) && $form['profile_languages_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_languages_visibility']) && $community_fields['profile_languages_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                            <div class="profile__select-container profile__select-container--empty">
                  
                            </div>  
                        <?php endif; ?>
                    </div>
                    <?php if(($index + 1) === sizeof($languages_spoken)): ?>
                    <div class="profile__add-language-container"> 
                        <a href="#" class="profile__add-language"><?php _e('Add Another Language', 'community-portal'); ?></a>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="profile__select-container profile__select-container--mobile">
                <label class="profile__label" for=""><?php _e('Can be viewed by', 'community-portal'); ?></label>
                <select id="profile-languages-visibility-mobile" class="profile__select profile__select--mobile">
                    <?php foreach($visibility_options AS $key   =>  $value): ?>
                    <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_languages_visibility']) && $form['profile_languages_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_languages_visibility']) && $community_fields['profile_languages_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="profile_languages_visibility" value="" />
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div>
					<fieldset class="fieldset">
						<legend class="profile__label"><?php _e('Skills and interests (optional)', 'community-portal'); ?></legend>
						<?php 
							// Get all tags
							$tags = get_tags(array('hide_empty' => false));
						?>
						<div class="profile__tag-container">
							<?php foreach($tags AS $tag): ?>
								<input class="profile__checkbox" type="checkbox" id="<?php echo $tag->slug ?>" data-value="<?php print $tag->slug; ?>">
								<label class="profile__tag<?php if(in_array($tag->slug, $form_tags)): ?> profile__tag--active<?php endif; ?>" for="<?php echo $tag->slug ?>"><?php echo $tag->name ?></label>
							<?php endforeach; ?>
						</div>
						<input type="hidden" value="<?php print ($form && isset($form['tags'])) ? $form['tags'] : ($community_fields && isset($community_fields['tags'])) ? $community_fields['tags'] : ""; ?>" name="tags" id="tags" /> 
					</fieldset>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-tags-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-tags-visibility" name="profile_tags_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_tags_visibility']) && $form['profile_tags_visibility'] == $key): ?> selected<?php else: ?><?php if(isset($community_fields['profile_tags_visibility']) && $community_fields['profile_tags_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </section>
        <section class="profile__form-container">
            <div class="profile__form-primary">
                <h2 class="profile__form-title"><?php _e('Community Portal Activity', 'community-portal'); ?></h2>
                <div class="profile__select-container">
                    <label class="profile__label"><?php _e('Visibility Settings', 'community-portal'); ?></label>
                    <select id="portal-visibility" name="portal_visibility" class="profile__select">
                        <option><?php _e('Custom', 'community-portal'); ?></option>
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <div class="profile__copy"><?php _e('Groups joined', 'community-portal'); ?></div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-discourse-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-groups-joined-visibility" name="profile_groups_joined_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_groups_joined_visibility']) && $form['profile_groups_joined_visibility'] == $key): ?> <?php else: ?><?php if(isset($community_fields['profile_groups_joined_visibility']) && $community_fields['profile_groups_joined_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <div class="profile__copy"><?php _e('Events attended', 'community-portal'); ?></div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-discourse-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-events-attended-visibility" name="profile_events_attended_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_events_attended_visibility']) && $form['profile_events_attended_visibility'] == $key): ?> <?php else: ?><?php if(isset($community_fields['profile_events_attended_visibility']) && $community_fields['profile_events_attended_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <div class="profile__copy"><?php _e('Events organized', 'community-portal'); ?></div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-discourse-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-events-organized-visibility" name="profile_events_organized_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_events_organized_visibility']) && $form['profile_events_organized_visibility'] == $key): ?> <?php else: ?><?php if(isset($community_fields['profile_events_organized_visibility']) && $community_fields['profile_events_organized_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="profile__keyline" />
            <div class="profile__form-field">
                <div class="profile__input-container">
                    <div class="profile__copy"><?php _e('Campaigns participated in', 'community-portal'); ?></div>
                </div>
                <div class="profile__select-container">
                    <label class="profile__label" for="profile-discourse-visibility"><?php _e('Can be viewed by', 'community-portal'); ?></label>
                    <select id="profile-campaigns-visibility" name="profile_campaigns_visibility" class="profile__select">
                        <?php foreach($visibility_options AS $key   =>  $value): ?>
                        <option value="<?php print $key; ?>"<?php if($form && isset($form['profile_campaigns_visibility']) && $form['profile_campaigns_visibility'] == $key): ?> <?php else: ?><?php if(isset($community_fields['profile_campaigns_visibility']) && $community_fields['profile_campaigns_visibility'] == $key): ?> selected<?php endif; ?><?php endif; ?>><?php print $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </section>  
        <?php endif; ?>
        <?php
            $category_id = get_cat_ID('Community Participation Guidelines');

            $guidelines = get_posts(Array(
                'numberposts'   =>  1,
                'category'      =>  $category_id
            ));  
        ?>
		<?php 
			if (!isset($subscribed) || (isset($subscribed) && intval($subscribed) !== 1)):
			?>
				<section class="profile__form-container">
					<div class="profile__newsletter">
						<?php include get_template_directory()."/templates/newsletter_form.php"; ?>
					</div>
				</section>
			<?php
			endif;
		?>
		<?php if(!isset($meta['agree'][0]) || $meta['agree'][0] != 'I Agree'): ?>
        <?php if(sizeof($guidelines) === 1): ?> 
        <section class="profile__form-container cpg">
			<?php print apply_filters('the_content', $guidelines[0]->post_content); ?>
			<input class="checkbox--hidden" type="checkbox" name="agree" id="agree" value="<?php print "I Agree"; ?>" required />
            <label class="create-group__checkbox-container cpg__label" for="agree">
                <p class="create-group__checkbox-container__copy">
					<?php _e('I agree to respect and adhere to', 'community-portal'); ?>
					<a class="create-group__checkbox-container__link" href="https://www.mozilla.org/en-US/about/governance/policies/participation/"><?php _e('Mozilla\'s Community Participation Guidelines*', 'community-portal'); ?></a>
                </p>
                <div class="form__error-container form__error-container--checkbox">
                    <div class="form__error"><?php _e('This field is required', 'community-portal'); ?></div>
				</div>
            </label>
        </section>
		<?php endif ?>
		<?php endif ?>
        <section class="profile__cta-container">
            <input type="submit" class="profile__cta" value="<?php _e('Complete Profile', 'community-portal'); ?>" />
            <?php if(isset($meta['agree'][0]) && $meta['agree'][0] == 'I Agree'): ?>
            <a id="profile-delete-account" class="profile__delete-cta"><?php _e('Delete Profile', 'community-portal'); ?></a>
            <div class="profile__delete-account-error profile__delete-account-error--hidden"><?php _e('Could not delete profile at this time, please contact a community manager', 'community-portal'); ?></div>
            <?php endif; ?>
        </section>
    </form>
    <?php endif; ?>