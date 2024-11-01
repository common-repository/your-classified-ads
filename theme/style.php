<?php
require_once('../../../../wp-blog-header.php');
header('Content-type: text/css');

?>
body{
	background-color:#000;
}
@import url('_inc/css/shared.css');

<?php if(!defined('BP_VERSION')){//load styles for WP ?>
@import url('_inc/css/screen-wp.css');
<?php }else{ //load styles for BP ?>
@import url('_inc/css/screen-bp.css');
<?php
}
?>



