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
 * @subpackage ById
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Function_ById regroupe les fonctions de recherche par Id article.
 * ADELE
 * @author Olivier
 */
class Fonction_ById {
    /**
     * Function qui retourne le numéro de la page, dont l'id est mis en parametre.
     * Exemple :
     * <code>
     * // Retourne le numéro de la page sur laquelle se trouve l'article 6
     * echo Fonction_ById::get_numpage(6);
     * </code>
     * @global object $DB variable de connection à la base de données
     * @param int $id_article Id de l'a page'article sur lequel porte la demande
     * @return int Retourne le numero de la page
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function get_numpage($id_article) {
        global $DB;
        $req = $DB->query("SELECT id_page FROM `articles_vue` WHERE `id` =$id_article");
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->id_page;
    }
    /**
     * Function qui retourne le numéro de la page, dont l'id est mis en parametre.
     * Exemple :
     * <code>
     * // Retourne le numéro de la page sur laquelle se trouve l'article 6
     * echo Fonction_ById::get_numpage(6);
     * </code>
     * @global object $DB variable de connection à la base de données
     * @param int $id_article Id de l'a page'article sur lequel porte la demande
     * @return int Retourne le numero de la page
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function get_numcolonne($id_article) {
        global $DB;
        $req = $DB->query("SELECT colonne FROM `articles_vue` WHERE `id` =$id_article");
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->colonne;
    }
    /**
     * Function qui retourne le lien de la page, dont l'id est mis en parametre.
     * Exemple :
     * <code>
     * // Retourne le lien de la page sur laquelle se trouve l'article 6
     * echo Fonction_ById::get_lien(6);
     * </code>
     * @global object $DB variable de connection à la base de données
     * @param int $id_article Id de l'a page'article sur lequel porte la demande
     * @param string optional $langue langue sur laquelle porte la demande
     * @return int Retourne le lien de la page
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function get_lien($id_article, $langue=LANGUE) {
        global $DB;
        $req = $DB->query("SELECT lien FROM `menu_" . $langue . "` WHERE `id` =$id_article");
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->lien;
    }
    /**
     * Function qui retourne le nom de la page d'administration, dont l'id est mis en parametre.
     * Exemple :
     * <code>
     * // Retourne le nom de la page d'administration sur laquelle se trouve l'article 6
     * echo Fonction_ById::get_pageadmin(6);
     * </code>
     * @global object $DB variable de connection à la base de données
     * @param int $id_article Id de l'a page'article sur lequel porte la demande
     * @return string Retourne le nom de la page d'administration
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function get_pageadmin($id_article) {
        global $DB;
        //administration_article-(\d+)-(\d+)-(\d+)-(\d+).php$ admin_article.php?structure=$1&np=$2&col=$3&id=$4
        $req = $DB->query("SELECT structure,colonne,id_page FROM `articles` WHERE `id` =$id_article");
        $data = $req->fetch(PDO::FETCH_OBJ);
        return "administration_article-" . $data->structure . "-" . $data->id_page . "-" . $data->colonne . "-" . $id_article;
    }
    
    public static function get_structure($id_article) {
        global $DB;
        $req = $DB->query("SELECT structure FROM `articles` WHERE `id` =$id_article");
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->structure;
    }
}

?>
