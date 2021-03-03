
// CERRAR TIENDA SEGUN EL HORARIO
// 

// // Utility conditional funtion for store open hours (returns boolean true when store is open)
function is_store_open() {
    // Set Your shop time zone (http://php.net/manual/en/timezones.php)
    date_default_timezone_set('Europe/Madrid');

    // Below your shop time and dates settings
    $start_time = mktime('12', '00', '00', date('m'), date('d'), date('Y')); // 13:00:00
    $end_time   = mktime('22', '00', '00', date('m'), date('d'), date('Y')); // 22:00:00
    $now        = time(); // Current timestamp in seconds

    return ( $now >= $start_time && $now <= $end_time ) ? true : false;
}

// Get current day of the week
function day_week(){
    //We get current date but we just need the number of the day of the week.
    //Monday is 1 Thursday 2 Friday 5 ...
    //Im gonna close the business from Monday to Tuesday so im gonna open only on weekends!
    $today = getdate();
    
    //Checking if is weekend
     if ($today["wday"] >= 5 ) {
         return 1;
     } else {
         return 0;
     }
}



// Disable purchases on closing shop time
add_filter( 'woocommerce_variation_is_purchasable', 'disable_purchases_on_shop', 10, 2 );
add_filter( 'woocommerce_is_purchasable', 'disable_purchases_on_shop', 10, 2 );
function disable_purchases_on_shop( $purchasable, $product ) {
    // Disable purchases on closing shop time
    if( ! is_store_open() || day_week() == 0 )
        $purchasable = false;

    return $purchasable;
}

// Cart and checkout validation
add_action( 'woocommerce_check_cart_items', 'conditionally_allowing_checkout' );
add_action( 'woocommerce_checkout_process', 'conditionally_allowing_checkout' );
function conditionally_allowing_checkout() {
    if ( ! is_store_open() || day_week() == 0 ) {
        // Store closed
        wc_add_notice( __("Servicio a domicilio cerrado, estamos abiertos de 12:00 a 22:00"), 'error' );
    }
}

add_action( 'template_redirect', 'closing_shop_notice' );
function closing_shop_notice(){
    if ( ! ( is_cart() || is_checkout() ) && ! is_store_open() ) {
        // Store closed notice
        wc_add_notice( __("Servicio a domicilio cerrado, estamos abiertos de 12:00 a 22:00"), 'notice' );
    }
}





