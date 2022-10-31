<?php
if( ! class_exists('BeRocket_minmax_import_export') ) {
    class BeRocket_minmax_import_export {
        function __construct() {
            //WooCommerce default
            //Export
            add_filter( 'woocommerce_product_export_column_names', array($this, 'add_export_column') );
            add_filter( 'woocommerce_product_export_product_default_columns', array($this, 'add_export_column') );
            add_filter( 'woocommerce_product_export_product_column_min_quantity', array($this, 'add_export_data_min_quantity'), 10, 2 );
            add_filter( 'woocommerce_product_export_product_column_max_quantity', array($this, 'add_export_data_max_quantity'), 10, 2 );
            //Import
            add_filter( 'woocommerce_csv_product_import_mapping_options', array($this, 'add_column_to_importer') );
            add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array($this, 'add_column_to_mapping_screen') );
            add_filter( 'woocommerce_product_import_pre_insert_product_object', array($this, 'process_import'), 10, 2 );
        }
        function add_export_column( $columns ) {
            $columns['min_quantity'] = 'Minimum Quantity';
            $columns['max_quantity'] = 'Maximum Quantity';
            return $columns;
        }
        function add_export_data_min_quantity( $value, $product ) {
            $value = $product->get_meta( 'min_quantity', true, 'edit' );
            return $value;
        }
        function add_export_data_max_quantity( $value, $product ) {
            $value = $product->get_meta( 'max_quantity', true, 'edit' );
            return $value;
        }
        function add_column_to_importer( $options ) {
            $options['min_quantity'] = 'Minimum Quantity';
            $options['max_quantity'] = 'Maximum Quantity';
            return $options;
        }
        function add_column_to_mapping_screen( $columns ) {
            $columns['Minimum Quantity'] = 'min_quantity';
            $columns['minimum quantity'] = 'min_quantity';
            $columns['Maximum Quantity'] = 'max_quantity';
            $columns['maximum quantity'] = 'max_quantity';
            return $columns;
        }
        function process_import( $object, $data ) {
            if ( ! empty( $data['min_quantity'] ) ) {
                $object->update_meta_data( 'min_quantity', $data['min_quantity'] );
            }
            if ( ! empty( $data['max_quantity'] ) ) {
                $object->update_meta_data( 'max_quantity', $data['max_quantity'] );
            }
            return $object;
        }
    }
    new BeRocket_minmax_import_export();
}