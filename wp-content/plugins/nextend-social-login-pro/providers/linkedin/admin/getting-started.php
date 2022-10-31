<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$lastUpdated = '2021-09-09';

$provider = $this->getProvider();
?>
<div class="nsl-admin-sub-content">
    <div class="nsl-admin-getting-started">
        <h2 class="title"><?php _e('Getting Started', 'nextend-facebook-connect'); ?></h2>

        <p><?php printf(__('To allow your visitors to log in with their %1$s account, first you must create a %1$s App. The following guide will help you through the %1$s App creation process. After you have created your %1$s App, head over to "Settings" and configure the given "%2$s" and "%3$s" according to your %1$s App.', 'nextend-facebook-connect'), "LinkedIn", "Client  ID", "Client  secret"); ?></p>

        <p><?php do_action('nsl_getting_started_warnings', $provider, $lastUpdated); ?></p>

        <h2 class="title"><?php printf(_x('Create %s', 'App creation', 'nextend-facebook-connect'), 'LinkedIn App'); ?></h2>

        <ol>
            <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://www.linkedin.com/developer/apps" target="_blank">https://www.linkedin.com/developer/apps</a>'); ?></li>
            <li><?php printf(__('Log in with your %s credentials if you are not logged in', 'nextend-facebook-connect'), 'LinkedIn'); ?></li>
            <li><?php _e('Locate the "<b>Create app</b>" button and click on it.', 'nextend-facebook-connect'); ?></li>
            <li><?php _e('Enter the name of your App to the "App name" field.', 'nextend-facebook-connect'); ?></li>
            <li><?php printf(__('Find your company page in the "<b>Company</b>" field. If you don\'t have one yet, create new one at: %s', 'nextend-facebook-connect'), '<a href="https://www.linkedin.com/company/setup/new/" target="_blank">https://www.linkedin.com/company/setup/new/</a>'); ?></li>
            <li><?php _e('Enter your "<b>Privacy policy URL</b>" and upload an "<b>App logo</b>"', 'nextend-facebook-connect'); ?></li>
            <li><?php _e('Read and agree the "<b>API Terms of Use</b>" then click the "<b>Create App</b>" button!', 'nextend-facebook-connect'); ?></li>
            <li><?php _e('You will end up in the App setting area. Click on the "<b>Products</b>" tab.', 'nextend-facebook-connect'); ?></li>
            <li><?php printf(__('Find <b>"%s"</b> and click "<b>Select</b>".', 'nextend-facebook-connect'), 'Sign In with LinkedIn'); ?></li>
            <li><?php _e('A modal will appear where you need to tick the "<b>I have read and agree to these terms</b>" checkbox and finally press the "<b>Add product</b>" button.', 'nextend-facebook-connect'); ?></li>
            <li><?php _e('Click on the "<b>Auth</b>" tab.', 'nextend-facebook-connect'); ?></li>
            <li><?php
                $loginUrls = $provider->getAllRedirectUrisForAppCreation();
                printf(__('Find "<b>OAuth 2.0 settings</b>" section and add the following URL to the "<b>Redirect URLs</b>" field:', 'nextend-facebook-connect'));
                echo "<ul>";
                foreach ($loginUrls as $loginUrl) {
                    echo "<li><strong>" . $loginUrl . "</strong></li>";
                }
                echo "</ul>";
                ?>
            </li>
            <li><?php _e('Click on "<b>Update</b>" to save the changes', 'nextend-facebook-connect'); ?></li>
            <li><?php _e('Find the necessary "<b>Client ID</b>" and "<b>Client Secret</b>" under the Application credentials section, on the <b>Auth</b> tab.', 'nextend-facebook-connect'); ?></li>
        </ol>

        <a href="<?php echo $this->getUrl('settings'); ?>"
           class="button button-primary"><?php printf(__('I am done setting up my %s', 'nextend-facebook-connect'), 'LinkedIn App'); ?></a>
    </div>

    <br>
    <div class="nsl-admin-embed-youtube">
        <div></div>
        <iframe src="https://www.youtube.com/embed/Dfm-FeXuLNI?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
    </div>
</div>