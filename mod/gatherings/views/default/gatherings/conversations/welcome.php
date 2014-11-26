<?php
?>

<form action="<?= elgg_get_site_url() ?>gatherings/conversation/new" class="conversation-engage">
	
	<?= elgg_view('input/autocomplete', array('data-type'=>'user', 'placeholder'=>'Who do you want to chat with?', 'class'=>'user-lookup', 'name'=>'u', 'value'=>get_input('referrer'))) ?>
	
	<input type="submit" value="Start conversation" class="elgg-button elgg-button-action"/>

</form>


<div class="conversation-configuration">
	<h3>Encryption</h3>
	
	<?php 
		$option = elgg_get_plugin_user_setting('option', elgg_get_logged_in_user_guid(), 'gatherings');
		$publickey = elgg_get_plugin_user_setting('publickey', elgg_get_logged_in_user_guid(), 'gatherings');
		if((int) $option == 1){ ?>
			<div class="keypair-1 configured">
				<p>You have configured encryption. </p>
				<!--<pre><?= $publickey ?></pre>-->
			</div>
		<?php } elseif((int)$options == 2){ ?>
			<div class="keypair-2 configured">
				<p>You have configured 'advanced' encryption. Your public key is:</p>
				<!--<pre><?= $publickey ?></pre>-->
			</div>
		<?php } else { ?>
			<div class="un-configured">
				<p>Your messages are currently <b>NOT</b> encrypted. Please select an encryption method below:</p>
				
				<form action="<?= elgg_get_site_url() ?>gatherings/configuration/keypair-1" method="POST">
					<input type="password" name="passphrase" placeholder="Enter a secure password - recommended"/>
					<input type="submit" value="Enable encryption" class="elgg-button elgg-button-action"/>
					
					<?= elgg_view('input/securitytoken') ?>
				</form>
				
				<form action="<?= elgg_get_site_url() ?>gatherings/configuration/keypair-2" method="POST">
					<input type="submit" value="Enable advanced encryption" class="elgg-button elgg-button-action" disabled/>
				</form>
				
			</div>
		<?php }
	
	?>
	
</div>
