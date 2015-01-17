<?php
/**
 * Save blog entity
 *
 * Can be called by clicking save button or preview button. If preview button,
 * we automatically save as draft. The preview button is only available for
 * non-published drafts.
 *
 * Drafts are saved with the access set to private.
 *
 * @package Blog
 */

// start a new sticky form session in case of failure
elgg_make_sticky_form('blog');

// save or preview
$save = (bool)get_input('save');

// store errors to pass along
$error = FALSE;
$error_forward_url = REFERER;
$user = elgg_get_logged_in_user_entity();

if(get_input('license') == 'not-selected'){
	register_error(elgg_echo('minds:license:not-selected'));
	return	forward(REFERER);
}

// edit or create a new entity
$guid = get_input('guid');

if ($guid) {
	$entity = get_entity($guid, 'object');
	if (elgg_instanceof($entity, 'object', 'blog') && $entity->canEdit()) {
		$blog = $entity;
	} else {
		register_error(elgg_echo('blog:error:post_not_found'));
		forward(get_input('forward', REFERER));
	}

	// save some data for revisions once we save the new edit
	$revision_text = $blog->description;
	$new_post = $blog->new_post;
} else {
	$blog = new ElggBlog();
	$blog->subtype = 'blog';
	$new_post = TRUE;
}

// set the previous status for the hooks to update the time_created and river entries
$old_status = $blog->status;

// set defaults and required values.
$values = array(
	'title' => '',
	'description' => '',
	'status' => 'draft',
	'access_id' => ACCESS_DEFAULT,
	'comments_on' => 'On',
	'excerpt' => '',
	'tags' => '',
	'container_guid' => (int)get_input('container_guid'),
	'license' => '',
	'banner_position' => 0
);

// fail if a required entity isn't set
$required = array('title', 'description');

// load from POST and do sanity and access checking
foreach ($values as $name => $default) {
	if ($name === 'title') {
		$value = htmlspecialchars(get_input('title', $default, false), ENT_QUOTES, 'UTF-8');
	} else {
		$value = get_input($name, $default);
	}

	if (in_array($name, $required) && empty($value)) {
		$error = elgg_echo("blog:error:missing:$name");
	}

	if ($error) {
		break;
	}

	switch ($name) {
		case 'excerpt':
			if ($value) {
				$values[$name] = elgg_get_excerpt($value);
			}
			break;

		case 'container_guid':
			// this can't be empty or saving the base entity fails
			if (!empty($value)) {
				if (can_write_to_container($user->getGUID(), $value)) {
					$values[$name] = $value;
				} else {
					$error = elgg_echo("blog:error:cannot_write_to_container");
				}
			} else {
				unset($values[$name]);
			}
			break;

		default:
			$values[$name] = $value;
			break;
	}
}

// if preview, force status to be draft
if ($save == false) {
	$values['status'] = 'draft';
}

// if draft, set access to private and cache the future access
if ($values['status'] == 'draft') {
	$values['future_access'] = $values['access_id'];
	$values['access_id'] = ACCESS_PRIVATE;
}

// assign values to the entity, stopping on error.
if (!$error) { 
	foreach ($values as $name => $value) {
		if (FALSE === ($blog->$name = $value)) {
			$error = elgg_echo('blog:error:cannot_save' . "$name=$value");
			break;
		}
	}
}

if(get_input('removeHeader')){
	$blog->header_bg = false;
}

$blog->banner_position = get_input('banner_position', 0);

// only try to save base entity if no errors
if (!$error) {
	if ($guid = $blog->save()) {
		
		
		
		/**
		 * If we have a header banner image
		 */
		if(is_uploaded_file($_FILES['header']['tmp_name'])){
			$resized = get_resized_image_from_uploaded_file('header', 2000);
			$file = new ElggFile();
			$file->owner_guid = $blog->owner_guid;
			$file->setFilename("blog/{$guid}.jpg");
			$file->open('write');
			$file->write($resized);
			$file->close();
			$blog->header_bg = true;
			$blog->last_updated = time();
			$blog->save();
		}
		
		// remove sticky form entries
		elgg_clear_sticky_form('blog');

		system_message(elgg_echo('blog:message:saved'));

		$status = $blog->status;

		// add to river if changing status or published, regardless of new post
		// because we remove it for drafts.
		if (($new_post || $old_status == 'draft') && $status == 'published') {
		
			$activity = new minds\entities\activity();
			$activity->setTitle($blog->title)
					->setBlurb(elgg_get_excerpt($blog->description))
					->setUrl($blog->getURL())
					->setThumbnail($blog->getIconURL())
					->setFromEntity($blog)
					->save();
		
		} else {
			$activity_guids = Minds\Core\Data\indexes::fetch("activity:entitylink:$entity->guid");
			foreach($activity_guids as $activity_guid){
				$activity = new minds\entities\activity($activity_guid);
				$activity->setTitle($blog->title)
					->setBlurb(elgg_get_excerpt($blog->description))
					->setUrl($blog->getURL())
					->setThumbnail($blog->getIconURL())
					->setFromEntity($blog)
					->save();
			}
		}
		

		if ($blog->status == 'published' || $save == false) {
			forward($blog->getURL());
		} else {
			forward("blog/edit/$blog->guid");
		}
	} else {
		register_error(elgg_echo('blog:error:cannot_save'));
		forward($error_forward_url);
	}
} else {
	register_error($error);
	forward($error_forward_url);
}
