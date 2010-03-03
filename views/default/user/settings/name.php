<?php
/**
 * Provide a way of setting your full name.
 *
 * @package Elgg
 * @subpackage Core

 * @author Curverider Ltd

 * @link http://elgg.org/
 */

$user = page_owner_entity();

// all hidden, but necessary for properly updating user details
echo elgg_view('input/hidden', array('internalname' => 'name', 'value' => $user->name));
echo elgg_view('input/hidden', array('internalname' => 'guid', 'value' => $user->guid));
