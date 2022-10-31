<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php
$arabic_labels_total = array(
    'Subtotal' => 'السعر شامل الضريبة',
    'Shipping' => 'الشحن',
    'Cash on delivery' => 'الدفع عند الاستلام',
    'Total' => 'الإجمالي',
    'Flat rate' => 'رسوم الشحن',
);

$shiaka__settings = get_option('shiaka__settings');
?>
<style>
    .address p {
        margin-top: 0;
    }
    .rtl p {
        direction: rtl;
        text-align: right;
        width: 100%;
    }
    .retex p {
        width: 100%;
        margin: 2px 0;
    }
   
    .retex tr td:first-of-type {
        text-align: right;
        padding-right: 20px;
    }

   
    .retex tr td:last-of-type {
        text-align: left;
        padding-left: 20px;
    }
    
</style>

<?php do_action( 'wpo_wcpdf_before_document', $this->get_type(), $this->order ); ?>

<table class="head container">
	<tr>
		<td class="header">
		<?php
		if ( $this->has_header_logo() ) {
			$this->header_logo();
		} else {
			echo $this->get_title();
		}
		?>
		</td>
		<td class="shop-info">
			<?php do_action( 'wpo_wcpdf_before_shop_name', $this->get_type(), $this->order ); ?>
			<div class="shop-name"><h3><?php $this->shop_name(); ?></h3></div>
			<?php do_action( 'wpo_wcpdf_after_shop_name', $this->get_type(), $this->order ); ?>
			<?php do_action( 'wpo_wcpdf_before_shop_address', $this->get_type(), $this->order ); ?>
			<div class="shop-address"><?php $this->shop_address(); ?></div>
			<?php do_action( 'wpo_wcpdf_after_shop_address', $this->get_type(), $this->order ); ?>
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_document_label', $this->get_type(), $this->order ); ?>

<h1 class="document-type-label">
	<?php if ( $this->has_header_logo() ) echo $this->get_title(); ?>
</h1>

<?php do_action( 'wpo_wcpdf_after_document_label', $this->get_type(), $this->order ); ?>

<table class="order-data-addresses">
	<tr>
	
		<?php
// 		echo '<pre>';
		  //  print_r($this);
		  //  print_r($this->order->data['shipping']);
        // echo '</pre>';
		?>
		<td class="address shipping-address">
			<?php if ( $this->show_shipping_address() ) : ?>

		        <h3><b><?= __('From',  'shiaka') ?>:</b> <?=   $this->shop_name();   ?></h3>
		        <p><b><?= __('Location', 'shiaka') ?>:</b> <?=  __('Saudi Arabia', 'shiaka')  ?></p>
    	        <p><b><?= __('Order Number') ?>:</b> <?= $this->order_number();  ?></p>
                <p><b><?=  __('Order Date', 'shiaka') ?></b> <?= $this->order_date(); ?> </p>
                
                
				<?php do_action( 'wpo_wcpdf_before_shipping_address', $this->get_type(), $this->order ); ?>
				<!--<?php $this->shipping_address(); ?>-->
				
				<p><b><?= __('Ship To', 'shiaka'); ?>:</b> <?= $this->order->data['shipping']['first_name'] . ' ' . $this->order->data['shipping']['last_name']?></p>
			
				<p><b><?= __('Region',  'shiaka') ?>:</b> <?= $this->order->data['shipping']['state'] ?></p>
                <p><b><?= __('City',  'shiaka') ?>:</b> <?= $this->order->data['shipping']['city'] ?></p>

				<p><b><?= __('Address 1',  'shiaka') ?>:</b> <?= $this->order->data['shipping']['address_1'] ?></p>
			
                <?php if(!empty($this->order->data['shipping']['address_2'])): ?>
				    <p><b><?= __('Address 2') ?>:</b> <?= $this->order->data['shipping']['address_2'] ?></p>
                <?php endif; ?>

				<?php do_action( 'wpo_wcpdf_after_shipping_address', $this->get_type(), $this->order ); ?>
				<?php if ( isset( $this->settings['display_phone'] ) ) : ?>
			    <p><b><?= __('Phone', 'shiaka'); ?></b> <?php $this->shipping_phone(); ?> </p>
				<?php endif; ?>
				
	

			<?php endif; ?>
		</td>
        		
        <td class="address shipping rtl">
        	<?php if ( $this->show_shipping_address() ) : ?>
        
                <p><b>من:</b> <?=   $this->shop_name();   ?></p>
                <p><b>العنوان:</b> <?=  __('Saudi Arabia', 'shiaka')  ?></p>
                <p><b>رقم الطلب:</b> <?= $this->order_number();  ?></p>
                <p><b>تاريخ الطلب</b> <?= $this->order_date(); ?> </p>
                
                
        		<?php do_action( 'wpo_wcpdf_before_shipping_address', $this->get_type(), $this->order ); ?>
        		<!--<?php $this->shipping_address(); ?>-->
        		
        		<p><b>الشحن إلى:</b> <?= $this->order->data['shipping']['first_name'] . ' ' . $this->order->data['shipping']['last_name']?></p>
        	
        		<p><b>المنطقة:</b> <?= $this->order->data['shipping']['state'] ?></p>
                <p><b>المدينة:</b> <?= $this->order->data['shipping']['city'] ?></p>
        
        		<p><b>العنوان 1:</b> <?= $this->order->data['shipping']['address_1'] ?></p>
        	
                <?php if(!empty($this->order->data['shipping']['address_2'])): ?>
        		    <p><b>العنوان 2:</b> <?= $this->order->data['shipping']['address_2'] ?></p>
                <?php endif; ?>
        
        		<?php do_action( 'wpo_wcpdf_after_shipping_address', $this->get_type(), $this->order ); ?>
        		<?php if ( isset( $this->settings['display_phone'] ) ) : ?>
        	    <p><b>رقم الهاتف:</b> <?php $this->shipping_phone(); ?> </p>
        		<?php endif; ?>
        		
        
        
        	<?php endif; ?>
        </td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $this->get_type(), $this->order ); ?>

