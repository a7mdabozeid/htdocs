<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$provider = $this->getProvider();

$settings = $provider->settings;
?>

<div class="nsl-admin-sub-content">
    <?php
    $this->renderSettingsHeader();
    ?>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" novalidate="novalidate">

        <?php wp_nonce_field('nextend-social-login'); ?>
        <input type="hidden" name="action" value="nextend-social-login"/>
        <input type="hidden" name="view" value="provider-<?php echo $provider->getId(); ?>"/>
        <input type="hidden" name="subview" value="settings"/>
        <input type="hidden" name="settings_saved" value="1"/>
        <input type="hidden" name="tested" id="tested" value="<?php echo esc_attr($settings->get('tested')); ?>"/>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="client_id"><?php _e('Channel ID', 'nextend-facebook-connect'); ?>
                        - <em>(<?php _e('Required', 'nextend-facebook-connect'); ?>)</em></label>
                </th>
                <td>
                    <input name="client_id" type="text" id="client_id"
                           value="<?php echo esc_attr($settings->get('client_id')); ?>" class="regular-text">
                    <p class="description"
                       id="tagline-client_id"><?php printf(__('If you are not sure what is your %1$s, please head over to <a href="%2$s">Getting Started</a>', 'nextend-facebook-connect'), 'Channel ID', $this->getUrl()); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label
                            for="client_secret"><?php _e('Channel Secret', 'nextend-facebook-connect'); ?>
                        - <em>(<?php _e('Required', 'nextend-facebook-connect'); ?>)</em></label></th>
                <td><input name="client_secret" type="text" id="client_secret"
                           value="<?php echo esc_attr($settings->get('client_secret')); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Force reauthorization on each login', 'nextend-facebook-connect'); ?></th>
                <td>
                    <label for="force_reauth">
                        <input type="hidden" name="force_reauth" value="0">
                        <input type="checkbox" name="force_reauth" id="force_reauth" value="1" <?php if ($settings->get('force_reauth') == 1) : ?> checked="checked" <?php endif; ?>>
                        <?php _e('Enabled', 'nextend-facebook-connect'); ?>
                    </label>
                    <p class="description" id="tagline-force_reauth">
                        <?php _e('Enable, when you want to see the consent screen on each login.', 'nextend-facebook-connect'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Add LINE Official Account as a friend', 'nextend-facebook-connect'); ?></th>
                <td>
                    <?php
                    $bot_prompt = $settings->get('bot_prompt');
                    ?>

                    <fieldset>
                        <label><input type="radio" name="bot_prompt"
                                      value="" <?php if ($bot_prompt == '') : ?> checked="checked" <?php endif; ?>>
                            <span><?php _e('Don\'t display', 'nextend-facebook-connect'); ?></span>
                        </label><br>
                        <label><input type="radio" name="bot_prompt"
                                      value="normal" <?php if ($bot_prompt == 'normal') : ?> checked="checked" <?php endif; ?>>
                            <span><?php _e('Display the add friend option in the consent screen', 'nextend-facebook-connect'); ?></span>
                        </label><br>
                        <label><input type="radio" name="bot_prompt"
                                      value="aggressive" <?php if ($bot_prompt == 'aggressive') : ?> checked="checked" <?php endif; ?>>
                            <span><?php _e('Opens a new screen with the add friend option after the consent screen', 'nextend-facebook-connect'); ?></span>
                        </label>
                        <p class="description"
                           id="tagline-bot_prompt"><?php printf(__('If you have a LINE Official Account, people can add it as a friend when they authorize your App. %1$sLearn more%2$s.', 'nextend-facebook-connect'), '<a href="https://nextendweb.com/nextend-social-login-docs/provider-line/#how-to-add-friend" target="_blank">', '</a>'); ?></p>
                    </fieldset>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                 value="<?php _e('Save Changes'); ?>"></p>

        <?php
        $this->renderOtherSettings();

        $this->renderProSettings();
        ?>
    </form>
</div>