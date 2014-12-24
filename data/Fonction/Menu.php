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
class Fonction_Menu {

    function configuration() {
        $data = new stdClass();
        $data->_imgDrop = '<img src="/images_site/icone/b_drop.png" title="supprimer"/>';
        $data->_imgDropGris = '<img src="/images_site/icone/b_drop_gris.png" title="supprimer"/>';
        $data->_imgOpen = '<img src="/images_site/icone/s_desc.png" title="ouvrir/fermer">';
        $data->_imgAjout = '<img src="/images_site/icone/b_newtbl.png" title="ajouter"/>';
        $data->_champInput = '<input name="{name}" type="text" class="champ-titre" value="{value}" />';
        return $data;
    }

    public static function lecture_menu_old($parent_id = 3, $langue = 'fr') {
//        global $DB;
//        $Niveau = 0;
//        $sql = "select * from menu_fr where parent_id = 0";
//        $req = $DB->query($sql);
//        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
//            $Niveau = 1;
//            self::getChild($data->id);
//        }
    }
    
    public static function getIdParent($num_page){
        global $DB;
        $sql  = "select parent_id from menu_fr where num_page = $num_page";
        $req  = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->parent_id;
    }

    
    public static function getNbrChild($parent_id){
        global $DB;
        $sql = "select * from menu_fr where parent_id = $parent_id";
        $req = $DB->query($sql);
        return $req->rowCount();
    }
    
    public static function lecture_menu($idParent = 0) {
        global $DB;
        $CM = self::configuration();
        $Niveau = 1;
        $sql = "select * from menu_fr where parent_id = $idParent order by ordre";
        $req = $DB->query($sql);
        echo "<ul id='ul_$idParent'>\n";
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $nbArt = Fonction_Article::get_nbrarticle($data->num_page)==0; 
            $SpanDev = ($_SERVER['REMOTE_ADDR']==IP_OLR) ? "<span>$data->num_page</span><span>$data->lien</span>" : "";
            $ImgSupp = (self::getNbrChild($data->id)!= 0 || $nbArt != 0) ? $CM->_imgDropGris : '<img src="/images_site/icone/b_drop.png" id="del_'.$data->num_page.'" title="supprimer"/>';
            $ImgOpen = (self::getNbrChild($data->id)!= 0) ? $CM->_imgOpen : '';
            
            echo '<li><span>'.$ImgSupp . $ImgOpen.'</span>'.$SpanDev.'<span class="S2"><input name="page_'.$data->num_page.'" type="text" class="champ-titre"  value="' . $data->titre . '" /><img src="../../images_site/icone/b_newtbl.png" id="add_'.$data->id.'" title="ajouter"/></span>' . "\n";
            if (self::getNbrChild($data->id)!=0){
                self::getChild($data->id);              
            }
            echo "</li>\n";          
        }
        echo "</ul>\n";
    }

    function getChild($idParent) {
        global $DB;
        $CM = self::configuration();
        $sql = "select * from menu_fr where parent_id = $idParent order by ordre";
        $req = $DB->query($sql);
        $Niveau = 2;
        echo "<ul id='ul_$idParent'>\n";
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $SpanDev = ($_SERVER['REMOTE_ADDR']==IP_OLR) ? "<span style='color:black'>$data->num_page</span><span style='color:black'>$data->lien</span>" : "";
            $BoutonSup = (Fonction_Article::get_nbrarticle($data->num_page)==0) ? '<img src="/images_site/icone/b_drop.png" id="del_'.$data->num_page.'" title="supprimer"/>' : $CM->_imgDropGris ; 
            echo '<li><span>'.$BoutonSup.'</span>'.$SpanDev.'<span class="S2"><input name="page_'.$data->num_page.'" type="text" class="champ-titre"  value="' . $data->titre . '" /><img src="../../images_site/icone/b_newtbl.png" id="add_'.$data->id.'" title="ajouter"/></span>' . "\n";
            if (self::getNbrChild($data->id)!=0){
                self::getChildS($data->id);              
            }
            echo "</li>\n";          
        }
        echo "</ul>\n";
    }

    function getChildS($idParent) {
        global $DB;
        $CM = self::configuration();
        $sql = "select * from menu_fr where parent_id = $idParent order by ordre";
        $req = $DB->query($sql);
        $Niveau = 3;

        echo "<ul id='ul_$idParent'>\n";
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $SpanDev = ($_SERVER['REMOTE_ADDR']==IP_OLR) ? "<span style='color:black'>$data->num_page</span><span style='color:black'>$data->lien</span>" : "";
            $BoutonSup = (Fonction_Article::get_nbrarticle($data->num_page)==0) ? '<img src="/images_site/icone/b_drop.png" id="del_'.$data->num_page.'" title="supprimer"/>' : $CM->_imgDropGris ; 
            echo '<li><span>'.$BoutonSup.'</span>'.$SpanDev.'<span class="S2"><input name="page_'.$data->num_page.'" type="text" class="champ-titre"  value="' . $data->titre . '" /></span></li>' . "\n";
        }
        echo "</ul>\n";
   }

}
