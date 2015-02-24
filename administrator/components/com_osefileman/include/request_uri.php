<?php
    defined('_JEXEC') or die('Restricted access');
    /*
    * REQUEST_URI for IIS Servers
    * Version: 1.1
    * Guaranteed to provide Apache-compliant $_SERVER['REQUEST_URI'] variables
    * Please see full documentation at

    * Copyright NeoSmart Technologies 2006-2008
    * Code is released under the LGPL and maybe used for all private and public code

    * Instructions: http://neosmart.net/blog/2006/100-apache-compliant-request_uri-for-iis-and-windows/
    * Support: http://neosmart.net/forums/forumdisplay.php?f=17
    * Product URI: http://neosmart.net/dl.php?id=7
    */

    //This file should be located in the same directory as php.exe or php5isapi.dll

    //ISAPI_Rewrite 3.x
    if (isset($_SERVER['HTTP_X_REWRITE_URL'])){
        $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_REWRITE_URL'];
    }
    //ISAPI_Rewrite 2.x w/ HTTPD.INI configuration
    else if (isset($_SERVER['HTTP_REQUEST_URI'])){
        $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];
        //Good to go!
    }
    //ISAPI_Rewrite isn't installed or not configured
    else{
        //Someone didn't follow the instructions!
        if(isset($_SERVER['SCRIPT_NAME']))
            $_SERVER['HTTP_REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
        else
            $_SERVER['HTTP_REQUEST_URI'] = $_SERVER['PHP_SELF'];
        if($_SERVER['QUERY_STRING']){
            $_SERVER['HTTP_REQUEST_URI'] .=  '?' . $_SERVER['QUERY_STRING'];
        }
        //WARNING: This is a workaround!
        //For guaranteed compatibility, HTTP_REQUEST_URI or HTTP_X_REWRITE_URL *MUST* be defined!
        //See product documentation for instructions!
        $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];
    }

?>