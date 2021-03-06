<?php
/**
 * @link http://www.pickledup.com/
 * @copyright Copyright (c) 2014 PickledUp
 */

namespace nitm\filemanager\assets;

use yii\web\AssetBundle;

/**
 * @author Malcolm Paul <lefteyecc@nitm.com>
 */
class ImageAsset extends AssetBundle
{
	public $sourcePath = "@nitm/filemanager/assets/";
	public $css = [
		'css/images.css'
	];
	public $js = [
		'js/images.js'
	];
	public $depends = [
        'yii\web\JqueryAsset',
		'nitm\assets\AppAsset',
		'nitm\filemanager\assets\FileAsset',
		'kartik\file\FileInputAsset',
	];
}
