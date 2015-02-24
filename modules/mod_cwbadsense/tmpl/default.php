<?php
// no direct access
defined('_JEXEC') or die;

//load the Parameters
$slot = $params->get('slot');
$client = $params->get('client');
$format = $params->get('format');
$margin_top = $params->get('margin-top');
$margin_right = $params->get('margin-right');
$margin_bottom = $params->get('margin-bottom');
$margin_left = $params->get('margin-left');
$align = $params->get('align');
$custom = $params->get('custom-styles');

//set default 
$client = ($client == '') ? "ca-pub-9345761606373400" : $client;
$slot = ($slot == '') ? "3471978486" : $slot;
$format = ($format == '') ? "120 x 240" : $format;

//extract the ad size ( width and height from format params
$adformat = explode("x", $format);
$width = $adformat[0];
$height = $adformat[1];

// set style
if($custom) {
$style = $custom;
} else {
$style =  "text-align:".$align."; width:100%; margin: " . $margin_top ."px ". $margin_right ."px ". $margin_bottom ."px ". $margin_left . "px";
}
?>

<div style="<?php echo $style; ?>">
<script type="text/javascript"><!--
google_ad_client = "<?php echo $client; ?>";
google_ad_slot = "<?php echo $slot; ?>";
google_ad_width = "<?php echo $width; ?>";
google_ad_height = "<?php echo $height; ?>";
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</div>

