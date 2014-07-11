<?php

    $user = $vars['user'];
    $wallet = $vars['wallet'];
    

?><div class='wallet'>
    
    <?php
	if ($wallet) {
	    ?>
    
    <div class="header">
	<p>
	    <label>Wallet ID: </label> <a href="<?php echo $wallet->wallet_link; ?>" target="_blank"><?php echo $wallet->wallet_guid; ?></a>
	</p>
	<p>
	    <label>Wallet bitcoin address: </label> <?php echo $wallet->wallet_address; ?>
	</p>
	<p class="balance">
	    <label>Balance: </label> <?php
	    try {
		echo sprintf("%f", \minds\plugin\bitcoin\bitcoin()->getWalletBalance($wallet->guid));
		echo " BTC";
	    } catch (\Exception $e) {
		echo $e->getMessage();
		register_error($e->getMessage());
	    }
	    ?>
	</p>
	
    </div>
    
    <div class="report">
	<h3>Transactions</h3>
	
	<?php
	    echo elgg_list_entities(array(
		'type' => 'object',
		'subtype' => 'bitcoin_transaction',
		'owner_guid' => $user->guid,
		'full_view' => false
	    ));
	?>
    </div>
    
    <?php
	} else {
	  ?>
    
    <p>No wallet registered, <a href="<?php echo elgg_get_site_url(); ?>settings/plugins/<?php echo $user->username; ?>">why not create one?</a></p>
    
    <?php
	}
	?>
    
</div>