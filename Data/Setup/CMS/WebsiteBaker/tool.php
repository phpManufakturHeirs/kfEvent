<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/propangas24
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

if (!defined('WB_PATH') || !isset($_SESSION['USERNAME']))
    throw new Exception('Access is not authorized, please authenticate first!');

global $database;

if (null === ($pwd = $database->get_one("SELECT `password` FROM `".TABLE_PREFIX."users` WHERE `username`='".$_SESSION['USERNAME']."'", MYSQL_ASSOC)))
    throw new Exception($database->get_error());

$cms_info = array(
    'type' => defined('LEPTON_VERSION') ? 'LEPTON' : 'WebsiteBaker',
    'version' => defined('LEPTON_VERSION') ? LEPTON_VERSION : WB_VERSION,
    'locale' => strtolower(LANGUAGE),
    'username' => $_SESSION['USERNAME'],
    'authentication' => $pwd,
    'target' => 'cms'
);

$iframe_source = WB_URL.'/kit2/event/cms/'.base64_encode(json_encode($cms_info));

$cms_info['target'] = 'framework';
$framework_url = WB_URL.'/kit2/event/cms/'.base64_encode(json_encode($cms_info));
$expand_img = WB_URL.'/kit2/extension/phpmanufaktur/phpManufaktur/Event/Template/default/backend/image/expand_10x10.png';

echo <<<EOD
    <div style="width:100%;height:10px;margin:0 0 2px 0;padding:0;text-align:right;">
        <a href="$framework_url" target="_blank">
            <img src="$expand_img" width="10" height="10" alt="Open in kitFramework" title="Open in kitFramework" />
        </a>
    </div>
    <iframe id="kitframework_iframe" width="100%" height="700" src="$iframe_source" name="event" frameborder="0" style="border: 1px solid #ccc;" scrolling="auto" marginheight="0px" marginwidth="0px">
        <p>Sorry, but your browser does not support embedded frames!</p>
    </iframe>
    <div style="font-size:10px;text-align:right;margin:2px 0 0 0;padding:0;">
        <a href="https://kit2.phpmanufaktur.de" target="_blank">kitFramework by phpManufaktur</a>
    </div>
EOD;
