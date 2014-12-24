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
 * @subpackage Photo
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description of Photo
 *
 * @author Olivier
 */
class Fonction_Photo {

    
    
    
    public static function photo_existe($id_article, $Numero_image) {
        global $DB;
        $sql = "SELECT * FROM `images_vue_new` WHERE id_article=$id_article AND num_photo=$Numero_image AND LENGTH(photo)>0";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if($req->rowCount()==1){
            return true;
        }else{
            return false;
        }

    }
    public static function get_photo($id_photo) {
        global $DB;
        $sql = "SELECT photo FROM `images_liste` WHERE id=$id_photo";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->photo;
    }
    public static function get_classe($id_structure, $Numero_image) {
        global $DB;
        $sql = "SELECT classe FROM `images_niveau` WHERE `id_structure`=$id_structure and id_image=$Numero_image";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->classe;
    }
    
    
    public static function get_id($id_article, $Numero_image) {
        global $DB;
        $sql = "SELECT id FROM `images_liste` WHERE `id_article`=$id_article and num_photo=$Numero_image";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->id;
    }

    public static function get_height($id_article, $Numero_image) {
        global $DB;
        $sql = "SELECT height FROM `images_liste` WHERE `id_article`=$id_article and num_photo=$Numero_image";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->height;
    }

    public static function get_width($id_article, $Numero_image) {
        global $DB;
        $sql = "SELECT width FROM `images_liste` WHERE `id_article`=$id_article and num_photo=$Numero_image";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->width;
    }

    public static function get_nomphoto($id_article, $Numero_image) {
        global $DB;
        $sql = "SELECT nom_photo FROM `images_survol` WHERE `id_article`=$id_article and num_image=$Numero_image";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->nom_photo;
    }

    public static function get_nombrephoto($Nom_photo) {
        global $DB;
        $sql = "SELECT * FROM `images_survol` WHERE `nom_photo`='" . $Nom_photo . "'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $req->rowCount();
    }

    public static function retailler_image($fichier_Source, $fichier_Destination, $max_width, $max_height) {
        // Retaille Photo version 3
        //Fonction_Debug::trace("$fichier_Source $fichier_Destination", $max_width, $max_height);
        $ext = strtolower(strrchr($fichier_Source, '.'));
        if ($ext == '.jpg' || $ext == '.jpeg') {
            header('Content-type: image/jpeg');
        } elseif ($ext == '.gif') {
            header('Content-type: image/gif');
        } elseif ($ext == '.png') {
            header('Content-type: image/png');
        }
        // Cacul des nouvelles dimensions
        list($width_orig, $height_orig) = getimagesize($fichier_Source);

        $ratio_orig = $width_orig / $height_orig;
        if ($max_width / $max_height > $ratio_orig) {
            $max_width = $max_height * $ratio_orig;
        } else {
            $max_height = $max_width / $ratio_orig;
        }

        $image_p = imagecreatetruecolor($max_width, $max_height);

        if ($ext == '.jpg' || $ext == '.jpeg') {// Redimensionnement JPG
            $image = imagecreatefromjpeg($fichier_Source);
        } elseif ($ext == '.png') {        // Redimensionnement PNG
            $image = imagecreatefrompng($fichier_Source);
        } elseif ($ext == '.gif') {  // Redimensionnement GIF
            $image = imagecreatefromgif($fichier_Source);
        }
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $max_width, $max_height, $width_orig, $height_orig);

        // Affichage Qualité MAX
        if ($ext == '.jpg' || $ext == '.jpeg') {
            imagejpeg($image_p, $fichier_Destination, 100);
        } elseif ($ext == '.png') {
            imagepng($image_p, $fichier_Destination, 9);
        } elseif ($ext == '.gif') {
            imagegif($image_p, $fichier_Destination);
        }

