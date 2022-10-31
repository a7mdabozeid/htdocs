<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="orders-with-coupons-report acfw-report wp-core-ui">

    <div class="stats-range">
        <ul>
            <?php foreach ( $range_nav as $nrange => $label ) : ?>
                <li <?php echo ( $nrange == $current_range ) ? 'class="current"' : ''; ?>>
                    <a href="<?php echo admin_url( 'admin.php?page=wc-reports&tab=acfw_reports&range=' . $nrange ); ?>">
                        <?php echo $label; ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <li class="custom-range">
                <span><?php _e( 'Custom' , 'advanced-coupons-for-woocommerce' ); ?></span>
                <form id="custom-date-range" method="GET">
                    <input type="hidden" name="page" value="wc-reports">
                    <input type="hidden" name="tab" value="<?php echo $report_tab; ?>">
                    <input type="hidden" name="range" value="custom">
                    <input type="text" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( $start_date ); ?>" name="start_date" class="range_datepicker from" autocomplete="off" required>
                    <span>&mdash;</span>
                    <input type="text" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( $end_date ); ?>" name="end_date" class="range_datepicker to" autocomplete="off" required>
                    <button type="submit" class="button"><?php _e( 'Go' , 'advanced-coupons-for-woocommerce' ); ?></button>
                </form>
            </li>

            <?php do_action( 'acfw_orders_with_coupons_report_menu_items' ); ?>

            <li class="export-csv-button">
                <a id="export_order_with_coupons_csv" href="javascript:void(0);" data-filename="<?php echo 'orders-with-coupons-' . $current_range . '-' . $today_date . '.csv'; ?>">
                    <span class="dashicons dashicons-download"></span>
                    <?php _e( 'Export CSV' , 'advanced-coupons-for-woocommerce' ); ?>
                </a>
            </li>
        </ul>
    </div>

    <div class="acfw-report-wrap" data-total="0">
        <div class="table-filter-form">
            <div class="limit-field form-field">
                <?php _e( 'Show' , 'advanced-coupons-for-woocommerce' ); ?>
                <select name="limit">
                    <option>25</option>
                    <option>50</option>
                    <option>75</option>
                    <option>100</option>
                </select>
                <?php _e( 'entries' , 'advanced-coupons-for-woocommerce' ); ?>
            </div>
            <div class="search-field form-field">
                <input type="text" name="search" placeholder="<?php esc_attr_e( 'Search...' , 'advanced-coupons-for-woocommerce' ); ?>">
            </div>
            <div class="statuses-field form-field">
                <select class="wc-enhanced-select" name="statuses[]" multiple data-placeholder="Select order status">
                    <?php foreach ( $statuses as $status => $status_label ) : ?>
                        <option value="<?php echo $status ?>" <?php selected( $status , 'wc-completed' ); ?>>
                            <?php echo $status_label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="submit-field form-field">
                <button class="button-primary" id="filter-table-report">
                    <?php _e( 'Filter' , 'advanced-coupons-for-woocommerce' ); ?>
                </button>
            </div>
            <input type="hidden" name="action" value="acfw_orders_with_coupons">
            <input type="hidden" name="paged" value="1">
            <input type="hidden" name="total" value="0">
            <input type="hidden" name="range" value="<?php echo esc_attr( $current_range ); ?>">
            <input type="hidden" name="start_date" value="<?php echo esc_attr( $start_date ); ?>">
            <input type="hidden" name="end_date" value="<?php echo esc_attr( $end_date ); ?>">
            <?php wp_nonce_field( 'acfw_filter_table_report' ); ?>
        </div>

        <div class="repor-table-wrap">
            <div class="responsive-table">
                <table class="report-table order-with-coupons-table">
                    <thead>
                        <tr>
                            <th class="order_id"><?php _e( 'Order' , 'advanced-coupons-for-woocommerce' ); ?></th>
                            <th class="order_date"><?php _e( 'Date' , 'advanced-coupons-for-woocommerce' ); ?></th>
                            <th class="order_coupons"><?php _e( 'Coupons' , 'advanced-coupons-for-woocommerce' ); ?></th>
                            <th class="order_status"><?php _e( 'Status' , 'advanced-coupons-for-woocommerce' ); ?></th>
                            <th class="order_total"><?php _e( 'Order Total' , 'advanced-coupons-for-woocommerce' ); ?></th>
                            <th class="discount_total"><?php _e( 'Discounts Total' , 'advanced-coupons-for-woocommerce' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <div class="pagination"></div>
            </div>
            <div class="overlay" style="background-image: url(<?php echo $overlay_image; ?>)"></div>
        </div>
    </div>

</div>