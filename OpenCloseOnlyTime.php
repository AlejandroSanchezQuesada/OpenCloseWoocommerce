
// CERRAR TIENDA SEGUN EL HORARIO
// 
// // Condicional que comprueba si la tienda esta abierta(Devuelve true si la tienda esta abierta)
function is_store_open() {
    // Establece tu zona horaria (http://php.net/manual/en/timezones.php)
    date_default_timezone_set('Europe/Madrid');

    // Establece los horarios start_time para apertura y end_time para finalizar
    $start_time = mktime('12', '00', '00', date('m'), date('d'), date('Y')); // 13:00:00
    $end_time   = mktime('22', '00', '00', date('m'), date('d'), date('Y')); // 22:00:00
    $now        = time(); // Current timestamp in seconds

    return ( $now >= $start_time && $now <= $end_time ) ? true : false;
}

// Desactivar compras durante la tienda cerrada
add_filter( 'woocommerce_variation_is_purchasable', 'disable_purchases_on_shop', 10, 2 );
add_filter( 'woocommerce_is_purchasable', 'disable_purchases_on_shop', 10, 2 );
function disable_purchases_on_shop( $purchasable, $product ) {
    // Desactivar compras durante la tienda cerrada
    if( ! is_store_open() )
        $purchasable = false;

    return $purchasable;
}

// Validacion del carrito y checkout, si esta cerrado no permite pagar
add_action( 'woocommerce_check_cart_items', 'conditionally_allowing_checkout' );
add_action( 'woocommerce_checkout_process', 'conditionally_allowing_checkout' );
function conditionally_allowing_checkout() {
    if ( ! is_store_open() ) {
        // Tienda cerrada
        wc_add_notice( __("Servicio a domicilio cerrado, estamos abiertos de 12:00 a 22:00"), 'error' );
    }
}

add_action( 'template_redirect', 'closing_shop_notice' );
function closing_shop_notice(){
    if ( ! ( is_cart() || is_checkout() ) && ! is_store_open() ) {
        // Aviso de tienda cerrada
        wc_add_notice( __("Servicio a domicilio cerrado, estamos abiertos de 12:00 a 22:00"), 'notice' );
    }
}