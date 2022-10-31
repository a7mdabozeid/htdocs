<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<tr valign="top" class="<?php echo esc_attr( $value[ 'id' ] ) . '-row'; ?>" id="<?php echo esc_attr( $value[ 'id' ] ); ?>">
    <th scope="row">
        <label>
            <?php echo sanitize_text_field( $value[ 'title' ] ); ?> 
            <?php echo $tooltip; ?>
        </label>
    </th>
    <td>
        <table class="breakpoints-data wp-list-table widefat striped">
            <thead>
                <tr>
                    <th class="amount-breakpoint">
                        <?php _e( 'Breakpoint' , 'loyalty-program-for-woocommerce' ); ?>
                        <span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Describes the total amount required for an order to earn the extra points.' , 'loyalty-program-for-woocommerce' ); ?>"></span>
                    </th>
                    <th class="points-earned">
                        <?php _e( 'Points Earned' , 'loyalty-program-for-woocommerce' ); ?>
                    </th>
                    <th class="actions"></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( is_array( $data ) && ! empty( $data ) ) : ?>
                    <?php foreach ( $data as $row ) : ?>
                        <tr>
                            <td class="amount-breakpoint">
                                <input type="text" class="wc_input_price" name="<?php echo sprintf( '%s[%s][amount]' , $value[ 'id' ] , $num ); ?>" value="<?php echo $row[ 'amount' ]; ?>" pattern="<?php echo $price_pattern; ?>">
                            </td>
                            <td class="points-earned">
                            <input type="number" class="wc_input_price" name="<?php echo sprintf( '%s[%s][points]' , $value[ 'id' ] , $num ); ?>" min="1" value="<?php echo absint( $row[ 'points' ] ); ?>">
                            </td>
                            <td class="actions">
                                <?php if ( $num > 0 ) : ?> 
                                    <a class="remove" href="javascript:void(0);"><span class="dashicons dashicons-no"></span></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php $num++; endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td class="amount-breakpoint">
                            <input type="text" class="wc_input_price" name="<?php echo sprintf( '%s[0][amount]' , $value[ 'id' ] ); ?>" value="" pattern="<?php echo $price_pattern; ?>">
                        </td>
                        <td class="points-earned">
                        <input type="number" class="wc_input_price" name="<?php echo sprintf( '%s[0][points]' , $value[ 'id' ] ); ?>" min="1" value="">
                        </td>
                        <td class="actions">
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="actions" colspan="3">
                        <a class="add-row" href="javascript:void(0);">
                            <span class="dashicons dashicons-plus"></span>
                            <?php _e( 'Add Row' , 'loyalty-program-for-woocommerce' ); ?>
                        </a>
                    </td>
                </tr>
                <tr class="blank-form">
                    <td class="amount-breakpoint">
                        <input type="text" class="wc_input_price" name="<?php echo sprintf( '%s[row_num][amount]' , $value[ 'id' ] ); ?>" value="" pattern="<?php echo $price_pattern; ?>">
                    </td>
                    <td class="points-earned">
                    <input type="number" class="wc_input_price" name="<?php echo sprintf( '%s[row_num][points]' , $value[ 'id' ] ); ?>" min="1" value="">
                    </td>
                    <td class="actions">
                        <a class="remove" href="javascript:void(0);"><span class="dashicons dashicons-no"></span></a>
                    </td>
                </tr>
            </tfoot>
        </table>
        <p class="description"><?php echo $desc; ?></p>
    </td>
</tr>

<script type="text/javascript">
jQuery(document).ready( function($) {

    var $field   = $( ".<?php echo esc_attr( $value[ 'id' ] ) . '-row'; ?>" ),
        $bpTable = $field.find( "table.breakpoints-data" ),
        $tbody   = $bpTable.find( "tbody" ),
        rowHtml  = "<tr>" + $bpTable.find( "tfoot tr.blank-form" ).html() + "</tr>";

    var Funcs = {

        events : function() {
            
            $bpTable.on( "click" , "tfoot td.actions .add-row" , Funcs.addRow );
            $bpTable.on( "click" , "td.actions .remove" , Funcs.deleteRow );

            $bpTable.find( "tfoot tr.blank-form" ).remove();
        },

        addRow : function() {
            
            var $firstRow = $tbody.find( "tr:first-child" ),
                rowNum    = Funcs.getNewRowNum(),
                regex     = new RegExp( "row_num" , 'g' );
            
            $tbody.append( rowHtml.replace( regex , rowNum ) );
        },

        deleteRow : function() {
            
            var $this = $(this),
                $row  = $this.closest( "tr" );

            $row.remove();
            Funcs.refreshRowNums();
        },

        refreshRowNums : function() {

            var $rows = $tbody.find( "tr" ),
                $amount, $points;

            for ( var x = 0; x < $rows.length; x++ ) {

                $amount = $( $rows[x] ).find( "input[type='text']" );
                $points = $( $rows[x] ).find( "input[type='number']" );

                $amount.prop( "name" , "acfw_loyalprog_earn_points_amount_breakpoints[" + x + "][amount]" );
                $points.prop( "name" , "acfw_loyalprog_earn_points_amount_breakpoints[" + x + "][points]" );
            }
        },

        getNewRowNum : function() {
            return $tbody.find( "tr" ).length;
        }
    };

    Funcs.events();
});
</script>