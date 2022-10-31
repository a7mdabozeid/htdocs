<?php
	// Prevent direct file access
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
?>

	<!-- Custom  Title Field -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">
			<?php esc_html_e( 'Title:', "cbxuseronline" ); ?>
		</label>

		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
<?php


	do_action( 'cbxuseronline_widget_form_admin_before', $instance, $this );
?>
	<p>
        <label for="<?php echo $this->get_field_id( 'memberlist' ); ?>"><?php esc_html_e( 'Show Memberlist', 'cbxuseronline' ); ?></label>
        <select class="widefat" name="<?php echo $this->get_field_name( 'memberlist' ); ?>"
                id="<?php echo $this->get_field_id( 'memberlist' ); ?>">
            <option value="1" <?php selected( $memberlist, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $memberlist, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $memberlist, 1 ) */?> id="<?php /*echo $this->get_field_id( 'memberlist' ); */?>" name="<?php /*echo $this->get_field_name( 'memberlist' ); */?>" />-->
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'linkusername' ); ?>"><?php esc_html_e( 'Link user to author page', 'cbxuseronline' ); ?></label>
        <select class="widefat" name="<?php echo $this->get_field_name( 'linkusername' ); ?>"
                id="<?php echo $this->get_field_id( 'linkusername' ); ?>">
            <option value="1" <?php selected( $linkusername, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $linkusername, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $linkusername, 1 ) */?> id="<?php /*echo $this->get_field_id( 'linkusername' ); */?>" name="<?php /*echo $this->get_field_name( 'linkusername' ); */?>" />-->
    </p>
    <p>

        <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php esc_html_e( 'Show online count', 'cbxuseronline' ); ?></label>
        <select class="widefat" name="<?php echo $this->get_field_name( 'count' ); ?>"
                id="<?php echo $this->get_field_id( 'count' ); ?>">
            <option value="1" <?php selected( $count, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $count, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $count, 1 ) */?> id="<?php /*echo $this->get_field_id( 'count' ); */?>" name="<?php /*echo $this->get_field_name( 'count' ); */?>" />-->
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'count_individual' ); ?>"><?php esc_html_e( 'Show individual count', 'cbxuseronline' ); ?></label>
        <select class="widefat" name="<?php echo $this->get_field_name( 'count_individual' ); ?>" id="<?php echo $this->get_field_id( 'count_individual' ); ?>">
            <option value="1" <?php selected( $count_individual, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $count_individual, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $count_individual, 1 ) */?> id="<?php /*echo $this->get_field_id( 'count_individual' ); */?>" name="<?php /*echo $this->get_field_name( 'count_individual' ); */?>" />-->
    </p>
    <p>

        <label for="<?php echo $this->get_field_id( 'member_count' ); ?>"><?php esc_html_e( 'Show member count', 'cbxuseronline' ); ?></label>
        <select class="widefat" name="<?php echo $this->get_field_name( 'member_count' ); ?>" id="<?php echo $this->get_field_id( 'member_count' ); ?>">
            <option value="1" <?php selected( $member_count, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $member_count, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $member_count, 1 ) */?> id="<?php /*echo $this->get_field_id( 'member_count' ); */?>" name="<?php /*echo $this->get_field_name( 'member_count' ); */?>" />-->
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'guest_count' ); ?>"><?php esc_html_e( 'Show guest count', 'cbxuseronline' ); ?></label>
        <select class="widefat" name="<?php echo $this->get_field_name( 'guest_count' ); ?>"  id="<?php echo $this->get_field_id( 'guest_count' ); ?>">
            <option value="1" <?php selected( $guest_count, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $guest_count, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $guest_count, 1 ) */?> id="<?php /*echo $this->get_field_id( 'guest_count' ); */?>" name="<?php /*echo $this->get_field_name( 'guest_count' ); */?>" />-->
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'bot_count' ); ?>"><?php esc_html_e( 'Show bot count', 'cbxuseronline' ); ?></label><br />
        <select class="widefat" name="<?php echo $this->get_field_name( 'bot_count' ); ?>"  id="<?php echo $this->get_field_id( 'bot_count' ); ?>">
            <option value="1" <?php selected( $bot_count, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $bot_count, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $bot_count, 1 ) */?> id="<?php /*echo $this->get_field_id( 'bot_count' ); */?>" name="<?php /*echo $this->get_field_name( 'bot_count' ); */?>" />-->
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'page' ); ?>"><?php esc_html_e( 'Show for current page', 'cbxuseronline' ); ?></label><br />
        <select class="widefat" name="<?php echo $this->get_field_name( 'page' ); ?>"  id="<?php echo $this->get_field_id( 'page' ); ?>">
            <option value="1" <?php selected( $page, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $page, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $page, 1 ) */?> id="<?php /*echo $this->get_field_id( 'page' ); */?>" name="<?php /*echo $this->get_field_name( 'page' ); */?>" />-->
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'mostuseronline' ); ?>"><?php esc_html_e( 'Show most user online', 'cbxuseronline' ); ?></label><br />
        <select class="widefat" name="<?php echo $this->get_field_name( 'mostuseronline' ); ?>"  id="<?php echo $this->get_field_id( 'mostuseronline' ); ?>">
            <option value="1" <?php selected( $mostuseronline, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $mostuseronline, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $mostuseronline, 1 ) */?> id="<?php /*echo $this->get_field_id( 'mostuseronline' ); */?>" name="<?php /*echo $this->get_field_name( 'mostuseronline' ); */?>" />-->
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'mobile' ); ?>"><?php esc_html_e( 'Show mobile or desktop logged in status', 'cbxuseronline' ); ?></label>
        <select class="widefat" name="<?php echo $this->get_field_name( 'mobile' ); ?>"  id="<?php echo $this->get_field_id( 'mobile' ); ?>">
            <option value="1" <?php selected( $mobile, 1 ); ?> ><?php esc_html_e( 'Yes', 'cbxuseronline' ); ?></option>
            <option value="0" <?php selected( $mobile, 0 ); ?> ><?php esc_html_e( 'No', 'cbxuseronline' ); ?></option>
        </select>
        <!--<input class="checkbox" type="checkbox" <?php /*checked( $mobile, 1 ) */?> id="<?php /*echo $this->get_field_id( 'mobile' ); */?>" name="<?php /*echo $this->get_field_name( 'mobile' ); */?>" />-->
	</p>
<?php
	do_action( 'cbxuseronline_widget_form_admin', $instance, $this );