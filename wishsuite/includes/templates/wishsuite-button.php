<a 
    href="<?php echo esc_url( $button_url ); ?>" 
    class="<?php echo $button_class; ?>"
    <?php if(!$show_text) {?> title="<?php echo esc_attr( $button_tooltip ); ?>" <?php } ?>
   
    data-switch-text="<?php if($show_text) { echo esc_attr( $added ? $button_text : $button_added_text, 'wishsuite'); }?>" 
    data-product_id="<?php echo esc_attr( $product_id ); ?>">
        <?php echo $button_icon; ?>
        <?php if($show_text) {echo '<span class="wishsuite-btn-text">'; echo $added ? $button_added_text : $button_text; echo '</span>'; }?>
</a>
