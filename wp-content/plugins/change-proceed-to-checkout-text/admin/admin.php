<?php
/*
** adding necessarey files
*/

function arrowdesign_custom_proceed_to_checkout_Style_script() {

    wp_enqueue_style('arrowdesign_custom_proceed_to_checkout_main_style_file', plugins_url('/css/style.css', __FILE__));
    wp_enqueue_script('arrowdesign_custom_proceed_to_checkout_logic_file', plugins_url('/js/logic.js',__FILE__ ));
}
add_action('admin_enqueue_scripts', 'arrowdesign_custom_proceed_to_checkout_Style_script');

/**
 * Adds a new settings page under Setting menu
*/
add_action( 'admin_menu', 'arrowdesign_custom_proceed_to_checkout_admin_page' );
function arrowdesign_custom_proceed_to_checkout_admin_page() {
    //only editor and administrator can add a polling
    if( current_user_can('editor') || current_user_can('administrator') ) {
    add_options_page( __( 'Change Proceed To Cart Text' ), __( 'Change Proceed To Cart Text' ), 'manage_options', 'ProceedToCheckoutText', 'arrowdesign_custom_proceed_to_checkout_homepage' );
}
}

/**
* Tabs Method
*/
function arrowdesign_custom_proceed_to_checkout_show_tabs_list( $current = 'first' ) {
    $tabs = array(
        'first'   => __( 'Update Button Text', 'plugin-textdomain' ),

        );
    $html = '<h2 class="wooLiveSalenav-tabnav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? 'nav-tab-active' : '';
        $html .= '<a class="nav-tab ' . esc_html($class) . '" href="?page=ProceedToCheckoutText&tab=' . esc_html($tab) . '">' . esc_html($name) . '</a>';
    }
    $html .= '</h2>';
    echo $html ;
}

function arrowdesign_custom_proceed_to_checkout_homepage(){
    ?>
    <div class="cont-p-dashboard">
        <div class="post_like_dislike_header wrap">

        	<h3>Dashboard for changing woocommerce 'Proceed to Checkout' button text.</h3>
					<p>Click the following link to contact Arrow Design for <span>
		            <a href="https://arrowdesign.ie">Web Design</a>, Support or WordPress Plugin Development.
        </span></p>

    </div>
    <?php

    // ================== Tabs ========================//
     $tab = ( ! empty( $_GET['tab'] ) ) ? esc_attr( $_GET['tab'] ) : 'first';
     arrowdesign_custom_proceed_to_checkout_show_tabs_list( $tab );


   // =========================== Tab 1 ========================//
    if ( $tab == 'first' ) {
        ?>
        <div class="woo-live-saleTabs woo-live-sale-firstTab">
        	<!--First tab -->
        	<div class="setting-left-sp">
        		<div class="list-sp list-sp-left">
					<h2>Instructions </h2>
					<h4>Enter text in the textbox</h4>
					<h4>Click update to save</h4>
					<h4>The textbox will display your saved text</h4>
					<h4>'Proceed To Checkout' will be replaced with your entry</h4>

        		</div>
        		<div class="list-sp list-polling-right">
	<?php
        			$terms = "";

        			if (isset($_POST['proceed-to-chekout-btn-ad'])) {

						$name = sanitize_text_field ( $_POST['proceed-to-chekout-btn-ad-text'] );
        				$terms = get_term_meta('2020', '_proceed_to_checkout_text', true);

      					if(is_null($terms)){
							add_term_meta('2020', "_proceed_to_checkout_text" ,$name);
						} //add term data on first use

						else{
        					update_term_meta( '2020', "_proceed_to_checkout_text", $name);
        				}

					$terms = get_term_meta('2020', '_proceed_to_checkout_text', true);
					if($terms==""){
						echo "You have reverted to woocommerce default settings";
					}
					else{
						echo 'Text Saved: '.$terms;
					}


        			}//end if button was clicked

        			?>
        			<!-- uploading user content -->

        			<H2>Update Proceed to checkout Button Text</H2>
        			<form method="POST" action="">
        			<input type="text" name="proceed-to-chekout-btn-ad-text" class="names-list first-name" value="<?php echo esc_attr ( $terms ); ?>">
        			<button class="button-primary update-names-and-titles" name="proceed-to-chekout-btn-ad" type="submit">Update</button>
        		</form>
        		<br><br><br>
        		</div>

        		</div>

        	</div>
        	<div class="display-right-sp">

        	</div>
        </div>

        <?php
    }
}