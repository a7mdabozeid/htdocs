<?php
/**
 * Social links widget
 *
 * @package Razzi
 */

namespace Razzi\Addons\Widgets;
/**
 * Class Razzi_Social_Links_Widget
 */
class Social_Links extends \WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $default;

	/**
	 * List of supported socials
	 *
	 * @var array
	 */
	protected $socials;

	/**
	 * Instantiate the object.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function __construct() {
		$socials = array(
			'facebook'    => esc_html__( 'Facebook', 'razzi' ),
			'twitter'     => esc_html__( 'Twitter', 'razzi' ),
			'google-plus' => esc_html__( 'Google Plus', 'razzi' ),
			'tumblr'      => esc_html__( 'Tumblr', 'razzi' ),
			'linkedin'    => esc_html__( 'Linkedin', 'razzi' ),
			'pinterest'   => esc_html__( 'Pinterest', 'razzi' ),
			'flickr'      => esc_html__( 'Flickr', 'razzi' ),
			'instagram'   => esc_html__( 'Instagram', 'razzi' ),
			'dribbble'    => esc_html__( 'Dribbble', 'razzi' ),
			'behance'     => esc_html__( 'Behance', 'razzi' ),
			'github'      => esc_html__( 'Github', 'razzi' ),
			'youtube'     => esc_html__( 'Youtube', 'razzi' ),
			'vimeo'       => esc_html__( 'Vimeo', 'razzi' ),
			'rss'         => esc_html__( 'RSS', 'razzi' ),
			'tiktok'         => esc_html__( 'Tiktok', 'razzi' ),
		);

		$this->socials = apply_filters( 'razzi_social_media', $socials );
		$this->default = array(
			'title' => '',
		);

		foreach ( $this->socials as $k => $v ) {
			$this->default["{$k}_title"] = $v;
			$this->default["{$k}_url"]   = '';
		}

		parent::__construct(
			'social-links-widget',
			esc_html__( 'Razzi - Social Links', 'razzi' ),
			array(
				'classname'                   => 'razzi-widget__social-links',
				'description'                 => esc_html__( 'Display links to social media networks.', 'razzi' ),
				'customize_selective_refresh' => true,
			),
			array( 'width' => 600 )
		);
	}

	/**
	 * Outputs the HTML for this widget.
     *
	 * @since 1.0.0
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 *
	 * @return void Echoes it's output
	 */
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->default );

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo '<div class="social-links">';

		foreach ( $this->socials as $social => $label ) {
			if ( empty( $instance[ $social . '_url' ] ) ) {
				continue;
			}

			$icon = $social;

			if ( 'google-plus' == $social ) {
				$icon = 'google';
			}

			printf(
				'<a href="%s" class="%s social" rel="nofollow" title="%s" data-toggle="tooltip" data-placement="top" target="_blank">%s</a>',
				esc_url( $instance[ $social . '_url' ] ),
				esc_attr( $social ),
				esc_attr( $instance[ $social . '_title' ] ),
				\Razzi\Icon::get_svg( $icon, '', 'social' )
			);
		}

		echo '</div>';

		echo $args['after_widget'];
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
	 * @since 1.0.0
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->default );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'razzi' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<?php
		foreach ( $this->socials as $social => $label ) {
			printf(
				'<div style="width: 280px; float: left; margin-right: 10px;">
					<label>%s</label>
					<p><input type="text" class="widefat" name="%s" placeholder="%s" value="%s"></p>
				</div>',
				$label,
				$this->get_field_name( $social . '_url' ),
				esc_html__( 'URL', 'razzi' ),
				$instance[ $social . '_url' ]
			);
		}
	}
}
