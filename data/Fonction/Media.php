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
 * @subpackage Media
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Media {
    /**
     * Fonction permettant de récuperer le nom du fichier media
     * @global object $DB Connection à la base de données
     * @param int $id_article Numéro d'article auquelle le media est attaché
     * @param int $numero_media numéro du media attaché à l'article 
     * @return string Retourne le nom du fichier media ou vide si il n'existe pas
     */
    public static function nom_fichier($id_article,$numero_media=1) {
        global $DB;
        $sql = "SELECT nom,extension FROM `medias_vue` WHERE id_article=" . $id_article." AND numero=$numero_media";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if ($req->rowCount() == 0) {
            return "";
        } else {
            return $data->nom . '.' . $data->extension;
        }
    }
    public static function nom_fichier_ByMedia($id_media) {
        global $DB;
        $sql = "SELECT nom,extension FROM `medias` WHERE id=$id_media";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if ($req->rowCount() == 0) {
            return "";
        } else {
            return $data->nom;
        }
    }
    
    
    
    
    /**
     * Fonction permettant de savoir si un fichier media existe dans la base de données
     * @global object $DB Connection à la base de données
     * @param string $Nomfichier Nom du fichier media
     * @param string $Extension Extension du fichier media
     * @return boolean Retourne True ou False
     */
    public static function fichier_existe($Nomfichier,$Extension){
        global $DB;
        $req = $DB->query("SELECT id_media FROM `medias_vue` WHERE nom='$Nomfichier' AND extension='$Extension'");
        if($req->rowCount()==1){
            return true;
        }else{
            return false;
        } 
    }
    /**
     * Fonction permettant de savoir si un media existe dans la base de données
     * @global object $DB Connection à la base de données
     * @param int $id_article Numéro d'article auquelle le media est attaché
     * @param int $numero_media numéro du media attaché à l'article 
     * @return boolean Retourne True ou False
     */
    public static function existe($id_article,$numero_media=1){
        global $DB;
        $req = $DB->query("SELECT id_media FROM `medias_vue` WHERE id_article=$id_article AND numero=$numero_media");
        if($req->rowCount()==1){
            return true;
        }else{
            return false;
        } 
    }
    /**
     * Fonction permettant de récupérer l'id du media
     * @global object $DB Connection à la base de données
     * @param int $id_article Numéro d'article auquelle le media est attaché
     * @param int $numero_media numéro du media attaché à l'article 
     * @return int Retourne l'id du media ou 0 si il n'existe pas
     */
    public static function id_media($id_article,$numero_media=1){
        global $DB;
        $req = $DB->query("SELECT id_media FROM `medias_vue` WHERE id_article=$id_article AND numero=$numero_media");
        $data = $req->fetch(PDO::FETCH_OBJ);
        if($req->rowCount()==1){
            return $data->id_media;
        }else{
            return 0;
        } 
    }
    /**
     * Fonction permettant de renommer un media à l'aide de son ID
     * @global object $DB Connection à la base de données
     * @param string $nom_fichier Nom du fichier media
     * @param string $extension_fichier Extension du fichier media
     * @param int $id_media Id du media dans la base de données
     */
    public static function nouveau_nom($nom_fichier,$extension_fichier,$id_media){
        global $DB;
        $d = array($nom_fichier, $extension_fichier, $id_media);
        $reqU = $DB->prepare("UPDATE `medias` SET `nom` = ?,`extension` = ? WHERE `id` =? LIMIT 1 ;");
        $reqU->execute($d);
    }
    /**
     * Fonction permettant la création d'un media dans la base de données
     * @global object $DB Connection à la base de données
     * @param int $id_article Numéro d'article auquelle le media est attaché
     * @param string $nom_fichier Nom du fichier media
     * @param string $extension_fichier Extension du fichier media
     * @param int $numero_media numéro du media attaché à l'article 
     */
    public static function creation($id_article,$nom_fichier,$extension_fichier,$numero_media=1){
        global $DB;
        
        $d = array($nom_fichier, $extension_fichier);
        $sql = 'INSERT INTO `medias` (`nom`, `extension`) VALUES (?,?);';
        $reqI = $DB->prepare($sql);
        $reqI->execute($d);
        $id_media = $DB->lastInsertId('id');

        $d = array($id_article, $id_media,$numero_media);
        $sql = 'INSERT INTO `medias_niveau` (`id_article`, `id_media`, `numero`) VALUES (?,?,?);';
        $reqI = $DB->prepare($sql);
        $reqI->execute($d);
    }

    
}

?>
