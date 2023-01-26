<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Security;

use Throwable;
use Tanweb\Config\Server as Server;
use Tanweb\Security\Security as Security;
use Tanweb\TanwebException as TanwebException;
use Tanweb\Container as Container;
use Tanweb\Logger\Logger as Logger;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Config\Pages as Pages;

/**
 * Security for pages, should be used on EVERY page that needs protection
 * for example allow only admins access to admin panel
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class PageAccess {
    
    /**
     * Allows to pass users with listed privilages
     * if empty array is passed it allows everyone
     * 
     * @param array $privilages - privilages required to pass
     */
    public static function allowFor(array $privilages){
        try{
            self::blockInternetExplorer();
            $address = Server::getRequestUrl();
            $logger = Logger::getInstance();
            $logger->logAccess($address);
            $security = Security::getInstance();
            $container = new Container($privilages);
            $granted = $security->userHaveAnyPrivilage($container);
            if(!$granted){
                $logger->logSecurity('Access Denied for url: ' . $address);
                $languages = Languages::getInstance();
                throw new SecurityException($languages->get('access_denied'));
            }
        } catch (TanwebException $ex) {
            $msg = '[url: ' . Server::getRequestUrl(). '] ' . $ex->errorMessage();
            self::redirectToIndex($msg);
        } catch (Throwable $ex) {
            $msg = '[url: ' . Server::getRequestUrl(). '] ' . 
                    $ex->getMessage() . " ;\n " .$ex->getTraceAsString();
            self::redirectToIndex($msg);
        }
    }
    
    public static function blockInternetExplorer() : void {
        $ie_user = !!preg_match('/MSIE (6|7|8)/', $_SERVER['HTTP_USER_AGENT']);
        if($ie_user){
            header("Location: " . Pages::getURL('unsupportedBrowser.html'));
            die();
        }
    }
    
    
    private static function redirectToIndex($msg){
        echo '<script>';
        echo 'console.log("' . $msg . '");';
        $languages = Languages::getInstance();
        echo 'alert("' . $languages->get('redirect_index') . '");';
        $path = Server::getIndexPath();
        echo 'window.location.replace("' . $path . '")';
        echo '</script>';
    }
}
