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
 * Description de Menu
 *
 * @author Olivier
 */
class Fonction_Menu_old {

    function configuration() {
        $data = new stdClass();
        $data->_imgDrop     = '<img src="/images_site/icone/b_drop.png" title="supprimer"/>';
        $data->_imgDropGris = '<img src="/images_site/icone/b_drop_gris.png" title="supprimer"/>';
        $data->_imgOpen     = '<img src="/images_site/icone/s_desc.png" title="ouvrir/fermer">';
        $data->_imgAjout    = '<img src="/images_site/icone/b_newtbl.png" title="ajouter"/>';
        $data->_champInput  = '<input name="{name}" type="text" class="champ-titre" value="{value}" />';
        return $data;
    }

    function createLi(){
        
    }

    function champInput($name, $value) {
        $CM = self::configuration();
        $v = str_replace("{name}", $name, $CM->_champInput);
        return str_replace("{value}", $value, $v);
    }

    public static function Nbr_SousMenu($Numero_Menu, $Menu, $Langue = 'fr') {
        global $DB;
        $sql = "SELECT * FROM `menu_$Langue` WHERE menu =" . $Menu . " AND num_menu=" . $Numero_Menu . " AND sous_menu<>0";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $req->rowCount();
    }

    public static function Nbr_SousSousMenu($Numero_Menu, $Menu, $SousMenu, $Langue = 'fr') {
        global $DB;
        $sql = "SELECT * FROM `menu_$Langue` WHERE menu =" . $Menu . " AND num_menu=" . $Numero_Menu . " AND sous_menu=" . $SousMenu . " AND sous_sous_menu<>0";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $req->rowCount();
    }

    public static function Nbr_Articles_Page($num_page) {
        global $DB;
        $sql = "SELECT id FROM `articles` WHERE id_page =$num_page";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $req->rowCount();
    }

