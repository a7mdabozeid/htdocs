<table class="form-table">
    <tr>
        <th><label for="mobile"><?php _e('OTP', 'wp-sms-pro'); ?></label></th>
        <td>
            <input type="checkbox" class="checkbox" name="wp_sms_otp" id="wp_sms_otp" <?php if (!empty($value)) {
                checked($value, '1');
            } ?> /> <span class="description"><?php _e('Active', 'wp-sms-pro'); ?></span>
        </td>
    </tr>
</table>