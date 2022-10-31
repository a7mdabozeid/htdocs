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
<div class="wpdesk-marketing-box wpdesk-marketing-box-slider wpdesk-marketing-box-<?php 
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
    if (!empty($box->get_links())) {
        ?>
                <ul class="owl-carousel">
                    <?php 
        foreach ($box->get_links() as $link) {
            ?>
                        <div class="slider-item">
                            <?php 
            if (isset($link['image']['medium'])) {
                ?>
                                <span class="image"><img src="<?php 
                echo \esc_url($link['image']['thumbnail']);
                ?>" alt=""/></span>
                            <?php 
            }
            ?>
                                <a href="<?php 
            echo \esc_url($link['url']);
            ?>" target="_blank"><?php 
            echo \wp_strip_all_tags($link['title']);
            ?></a>
                            <?php 
            if (!empty($link['description'])) {
                ?>
                                <p class="description"><?php 
                echo $bbcodes->replace($markers->replace(\wp_strip_all_tags($link['description'])));
                ?></p>
                            <?php 
            }
            ?>
                        </div>
                    <?php 
        }
        ?>
                </ul>
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
