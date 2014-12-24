<?php
/**
 * Neuro-Admin
 *
 * LICENSE
 * Avertissement : Cette source est protégée par la loi du copyright et par les conventions internationales.
 * Toute reproduction ou distribution partielle ou totale de cette source, par quelque moyen que ce soit, 
 * est strictement interdite. Toute personne ne respectant pas ces dispositions se rendra coupable du délit 
 * de contrefaçon et sera passible des peines pénales prévues par la loi.
 *
 * @category   Neuro-Admin
 * @package    Fonction
 * @subpackage Webservice
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class WebService {

    private static $_instance = null;
    public  $_ClassName;
    
    public static function getInstance($ClassName) {
        if(is_null(self::$_instance)) {
            self::$_instance = new WebService($ClassName);
        }
        return self::$_instance;
    }    
    
    function __construct($ClassName) {
        $this->_ClassName = $ClassName;
        if(file_exists($this->_ClassName.'.php')){
            require_once $this->_ClassName.'.php';
        }else{
            echo "Fichier Class '$this->_ClassName.php' non trouvé";
            exit();
        }
        if(!file_exists($this->_ClassName.'.wsdl')){
            echo "Fichier Wsdl '$this->_ClassName.wsdl' non trouvé";
            exit();
        }
        // Pour supprimer le cache du web-service
        ini_set('soap.wsdl_cache_enabled', 0);
        // Pour définir le temp maximal d'éxecution de notre web-service
        ini_set('default_socket_timeout', 180);

        $server = new SoapServer($this->_ClassName.'.wsdl');
        $server->setClass($this->_ClassName);
        $server->setPersistence(SOAP_PERSISTENCE_SESSION);
        $server->handle();
        
        
        }
    
}


//$Serveur->lancement();

?>
