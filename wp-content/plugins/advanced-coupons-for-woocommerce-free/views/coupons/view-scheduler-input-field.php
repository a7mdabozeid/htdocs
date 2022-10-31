<?php if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$hour   = isset( $time[0] ) ? str_pad( esc_attr( $time[0] ) , 2 , 0 , STR_PAD_LEFT ) : '';
$minute = isset( $time[1] ) ? str_pad( esc_attr( $time[1] ) , 2 , 0 , STR_PAD_LEFT ) : '';
$second = isset( $time[2] ) ? str_pad( esc_attr( $time[2] ) , 2 , 0 , STR_PAD_LEFT ) : '00';
?>

<p class="form-field <?php echo esc_attr( $field[ 'id' ] ); ?>_field <?php echo esc_attr( $field[ 'wrapper_class' ] ); ?>">

    <label for="<?php echo esc_attr( $field[ 'id' ] ); ?>"><?php echo wp_kses_post( $field['label'] ); ?></label>

    <?php  if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) :  
        echo wc_help_tip( $field[ 'description' ] );
    endif; ?>

    <span 
        class="date-time-field"
        data-date="<?php echo esc_attr( $date ); ?>"
        data-hour="<?php echo $hour; ?>"
        data-minute="<?php echo $minute; ?>"
        data-second="<?php echo $second; ?>"
    >
        <input type="<?php echo esc_attr( $field[ 'type' ] ); ?>" 
            class="date-field <?php echo esc_attr( $field[ 'class' ] ); ?>" 
            style="<?php echo esc_attr( $field['style'] ); ?>" 
            name="<?php echo esc_attr( $field[ 'name' ] ); ?>" 
            id="<?php echo esc_attr( $field[ 'id' ] ); ?>" 
            value="<?php echo esc_attr( $field['value'] ); ?>" 
            placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
            <?php echo implode( ' ', $custom_attributes ); ?>
            autocomplete="off" />
    </span>

    <?php if ( ! empty( $field[ 'description' ] ) && false === $field[ 'desc_tip' ] ) : ?>
        <span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
    <?php endif; ?>

    <a 
        class="clear-scheduler-fields dashicons-before dashicons-no" 
        href="javascript:void(0);" 
        alt="<?php _e( 'Clear field values' , 'advanced-coupons-for-woocommerce-free' ); ?>"
        title="<?php _e( 'Clear field values' , 'advanced-coupons-for-woocommerce-free' ); ?>"
    ></a>
</p>