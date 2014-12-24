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
 * @subpackage Fichier
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Fichier {

    /**
     * Fonction permttant de supprimer les photos 
     * Exemple :
     * <code>
     * <?php
     * // Détruira la photo ainsi que la miniature
     * detruit_photos('photo1.jpg');
     * ?>
     * </code>
     * @param string $fichier Nom du fichier à supprimer
     * @return void
     * @since Version 2.0
     * @todo Tracer la suppression
     */
    public static function detruit_photos($fichier) {
        if ($fichier != '') {
            $chemin = MEDIA_PATH . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . 'grandes' . DIRECTORY_SEPARATOR . $fichier;
            echo $chemin;
            if (file_exists($chemin) == 1) {
                @unlink($chemin);
            }
        }
    }

    public static function type_fichier($extension) {
        switch ($extension) {
            case 'doc' :
            case 'docx': $icone = "doc";
                break;
            case 'ppt' :
            case 'pps' :
            case 'pptx': $icone = "ppt";
                break;
            case 'pdf': $icone = "pdf";
                break;
            default : $icone = "vide";
                break;
        }
        return $icone;
    }

    /**
     * Fonction permettant de supprimer les photos d'un article dont l'id est passé en paramètre
     * Exemple :
     * <code>
     * <?php
     * // détruira toutes les photos de l'article dont l'id est 3
     * detruit_photo_byIdArticle(3);
     * ?>
     * </code>
     * @global object $DB Variable de connection à la base de données
     * @param int $id_article Id de l'article dont on veut supprimer les images 
     * @return void
     * @since Version 1.0
     * @todo Vérifier si l'id n'est pas vide
     * @todo Tracer la suppression
     */
    public static function detruit_photos_byIdArticle($id_article) {
        global $DB;
        $sql = "SELECT nom_photo FROM `images_vue_new` WHERE id_article=$id_article";
        $reqS = $DB->query($sql);
        while ($data = $reqS->fetch(PDO::FETCH_OBJ)) {
            if (trim($data->nom_photo != "")) {
                self::detruit_photos($data->nom_photo);
            }
        }
    }

    /**
     * Fonction permettant de supprimer les photos d'un article dont l'id est passé en paramètre
     * @global object $DB Variable de connection à la base de données
     * @param int $id_article Id de l'article dont on veut supprimer les images 
     * @return void
     * @example exemple : detruit_documents_byIdArticle(3) détruira tous les document de l'article dont l'id est 3
     * @since Version 1.0
     * @todo Vérifier si l'id n'est pas vide
     * @todo Tracer la supression
     * @todo Vérifier si la suppression de media niveau est nécessaire (suppression en cascade)
     */
    public static function detruit_documents_byIdArticle($id_article) {
        global $DB;
        $sql = "SELECT * FROM `medias_vue` WHERE id_article=" . $id_article;
        $reqS = $DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);
        if ($reqS->rowCount() != 0) {
            self::detruit_fichier(ROOT_PATH . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . $data->nom . '.' . $data->extension);
            $sql = "DELETE FROM `medias` WHERE `id` =" . $data->id_media . " LIMIT 1 ;";
            $nb = $DB->exec($sql);
            $sql = "DELETE FROM `medias_niveau` WHERE `id_article` =" . $id_article . " LIMIT 1 ;";
            $nb = $DB->exec($sql);
        }
        Fonction_Logue::trace_log("Administration", "Destruction du fichier " . $data->nom . '.' . $data->extension . " de l'article " . $id_article);
    }

    /**
     * Fonction permettant de rennomer une photo ainsi que la miniture
     * @param string $Source Nom du fichier source
     * @param string $Destination Nom du fichier de destination
     * @return void
     * @example exemple : renome_photos('AncienNom.jpg','NouveauNom.jpg') 
     * @since Version 1.0
     * @todo Vérifier si la source et la destination ne sont pas vide
     * @todo Tracer le rennomage
     * @todo Faute d'orthographe sur le nom de la fonction "rennomer"
     */
    public static function renome_photos($Source, $Destination) {
        $cheminSource = ROOT_PATH . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . 'grandes' . DIRECTORY_SEPARATOR . $Source;
        $cheminDestination = ROOT_PATH . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . 'grandes' . DIRECTORY_SEPARATOR . $Destination;
        if (file_exists($cheminSource) == 1) {
            rename($cheminSource, $cheminDestination);
        }
    }

    /**
     * Fonction permettant de supprimer un fichier
     * @param string $fichier Nom du fichier à détruire
     * @return void
     * @example exemple : detruit_fichier('NomDuFichier.jpg') 
     * @since Version 1.0
     * @todo Vérifier si la variable fichier n'est pas vide
     * @todo Tracer la suppression
     */
    public static function detruit_fichier($fichier) {
        if (file_exists($fichier) == 1) {
            unlink($fichier);
        }
    }

    public static function force_download($file) {
        $file = Cryptage::decrypte($file, true);
        $chemin = MEDIA_PATH . DIRECTORY_SEPARATOR . 'Contents' . DIRECTORY_SEPARATOR . 'Videos' . DIRECTORY_SEPARATOR;
        if (($file != "") && (file_exists($chemin . basename($file)))) {
            $size = filesize($chemin . basename($file));
            header("Content-Type: application/force-download; name=\"" . basename($file) . "\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: $size");
            header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
            header("Expires: 0");
            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            readfile($chemin . basename($file));
            exit();
        }
    }

    public static function force_download_lien($file) {
        return 'download.php?file=' . Cryptage::crypte($file);
    }

}

?>
