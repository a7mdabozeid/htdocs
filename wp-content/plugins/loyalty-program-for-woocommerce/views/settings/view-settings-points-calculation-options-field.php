<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<tr valign="top" class="<?php echo esc_attr( $value[ 'id' ] ) . '-row'; ?>" id="<?php echo esc_attr( $value[ 'id' ] ); ?>">
    <th scope="row">
        <label>
            <?php echo sanitize_text_field( $value[ 'title' ] ); ?> <?php echo $tooltip; ?>
        </label>
    </th>
    <td>
        <input type="hidden" name="<?php echo $value[ 'id' ]; ?>" value="none">

        <?php foreach ( $value[ 'options' ] as $option => $label ) : ?>
        <fieldset>
            <label>
                <?php $option_val = isset( $calc_options[ $option ] ) ? $calc_options[ $option ] : 'no';  ?>
                <input type="checkbox" name="<?php echo sprintf( '%s[%s]' , $value[ 'id' ] , $option ); ?>" value="yes" <?php checked( $option_val , 'yes' ); ?>>
                <?php echo esc_html( $label ); ?> <?php echo isset( $tooltips[ $option ] ) ? wc_help_tip( $tooltips[ $option ] ) : ''; ?>
            </label>
        </fieldset>
        <?php endforeach; ?>

        <p class="description"><?php echo $desc; ?></p>
    </td>
</tr>