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
 * @subpackage Cache
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Cache{
    /**
     * Variable contenant le cache
     * @var string $_fichier_cache variable contenant le cache 
     */
    private static $_fichier_cache='';
    
    /**
    * Fonction permettant de vérifier si une demande de regénération du cache de la page en cours
    * est demandée
    * Exemple :
    * <code>
    * <?php
        * // Retourne True si une demande de regénération du cache de la page en cours est demandée
        * echo regeneration_demandee();
    * ?>
    * </code>
    * @global object $DB Variable de connection à la base de données
    * @return bool Retourne true ou false
    * @since Version 1.0
    */
    public static function get_regenerationdemandee(){
        global $DB; 
        if (NUM_PAGE!=0){
            $sql = "SELECT * FROM `_gestion_cache` WHERE langue='".LANGUE."' and id_page=".NUM_PAGE;
            $req = $DB->query($sql);
            if($req->rowCount()!=0){ // Il y a une demande de regeneration du cache dans la base
                return true;
            }else{
                return false;
            }
        } 
    }
    /**
     * Fonction permettant de generer le fichier cache de la page en cours en version html, 
     * si il y une demande de regénération de cette dernière
     * @param string $_html contenu de la page à mettre dans le fichier cache
     * @return void
     * @since Version 1.0 
     */
    public static function save_fichier_cache($_html){
        if (self::get_regenerationdemandee() || !file_exists(self::$_fichier_cache)){
            self::fichier_cache();
            $pointeur = @fopen(self::$_fichier_cache, 'w');
            $_html = str_replace(chr(10)," ",$_html);
            $_html = str_replace(chr(13)," ",$_html);
            $_html = str_replace("\n"," ",$_html);
            $_html = str_replace("  "," ",$_html);
            @fwrite($pointeur,$_html);
            @fclose($pointeur);
            self::suppression_db(); 
        }
        echo $_html;
    }
    /**
     * Fonction permettant de supprimer l'entrée de la table après la generation du fichier cache
     * @global object $DB Variable de connection à la base de données
     * @return void
     * @since Version 1.0
     */
    public static function suppression_db(){ 
        global $DB; 
        $sql = "DELETE FROM `_gestion_cache` WHERE langue='".LANGUE."' and id_page=".NUM_PAGE;
        $res = $DB->exec($sql);  
    }
    /**
     * Fonction permettant de lire le fichier cache et de l'afficher
     * Si aucune regeneration n'est demandée et si le fichier cache existe
     * @return void
     * @since Version 1.0
     */
    public static function load_fichier_cache(){
        if(!self::get_regenerationdemandee()){
            self::fichier_cache();
            if (@file_exists(self::$_fichier_cache)) { 
                echo file_get_contents(self::$_fichier_cache);
                exit();
            }    
            
//            if (@file_exists(self::$_fichier_cache)) { 
//                @readfile(self::$_fichier_cache);
//                exit();
//            }
        }
    }
    /**
     * Fonction permettant de générer le nom du fichier cache 
     * @since Version 1.0
     * @return void
     */
    function fichier_cache(){
        $fichier = $_SERVER['REQUEST_URI'];       # on lit l'adresse de la page
        $fichier = str_replace('/', '-', $fichier);    # on tranforme l'adresse en nom de fichier
        if ($fichier == "-") {
            $fichier = "-index.html"; # si l'adresse est la racine du site, on ajoute index.html
        }
        $fichier = CACHE_PATH . "/" . LANGUE . "-cache" . $fichier;   # on construit le chemin du fichier cache de la page
        self::$_fichier_cache = $fichier.".html";
    }
    /**
    * Fonction permettant de savoir si une demande de renouvellement de cache a été émise pour la page spécifiée
    * Exemple :
    * <code>
    * <?php
        * // Retourne True si une demande de regénération du cache de la spécifiée
        * echo get_cachedemande(2, 'fr');
    * ?>
    * </code>
    * @global object $DB Variable de connection à la base de données
    * @param int $numero_page Numéro de la page sur laquelle porte la demande
    * @param string $LangueSelect langue de la page à interroger
    * @return bool Renvoie true ou false
    */
    public static function get_cachedemande($numero_page, $LangueSelect) {
        global $DB;
        $sql = "SELECT * FROM `_gestion_cache` WHERE id_page ='" . $numero_page . "' AND langue = '" . $LangueSelect . "'";
        $req = $DB->query($sql);
        if ($req->rowCount() > 0) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Fonction permettant de faire une demande une remise à jour du fichier cache
     * de toutes les pages de la langue spécifiée
    * Exemple :
    * <code>
    * <?php
        * // Demande la regénération de tous les fichiers cache. 
        * toutes_en_cache('fr');
    * ?>
    * </code>
     * @global object $DB Variable de connection à la base de données
     * @param string $LangueSelect langue de la page à interroger
     * @return void
     */
    public static function toutes_en_cache($LangueSelect) {
        global $DB;
        global $Config;
        
        $Exclude = (trim($Config->MEC_exclude)!='') ? " num_page NOT IN ($Config->MEC_exclude) AND " : '';

        $sql  = "SELECT num_page FROM `menu_" . $LangueSelect . "` where $Exclude num_page<>0 order by num_page";
        $req  = $DB->query($sql);
        while($data = $req->fetch(PDO::FETCH_OBJ)){
            $d = array($data->num_page,$LangueSelect);
            $reqI = $DB->prepare('INSERT INTO `_gestion_cache` (`id_page` ,`langue`) VALUES (?,?);');
            $reqI->execute($d);
        }
    }

    /**
     * Fonction permettant de faire une demande de mise à jour du cache
     * si elle n'ai pas deja faite. 
    * Exemple :
    * <code>
    * <?php
        * // Demande la regénération de la page numéro 2 en français. 
        * mise_en_cache(2,'fr');
    * ?>
    * </code>
     * @global string $Connect variable de connection à la base de données
     * @param int $NumeroPage Numero de page à mettre en cache
     * @param text $LangueSelect langue de la page à mettre en cache
     * @return void
     */
    public static function mise_en_cache($NumeroPage, $LangueSelect) {
        global $DB;
        // On vérifie si la page n'est pas exlue de la mise en cache
        $arrayExclue = explode(',',$Config->MEC_exclude);
        # Si il n'y a pas de demande de renouvellement de cache, on en demande une.
        if (self::get_cachedemande($NumeroPage, $LangueSelect) === false && !in_array($NumeroPage, $arrayExclue)) {
            # Demande de regenération du cache de la page en cours
            $d = array($NumeroPage,$LangueSelect);
            $req = $DB->prepare('INSERT INTO `_gestion_cache` (`id_page` ,`langue`) VALUES (?,?)');
            $req->execute($d);
        }
        
    }

}

?>
