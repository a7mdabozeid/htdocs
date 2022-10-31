<?php
namespace Razzi;

/**
 * HTML markup rendering class
 */
class Markup {
	/**
	 * The context data
	 *
	 * @since 1.0.0
     *
	 * @var array
	 */
	protected $data = [];

	/**
	 * The default context options.
	 *
	 * @since 1.0.0
     *
	 * @var array
	 */
	protected $default_args = [
		'tag'     => 'div',
		'attr'    => [],
		'actions' => true,
		'echo'    => true,
	];

	/**
	 * The single instance of the class
	 */
	protected static $instance = null;

	/**
	 * Initialize
	 */
	static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Output the open tag
     *
	 * @since 1.0.0
	 *
	 * @param string $context
	 * @param array $args
     *
	 * @return string
	 */
	public function open( $context, $args = [] ) {
		$this->set_context( $context, $args );
		$args = $this->get_context( $context );

		if ( empty( $args['tag'] ) ) {
			return '';
		}

		$html = '<' . esc_attr( $args['tag'] ) . ' ' . $this->parse_attributes( $context ) . '>';

		if ( true === $args['actions'] || 'before' === $args['actions'] ) {
			$html = $this->do_action( $context, 'before_open' ) . $html;
		}

		if ( $args['echo'] ) {
			echo apply_filters( strtolower( __NAMESPACE__ ) . '\markup_open_html', $html, $context, $args );
		}

		if ( true === $args['actions'] || 'after' === $args['actions'] ) {
			$html = $html . $this->do_action( $context, 'after_open' );
		}

		if ( ! $args['echo'] ) {
			return $html;
		}
	}

	/**
	 * Output the close tag
     *
	 * @since 1.0.0
	 *
	 * @param string $context
	 * @param array $args
     *
	 * @return string
	 */
	public function close( $context ) {
		$args = $this->get_context( $context );

		if ( ! $args || empty( $args['tag'] ) ) {
			return '';
		}

		$html = '</' . $args['tag'] . '>';


		if ( true === $args['actions'] || 'before' === $args['actions'] ) {
			$html = $this->do_action( $context, 'before_close' ) . $html;
		}

		if ( $args['echo'] ) {
			echo apply_filters( strtolower( __NAMESPACE__ ) . '\markup_close_html', $html, $context, $args );
		}

		if ( true === $args['actions'] || 'after' === $args['actions'] ) {
			$html = $html . $this->do_action( $context, 'after_close' );
		}

		$this->remove_context( $context );

		if ( ! $args['echo'] ) {
			return $html;
		}
	}

	/**
	 * Parse the context attributes
     *
	 * @since 1.0.0
	 *
	 * @param string $context
     *
	 * @return string
	 */
	public function parse_attributes( $context ) {
		if ( ! $this->has_context( $context ) ) {
			return '';
		}

		$args = $this->get_context( $context );

		if ( empty( $args['attr'] ) ) {
			return '';
		}

		$attr = ' ';

		foreach ( (array) $args['attr'] as $name => $value ) {
			$value = is_array( $value ) ? join( ' ', $value ) : $value;
			$attr .= $name . '="' . esc_attr( $value ) . '" ';
		}

		return trim( $attr );
	}

	/**
	 * Apply an action
     *
	 * @since 1.0.0
	 *
	 * @param string $context
	 * @param string $action
     *
	 * @return string
	 */
	protected function do_action( $context, $action = '' ) {
		if ( ! $this->has_context( $context ) ) {
			return '';
		}

		$args = $this->get_context( $context );
		$action = $action && $context ? strtolower( __NAMESPACE__ ) . '_' . $action . '_' . $context : $context;

		if ( $action ) {
			if ( ! $args['echo'] ) {
				ob_start();
			}

			do_action( $action, $args );

			if ( ! $args['echo'] ) {
				return ob_get_clean();
			}
		}

		return '';
	}

	/**
	 * Parse the context args
     *
	 * @since 1.0.0
	 *
	 * @param string $context
	 * @param array $args
     *
	 * @return array
	 */
	protected function parse_context_args( $context, $args ) {
		$args = wp_parse_args( (array) $args, $this->default_args );

		if ( empty( $context ) ) {
			$args['actions'] = false;
		} else {
			$context_key = sanitize_key( $context );
			$class = '';
			$class = ! isset( $args['attr']['class'] ) ? $class : $args['attr']['class'] . ' ' . $class;
			$class = apply_filters( strtolower( __NAMESPACE__ ) . '_' . str_replace( '-', '_', $context_key ) . '_class', $class, $args );

			$args['attr']['class'] = $class;
		}

		return apply_filters( strtolower( __NAMESPACE__ ) . '\markup_args', $args, $context );
	}

	/**
	 * Set context data
     *
	 * @since 1.0.0
	 *
	 * @param string $context
     *
	 * @param array $args
	 */
	public function set_context( $context, $args = [] ) {
		$args = $this->parse_context_args( $context, $args );
		$this->data[ $context ] = $args;
	}

	/**
	 * Get context data
     *
	 * @since 1.0.0
	 *
	 * @param string $context
     *
	 * @return array
	 */
	public function get_context( $context ) {
		return $this->has_context( $context ) ? $this->data[ $context ] : null;
	}

	/**
	 * Remove a context from the data list
     *
	 * @since 1.0.0
	 *
	 * @param string $context
     *
	 * @return void
	 */
	public function remove_context( $context ) {
		if ( $this->has_context( $context ) ) {
			unset( $this->data[ $context ] );
		}
	}

	/**
	 * Check if a context in exists in the data list
     *
	 * @since 1.0.0
	 *
	 * @param string $context
     *
	 * @return boolean
	 */
	public function has_context( $context ) {
		return array_key_exists( $context, $this->data );
	}
}