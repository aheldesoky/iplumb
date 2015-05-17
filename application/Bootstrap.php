<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    protected function _initRequest()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $request = new Zend_Controller_Request_Http();
        $front->setRequest($request);
        return $request;
    }
    
    protected function _initPlaceholders()
    {
        $this->bootstrap('View');
        $view = $this->getResource('View');
        $view->doctype('XHTML1_STRICT');
        
        //Meta
        $view->headMeta()
             ->appendName('viewport', 'width=device-width, initial-scale=1')
             ->appendName('keywords', 'iCount, Accountant')
             ->appendHttpEquiv('Content-Type','text/html;charset=utf-8');
        
        // Set the initial title and separator:
        $view->headTitle('iPlumb')->setSeparator(' :: ');
        
        // Set the initial stylesheet:
        $view->headLink()->prependStylesheet($view->baseUrl('/css/sb-admin-2.css'))
                         ->prependStylesheet($view->baseUrl('/css/bootstrap.min.css'))
                         ->prependStylesheet($view->baseUrl('/css/plugins/metisMenu/metisMenu.min.css'))
                         ->prependStylesheet($view->baseUrl('/css/plugins/morris.css'))
                         ->prependStylesheet($view->baseUrl('/css/font-awesome/css/font-awesome.min.css'));
        
        // Set the initial JS to load:
        $view->headScript()->prependFile($view->baseUrl('/js/sb-admin-2.js'))
                           ->prependFile($view->baseUrl('/js/bootstrap.min.js'))
                           ->prependFile($view->baseUrl('/js/plugins/metisMenu/metisMenu.min.js'))
                           ->prependFile($view->baseUrl('/js/jquery-1.11.0.js'));
        
        //Zend_Controller_Front::getInstance()->registerPlugin(new Plugins_Mylayout());
    }
    
    //To activate session
    protected function _initSession(){
        Zend_Session::start();
        $session = new Zend_Session_Namespace( 'Zend_Auth' );
        $session->setExpirationSeconds( 80000 );
    }

    //To initialize translation
    protected function _initTranslate() {
        // We use the Arabic locale
        $locale = new Zend_Locale('ar_AR');
        Zend_Registry::set('Zend_Locale', $locale);

        // Create Session block and save the locale
        $session = new Zend_Session_Namespace('session');
        $langLocale = isset($session->lang) ? $session->lang : $locale;

        // Set up and load the translations (all of them!)
        $translate = new Zend_Translate('gettext', APPLICATION_PATH . DIRECTORY_SEPARATOR .'languages', $langLocale,
           array('disableNotices' => true));

        //$translate->setLocale($langLocale); // Use this if you only want to load the translation matching current locale, experiment.

        // Save it for later
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Translate', $translate);
    }
    

}

