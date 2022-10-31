<?php 
/**
 * 
 * Plugin Name:       ALshiaka Swatches importer AIOPORT Addon
 * Plugin URI :       http://nebula.ps
 *  Description:      An primitve low level extention workes for a custom need to handle importing Shiak item master swatches images.
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Khalil Khassep
 * Author URI:        https://nebula.ps/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       swatches-importer
 * Domain Path:       /languages
 */


 // Load all plugin files 

 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'WCBOOST_SWATCHES_IMPORTER_FILE', __FILE__ );

function swatches_importer_activation_hook() {
   require_once plugin_dir_path(__FILE__).'inc/swatches-importer-core-class.php';  
   // die(var_dump(file_exists( plugin_dir_path(__FILE__).'inc/swatches-importer-core-class.php'))) ;
    \Includes\Swatches_Importer::instance()->run();

    $notices = get_option('notes_active_swatches' , []) ; 
    $notices[] = 'Swatches Importerd : Had been acive need to add a list of required pluigns';

    update_option('notes_active_swatches' , $notices) ;
    //   check for avalibalit 
 
}


 // afteer all pugins loaded fire an action with admin init 

  

 function add_more_note()
  {
      if(get_option('notes_active_swatches')) {
        $notes = get_option('notes_active_swatches');
        $notes[] = ['Plugin one ' , 'Plugin 2' , 'and soo on'];
        update_option('notes_active_swatches' , $notes);
      }
  }
function sample_admin_notice__success() {
     
    //$note  = get_option('notes_active_swatches') ; 
    if(get_option('sw_im_deactivate')) {
        delete_option('sw_im_deactivate');
    }
    if(get_option('notes_active_swatches'))
    {
        foreach (get_option('notes_active_swatches') as $n) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( $n, 'sample-text-domain' ); ?></p>
                <?php
                 if (is_array($n)) { 
                     ?>
                     <ul>
                         <?php
                     foreach($n as $_n) {
                         ?>
                          <li><?php echo $_n ?></li> 
                         <?php
                     }
                     ?>
                     </ul>
                     <?php
                 } 
                ?>
            </div>
            <?php 
        }
    }

    remove_action('admin_notices' , 'sample_admin_notice__success');
   
}
add_action( 'admin_notices', 'sample_admin_notice__success' );

add_action('admin_notices' , 'deactivate_note');

function deactivate_note()
{
    if(get_option('sw_im_deactivate'))  {
        
    }
    ?>
     <div class="notice notice-success is-dismissible">
         <p>Plugin had been deactivated</p>
     </div>
    <?php
     
}
	
register_activation_hook(__FILE__ , 'swatches_importer_activation_hook');
function swatches_importer_deactivation_hook(){}
register_deactivation_hook(__FILE__ , 'deactivate') ;

function deactivate()
{
    if(get_option('notes_active_swatches')) {
        delete_option('notes_active_swatches');
    }

    if(!get_option('sw_im_deactivate' )){
        add_option('sw_im_deactivate' , 'Plugin had been deactivated');
    }
}
add_action('wp_loaded' , 'add_more_note') ; 
add_action('admin_init' , function(){
    remove_action('admin_notices' , 'deactivate_note');
    remove_action('admin_notices' , 'sample_admin_notice__success');
});