<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<tr valign="top" class="<?php echo esc_attr( $value[ 'id' ] ) . '-row'; ?>" id="<?php echo esc_attr( $value[ 'id' ] ); ?>">
    <th scope="row"><?php echo sanitize_text_field( $value[ 'title' ] ); ?> <?php echo $tooltip; ?></th>
    <td>
        <table class="within-order-range-data wp-list-table widefat striped">
            <thead>
                <tr>
                    <th class="start-range">
                        <?php _e( 'Start Date/Time' , 'loyalty-program-for-woocommerce' ); ?>
                        <span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Describes when the promotional period should start. Orders within this period will get an extra amount of points given on top of the regular amount given.' , 'loyalty-program-for-woocommerce' ); ?>"></span>
                    </th> 
                    <th class="end-range">
                        <?php _e( 'End Date/Time' , 'loyalty-program-for-woocommerce' ); ?>
                        <span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Describes when the promotional period should end. Orders within this period will get an extra amount of points given on top of the regular amount given.' , 'loyalty-program-for-woocommerce' ); ?>"></span>
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
                            <td class="start-range">
                                <div class="wrapper">
                                    <input class="start-date" type="text" name="<?php echo sprintf( '%s[%s][sdate]' , $value[ 'id' ] , $num ); ?>" placeholder="mm/dd/yyyy" value="<?php echo $row[ 'sdate' ]; ?>" autocomplete="off" required>
                                    <input class="start-time" type="text" name="<?php echo sprintf( '%s[%s][stime]' , $value[ 'id' ] , $num ); ?>" placeholder="hh:mm p" value="<?php echo $row[ 'stime' ]; ?>" autocomplete="off" required>
                                </div>
                            </td>
                            <td class="end-range">
                                <div class="wrapper">
                                    <input class="end-date" type="text" name="<?php echo sprintf( '%s[%s][edate]' , $value[ 'id' ] , $num ); ?>" placeholder="mm/dd/yyyy" value="<?php echo $row[ 'edate' ]; ?>" autocomplete="off" required>
                                    <input class="end-time" type="text" name="<?php echo sprintf( '%s[%s][etime]' , $value[ 'id' ] , $num ); ?>" placeholder="hh:mm p" value="<?php echo $row[ 'etime' ]; ?>" autocomplete="off" required>
                                </div>
                            </td>
                            <td class="points-earned">
                                <input type="number" class="wc_input_price" name="<?php echo sprintf( '%s[%s][points]' , $value[ 'id' ] , $num ); ?>" min="1" value="<?php echo $row[ 'points' ]; ?>" autocomplete="off" required>
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
                        <td class="start-range">
                            <div class="wrapper">
                                <input class="start-date" type="text" name="<?php echo sprintf( '%s[0][sdate]' , $value[ 'id' ] ); ?>" placeholder="mm/dd/yyyy" value="" autocomplete="off" required>
                                <input class="start-time" type="text" name="<?php echo sprintf( '%s[0][stime]' , $value[ 'id' ] ); ?>" placeholder="hh:mm p" value="" autocomplete="off" required>
                            </div>
                        </td>
                        <td class="end-range">
                            <div class="wrapper">
                                <input class="end-date" type="text" name="<?php echo sprintf( '%s[0][edate]' , $value[ 'id' ] ); ?>" placeholder="mm/dd/yyyy" value="" autocomplete="off" required>
                                <input class="end-time" type="text" name="<?php echo sprintf( '%s[0][etime]' , $value[ 'id' ] ); ?>" placeholder="hh:mm p" value="" autocomplete="off" required>
                            </div>
                        </td>
                        <td class="points-earned">
                            <input type="number" class="wc_input_price" name="<?php echo sprintf( '%s[0][points]' , $value[ 'id' ] ); ?>" min="1" value=""  autocomplete="off" required>
                        </td>
                        <td class="actions"></td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="actions" colspan="4">
                        <a class="add-row" href="javascript:void(0);">
                            <span class="dashicons dashicons-plus"></span>
                            <?php _e( 'Add Row' , 'loyalty-program-for-woocommerce' ); ?>
                        </a>
                    </td>
                </tr>
                <tr class="blank-form">
                    <td class="start-range">
                        <div class="wrapper">
                            <input class="start-date" type="text" name="<?php echo sprintf( '%s[row_num][sdate]' , $value[ 'id' ] ); ?>" placeholder="mm/dd/yyyy" value="" autocomplete="off">
                            <input class="start-time" type="text" name="<?php echo sprintf( '%s[row_num][stime]' , $value[ 'id' ] ); ?>" placeholder="hh:mm p" value="" autocomplete="off">
                        </div>
                    </td>
                    <td class="end-range">
                        <div class="wrapper">
                            <input class="end-date" type="text" name="<?php echo sprintf( '%s[row_num][edate]' , $value[ 'id' ] ); ?>" placeholder="mm/dd/yyyy" value="" autocomplete="off">
                            <input class="end-time" type="text" name="<?php echo sprintf( '%s[row_num][etime]' , $value[ 'id' ] ); ?>" placeholder="hh:mm p" value="" autocomplete="off">
                        </div>
                    </td>
                    <td class="points-earned">
                        <input type="number" class="wc_input_price" name="<?php echo sprintf( '%s[row_num][points]' , $value[ 'id' ] ); ?>" min="1" value="" autocomplete="off">
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
jQuery(document).ready(function($) {

    var $field     = $( ".<?php echo esc_attr( $value[ 'id' ] ) . '-row'; ?>" ),
        $dataTable = $field.find( "table.within-order-range-data" ),
        $tbody     = $dataTable.find( "tbody" ),
        rowHtml    = "<tr>" + $dataTable.find( "tfoot tr.blank-form" ).html() + "</tr>";

    var Funcs = {

        events : function() {

            $dataTable.on( "click" , "tfoot td.actions .add-row" , Funcs.addRow );
            $dataTable.on( "click" , "td.actions .remove" , Funcs.deleteRow );
            $dataTable.on( "change" , "input[type='text']" , Funcs.preventTimeOverlap );

            $dataTable.find( "tfoot tr.blank-form" ).remove();
            Funcs.init();

        },

        init : function() {

            $tbody.find( "tr" ).each( function() {

                var $row   = $(this),
                    $from  = $row.find( "input.start-date" ),
                    $to    = $row.find( "input.end-date" ),
                    $stime = $row.find( "input.start-time" ),
                    $etime = $row.find( "input.end-time" );

                Funcs.initDatePicker( $from , $to );
                Funcs.initTimePicker( $stime , $etime , $from , $to );

            } );

        },

        addRow : function() {
            
            var $firstRow = $tbody.find( "tr:first-child" ),
                rowNum    = Funcs.getNewRowNum(),
                regex     = new RegExp( "row_num" , 'g' );
            
            $tbody.append( rowHtml.replace( regex , rowNum ) );

            var $row   = $tbody.find( "tr:last-child" ),
                $from  = $row.find( "input.start-date" ),
                $to    = $row.find( "input.end-date" ),
                $stime = $row.find( "input.start-time" ),
                $etime = $row.find( "input.end-time" );

            $row.find( "input,select" ).prop( "required" , true );

            Funcs.initDatePicker( $from , $to );
            Funcs.initTimePicker( $stime , $etime , $from , $to );
        },

        deleteRow : function() {
            
            var $this = $(this),
                $row  = $this.closest( "tr" );

            $row.remove();
            Funcs.refreshRowNums();
        },

        refreshRowNums : function() {

            var $rows = $tbody.find( "tr" ),
                $sdate, $stime, $edate, $etime, $points;

            for ( var x = 0; x < $rows.length; x++ ) {

                $sdate  = $( $rows[x] ).find( "input.start-date" );
                $stime  = $( $rows[x] ).find( "input.start-time" );
                $edate  = $( $rows[x] ).find( "input.end-date" );
                $etime  = $( $rows[x] ).find( "input.end-time" );
                $points = $( $rows[x] ).find( ".points-earned input[type='number']" );

                $sdate.prop( "name" , "acfw_loyalprog_earn_points_order_period[" + x + "][sdate]" );
                $stime.prop( "name" , "acfw_loyalprog_earn_points_order_period[" + x + "][stime]" );
                $edate.prop( "name" , "acfw_loyalprog_earn_points_order_period[" + x + "][edate]" );
                $etime.prop( "name" , "acfw_loyalprog_earn_points_order_period[" + x + "][etime]" );
                $points.prop( "name" , "acfw_loyalprog_earn_points_order_period[" + x + "][points]" );
            }
        },

        initDatePicker : function( $from , $to ) {

            var dateFormat = "mm/dd/yy";

            $from.datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: dateFormat
            })
            .on( "change" , function() {

                $to.datepicker( "option" , "minDate" , Funcs.getDate( this ) )
            } );

            $to.datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: dateFormat
            })
            .on( "change" , function() {
                $from.datepicker( "option" , "maxDate" , Funcs.getDate( this ) )
            } );
        },

        initTimePicker : function( $stime , $etime , $from , $to ) {

            $stime.timepicker({
                timeFormat: 'h:mm p',
                interval: 30,
                defaultTime: '',
                startTime: '6:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true,
                change: Funcs.preventTimeOverlap
            });

            $etime.timepicker({
                timeFormat: 'h:mm p',
                interval: 30,
                defaultTime: '',
                startTime: '6:00',
                dynamic: false,
                dropdown: true,
                scrollbar: true,
                change: Funcs.preventTimeOverlap
            });

        },

        getDate( element ) {

            var dateFormat = "mm/dd/yy",
                date;
                
            try {
                date = $.datepicker.parseDate( dateFormat, element.value );
            } catch( error ) {
                date = null;
            }
        
            return date;
        },

        getNewRowNum : function() {
            return $tbody.find( "tr" ).length;
        },

        preventTimeOverlap : function() {

            var $this = $( this ),
                $row  = $this.closest( "tr" );

            if ( $row.find( ".start-date" ).val() !== $row.find( ".end-date" ).val() ) return;

            var $stime     = $row.find( ".start-time" ),
                $etime     = $row.find( ".end-time" ),
                startArray = $stime.val().replace( ":" , "" ).split(" "),
                endArray   = $etime.val().replace( ":" , "" ).split(" "),
                startTime  = parseInt( startArray[0] ),
                endTime    = parseInt( endArray[0] );

            if ( startTime >= 1200 ) startTime -= 1200;
            if ( endTime >= 1200 ) endTime -= 1200;

            if ( startArray[1] == "PM" ) startTime += 1200;
            if ( endArray[1] == "PM" ) endTime += 1200;
            
            if ( endTime <= startTime ) {
                alert( "<?php _e( 'End time cannot be the same or lower than the set start time when start and end date values are the same. Field value will be reset.' , 'loyalty-program-for-woocommerce' ) ?>" )
                $etime.val( "" );
            }
                
        }
    };

    Funcs.events();

});
</script>