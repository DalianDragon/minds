<?php
/**
* Kaltura video client
* @package ElggKalturaVideo
* @license http://www.gnu.org/licenses/gpl.html GNU Public License version 3
* @author Ivan Vergés <ivan@microstudi.net>
* @copyright Ivan Vergés 2010
* @link http://microstudi.net/elgg/
**/

include_once($CONFIG->pluginspath."kaltura_video/kaltura/api_client/includes.php");

$owner = $vars['entity']->getOwnerEntity();
$access_id = $vars['entity']->access_id;

$metadata = kaltura_get_metadata($vars['entity']);
if(@empty($metadata->kaltura_video_id)) {
	$vars['entity']->delete();
	return false;
}

list($votes,$rating_image,$rating) = kaltura_get_rating($vars['entity']);

$rating = round($rating);

//get the number of comments
$num_comments = $vars['entity']->countComments();

$group = get_entity($vars['entity']->container_guid);
if(!($group instanceof ElggGroup)) $group = false;

if(elgg_get_context()!='search') {
	//this view is for My videos:

?>

<div class="contentWrapper singleview">

<div class="kalturavideoitem" id="kaltura_video_<?php echo $metadata->kaltura_video_id; ?>">

<div class="left">
<p><a href="<?php echo $vars['entity']->getURL(); ?>" rel="<?php echo $metadata->kaltura_video_id; ?>" class="play"><img src="<?php echo $metadata->kaltura_video_thumbnail; ?>" alt="<?php echo htmlspecialchars($vars['entity']->title); ?>" title="<?php echo htmlspecialchars($vars['entity']->title); ?>" width="50" /></a></p>
</div>

<div class="right">

<h3><a href="<?php echo $vars['entity']->getURL(); ?>"><?php echo $vars['entity']->title; ?></a></h3>


<p class="small">
<?php echo ' <b class="kaltura_video_created">'.$metadata->kaltura_video_created.'</b>'; ?>
 <?php echo elgg_echo('by'); ?> <a href="<?php echo $CONFIG->wwwroot.'pg/kaltura_video/'.$owner->username; ?>" title="<?php echo htmlspecialchars(elgg_echo("kalturavideo:user:showallvideos")); ?>"><?php echo $owner->name; ?></a>

<?php
if($group) {
	echo elgg_echo('ingroup')." <a href=\"{$CONFIG->wwwroot}pg/kaltura_video/{$group->username}/\" title=\"".htmlspecialchars(elgg_echo("kalturavideo:user:showallvideos"))."\">{$group->name}</a> ";
}

if($metadata->kaltura_video_comments_on != 'Off') {
 ?>
 - <a href="<?php echo $vars['entity']->getURL(); ?>#comments"><?php echo sprintf(elgg_echo("comments")) . " (" . $num_comments . ")"; ?></a>
<?php
}
?>
</p>


<p class="small">
<?php echo elgg_echo("kalturavideo:label:length"); echo ' <strong class="kaltura_video_length">'.$metadata->kaltura_video_length.'</strong>'; ?>

<?php echo elgg_echo("kalturavideo:label:plays"); echo ' <strong class="kaltura_video_plays" rel="'.$metadata->kaltura_video_id.'">'.intval($metadata->kaltura_video_plays).'</strong>'; ?>

</p>


</div>

<div class="clear"></div>
</div>

</div>

<?php
}
else {
	//this view is for everything else

	$icon = '<a href="'.$vars['entity']->getURL().'">';
	$icon .= '<img src="' . $metadata->kaltura_video_thumbnail . '" alt="' . htmlspecialchars($vars['entity']->title) . '" title="' . htmlspecialchars($vars['entity']->title) . '" />';
	$icon .= '</a>';
	$info = "<p class=\"shares_gallery_title\">". elgg_echo("kalturavideo:river:shared") .": <a href=\"";
	$info .= $vars['entity']->getURL();
	$info .= "\">{$vars['entity']->title}</a> ";
	$info .= "</p>";
	//when listing user videos is ok:
	$info .= "<p class=\"owner_timestamp\">";
	$info .= $metadata->kaltura_video_created." ";
	$info .= elgg_echo('by')." <a href=\"{$vars['url']}pg/kaltura_video/{$owner->username}/\" title=\"".htmlspecialchars(elgg_echo("kalturavideo:user:showallvideos"))."\">{$owner->name}</a> ";
	if($group) $info .= elgg_echo('ingroup')." <a href=\"{$vars['url']}pg/kaltura_video/{$group->username}/\" title=\"".htmlspecialchars(elgg_echo("kalturavideo:user:showallvideos"))."\">{$group->name}</a> ";
	$info .= elgg_echo("kalturavideo:label:length"). ' <strong>'.$metadata->kaltura_video_length.'</strong> ';
	$info .= elgg_echo("kalturavideo:label:plays"). ' <strong>'.((int)$metadata->kaltura_video_plays).'</strong>';

	if($metadata->kaltura_video_rating_on != 'Off') {
		$info .= " ".elgg_echo("kalturavideo:rating").": <strong>".$rating."</strong>";
	}
	

	if ($num_comments && $metadata->kaltura_video_comments_on != 'Off')
		$info .= ", <a href=\"{$vars['entity']->getURL()}\">".sprintf(elgg_echo("comments")). " (" . $num_comments . ")</a>";



	$info .= "</p>";

	if (get_input('search_viewtype') == "gallery") {
		//display
		$info = '<p class="shares_gallery_title">'.$icon.'</p>';
		$info .= "<p class=\"shares_gallery_title\"><a href=\"";
		$info .= $vars['entity']->getURL();
		$info .= "\">{$vars['entity']->title}</a> ";
		$info .= "</p>";
		$info .= "<p class=\"shares_gallery_user\">";
		$info .= elgg_echo("kalturavideo:label:length"). ' <strong>'.$metadata->kaltura_video_length.'</strong> ';
		$info .= elgg_echo("kalturavideo:label:plays"). ' <strong>'.intval($metadata->kaltura_video_plays).'</strong>';
		$info .= "</p>";
		//when listing user videos is ok:
		$info .= "<p class=\"shares_gallery_user\"><a href=\"{$vars['url']}pg/kaltura_video/{$owner->username}/\">{$owner->name}</a> ";
		if($group) $info .= elgg_echo('ingroup')." <a href=\"{$vars['url']}pg/kaltura_video/{$group->username}/\" title=\"".htmlspecialchars(elgg_echo("kalturavideo:user:showallvideos"))."\">{$group->name}</a> ";

		$info .= '<span class="shared_timestamp">'.$metadata->kaltura_video_created.'</span>';

		if($metadata->kaltura_video_rating_on != 'Off') {
			$info .= " ".elgg_echo("kalturavideo:rating").": <strong>".$rating."</strong>";
		}

		if ($num_comments && $metadata->kaltura_video_comments_on != 'Off')
			$info .= ", <a href=\"{$vars['entity']->getURL()}\">".sprintf(elgg_echo("comments")). " (" . $num_comments . ")</a>";


		echo "<div class=\"share_gallery_view\">";
		echo "<div class=\"share_gallery_info\">" . $info . "</div>";
		echo "</div>";

	}
	else {
		//this view is for context search listing
		echo elgg_view_listing($icon, $info);
	}
}
?>