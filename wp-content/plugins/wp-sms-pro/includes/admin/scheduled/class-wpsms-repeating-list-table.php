<?php

namespace WP_SMS\Pro\Admin;

use WP_SMS\Admin\Helper;
use WP_SMS\Pro\RepeatingMessages;

class RepeatingListTable extends \WP_List_Table
{
    protected $db;
    protected $tb_prefix;
    protected $limit;
    protected $count;
    protected $adminUrl;
    public $data;

    public function __construct()
    {
        global $wpdb;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'ID',
            'plural'   => 'ID',
            'ajax'     => false
        ));
        $this->db        = $wpdb;
        $this->tb_prefix = $wpdb->prefix;
        $this->count     = $this->get_total();
        $this->limit     = $this->get_items_per_page('wp_sms_scheduled_per_page');
        $this->data      = $this->get_data();
        $this->adminUrl  = admin_url('admin.php?page=wp-sms-scheduled&tab=repeating_messages');
    }

    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'sender':
                return $item[$column_name];
            case 'message':
                return $item[$column_name];
            case 'recipient':

                if (function_exists('wp_sms_render_quick_reply')) {
                    return '<details>
                            <summary>' . __('Click to View more...', 'wp-sms-pro') . '</summary>
                            <p>' . wp_sms_render_quick_reply($item[$column_name]) . '</p>
                        </details>';
                } else {
                    return '<details>
                            <summary>' . __('Click to View more...', 'wp-sms-pro') . '</summary>
                            <p>' . $item[$column_name] . '</p>
                        </details>';
                }

            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render status column
     *
     * @param array $item
     * @return string html
     */
    public function column_status($item)
    {
        if ($item['ends_at'] > time() || $item['ends_at'] == null) {
            return '<span class="wp_sms_status_repeat-on-going">' . __('Ongoing', 'wp-sms-pro') . '</span>';
        }

        if ($item['ends_at'] <= time()) {
            return '<span class="wp_sms_status_repeat-ended">' . __('Finished', 'wp-sms-pro') . '</span>';
        }
    }

    /**
     * @param $item
     * @return string|void
     */
    public function column_media($item)
    {
        return wp_sms_render_media_list($item['media']);
    }

    public function column_starts_at($item)
    {
        //Build row actions
        $actions = [];
        if ($item['ends_at'] >= time() || $item['ends_at'] == null) {
            $actions['edit'] = sprintf('<a href="#" onclick="wp_sms_edit_repeating(%s)">' . __('Edit', 'wp-sms-pro') . '</a>', $item['ID']);
        }
        $actions['delete'] = sprintf('<a href="?page=%s&tab=%s&action=%s&ID=%s">' . __('Delete', 'wp-sms-pro') . '</a>', sanitize_text_field($_REQUEST['page']), 'repeating_messages', 'delete', $item['ID']);

        //Return the title contents
        return sprintf(
            '%1$s <span style="color:silver">(ID: %2$s)</span>%3$s',
            /*$1%s*/
            get_date_from_gmt(date('Y-m-d H:i:s', $item['starts_at']), 'Y-m-d H:i'),
            /*$2%s*/
            $item['ID'],
            /*$3%s*/
            $this->row_actions($actions)
        );
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/
            $item['ID']                //The value of the checkbox should be the record's id
        );
    }

    public function column_repeat($item)
    {
        $interval = intval($item['interval']);
        $unit     = $item['interval_unit'];

        $html = sprintf(__('Every %u %s', 'wp-sms-pro'), $interval, $unit);

        return esc_html($html);
    }

    public function column_ends_at($item)
    {
        return isset($item['ends_at']) ? get_date_from_gmt(date('Y-m-d H:i:s', $item['ends_at']), 'Y-m-d H:i:s') : '-';
    }

    public function get_columns()
    {
        return array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'starts_at' => __('Start at', 'wp-sms-pro'),
            'sender'    => __('Sender', 'wp-sms-pro'),
            'message'   => __('Message', 'wp-sms-pro'),
            'recipient' => __('Recipient', 'wp-sms-pro'),
            'media'     => __('Media', 'wp-sms-pro'),
            'repeat'    => __('Repeat', 'wp-sms-pro'),
            'ends_at'   => __('End at', 'wp-sms-pro'),
        );
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'ID'        => array('ID', true),     //true means it's already sorted
            'starts_at' => array('starts_at', false),  //true means it's already sorted
            'sender'    => array('sender', false),     //true means it's already sorted
            'message'   => array('message', false),   //true means it's already sorted
            'recipient' => array('recipient', false), //true means it's already sorted
            'media'     => array('media', false), //true means it's already sorted
            'repeat'    => array('interval', false),
            'ends_at'   => array('ends_at', false),

        );

        return $sortable_columns;
    }

    public function get_bulk_actions()
    {
        $actions = array(
            'bulk_delete' => __('Delete', 'wp-sms-pro')
        );

        return $actions;
    }

    public function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        // Search action
        if (isset($_GET['s'])) {
            $prepare     = $this->db->prepare("SELECT * from `{$this->tb_prefix}sms_repeating` WHERE message LIKE %s OR recipient LIKE %s", '%' . $this->db->esc_like($_GET['s']) . '%', '%' . $this->db->esc_like($_GET['s']) . '%');
            $this->data  = $this->get_data($prepare);
            $this->count = $this->get_total($prepare);
        }

        // Bulk delete action
        if ('bulk_delete' == $this->current_action()) {
            foreach ($_GET['id'] as $id) {
                RepeatingMessages::deleteMessageById((int)$id);
            }

            $this->data  = $this->get_data();
            $this->count = $this->get_total();

            if (method_exists(Helper::class, 'addFlashNotice')) {
                Helper::addFlashNotice(__('Items removed.', 'wp-sms'), 'success', $this->adminUrl); // TODO
            } else {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Items removed.', 'wp-sms-pro') . '</p></div>';
            }
        }

        // Single delete action
        if ('delete' == $this->current_action()) {
            RepeatingMessages::deleteMessageById((int)$_GET['ID']);
            $this->data  = $this->get_data();
            $this->count = $this->get_total();

            if (method_exists(Helper::class, 'addFlashNotice')) {
                Helper::addFlashNotice(__('Item removed.', 'wp-sms'), 'success', $this->adminUrl);
            } else {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Item removed.', 'wp-sms-pro') . '</p></div>';
            }
        }

        if (!empty($_GET['_wp_http_referer'])) {
            wp_redirect(remove_query_arg(array('_wp_http_referer', '_wpnonce'), stripslashes($_SERVER['REQUEST_URI'])));
            exit;
        }
    }

    public function prepare_items()
    {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = $this->limit;

        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();

        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);

        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->data;

        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         *
         * In a real-world situation involving a database, you would probably want
         * to handle sorting by passing the 'orderby' and 'order' values directly
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */

        usort($data, [self::class, 'usort_reorder']);

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = $this->count;

        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }

    /**
     * Usort Function
     *
     * @param $a
     * @param $b
     *
     * @return array
     */
    public function usort_reorder($a, $b)
    {
        $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : 'starts_at'; //If no sort, default to sender
        $order   = (!empty($_REQUEST['order'])) ? sanitize_text_field($_REQUEST['order']) : 'desc'; //If no order, default to asc
        $result  = strcmp($a[$orderby], $b[$orderby]); //Determine sort order

        return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
    }

    //set $per_page item as int number
    public function get_data($query = '')
    {
        $page_number = ($this->get_pagenum() - 1) * $this->limit;
        $orderby     = "";

        if (isset($_REQUEST['orderby'])) {
            $orderby .= "ORDER BY {$this->tb_prefix}sms_repeating.{$_REQUEST['orderby']} {$_REQUEST['order']}";
        } else {
            $orderby .= "ORDER BY starts_at DESC";
        }

        if (!$query) {
            $query = $this->db->prepare("SELECT * FROM {$this->tb_prefix}sms_repeating {$orderby} LIMIT %d OFFSET %d", $this->limit, $page_number);
        } else {
            $query .= $this->db->prepare(" LIMIT %d OFFSET %d", $this->limit, $page_number);
        }

        $result = $this->db->get_results($query, ARRAY_A);

        array_walk($result, function (&$record) {
            $record['recipient'] = implode(', ', unserialize($record['recipient']));
        });

        return $result;
    }

    //get total items on different Queries
    public function get_total($query = '')
    {
        if (!$query) {
            $query = 'SELECT * FROM `' . $this->tb_prefix . 'sms_repeating`';
        }
        $result = $this->db->get_results($query, ARRAY_A);
        $result = count($result);

        return $result;
    }
}
