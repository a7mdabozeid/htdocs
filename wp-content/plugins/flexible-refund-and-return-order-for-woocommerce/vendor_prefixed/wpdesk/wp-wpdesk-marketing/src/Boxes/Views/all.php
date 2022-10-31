<?php

namespace FRFreeVendor;

use FRFreeVendor\WPDesk\Library\Marketing\Boxes\Helpers\BBCodes;
use FRFreeVendor\WPDesk\Library\Marketing\Boxes\Helpers\Markers;
use FRFreeVendor\WPDesk\Library\Marketing\Boxes\MarketingBoxes;
use FRFreeVendor\WPDesk\Library\Marketing\Boxes\BoxRenderer;
use FRFreeVendor\WPDesk\View\Renderer\Renderer;
/**
 * @var array $boxes
 */
$boxes = $params['boxes'] ?? [];
/**
 * @var Renderer $renderer ;
 */
$renderer = $params['renderer'];
/**
 * @var BoxRenderer $plugin
 */
$plugin = $params['plugin'];
/**
 * @var BBCodes $bbcodes
 */
$bbcodes = $params['bbcodes'];
/**
 * @var Markers $markers
 */
$markers = $params['markers'];
?>
<div class="wpdm-box-wrapper">
    <?php 
foreach ($boxes as $box) {
    $box = $plugin->get_box_type($box);
    $type = $box->get_type();
    if ($box->get_row_open()) {
        echo $renderer->render('row_open', []);
    }
    echo '<div class="col-xs">' . $box->render(['bbcodes' => $bbcodes, 'markers' => $markers]) . '</div>';
    if ($box->get_row_close()) {
        echo $renderer->render('row_close', []);
    }
    ?>
    <?php 
}
?>
</div>
<?php 
