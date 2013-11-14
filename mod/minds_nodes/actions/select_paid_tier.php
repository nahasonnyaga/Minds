<?php

// "Buy" a free tier

gatekeeper();
elgg_load_library('elgg:pay');

$ia = elgg_set_ignore_access();

if ($tier = get_entity(get_input('tier_id'),'object')) {
    $order = new ElggObject();
    $order->subtype = 'pay';

    $order->order = true;

    //temp variables
    $order->seller_guid = elgg_get_site_entity()->guid;
    $order->object_guid = $tier->guid;

    $item = new stdClass();
    $item->title = $tier->title;
    $item->description = $tier->description;
    $item->price = $tier->price;
    $item->quantity = 1;
    $item->object_guid = $order->object_guid;
    $item->seller_guid = $order->seller_guid;
    if ($item->recurring == 'y') // TODO: Currently we have to set whole basket to recurring if one item repeats. Not idea.
            $recurring = true;
    $items[] = $item;

    $order->items = serialize($items);

    $order->amount = $tier->price;

    $order->access_id = 1;

    $order->payment_method = 'paypal';
    
    $order->recurring = true;
    
    $order_guid = $order->save();


	//now create a blank MindsNode ... don't launch yet though...
	$node = new MindsNode();
	$node->owner_guid = elgg_get_logged_in_user_guid();
	$node->launched = false;
	$node->tier_guid = $tier->guid;
	$node->order_guid = $order_guid;	
	$node->foo = bar;    
	$node->save();

 	pay_call_payment_handler($order->payment_method, array( 'order_guid' => $order_guid,
            'user_guid' => elgg_get_logged_in_user_guid(),
            'amount' => $order->amount,
            'recurring' => true
        ));

}
