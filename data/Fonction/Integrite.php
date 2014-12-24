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
 * @subpackage Article
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de Integrite
 *
 * @author Olivier
 */
class Fonction_Integrite {
    public static function Erreur_mail($UserMail) {
        global $DB;
        $sql = "SELECT * FROM `Erreur_Integrite_Mail` WHERE mail='$UserMail'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }    
    public static function Erreur_NomPrenom($UserLogin) {
        global $DB;
        $sql = "SELECT * FROM `Erreur_Integrite_NomPrenom` WHERE login='$UserLogin'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }
}