    public static function lecture_menu($Numero_Menu = 3, $langue = 'fr') {
        global $DB;
        $CM = self::configuration();
        $chaine = "";
        $sql = "SELECT * FROM `menu_$langue` WHERE sous_menu=0 AND sous_sous_menu=0 ORDER BY `num_menu` , `menu` , `sous_menu` , `sous_sous_menu`";
        $req = $DB->query($sql);
        echo "\n";
        $chaine .= "<ul id=\"{nomULNiv1}\">\n";
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $nbArt = Fonction_Article::get_nbrarticle($data->num_page);

            $chaine = str_replace('{nomULNiv1}', "menu_$data->menu", $chaine);
            $ImgSupp = (self::Nbr_SousMenu($Numero_Menu, $data->menu) != 0 || $nbArt != 0) ? $CM->_imgDropGris : '<img src="/images_site/icone/b_drop.png" id="droppage_'.$data->num_page.'" title="supprimer"/>';
            $ImgOpen = (self::Nbr_SousMenu($Numero_Menu, $data->menu) != 0) ? $CM->_imgOpen : '';

            if (self::Nbr_SousMenu($Numero_Menu, $data->menu) != 0) {
                $chaine .= '<li><span>' . $ImgSupp . $ImgOpen . '</span><span class="S2"><input name="page_'.$data->num_page.'" type="text" class="champ-titre"  value="'. $data->titre . '" /><img src="/images_site/icone/b_newtbl.png"  id="addMenu_'.$data->menu.'" title="ajouter"/></span>' . "\n";
                $chaine .= self::lecture_Smenu($Numero_Menu, $data->menu);
                $chaine .= "</li>\n";
            } else {
                $chaine .= '<li><span>' . $ImgSupp . '</span><span class="S2"><input name="page_'.$data->num_page.'" type="text" class="champ-titre"  value="' .  $data->titre . '" /><img src="/images_site/icone/b_newtbl.png" id="addMenu_'.$data->menu.'" title="ajouter"/></span></li>' . "\n";
            }
        }
        $chaine .= "</ul>\n";
        return $chaine . "\n";
    }

    public static function lecture_Smenu($Numero_Menu, $Menu, $langue = 'fr') {
        global $DB;
        $CM = self::configuration();
        $chaine = "";
        $sql = "SELECT * FROM `menu_$langue` WHERE num_menu=$Numero_Menu AND menu=$Menu AND sous_menu<>0 AND sous_sous_menu=0 ORDER BY `num_menu` , `menu` , `sous_menu` , `sous_sous_menu`";
        $req = $DB->query($sql);
        if ($req->rowCount() != 0) {
            $chaine .= "<ul id=\"ul_{Ulsmenu}\">\n";
            while ($data = $req->fetch(PDO::FETCH_OBJ)) {
                $nbArt = Fonction_Article::get_nbrarticle($data->num_page);

                $idUl = "oc_$data->menu"."_$data->sous_menu";
                $ImgSupp = (self::Nbr_SousSousMenu($Numero_Menu, $Menu, $data->sous_menu) != 0 || $nbArt != 0) ? $CM->_imgDropGris : '<img src="/images_site/icone/b_drop.png" id="droppage_'.$data->num_page.'" title="supprimer"/>';
                $ImgOpen = (self::Nbr_SousSousMenu($Numero_Menu, $Menu, $data->sous_menu) != 0 || $nbArt != 0) ? '<img src="/images_site/icone/s_desc.png" id="' . $idUl . '" title="ouvrir/fermer">' : '';
                $chaine .= '<li><span>' . $ImgSupp . $ImgOpen . '</span><span class="S2"><input name="page_'.$data->num_page.'" type="text" class="champ-titre"  value="'. $data->titre . '" /><img src="/images_site/icone/b_newtbl.png" id="add_' . $data->menu.'_'.$data->sous_menu . '" title="ajouter"/></span>';
                $ChaineUl = $data->menu;

                if (self::Nbr_SousSousMenu($Numero_Menu, $Menu, $data->sous_menu) != 0) {
                    $chaine .= "\n";
                    $chaine .= self::lecture_SSmenu($Numero_Menu, $Menu, $data->sous_menu);
                }
                $chaine .= "</li>\n";
            }
            $chaine = str_replace('{Ulsmenu}', $ChaineUl, $chaine);
            $chaine .= "</ul>\n";
        }
        return $chaine;
    }

    public static function lecture_SSmenu($Numero_Menu, $Menu, $SousMenu, $langue = 'fr') {
        global $DB;
        $CM = self::configuration();
        $chaine = "";
        $sql = "SELECT * FROM `menu_$langue` WHERE num_menu =$Numero_Menu AND menu=$Menu AND sous_menu=$SousMenu AND sous_sous_menu<>0 ORDER BY `num_menu` , `menu` , `sous_menu` , `sous_sous_menu`";
        $req = $DB->query($sql);
        if ($req->rowCount() != 0) {
            $chaine .= "\n";
            $chaine .= "<ul id=\"ul_{Ulssmenu}\">\n";
            while ($data  = $req->fetch(PDO::FETCH_OBJ)) {
                $ImgSupp  = (Fonction_Article::get_nbrarticle($data->num_page) == 0) ? '<img src="/images_site/icone/b_drop.png" id="dropssm_'.$data->num_page.'" title="supprimer"/>' : $CM->_imgDropGris;
                $nbArt    = Fonction_Article::get_nbrarticle($data->num_page);
                $ChaineUl = $data->menu.'_'.$data->sous_menu;
                $chaine .= '<li><span>' . $ImgSupp . '</span><span class="S2"><input name="page_'.$data->num_page.'" type="text" class="champ-titre"  value="' . $data->titre . '" /></span></li>' . "\n";
            }
            $chaine .= "</ul>\n";
            $chaine = str_replace('{Ulssmenu}', $ChaineUl, $chaine);
        }
        return $chaine;
    }

}
