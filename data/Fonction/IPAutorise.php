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
 * @subpackage IPAutorise
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * IPAutorise est une class utilisée lors du développement.
 *
 * @author Olivier
 */
class Fonction_IPAutorise {
    /**
     * Function permettant de vérifier si une adresse IP est Autorisée dans la table Neuro_ip
     * Exemple :
     * <code>
     * // L'exemple suivant renvoi True ou False
     *  echo Fonction_IPAutorise::__get();
     * </code>
     * @global object $DB Connection à la base de données
     * @return boolean Retourne True ou false
     * @since Version 3.0
     * @version Version 1.0
     */
    public static function get() {
        global $DB;
        $sql = "SELECT ip FROM neuro_ip WHERE ip='" . $_SERVER['REMOTE_ADDR'] . "'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }
    /**
     * Function permettant d'autoriser une adresse IP dans la table Neuro_ip
     * Exemple :
     * <code>
     * // Autorise l'IP REMOTE_ADDR en cours
     *  echo Fonction_IPAutorise::__set();
     * </code>
     * @global object $DB Connection à la base de données
     * @return void
     * @since Version 3.0
     * @version Version 1.0
     */
    public static function set() {
        global $DB;
        if (self::get()==false){
            $d = array($_SERVER['REMOTE_ADDR']);
            $reqI = $DB->prepare('INSERT INTO `neuro_ip` (`ip`) VALUES (?);');
            $reqI->execute($d);
        }
    }

}

?>
