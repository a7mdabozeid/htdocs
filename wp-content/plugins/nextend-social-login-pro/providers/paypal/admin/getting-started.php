<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$lastUpdated = '2021-09-09';

$provider = $this->getProvider();
?>
<div class="nsl-admin-sub-content">
    <div class="nsl-admin-getting-started">
        <h2 class="title"><?php _e('Getting Started', 'nextend-facebook-connect'); ?></h2>

        <p><?php printf(__('To allow your visitors to log in with their %1$s account, first you must create an %1$s App. The following guide will help you through the %1$s App creation process. After you have created your %1$s App, head over to "Settings" and configure the given "%2$s" and "%3$s" according to your %1$s App.', 'nextend-facebook-connect'), "PayPal", "Client ID", "Secret"); ?></p>

        <p><?php do_action('nsl_getting_started_warnings', $provider, $lastUpdated); ?></p>

        <h2 class="title"><?php printf(_x('Create %s', 'App creation', 'nextend-facebook-connect'), 'PayPal App'); ?></h2>

        <ol>
            <li><?php printf(__('Editing Live Apps are only possible with a %s. If you own one, go to the 4. step, if not click on the link!', 'nextend-facebook-connect'), '<a href="https://www.paypal.com/" target="_blank">PayPal Business Account</a>'); ?></li>
            <li><?php _e('Click on Registration and create a Business account.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('If you are done, follow the guide from the 5. step.', 'nextend-facebook-connect') ?></li>
            <li><?php printf(__('Log in with your %s credentials.', 'nextend-facebook-connect'), 'PayPal'); ?></li>
            <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://developer.paypal.com/developer/applications/" target="_blank">https://developer.paypal.com/developer/applications/</a>'); ?></li>
            <li><?php _e('There is a Sandbox/Live switch. Make sure "<b>Live</b>" is selected!', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Click the "<b>Create App</b>" button under the REST API apps section.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Fill the "<b>App Name</b>" field and click "<b>Create App</b>" button.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Scroll down to "<b>LIVE APP SETTINGS</b>", find the "<b>Live Return URL</b>" heading then click "<b>Show</b>".', 'nextend-facebook-connect') ?></li>
            <li><?php
                $loginUrls = $provider->getAllRedirectUrisForAppCreation();
                printf(__('Add the following URL to the "<b>Live Return URL</b>" field:', 'nextend-facebook-connect'));
                echo "<ul>";
                foreach ($loginUrls as $loginUrl) {
                    echo "<li><strong>" . $loginUrl . "</strong></li>";
                }
                echo "</ul>";
                ?>
            </li>
            <li><?php _e('Scroll down to "<b>App feature options</b>" section and tick "<b>Log In with PayPal</b>".', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Click "<b>Advanced Options</b>" which can be found at the end of text after "<b>Connect with PayPal (formerly Log In with PayPal)</b>".', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Tick "<b>Full name</b>".', 'nextend-facebook-connect') ?></li>
            <li><?php _e('"<b>Email address</b>" now requires an App Review by PayPal. To get the email address as well, <b>please submit your App for a review</b> after your App configuration is finished. Once the App review is successful, you need to pick "Email address" here to retrieve the email of the user. Until then make sure the Email scope is not "Enabled" in our PayPal Settings tab.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Fill "<b>Privacy policy URL</b>" and  "<b>User agreement URL</b>".', 'nextend-facebook-connect') ?></li>
            <li><?php _e('When all fields are filled, click "<b>Save</b>".', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Scroll up to "<b>LIVE API CREDENTIALS</b>" section and find the necessary "<b>Client ID</b>" and "<b>Secret</b>"! ( Make sure you are in "<b>Live</b>" mode and not "Sandbox". )', 'nextend-facebook-connect') ?></li>
        </ol>

        <a href="<?php echo $this->getUrl('settings'); ?>"
           class="button button-primary"><?php printf(__('I am done setting up my %s', 'nextend-facebook-connect'), 'PayPal App'); ?></a>
    </div>
</div>