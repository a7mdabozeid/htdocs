<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$provider = $this->getProvider();
?>
<ol>
    <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://developer.paypal.com/developer/applications/" target="_blank">https://developer.paypal.com/developer/applications/</a>'); ?></li>
    <li><?php printf(__('Log in with your %s credentials if you are not logged in.', 'nextend-facebook-connect'), 'PayPal'); ?></li>
    <li><?php _e('There is a Sandbox/Live switch. Make sure "<b>Live</b>" is selected!', 'nextend-facebook-connect') ?></li>
    <li><?php printf(__('Click on the name of your %s App, under the REST API apps section.', 'nextend-facebook-connect'), 'PayPal'); ?></li>
    <li><?php _e('Scroll down to "<b>LIVE APP SETTINGS</b>", find the "<b>Live Return URL</b>" heading and click "<b>Show</b>".', 'nextend-facebook-connect') ?></li>
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
    <li><?php _e('Click on "Save"', 'nextend-facebook-connect'); ?></li>
</ol>