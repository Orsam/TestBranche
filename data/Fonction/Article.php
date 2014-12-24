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
 * Fonction_Article est une classe qui permet la gestion des articles
 *
 * @author Olivier
 */
class Fonction_Article {

    /**
     * Fonction qui permet la génération dynamique des fonction get, set, option
     * Si celle-ci n'existe pas.
     * Exemple :
     * <code>
     * // Recupere la valeur du champ prenom dans la table article
     * Fonction_Article::get_prenom($id_article);
     * // Affecte une valeur au champ prenom dans la table article
     * Fonction_Article::set_prenom($id_article,"Olivier");
     * // Gestion des options
     * Fonction_Article::option_add($id_article, $nom_option, $valeur_option);
     * Fonction_Article::option_find($id_article, $nom_option);
     * Fonction_Article::option_existe($id_article, $nom_option);
     * Fonction_Article::option_update($id_article, $nom_option, $valeur_option);
     * Fonction_Article::option_delete($id_article, $nom_option);
     * </code>
     * @param string $name Nom de la fonction non trouvée
     * @param array $args Paramètres passés à la fonction
     * @return mixed Renvoi une valeur ou un boolean
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function __callStatic($name, $args) {
        // Gestion d'Alias
        switch ($name) {
            case 'get_numstructure': $name = 'get_structure';
                break;
            case 'get_numcolonne'  : $name = 'get_colonne';
                break;
        }
        
        $data = new stdClass();
        $data->table = 'articles_vue';
        $data->id    = 'id_articles';
        $data->class = __CLASS__;
        return Model_Autoclass::dispatcher($name, $args, $data);
    }
    
    /**
     * Fonction permentant de renumeroter l'ordre des articles d'une page
     * lors de la création, archivage, suppression d'un nouvel article.
     * Exemple :
     * <code>
     * // Renumérote les articles de la colonne 2 de la page 1 en francais
     * Fonction_Article::renumerote_articles(1, 2, 'fr');
     * </code>
     * @global object $DB Variable de connection à la base de données
     * @global object $User Variable Utilisateur de la session actuelle
     * @param int $numpage Numéro de la page sur laquelle porte la demande
     * @param int $numColonne Colonne concernée
     * @param string $langue optional langue concernée
     * @return void
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function renumerote_articles($numpage, $numColonne, $langue = 'fr') {
        global $DB;
        global $User;
        if ($langue == '') {
            $langue = $User->langue_user;
        }
        $sql = "SELECT id,ordre FROM `articles_vue` WHERE `langue`='" . $langue . "' AND `id_page` = " . $numpage . " AND `colonne` = " . $numColonne . " AND `corbeille` = 0 ORDER BY `ordre`";
//        echo "$sql<br>";
        $req = $DB->query($sql);
        $Nouveau_compteur = 0;
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            echo "$data->id => $data->ordre<br>";
            $Nouveau_compteur++;
            $d = array($Nouveau_compteur, $data->id);
//            echo "UPDATE `articles` SET ordre=$Nouveau_compteur WHERE id=$data->id LIMIT 1<br>";
            $reqU = $DB->prepare('UPDATE `articles` SET ordre=? WHERE id=? LIMIT 1');
            $reqU->execute($d);
        }
    }

    /**
     * Fonction permettant d'activer ou de désactiver un article à l'affichage
     * Exemple :
     * <code>
     * // Active/Désactive l'article 345
     * echo Fonction_Article::actif_inactif(345);
     * </code>
     * @global object $DB Variable de connection à la base de données
     * @global object $User Variable Utilisateur de la session actuelle
     * @param int $id_article Id de l'article à Activer/Désactiver
     * @return int Retourne l'état de l'article 0=Désactivé / 1=Activé
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function actif_inactif($id_article) {
        if (!self::get_actif($id_article)) {
            self::set_actif($id_article, true);
            //Fonction_Search::add($id_article);
            return 1;
        } else {
            self::set_actif($id_article, false);
            //Fonction_Search::delete($id_article);
            return 0;
        }
    }

    /**
     * Fonction permettant de savoir si un article à une page 'en savoir plus'
     * Exemple :
     * <code>
     * // Renvoi le numéro de la page 'en savoir plus' de l'article 345
     * echo Fonction_Article::get_pageensavoirplus(345);
     * </code>
     * @global object $DB Variable de connection à la base de données
     * @param int $id_article optional Numéro d'article concerné
     * @return bool retourne 0 ou le numéro de la page "en savoir plus"
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function get_pageensavoirplus($id_article = null) {
        global $DB;
        if (is_null($id_article)) {
            return '0';
        }
        $sql = "SELECT ensavoirplus FROM `articles_niveau` WHERE id_article=$id_article";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->ensavoirplus != 0 ? $data->ensavoirplus : 0;
    }

    /**
     * Fonction permettant de détruire un article avec la possibilité de supprimer ou d'archiver 
     * la page 'En savoir plus' lié à cet article
     * @global object $DB Variable de connection à la base de données
     * @global object $User Variable Utilisateur de la session actuelle
     * @param int $id_article Id de l'article à supprimer
     * @param bool $archiveESP optional 
     * @return void
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function detruire($id_article, $archiveESP = 0) {
        global $DB;
        global $User;
        Fonction_Fichier::detruit_photos_byIdArticle($id_article);
        Fonction_Logue::trace_log("Administration",'Destruction de l\'article "' . Fonction_Article::get_titre($id_article) . '"');
        //Fonction_Fichier::detruit_documents_byIdArticle($id_article);
        $sql = "DELETE FROM `articles` WHERE `id` =" . $id_article . " LIMIT 1 ;";
        $nb = $DB->exec($sql);

        if ($archiveESP == "0") { // Article qui est attaché à une page "en savoir plus"
            // Suppression des pages En savoir Plus Lié à l'article supprimé
            if (self::get_pageensavoirplus($id_article) != 0) {
                # Suppression de toutes les photos liés à chaque article de la page à supprimer
                $sql = "SELECT id FROM `articles_vue` WHERE `id_page`=" . self::get_pageensavoirplus($id_article) . " AND langue='" . $User->langue_user . "'";
                $reqS = $DB->query($sql);
                while ($data = $reqS->fetch(PDO::FETCH_OBJ)) {
                    Fonction_Fichier::detruit_photos_byIdArticle($data->id);
                }
                # On detruit la page Ensavoir Plus
                $sql = "DELETE FROM `menu_" . $User->langue_user . "` WHERE `num_page` = " . self::get_pageensavoirplus($id_article) . " LIMIT 1;";
                $nb = $DB->exec($sql);
                $sql = "DELETE FROM `structure_niveau` WHERE `id_page` = " . self::get_pageensavoirplus($id_article);
                $nb = $DB->exec($sql);
                $sql = "DELETE FROM `articles` WHERE `id_page` = " . self::get_pageensavoirplus($id_article);
                $nb = $DB->exec($sql);
                Fonction_Logue::trace_log("Administration",'Destruction de la page ' . self::get_pageensavoirplus($id_article) . ' liée à l\'article "' . self::get_titre($id_article)) . '"';
            }
        } else { // On archive la page
            $d = array(self::get_pageensavoirplus($id_article));
            $reqU = $DB->prepare("UPDATE `menu_" . $User->langue_user . "` SET page_archivee=1 where num_page=?");
            $reqU->execute($d);
        }
    }

    /**
     * Fonction qui permet de restaurer un article
     * Exemple :
     * <code>
     * // Restauration de l'article 345
     * Fonction_Article::restaurer(345);
     * </code>
     * @global object $DB Connection à la base de données
     * @global object $User Variable Utilisateur de la session actuelle
     * @param int $id_article Id de l'article sur lequelle porte la demande
     * @return void
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function restaurer($id_article) {
        global $DB;
        global $User;

        $num_page = Fonction_ById::get_numpage($id_article);
        $num_colonne = Fonction_ById::get_numcolonne($id_article);
        $Ordre = Fonction_Page::get_nbrArticleConstant($num_page,$num_colonne)+0.5;

        $d = array(0, '', $Ordre, $id_article);
        $reqU = $DB->prepare("UPDATE `articles` SET `corbeille` = ?,`user_corbeille` = ?, `ordre` = ? WHERE `id` =? LIMIT 1 ;");
        $reqU->execute($d);
        Fonction_Logue::trace_log("Administration",'Restauration de l\'article "' . self::get_titre($id_article)) . '"';

        // Recuperation du numero de la page pour renumerotation
        //$num_page = Fonction_ById::get_numpage($id_article);

        Fonction_Article::renumerote_articles($num_page, $num_colonne);
        Fonction_Cache::mise_en_cache($num_page, $User->langue_user);
    }

    /**
     * Fonction qui retourne le nombre d'article de la page passée en paramètre
     * Exemple :
     * <code>
     * // Retourne le nombre d'article de la page 4
     * echo Fonction_Article::get_nbrarticle(4);
     * </code>
     * @global object $DB Connection à la base de données
     * @param int $num_page Numero de la page sur lequelle porte la demande
     * @param boolean $actif optional demande le nombre d'article actif ou non
     * @return int Retourne le nombre d'article
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function get_nbrarticle($num_page, $actif = null) {
        
        global $DB;
        if (is_null($actif)) {
            $Param_actif = '';
        } else {
            $Param_actif = ($actif) ? " AND Actif=1" : " AND Actif=0";
        }
        $sql = "SELECT * FROM `articles_vue` where id_page=$num_page $Param_actif AND corbeille=0 AND langue='" . LANGUE . "'";
        $req = $DB->query($sql);
        return $req->rowCount();
    }

    /**
     * Fonction qui retourne le numéro de page de l'article dont l'id est passé en paramètre
     * Exemple :
     * <code>
     * // Recuperation du numéro de page de l'article 345
     * echo Fonction_Article::get_numpage(345);
     * </code>
     * @global object $DB Connection à la base de données
     * @param int $id_article Id de l'article sur lequelle porte la demande
     * @return string Retourne le numéro de page de l'article
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function get_numpage($id_article) {
        global $DB;
        $sql = "SELECT id_page FROM `articles`  WHERE `id` =$id_article LIMIT 1 ;";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->id_page;
    }

    /**
     * Fonction qui permet d'activer tous les articles d'une page
     * Exemple :
     * <code>
     * // Active tous les articles de la page 5
     * Fonction_Article::ActiveAll(5);
     * </code>
     * @global object $DB Connection à la base de données
     * @global object $User Variable Utilisateur de la session actuelle
     * @param int $NumPage Numéro de page sur lequelle porte la demande
     * @return void
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function ActiveAll($NumPage) {
        global $DB;
        global $User;
        $sql = "SELECT id FROM `articles_vue` WHERE `langue`='" . $User->langue_user . "' AND `id_page`=" . $NumPage . "  AND `actif` =0 AND `corbeille` =0 order by `id`";
        $reqS = $DB->query($sql);
        while ($data = $reqS->fetch(PDO::FETCH_OBJ)) {
            $d = array($data->id);
            $reqU = $DB->prepare("UPDATE `articles` SET actif=1 WHERE `id`=?");
            $reqU->execute($d);
        }
        Fonction_Logue::trace_log("Administration",'activation de tous les articles de la page "' . Fonction_Page::get_titrelien($NumPage) . '"');
        Fonction_Cache::mise_en_cache($NumPage, $User->langue_user);
    }

    // Démmenage des articles d'une page vers une autre
    public static function Change_Page($page_source,$page_destination) {
        global $DB;
        $d = array($page_destination, $page_source);
        $reqU = $DB->prepare("UPDATE `articles` SET id_page=? WHERE `id_page`=?");
        $reqU->execute($d);
    }
    
    
    /**
     * Fonction qui permet désactiver tous les articles d'une page
     * Exemple :
     * <code>
     * // Active tous les articles de la page 5
     * Fonction_Article::DesactiveAll(5);
     * </code>
     * @global object $DB Connection à la base de données
     * @global object $User Variable Utilisateur de la session actuelle
     * @param int $NumPage Numéro de page sur lequelle porte la demande
     * @return void
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function DesactiveAll($NumPage) {
        global $DB;
        global $User;
        $sql = "SELECT id FROM `articles_vue` WHERE `langue`='" . $User->langue_user . "' AND `id_page`=" . $NumPage . "  AND `actif` =1 AND `corbeille` =0 order by `id`";
        $reqS = $DB->query($sql);
        while ($data = $reqS->fetch(PDO::FETCH_OBJ)) {
            $d = array($data->id);
            $reqU = $DB->prepare("UPDATE `articles` SET actif=0 WHERE `id`=?");
            $reqU->execute($d);
        }
        Fonction_Logue::trace_log("Administration",'déactivation de tous les articles de la page "' . Fonction_Page::get_titrelien($NumPage) . '"');
        Fonction_Cache::mise_en_cache($NumPage, $User->langue_user);
    }
    public static function Duplique_Article($id_article_a_duplique,$page_destination, $data_array) {
        global $DB;
        global $User;
        //$d = array($np, $col, $Ordre, 0, $structure, time(), $lien, $article_newsletter);
        $data_array[0] = $page_destination;
        $data_array[1] = 2;
        $data_array[2] = Fonction_Page::get_nbrArticleConstant($page_destination,2)+0.5;
        
        $sql = 'INSERT INTO `articles` (`id_page`, `colonne` , `ordre` , `corbeille` , `structure`, `datepubli`, `lien` ,`article_newsletter`) VALUES (?,?,?,?,?,?,?,?);';
        $reqI = $DB->prepare($sql);
        $reqI->execute($data_array);
        $last = $DB->lastInsertId('id');
        return $last;
    }
    /**
     * Fonction qui permet de mettre à jour la date de dernière modification de l'article
     * Exemple :
     * <code>
     * // Met à jour la date de dernière modification l'article 345
     * Fonction_Article::set_lastupdate(345);
     * </code>
     * @global object $DB Connection à la base de données
     * @param int $id_article Id de l'article sur lequelle porte la demande
     * @return void
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function set_lastupdate($id_article) {
        global $DB;
        $d = array(time(), $id_article);
        $reqU = $DB->prepare("UPDATE `articles` SET `lastupdate` = ? WHERE `id` =? LIMIT 1 ;");
        $reqU->execute($d);
    }

    public static function set_ensavoirplus($id_article, $value) {
        global $DB;
        $d = array($value, $id_article);
        $reqU = $DB->prepare("UPDATE `articles_niveau` SET `ensavoirplus` = ? WHERE `id_article` =? LIMIT 1 ;");
        $reqU->execute($d);
    }

    /**
     * Fonction qui permet de récuperer la date de dernière modification de l'article
     * Exemple :
     * <code>
     * // Récupere la date de dernière modification l'article 345
     * Fonction_Article::get_lastupdate(345);
     * </code>
     * @global object $DB Connection à la base de données
     * @param int $id_article Id de l'article sur lequelle porte la demande
     * @param string $mask optional masque de sortie de la date. Par defaut "d/m/Y H:i:s"
     * @return string Retourne la date de dernière modification
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function get_lastupdate($id_article, $mask = "d/m/Y H:i:s") {
        global $DB;
        $mask = (trim($mask) == '') ? "d/m/Y H:i:s" : $mask;
        $sql = "SELECT lastupdate FROM `articles_vue` WHERE `id`=$id_article";
        $reqS = $DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);
        return date($mask, $data->datepubli);
    }

    /**
     * Fonction qui permet de récuperer la date de création de l'article
     * Exemple :
     * <code>
     * // Récupere la date de création l'article 345
     * Fonction_Article::get_datepublication(345);
     * </code>
     * @global object $DB Connection à la base de données
     * @param int $id_article Id de l'article sur lequelle porte la demande
     * @param string $mask optional masque de sortie de la date. Par defaut "d/m/Y H:i:s"
     * @return string Retourne la date de dernière modification
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function get_datepublication($id_article, $mask = "") {
        global $DB;
        $mask = (trim($mask) == '') ? "d/m/Y H:i:s" : $mask;
        $sql = "SELECT datepubli FROM `articles_vue` WHERE `id`=$id_article";
        $reqS = $DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);
        return date($mask, $data->datepubli);
    }

    /**
     * Fonction permentant de récuperer le nombre de photo d'un article
     * Exemple :
     * <code>
     * // Récuperation du nombre de photo de l'article
     * echo Fonction_Article::get_nombrephoto(1);
     * </code>
     * @global object $DB Variable de connection à la base de données
     * @param int $id_article Id de l'article sur lequel porte la demande
     * @return int Retourne le nombre de photo de cet article
     * @since Version 3.0
     * @version Version 1.0
     */
    public static function get_nombrephoto($id_article) {
        global $DB;
        $sql = "SELECT id FROM `images_vue_new` WHERE id_article=$id_article";
        $req = $DB->query($sql);
        return $req->rowCount();
    }

