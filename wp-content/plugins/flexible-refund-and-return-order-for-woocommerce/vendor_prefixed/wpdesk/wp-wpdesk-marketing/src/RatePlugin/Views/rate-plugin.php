<?php

namespace FRFreeVendor;

use FRFreeVendor\WPDesk\Library\Marketing\Boxes\Helpers\Markers;
$url = $params['url'] ?? '#';
$description = $params['description'] ?? null;
$header = $params['header'] ?? null;
$footer = $params['footer'] ?? null;
?>
<section class="rate-plugin-wrapper wpdesk-rate-icons">
	<?php 
if ($header) {
    ?>
		<header><?php 
    echo $header;
    ?></header>
	<?php 
}
?>
	<?php 
if ($description) {
    ?>
		<p class="description"><?php 
    echo $description;
    ?></p>
	<?php 
}
?>
	<a href="<?php 
echo \esc_url($url);
?>" target="blank">
		<span class="dashicons dashicons-star-filled"></span>
		<span class="dashicons dashicons-star-filled"></span>
		<span class="dashicons dashicons-star-filled"></span>
		<span class="dashicons dashicons-star-filled"></span>
		<span class="dashicons dashicons-star-filled"></span>
	</a>
	<?php 
if ($footer) {
    ?>
		<footer><?php 
    echo $footer;
    ?></footer>
	<?php 
}
?>
</section>
<style>
	.rate-plugin-wrapper p.description {
		display: inline-block;
		padding-left: 10px;
	}

	.rate-plugin-wrapper span.love {
		color: firebrick;
	}

	.wpdesk-rate-icons {
		padding: 10px;
		text-align: center;
	}

	.wpdesk-rate-icons a {
		text-decoration: none;
	}

	.wpdesk-rate-icons p {
		margin: 0 0;
		color: #AAA;
	}

	.wpdesk-rate-icons p.description {
		color: #333;
		margin-bottom: 5px;
	}

	.wpdesk-rate-icons p.description2 {
		margin-top: 5px;
	}

	.wpdesk-rate-icons [class*="dashicons-star-"] {
		color: #ffb900;
	}

	.wpdesk-rate-icons span.dashicons-star-filled {
		transition: all 3s;
	}

	.wpdesk-rate-icons a:hover span.dashicons-star-filled {
		transform: rotate(180deg);
	}

	.wpdesk-rate-icons .love .dashicons {
		font-size: 24px;
		width: 24px;
		height: 24px;
	}
</style>
<?php 
