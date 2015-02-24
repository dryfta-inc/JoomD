<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 **/
$players = $_GET['play'];
$sources = array();
$sources[] = '//intro.js';
$sources[] = '//core.js';
$sources[] = '//util.js';
$sources[] = '//adapters/'.$_GET['ad'].'.js';
$sources[] = '//load.js';
$sources[] = '//plugins.js'; 
$sources[] = '//cache.js'; 
//if($_GET['css'] != 'no') {
//	$sources[] = '//find.js';
//}
if (substr_count($players, 'swf') > 0 || substr_count($players, 'flv')) {
	$sources[] = '//flash.js';
}
$sources[] = '//languages/'.$_GET['lan'].'.js';
foreach (explode("-", $players) as $player) {
	$sources[] = '//players/'.$player.'.js';
}
$sources[] = '//skin.js';
$sources[] = '//outro.js';

return array(

	'sb' => $sources

    // 'js' => array('//js/file1.js', '//js/file2.js'),
    // 'css' => array('//css/file1.css', '//css/file2.css'),

    // custom source example
    /*'js2' => array(
        dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
        // do NOT process this file
        new Minify_Source(array(
            'filepath' => dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
            'minifier' => create_function('$a', 'return $a;')
        ))
    ),//*/

    /*'js3' => array(
        dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
        // do NOT process this file
        new Minify_Source(array(
            'filepath' => dirname(__FILE__) . '/../min_unit_tests/_test_files/js/before.js',
            'minifier' => array('Minify_Packer', 'minify')
        ))
    ),//*/
);