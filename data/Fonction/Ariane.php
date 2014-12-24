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
 * @package    Administration
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Ariane {

    public static function lecture() {
//        $_html = '';
//        $_html .= '<ul class="ariane">';
//        $_html .= '<li><a href="index">Accueil</a></li>';
//        if (NUM_PAGE != 1) {
//            $Tab = array();
//            $Tab[] = NUM_PAGE;
//            $Tab[] = self::NumPageById(self::MyParent(NUM_PAGE));
//            $reversed = array_reverse($Tab);
//            foreach ($reversed as $value) {
//                $_html .= '<li><a href="'.Fonction_Page::get_lien($value).'">' . Fonction_Page::get_titre($value) . "</li>";
//            }
//            
//            $_html .= '</ul>';
//        }
//        echo $_html;
//                        <ul class="ariane">
//                    <li><a href="menu-tresorier.php">Menu</a></li> 
//                    <li>Menu organisateur carrefour</li>                   
//                </ul>

    }

    function MyParent($num_page) {
        global $DB;
        $sql = "select parent_id from menu_" . LANGUE . " where num_page = $num_page";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->parent_id;
    }

    function NumPageById($id) {
        global $DB;
        $sql = "select num_page from menu_" . LANGUE . " where id = $id";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->num_page;
    }

}
