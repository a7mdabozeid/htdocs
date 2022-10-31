<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>


<dl>
    <dt><?php _e( 'User:' , 'loyalty-program-for-woocommerce' ); ?></dt>
    <dd><a href="<?php echo get_edit_user_link( $this->_user->ID ); ?>"><?php echo $this->_user->user_nicename; ?></a></dd>
    
    <dt><?php _e( 'Points:' , 'loyalty-program-for-woocommerce' ); ?></dt>
    <dd><?php echo $points; ?></dd>

    <dt><?php _e( 'Status:' , 'loyalty-program-for-woocommerce' ); ?></dt>
    <dd><?php echo $status; ?></dd>
</dl>