        imagedestroy($image_p); // Libère toute la mémoire associée à l'image image
        imagedestroy($image); // Libère toute la mémoire associée à l'image image
        return array('width' => $max_width, 'height' => $max_height);
    }

    public static function set_nomphoto($id_article, $Numero_image, $NouveauNomPhoto) {
        global $DB;
        global $User;

        // Recuperation de l'ancien nom
        $AncienNom = self::get_nomphoto($id_article, $Numero_image);
        $ExtAncienNom = strtolower(strrchr($AncienNom, '.'));

        if (trim($NouveauNomPhoto) == '') {
            if (Fonction_Article::get_titre($id_article) != '') {
                $NouveauNomPhoto = Fonction_Article::get_titre($id_article);
            } else {
                $NouveauNomPhoto = NumeroClient(7);
            }
        }
        // Codage du nouveau nom
        $NouveauNom = Fonction_Filter::NomFichier($NouveauNomPhoto) . "-" . NumeroClientInt(7) . $ExtAncienNom;
        $d = array($NouveauNom, $id_article, $Numero_image);
        $reqU = $DB->prepare("UPDATE `images_survol` SET nom_photo=? WHERE id_article=? and num_image=? LIMIT 1");
        rename(MEDIA_PATH . '/photos/grandes/' . $AncienNom, MEDIA_PATH . '/photos/grandes/' . $NouveauNom);
        $reqU->execute($d);

        # Demande de regenération du cache de la page en cours
        $num_page = Fonction_ById::get_numpage($id_article);
        Fonction_Cache::mise_en_cache($num_page, $User->langue_user);

        return $NouveauNom;
    }

    public static function get_survol($id_article, $Numero_image) {
        global $DB;
        $sql = "SELECT survol FROM `images_vue_new` WHERE `id_article`=$id_article and num_photo=$Numero_image";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->survol;
    }

    public static function set_survol($id_article, $Numero_image, $Survol) {
        global $DB;
        global $User;
        $d = array($Survol, $id_article, $Numero_image);
        $reqU = $DB->prepare("UPDATE `images_survol` SET survol=? WHERE id_article=? and num_image=? LIMIT 1");
        $reqU->execute($d);

        # Demande de regenération du cache de la page en cours
        $num_page = Fonction_ById::get_numpage($id_article);
        Fonction_Cache::mise_en_cache($num_page, $User->langue_user);
    }

    public static function get_legende($id_article, $Numero_image) {
        $data = Model_ImagesSurvol::find(array("champs" => "legende", "where" => "id_article = $id_article AND `num_image`=$Numero_image"));
        return $data->legende;
    }

    public static function set_legende($id_article, $Numero_image, $legende) {
        global $DB;
        $d = array($legende, $id_article, $Numero_image);
        $reqU = $DB->prepare("UPDATE `images_survol` SET legende=? WHERE id_article=? and num_image=? LIMIT 1");
        $reqU->execute($d);
    }

    public static function get_geolocation($id_article, $Numero_image) {
        $data = Model_ImagesSurvol::find(array("champs" => "geolocation", "where" => "id_article = $id_article AND `num_image`=$Numero_image"));
        return $data->geolocation;
    }

    public static function set_geolocation($id_article, $Numero_image, $geolocation) {
        global $DB;
        $d = array($geolocation, $id_article, $Numero_image);
        $reqU = $DB->prepare("UPDATE `images_survol` SET geolocation=? WHERE id_article=? and num_image=? LIMIT 1");
        $reqU->execute($d);
    }

    public static function delete($id_article, $Numero_image) {
        global $DB;
        global $User;
//        $sql = "DELETE FROM `images_liste` WHERE `id_article`=$id_article and num_photo=$Numero_image";
//        $DB->exec($sql);

        $d = array(0,0,0,0, null,"",$id_article, $Numero_image);
        $reqU = $DB->prepare("UPDATE `images_liste` SET width=?, width_min=?,height=?, height_min=?, photo=?, ext=? WHERE id_article=? and num_photo=? LIMIT 1");
        $reqU->execute($d);
        
        $d = array("","","","","",$id_article, $Numero_image);
        $reqU = $DB->prepare("UPDATE `images_survol` SET nom_photo=?, ext=?,survol=?, legende=?, geolocation=? WHERE id_article=? and num_image=? LIMIT 1");
        $reqU->execute($d);
        
        # Demande de regenération du cache de la page en cours
        $num_page = Fonction_ById::get_numpage($id_article);
        //Fonction_Cache::mise_en_cache($num_page, $User->langue_user);
    }

    // En Attente
    public static function CreationLigneSurvolPhotosDB($id_article, $id_structure, $Numero_de_page, $num_colonne, $langue_user,$titre="", $article="") {
        global $DB;
        global $User;

        $langues_site = explode('-', $Config->langue);
        if (Fonction_Page::jumelle($Numero_de_page, $langue_user)) {
            foreach ($langues_site as $value) {
                if (strtolower($value) != $User->langue_user) {
                    $d = array($id_article, "copie de $titre", "copie de $article", $value);
                    $reqI = $DB->prepare('INSERT INTO `articles_niveau` (`id_article` , `titre` , `article` , `langue`) VALUES (?,?,?,?);');
                    $reqI->execute($d);
                }
                for ($Element = 1; $Element < Fonction_Structure::NbrPhoto($id_structure) + 1; $Element++) {
                    $d = array($id_article, $Element, 0, 0, 0, 0);
                    $reqI = $DB->prepare('INSERT INTO `images_liste` (`id_article`,`num_photo` ,`width` ,`height` ,`width_min` ,`height_min`) VALUES (?,?,?,?,?,?);');
                    $reqI->execute($d);
                    $id_LastPhoto = $DB->lastInsertId('id');

                    $d = array($id_article, $id_LastPhoto, $Element, strtolower($value), '');
                    $reqI = $DB->prepare('INSERT INTO `images_survol` (`id_article` ,`id_photo` ,`num_image` ,`langue` ,`survol`) VALUES (?,?,?,?);');
                    $reqI->execute($d);
                }
            }
        } else {
            // Ce n'est pas une page jumelle
            for ($Element = 1; $Element < Fonction_Structure::NbrPhoto($id_structure) + 1; $Element++) {
                $d = array($id_article, $Element,  0, 0, 0, 0);
                $reqI = $DB->prepare('INSERT INTO `images_liste` (`id_article`,`num_photo`,`width` ,`height` ,`width_min` ,`height_min`) VALUES (?,?,?,?,?,?);');
                $reqI->execute($d);
                $id_LastPhoto = $DB->lastInsertId('id');

                $d = array($id_article, $id_LastPhoto, $Element, $langue_user, '');
                $reqI = $DB->prepare('INSERT INTO `images_survol` (`id_article` ,`id_photo` ,`num_image` ,`langue` ,`survol`) VALUES (?,?,?,?,?);');
                $reqI->execute($d);
            }
        }
    }

}

?>
