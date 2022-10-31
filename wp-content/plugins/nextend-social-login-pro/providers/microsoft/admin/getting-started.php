<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$lastUpdated = '2021-09-09';

$provider = $this->getProvider();
?>
<div class="nsl-admin-sub-content">
    <div class="nsl-admin-getting-started">
        <h2 class="title"><?php _e('Getting Started', 'nextend-facebook-connect'); ?></h2>

        <p><?php printf(__('To allow your visitors to log in with their %1$s account, first you must create an %1$s App. The following guide will help you through the %1$s App creation process. After you have created your %1$s App, head over to "Settings" and configure the given "%2$s" and "%3$s" according to your %1$s App.', 'nextend-facebook-connect'), "Microsoft", "Client ID", "Secret"); ?></p>

        <p><?php do_action('nsl_getting_started_warnings', $provider, $lastUpdated); ?></p>

        <h2 class="title"><?php printf(_x('Create %s', 'App creation', 'nextend-facebook-connect'), 'Microsoft App'); ?></h2>

        <ol>
            <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://portal.azure.com/" target="_blank">https://portal.azure.com/</a>'); ?></li>
            <li><?php printf(__('Log in with your %s credentials if you are not logged in or create a new account.', 'nextend-facebook-connect'), 'Microsoft Azure'); ?></li>
            <li><?php _e('Click on the Search bar and search for "<b>App registrations</b>".', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Click on "<b>New registration</b>".', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Fill the "<b>Name</b>" field with your App Name.', 'nextend-facebook-connect') ?></li>
            <li><?php printf(__('Select an option at Supported account types.<br><strong><u>Important:</u></strong> On our Settings tab, you will need to select the Audience depending on the selected value! If you are not sure what to choose, then %1$shere%2$s you can learn more.', 'nextend-facebook-connect'), '<a href="https://nextendweb.com/nextend-social-login-docs/provider-microsoft/#audience" target="_blank">', '</a>'); ?></li>
            <li><?php
                $loginUrls = $provider->getAllRedirectUrisForAppCreation();
                printf(__('Add the following URL to the "<b>Redirect URI</b>" field:', 'nextend-facebook-connect'));
                echo "<ul>";
                foreach ($loginUrls as $loginUrl) {
                    echo "<li><strong>" . $loginUrl . "</strong></li>";
                }
                echo "</ul>";
                ?>
            </li>
            <li><?php _e('Create your App with the "<b>Register</b>" button.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('You land on the "<b>Overview</b>" page.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Copy the "<b>Application (client) ID</b>", this will be the <b>Application (client) ID</b> in the plugin settings.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Click on the link named "<b>Add a certificate or secret</b>" next to the Client credentials label.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Click on "<b>New client secret</b>".', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Fill the "<b>Description</b>" field.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Set the expiration date at the "<b>Expires</b>" field.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Then create your Client Secret with the "<b>Add</b>" button.', 'nextend-facebook-connect') ?></li>
            <li><?php _e('Copy the "<b>Client Secret Value</b>", this will be the <b>Client secret</b> in the plugin settings.', 'nextend-facebook-connect') ?></li>
        </ol>

        <a href="<?php echo $this->getUrl('settings'); ?>"
           class="button button-primary"><?php printf(__('I am done setting up my %s', 'nextend-facebook-connect'), 'Microsoft Azure App'); ?></a>
    </div>
</div>