    /**
     * Fonction permentant de vérifier si l'article est libre pour la suppression
     * Exemple :
     * <code>
     * // Verifie si l'article peut-etre supprimé
     * echo Fonction_Article::get_libre(1);
     * </code>
     * @global object $DB Connection à la base de données
     * @global object $User Variable Utilisateur de la session actuelle
     * @param int $id_article Id de l'article sur lequel porte la demande
     * @return boolean Retourne 0 ou 1
     * @since Version 3.0
     * @version Version 1.0
     */
    public static function get_libre($SearchPage) {
        global $DB;
        global $User;
        $sql = "SELECT * FROM `sessions` WHERE login <> '" . $User->login . "'";
        $reqS = $DB->query($sql);
        while ($data = $reqS->fetch(PDO::FETCH_OBJ)) {
            $ObjSession = json_decode($data->session_data);
            if($ObjSession->page==$SearchPage){
                return false;
            }
        }
        return true;
    }

    /**
     * Fonction permentant de mettre un article à la corbeille
     * Exemple :
     * <code>
     * // Met l'article 6 à la corbeille
     * echo Fonction_Article::set_corbeille(6);
     * </code>
     * @global object $DB Connection à la base de données
     * @global object $User Variable Utilisateur de la session actuelle
     * @param int $id_article Id de l'article sur lequel porte la demande
     * @return void
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function set_corbeille($id_article) {
        global $DB;
        global $User;
        $d = array($User->login, $id_article);
        $reqU = $DB->prepare("UPDATE `articles` SET `corbeille` = 1,`actif` = 0,`ordre` = 0,`user_corbeille` = ? WHERE  `id` =? LIMIT 1 ;");
        $reqU->execute($d);

        Fonction_Logue::trace_log("Administration",'Mise en corbeille de l\'article "' . Fonction_Article::get_titre($id_article) . '"');

        $num_page = Fonction_ById::get_numpage($id_article);
        $num_colonne = Fonction_ById::get_numcolonne($id_article);

        // Renumerotation des articles
        Fonction_Article::renumerote_articles($num_page, $num_colonne);

        # Demande de regenération du cache de la page en cours
        Fonction_Cache::mise_en_cache($num_page, $User->langue_user);
    }

    /**
     * Raccourci de set_prix 
     * @param type $id_article
     * @param type $prix_ht
     * @param int $code_tva
     */
    public static function set_prix_ht($id_article, $code_article, $prix_ht, $code_tva=1) {
        Fonction_Article::set_prix($id_article, $code_article, $prix_ttc="", $prix_ht, $code_tva);
    }
    /**
     * Raccourci de set_prix 
     * @param type $id_article
     * @param type $prix_ht
     * @param int $code_tva
     */
    public static function set_prix_ttc($id_article, $code_article, $prix_ttc, $code_tva=1) {
        Fonction_Article::set_prix($id_article, $code_article, $prix_ttc, $prix_ht="", $code_tva);
    }
    
    
    public static function set_prix($id_article, $code_article=1, $prix_ttc="", $prix_ht="", $code_tva=1) {
        global $DBoutique;
        
        $sql  = "SELECT taux FROM `tva` WHERE id_taux = $code_tva";
        $req  = $DBoutique->query($sql);
        $tva  = $req->fetch(PDO::FETCH_OBJ);
        
        if(empty($prix_ht) && !empty($prix_ttc)){
            $taux     = ($tva->taux/100)+1;
            $prix_ht  = $prix_ttc / $taux;
            $prix_tva = $prix_ht * ($taux-1);
        }
        if(!empty($prix_ht) && empty($prix_ttc)){
            $taux      = $tva->taux;
            $prix_tva  = $prix_ht * $tva->taux / 100;
            $prix_ttc  = $prix_ht + $prix_tva;
        }
        $sql = "SELECT id FROM `articles_prix` WHERE id_article = $id_article AND code_article=$code_article";
        $req = $DBoutique->query($sql);
        if ($req->rowCount() == 0) {
            $d = array($id_article, $code_article, $prix_ht, $prix_tva, $prix_ttc, $code_tva);
            $reqI = $DBoutique->prepare('INSERT INTO `articles_prix` (`id_article` ,`code_article` ,`prix_ht`,`prix_tva`,`prix_ttc`,`code_tva`) VALUES (?,?,?,?,?,?);');
            $reqI->execute($d);
        }else{
            $d = array($prix_ht, $prix_tva, $prix_ttc, $code_tva,$id_article,$code_article);
            $reqU = $DBoutique->prepare("UPDATE `articles_prix` SET `prix_ht` = ?, `prix_tva` = ?, `prix_ttc` = ?, `code_tva` = ? WHERE `id_article` =? AND `code_article` = ? LIMIT 1 ;");
            $reqU->execute($d);
        }
    }
    public static function get_prix($id_article, $code_article=1) {
        global $DBoutique;
        $sql = "SELECT * FROM `articles_prix` WHERE id_article = $id_article AND code_article=$code_article";
        $req = $DBoutique->query($sql);
        return $req->fetch(PDO::FETCH_OBJ);
    }
}
