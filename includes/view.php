<?php ob_start(); ?>
    <div class="my-cdek__form">
        <input type="hidden" class="my-cdek__weight" name="weight" value="<?php echo round(wc()->cart->get_cart_contents_weight(), 0, PHP_ROUND_HALF_UP) ?>">
        <input type="hidden" class="my-cdek__pvz" name="pvz" value="">
    </div>
<?php echo ob_get_clean(); ?>