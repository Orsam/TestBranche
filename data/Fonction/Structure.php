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
 * @subpackage Structure
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description of Structure
 *
 * @author Olivier
 */
class Fonction_Structure {
    public static function NbrOption($id_structure) {
        // Renvoie le nombre de variable option d'une structure
        global $DB;
        $sql = "SELECT structure FROM `articles_structure` where `id` =" . $id_structure;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        preg_match_all("/{option_?([A-z0-9]+)?}/",$data->structure,  $out, PREG_SET_ORDER);
        return count($out);
    }
    public static function NbrPhoto($id_structure) {
        // Renvoie le nombre de photo d'une structure
        global $DB;
        $sql = "SELECT structure FROM `articles_structure` where `id` =" . $id_structure;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        preg_match_all("/{(photo|image)([0-9]+)?}/",$data->structure,  $out, PREG_SET_ORDER);
        return count($out);
    }
    public static function get_variable($id_structure,$variable_structure) {
        global $DB;
        $sql = "SELECT structure FROM `articles_structure` where `id` =" . $id_structure;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if (strripos($data->structure, $variable_structure) === false) {
            return false;
        } else {
            return true;
        }
    }
    

}

?>
