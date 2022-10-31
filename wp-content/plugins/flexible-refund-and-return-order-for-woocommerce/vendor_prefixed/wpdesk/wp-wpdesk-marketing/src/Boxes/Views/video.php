<?php

namespace FRFreeVendor;

use FRFreeVendor\WPDesk\Library\Marketing\Boxes\Abstracts\BoxInterface;
use FRFreeVendor\WPDesk\Library\Marketing\Boxes\Helpers\BBCodes;
use FRFreeVendor\WPDesk\Library\Marketing\Boxes\Helpers\Markers;
/**
 * @var BoxInterface $box
 */
$box = $params['box'];
/**
 * @var BBCodes $bbcodes
 */
$bbcodes = $params['bbcodes'];
/**
 * @var Markers $markers
 */
$markers = $params['markers'];
?>
<div class="wpdesk-marketing-box wpdesk-marketing-box-video wpdesk-marketing-box-<?php 
echo $box->get_slug();
?>">
	<?php 
if (!empty($box->get_title())) {
    ?>
		<header>
			<h3>
				<?php 
    echo \wp_strip_all_tags($box->get_title());
    ?>
			</h3>
			<?php 
    if (!empty($box->get_description())) {
        ?>
				<p class="description"><?php 
        echo $bbcodes->replace($markers->replace(\wp_strip_all_tags($box->get_description())));
        ?></p>
			<?php 
    }
    ?>
		</header>
		<section>
			<?php 
    $is_carousel = \count($box->get_links()) > 1 ? 'video-carousel' : 'video-single';
    ?>
			<?php 
    if (!empty($box->get_links())) {
        ?>
				<div class="<?php 
        echo $is_carousel;
        ?> owl-theme">
					<?php 
        foreach ($box->get_links() as $link) {
            ?>
						<div class="item-video">
							<?php 
            echo \wp_oembed_get($link['video']);
            ?>
						</div>
					<?php 
        }
        ?>
				</div>
			<?php 
    }
    ?>
			<?php 
    if (!empty($box->get_button()['name'])) {
        ?>
				<p class="box-button">
					<a
							class="button button-primary"
							href="<?php 
        echo \esc_url($box->get_button()['url']);
        ?>"
							target="_blank"
					>
						<?php 
        echo $box->get_button()['name'];
        ?>
					</a>
				</p>
			<?php 
    }
    ?>
		</section>
	<?php 
}
?>
</div>
<?php 