<table class="order-details">
	<thead>
		<tr>
			<th class="product"><?php _e( 'Product', 'woocommerce-pdf-invoices-packing-slips' ); ?> المنتج</th>
			<th class="quantity"><?php _e( 'Quantity', 'woocommerce-pdf-invoices-packing-slips' ); ?> الكمية</th>
			<th class="price"><?php _e( 'Price', 'woocommerce-pdf-invoices-packing-slips' ); ?> السعر</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $this->get_order_items() as $item_id => $item ) : ?>
			<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', 'item-'.$item_id, $this->get_type(), $this->order, $item_id ); ?>">
				<td class="product">
					<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
					<span class="item-name"><?php echo $item['name']; ?></span>
					<?php do_action( 'wpo_wcpdf_before_item_meta', $this->get_type(), $item, $this->order  ); ?>
					<span class="item-meta"><?php echo $item['meta']; ?></span>
					<dl class="meta">
						<?php $description_label = __( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
						<?php if ( ! empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; ?>
						<?php if ( ! empty( $item['weight'] ) ) : ?><dt class="weight"><?php _e( 'Weight:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="weight"><?php echo $item['weight']; ?><?php echo get_option( 'woocommerce_weight_unit' ); ?></dd><?php endif; ?>
					</dl>
					<?php do_action( 'wpo_wcpdf_after_item_meta', $this->get_type(), $item, $this->order  ); ?>
				</td>
				<td class="quantity"><?php echo $item['quantity']; ?></td>
				<td class="price"><?php echo $item['order_price']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders">
				<div class="document-notes">
					<?php do_action( 'wpo_wcpdf_before_document_notes', $this->get_type(), $this->order ); ?>
					<?php if ( $this->get_document_notes() ) : ?>
						<h3><?php _e( 'Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $this->document_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_document_notes', $this->get_type(), $this->order ); ?>
				</div>
				<div class="customer-notes">
					<?php do_action( 'wpo_wcpdf_before_customer_notes', $this->get_type(), $this->order ); ?>
					<?php if ( $this->get_shipping_notes() ) : ?>
						<h3><?php _e( 'Customer Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $this->shipping_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_customer_notes', $this->get_type(), $this->order ); ?>
				</div>				
			</td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
						<?php foreach ( $this->get_woocommerce_totals() as $key => $total ) : ?>
							<tr class="<?php echo $key; ?>">
								<th class="description"><?php echo $total['label']; ?></th>
								<td class="price"><span class="totals-price"><?php echo $total['value']; ?></span></td>
								<th class="description"><?php echo $arabic_labels_total[$total['label']] ?? $total['label']; ?></th>
							</tr>
						<?php endforeach; ?>
					</tfoot>
				</table>
			</td>

		</tr>
	</tfoot>
</table>

<div style="margin: 20px 0;"></div>

<table class="order-data-addresses retex">
	<tr>
        <td class="address shipping rtl">
        	<h3>Return and Exchange Policy</h3>
        </td>
        <td>
            <h3>
                ‫والإسترجاع‬ ‫الإستبدال‬ ‫سياسة
            </h3>    
        </td>
	</tr>
	
	<tr>
        <td class="address shipping rtl">
        	<?php 
        	    if(!empty($shiaka__settings['order_terms_en'])) {
        	        foreach(explode("\n", $shiaka__settings['order_terms_en']) as $t => $term) {
        	            echo '<p>'.$term.'</p>';
        	        }
        	    }
        	?>
        </td>
        
        
        <td>
        	<?php 
        	    if(!empty($shiaka__settings['order_terms_ar'])) {
        	        foreach(explode("\n", $shiaka__settings['order_terms_ar']) as $t => $term) {
        	            echo '<p>'.$term.'</p>';
        	        }
        	    }
        	?>
        </td>
	</tr>
</table>


<div class="bottom-spacer"></div>

<?php do_action( 'wpo_wcpdf_after_order_details', $this->get_type(), $this->order ); ?>

<?php if ( $this->get_footer() ) : ?>
	<div id="footer">
		<!-- hook available: wpo_wcpdf_before_footer -->
		<?php $this->footer(); ?>
		<!-- hook available: wpo_wcpdf_after_footer -->
	</div><!-- #letter-footer -->
<?php endif; ?>

<?php do_action( 'wpo_wcpdf_after_document', $this->get_type(), $this->order ); ?>
