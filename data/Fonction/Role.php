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
 * Description de Role
 *
 * @author Olivier
 */
class Fonction_Role {
    public static function add($role,$libelle_role='') {
        global $DB;
        $sql = "SELECT role FROM `auth_roles` WHERE role ='$role'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            $d = array(str_replace("-", "_", Fonction_Page::nom_dynamique($role)),$libelle_role);
            $sql2 = "INSERT INTO `auth_roles` (`role`,`libelle_role`) VALUES (?,?)";
            $reqI = $DB->prepare($sql2);
            $reqI->execute($d);
            return true;
        }else{
            return false;
        }
    }
    public static function remove($role) {
        global $DB;
        $role = str_replace("-", "_", Fonction_Page::nom_dynamique($role));

        // Avant de supprimer, on vérifie si le role n'est pas utilisé par un User
        $sql = "SELECT role FROM `auth_users_roles` WHERE role ='$role'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            $sql = "DELETE FROM auth_roles WHERE role='$role'"; //on supprime un role
            return $DB->exec($sql);
        }else{
            return false;
        }
    }
    public static function existe($role) {
        global $DB;
        $role = str_replace("-", "_", Fonction_Page::nom_dynamique($role));

        $sql = "SELECT role FROM `auth_roles` WHERE role ='$role'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        }else{
            return true;
        }
    }
    
}
