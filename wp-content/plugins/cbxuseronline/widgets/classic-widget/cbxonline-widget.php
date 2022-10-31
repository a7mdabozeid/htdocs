<?php


// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Unique identifier for your widget.
 *
 *
 * The variable name is used as the text domain when internationalizing strings
 * of text. Its value should match the Text Domain file header in the main
 * widget file.
 *
 * @since    1.0.0
 *
 * @var      string
 */
class CBXOnlineWidget extends WP_Widget {
	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		parent::__construct(
			$this->get_widget_slug(),
			esc_html__( 'CBX: Online Users', 'cbxuseronline' ),
			array(
				'classname'   => $this->get_widget_slug() . '-class',
				'description' => esc_html__( 'This widget shows online users based on widget setting', 'cbxuseronline' ),
				'customize_selective_refresh' => true,
				'show_instance_in_rest'       => true,
			)
		);

	}//end constructor


	/**
	 * Return the widget slug.
	 *
	 * @return    Plugin slug variable.
	 * @since    1.0.0
	 *
	 */
	public function get_widget_slug() {
		//return $this->widget_slug;
		return 'cbxuseronline';
	}

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array args  The array of form elements
	 * @param array instance The current instance of the widget
	 *
	 * @return int
	 */
	public function widget( $args, $instance ) {


		if ( ! isset ( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		extract( $args, EXTR_SKIP );

		$widget_string = $before_widget;

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Online Users', 'cbxuseronline' ) : $instance['title'], $instance, $this->id_base );
		// Defining the Widget Title
		if ( $title ) {
			$widget_string .= $args['before_title'] . $title . $args['after_title'];
		} else {
			$widget_string .= $args['before_title'] . $args['after_title'];
		}

		$checked_fields = array(
			'count'            => 1, //show user count
			'count_individual' => 1, //show individual count as per user type  member, guest and bot
			'member_count'     => 1, //show member user type count
			'guest_count'      => 1, //show guest user type count
			'bot_count'        => 1, //show bot user type count
			'page'             => 0, //show count for this page
			'mobile'           => 1, //show user mobile or desktop login information
			'memberlist'       => 1, //show member list
			'mostuseronline'   => 1, //most user online date and count
			'linkusername'     => 1  //link author page
		);



		foreach ( $checked_fields as $field => $val ) {
			if ( isset( $instance[ $field ] ) ) {
				$instance[ $field ] = intval( $instance[ $field ] );
			} else {
				$instance[ $field ] = $val;
			}
		}


		$instance = apply_filters( 'cbxuseronline_widget_widget', $instance, $checked_fields );


		$instance['page'] = ( isset($instance['page']) && intval($instance['page']) ) ? esc_url( $_SERVER['REQUEST_URI'] ) : '';
		$scope            = 'widget';


		$widget_string .= CBXUseronlineHelper::cbxuseronline_display( $instance, $scope );




		$widget_string .= $after_widget;

		print $widget_string;

	}//end widget


	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 *
	 * @return array|mixed|void
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		$checked_fields = array(
			'count'            => 1, //show user count
			'count_individual' => 1, //show individual count as per user type  member, guest and bot
			'member_count'     => 1, //show member user type count
			'guest_count'      => 1, //show guest user type count
			'bot_count'        => 1, //show bot user type count
			'page'             => 0, //show count for this page
			'mobile'           => 1, //show user mobile or desktop login information
			'memberlist'       => 1, //show member list
			'mostuseronline'   => 1, //most user online date and count
			'linkusername'     => 1  //link author page
		);

		foreach ( $checked_fields as $field => $val ) {
			if ( isset( $new_instance[ $field ] ) ) {
				$instance[ $field ] = intval($new_instance[ $field ]); //either te
			} else {
				$instance[ $field ] = $val;
			}
		}

		$instance = apply_filters( 'cbxuseronline_widget_update', $instance, $new_instance, $old_instance );

		return $instance;

	}//end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param array instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$fields = array(
			'title'            => esc_html__( 'Online Users', 'cbxuseronline' ),
			'count'            => 1, //show user count
			'count_individual' => 1, //show individual count as per user type  member, guest and bot
			'member_count'     => 1, //show member user type count
			'guest_count'      => 1, //show guest user type count
			'bot_count'        => 1, //show bot user type count
			'page'             => 0, //show count for this page
			'mobile'           => 1, //show user mobile or desktop login information
			'memberlist'       => 1, //show member list
			'mostuseronline'   => 1, //most user online date and count
			'linkusername'     => 1  //link author page
		);

		$fields = apply_filters( 'cbxuseronline_widget_form_fields', $fields );

		$instance = wp_parse_args(
			(array) $instance,
			$fields
		);

		extract( $instance, EXTR_SKIP );

		// Display the admin form
		include( plugin_dir_path( __FILE__ ) . 'views/admin.php' );
	}//end form
}//end class CBXOnlineWidget