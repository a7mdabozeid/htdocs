<div class="xoo-wsc-prem">
	<div class="xoo-hero-btns">	
		<a class="live-demo button button-primary button-hero" href="http://xootix.com/plugins/mobile-login-for-woocommerce/">BUY PREMIUM - 9$</a>
	</div>

	<!-- Free V/s Premium -->
	<div class="xoo-fvsp">
		<span class="xoo-fvsp-head">Free V/s Premium</span>

		<?php

		$table_content = array(
			array('Fully Ajaxed','yes','yes'),
			array('Login & Register with OTP','yes','yes'),
			array('Woocommerce Checkout form integration','no','yes','alert'),
			array('Popup Design( Compatible with our free <a target="_blank" href="https://wordpress.org/plugins/easy-login-woocommerce/">login/signup popup plugin</a> )','no','yes','alert'),
		);

		?>

		<table class="xoo-fvsp-table">
			<thead>
				<tr>
					<th></th>
					<th>Free</th>
					<th>Premium</th>
				</tr>
			</thead>

			<tbody>
				<?php 
					$html = '';
					foreach ($table_content as $table_row) {
						$html .= '<tr>';
						$alert = isset($table_row[3]) ? 'class=xfp-alert' : '';
						$html .= '<td '.$alert.'>'.$table_row[0].'</td>';
						$html .= '<td class="xfp-'.$table_row[1].'"><span class="dashicons dashicons-'.$table_row[1].'"></span></td>';
						$html .= '<td class="xfp-'.$table_row[2].'"><span class="dashicons dashicons-'.$table_row[2].'"></span></td>';
						$html .= '</tr>';
					}

					echo wp_kses_post( $html );
				?>
			</tbody>

		</table>

	</div>
</div>