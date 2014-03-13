<?php


$tier_id = get_input('tier_id');
$node_guid = get_input('node_guid');

$ia = elgg_set_ignore_access();

if ($tier_id && $node_guid) {
    
    $tier = get_entity($tier_id);
    $node = get_entity($node_guid);
    
    
    if ($tier && $node && ($order = get_entity($node->order_guid, 'object'))) {
        
        // Authorize
        
        
        
        
        // Cancel old order
        if (!pay_call_cancel_recurring_payment($order->payment_method, $order->guid)) {
            register_error("Could not cancel existing order, please contact support");
            forward(REFERRER);
        }
        
        // Buy new order
        action('select_tier');
    }
    else
        register_error("No tier or node");
}
else
    register_error("No tier id or node id");