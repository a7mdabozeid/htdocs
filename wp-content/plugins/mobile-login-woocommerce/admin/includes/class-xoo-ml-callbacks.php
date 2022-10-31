<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Xoo_Ml_Callbacks {

	public function __construct(){
		//Stop calling parent constructor
	}

	public function simplify_args( $args ){

		if( !isset( $args['id'] ) || ( !isset( $args['option_name'] )  && $args['type'] === 'setting' )  ){
			return;
		}

		$data = array(); //Data to return from this function

		$value = get_option($args['option_name']);


		if( is_array( $value ) ){
			$value = isset( $value[$args['id']] ) ? $value[$args['id']] : ( isset( $args['default'] ) ? $args['default'] : null );
		}

		//Check for extra arguments
		if( isset( $args['extra'] ) ){

			//If options
			if( isset( $args['extra']['options'] ) ){
				$data['options'] = $args['extra']['options'];
			}

		}

		$description = isset( $args['desc'] ) ? esc_attr( $args['desc'] ) : null;

		//Merging all data
		$data = array_merge($data,
			array(
				'id' 			=> $args['option_name'].'['.$args['id'].']',
				'value' 		=> $value,
				'description' 	=> $description, 
			)
		);

		return $data;

	}

	public function section($args){
		extract( $args );
		?>
		<span class="section-title"><?php echo esc_attr($title); ?></span>
		<?php
	}

	public function checkbox( $args ){
		extract( $this->simplify_args( $args ) );
		?>
		<input type="hidden" name="<?php echo esc_attr($id); ?>" value="no">
		<input type="checkbox" class="xoo-input-checkbox" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="yes" <?php checked($value, "yes"); ?> />
		<?php
		$this->description($description);
	}


	public function color( $args ){
		extract( $this->simplify_args( $args ) );
		?>
		<input type="text" class="color-field xoo-input-text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" />
		<?php
		$this->description($description);
	}


	public function text( $args ){
		extract( $this->simplify_args( $args ) );
		?>
		<input type="text" class="xoo-input-text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" />
		<?php
		$this->description($description);
	}


	public function textarea( $args ){
		extract( $this->simplify_args( $args ) );
		?>
		<textarea rows="4" cols="50" class="xoo-input-text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>"><?php echo esc_attr($value); ?></textarea>
		<?php
		$this->description($description);
	}


	public function number( $args ){
		extract( $this->simplify_args( $args ) );
		?>
		<input type="number" class="xoo-input-number" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" />
		<?php
		$this->description($description);
	}

	public function upload( $args ){
		extract( $this->simplify_args( $args ) );
		?>
		<a class="button-primary xoo-upload-icon">Select</a>
		<input type="hidden" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" class="xoo-upload-url" value="<?php echo esc_attr($value); ?>">
		<a class="button xoo-remove-media">Remove</a>
		<span class="xoo-upload-title"></span>
		<p class="description">Supported format: JPEG,PNG </p>
		<?php
	}

	public function select( $args ){
		extract( $this->simplify_args( $args ) );
		?>
		<select name="<?php echo esc_attr($id); ?>">
			<?php foreach ($options as $option_value => $option_label ): ?>
				<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?> > <?php echo esc_html( $option_label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
		$this->description($description);
	}


	public function description($description){
		if( !isset( $description ) ) return;
		?>
		<p class="description"><?php echo esc_html( $description ); ?></p>
		<?php
	}
}

return new Xoo_Ml_Callbacks(); 

?>
