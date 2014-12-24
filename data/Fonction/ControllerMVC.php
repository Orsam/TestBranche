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
 * @subpackage ControllerMVC
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_ControllerMVC {

    public static function _Init($ArrayController) {
        $ObjMVC = new stdClass();
        # Renvoi le nom du fichier de la page
        $ObjMVC->page     = self::fichier_page();
        
        # On detecte si c'est une page En savoir plus
        $ObjMVC->page_esp = self::page_esp();
        # Renvoi le nom de la page
        $ObjMVC->NomPage  = self::nom_page();
        //$ObjMVC->GlobalNomPage  = Fonction_ControllerMVC::nom_page(true);
        define("NOM_PAGE", self::nom_page(true));
        
        $ObjMVC->PageAction = self::determination_controller($ArrayController);
        return $ObjMVC;
    }
     
    public static function fichier_page($chemin='') {
        // Revoi index.php
        $page = basename($_SERVER['REQUEST_URI']);
        if ($page == $chemin) {
            $page = 'index';
        }
        return $page;
    }

    public static function extension() {
        $nom = explode(".", Fonction_ControllerMVC::fichier_page());
        return $nom[1];
    }
    /**
     * Fonction permettant de vérifier si le nom de la page se termine par _r(0-9)
     * Dans ce cas c'est une page en savoir plus.
     * @return boolean Retourne True ou False 
     */
    public static function page_esp() {
        if (preg_match('/_r([0-9]+)/i', Fonction_ControllerMVC::fichier_page())) {
            return true;
        } else {
            return false;
        }
    }

    public static function nom_page($clean = false) {
        $nom = explode(".", Fonction_ControllerMVC::fichier_page());
        if ($clean) {
            // Revoie changepassword (sans extension)
            return str_replace("-", "", $nom[0]);
        } else {
            // Revoie change-password (sans extension)
            return $nom[0];
        }
    }

    /**
     * Fonction qui vérifie l'existance de la page en cours dans un controleur pré-determiné
     * @param array $Controller Tableau des controller de page défini dans index.php
     * @return string retourne le nom du controller ou vide 
     */
    public static function determination_controller($Controller) {
        $NomPage = self::nom_page();
        $ControllerPage = '';
        foreach ($Controller as $key => $value) {
            if (in_array($NomPage, $Controller[$key])) {
                $ControllerPage = $key."Controller";
            }
        }
        
        if ($ControllerPage == '') {
            $ControllerPage = self::fichier_controller_action();
        }

        return $ControllerPage;
    }

    /**
     * Fonction qui determine si c'est une action ou un controller
     */
    public static function fichier_controller_action() {
        
        $NomPage = self::nom_page(true);
        $FichierController = file_exists(CONTROLLERS_PATH . DIRECTORY_SEPARATOR . $NomPage . 'Controller.php');
        $FichierAction     = file_exists(ACTIONS_PATH     . DIRECTORY_SEPARATOR . $NomPage . 'Action.php');

        if ($FichierController == true && $FichierAction == true) {
            // Si il existe un fichier Action ET un fichier Controller
            throw new Exception('Une action ET un controller sont présent pour cette page');
        } else {
            if (file_exists(CONTROLLERS_PATH . DIRECTORY_SEPARATOR . $NomPage . 'Controller.php')) {
                return $NomPage."Controller";
            } else if (file_exists(ACTIONS_PATH . DIRECTORY_SEPARATOR . $NomPage . 'Action.php')) {
                return $NomPage."Action";
            } else {
                # Si il n'existe pas de fichier Action ni de fichier Controller
                //throw new Exception('Aucun Controlleur ou action pour cette page');
                return "_defaultController";
            }
        }
    }

}

?>
