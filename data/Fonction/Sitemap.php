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
 * @subpackage Sitemap
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de Sitemap
 *
 * @author Olivier
 */
class Fonction_Sitemap {
    public static function photo($id_article){
        global $DB;
        global $Config;
        $sql = "SELECT nom_photo,num_photo FROM `images_vue_new` WHERE `id_article`=$id_article ORDER BY `num_photo`";
        $req = $DB->query($sql);
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $r .= "\t\t<image:image>\r";
            $r .= "\t\t\t<image:loc>http://www.$Config->adresse_site/media/photos/$data->nom_photo</image:loc>\r";
            $Survol      = Fonction_Photo::get_survol($id_article, $data->num_photo);
            $Legende     = Fonction_Photo::get_legende($id_article, $data->num_photo);
            $Geolocation = Fonction_Photo::get_geolocation($id_article, $data->num_photo);
            if(!empty($Survol)){
                $r .= "\t\t\t<image:title>$Survol</image:title>\r";
            }
            if(!empty($Legende)){
                $r .= "\t\t\t<image:caption>$Legende</image:caption>\r";
            }
            if(!empty($Geolocation)){
                $r .= "\t\t\t<image:geo_location>$Geolocation</image:geo_location>\r";
            }
            $r .= "\t\t</image:image>\r";
        }
        return $r;
        
    }
    public static function article($numero_page,$langue='fr') {
        global $DB;
        $sql = "SELECT id FROM `articles_vue` WHERE `id_page`=$numero_page AND langue='$langue' AND corbeille=0 AND actif=1 ORDER BY `ordre`";
        $req = $DB->query($sql);
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $r .= self::photo($data->id); 
        }
        return $r;
    }
    public static function page($langue='fr') {
        global $DB;
        global $Config;
        $sql = "SELECT lien,num_page,priority FROM `menu_$langue` WHERE `sitemap`=1 ORDER BY `num_page`";
        $req = $DB->query($sql);
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $r .= "\t<url>\r";
            $r .= "\t\t<loc>http://www.$Config->adresse_site/$data->lien</loc>\r";
            $r .= "\t\t<priority>$data->priority</priority>\r";
            $r .= self::article($data->num_page); 
            $r .= "\t</url>\r";
        }
        return self::debut().$r.self::fin();
    }
    public static function debut(){
        $t = "<?xml version='1.0' encoding='UTF-8'?>";
        return $t."<urlset xmlns='http://www.google.com/schemas/sitemap/0.9' xmlns:image=\"http://www.google.com/schemas/sitemap-image/1.1\">";
    }
    public static function fin() {
        return "</urlset>";
        
    }
}

?>
