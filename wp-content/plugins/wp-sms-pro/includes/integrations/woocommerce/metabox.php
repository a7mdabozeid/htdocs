<span class="wpsms-spinner"></span><span class="wpsms-overlay"></span>
<ul class="wpsms_send_message">
    <p><?php _e('Send an sms to the customer.', 'wp-sms'); ?></p>
    <p>Message: </p>
    <li class="wide">
        <textarea type="text" dir="auto" class="wpsms_message input-text" name="wpsms_message" id="wpsms_message"></textarea>
        <input type="hidden" name="wpsms_order_id" id="wpsms_message"/>
    </li>
    <li class="wide">
        <button type="button" class="send_sms button-primary" name="send_sms"><?php _e('Send', 'wp-sms'); ?></button>
    </li>
</ul>


<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#wpsms_message").counter({
            count: 'up',
            goal: 'sky',
            msg: '<?php _e('characters', 'wp-sms'); ?>'
        })
    });
</script>