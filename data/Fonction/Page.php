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
 * @subpackage Page
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Fonction_Page est une classe qui permet la gestion des Pages
 * ADELE : nom_dynamique
 * @author Olivier
 */
class Fonction_Page {

    /**
     * Function qui retourne le titre d'une page
     * Exemple :
     * <code>
     * // Retourne le titre de la page 5
     * echo Fonction_Page::get_titre(5);
     * </code>
     * @global object $DB Variable de connection à la base de données
     * @param int $num_page optional Numéro de la page sur laquelle porte la demande
     * @return string Retourne le titre de la page
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function get_titre($num_page = NUM_PAGE) {
        global $DB;
        $sql = "SELECT titre_page FROM `menu_" . LANGUE . "` WHERE `num_page` =" . $num_page;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->titre_page;
    }
    public static function get_ParentId($num_page = NUM_PAGE) {
        global $DB;
        $sql = "SELECT parent_id FROM `menu_" . LANGUE . "` WHERE `num_page` =" . $num_page;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->parent_id;
    }
    public static function get_ParentId_byLien($nom_page=null) {
        global $DB;
        if(is_null(nom_page)){return 0;}
        $sql = "SELECT parent_id FROM `menu_" . LANGUE . "` WHERE `lien` ='" . $nom_page."'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->parent_id;
    }
    public static function get_titreAccroche($num_page = NUM_PAGE) {
        global $DB;
        $sql = "SELECT titre_accroche_page FROM `menu_" . LANGUE . "` WHERE `num_page` =" . $num_page;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->titre_accroche_page;
    }
    
    public static function get_nbrArticleConstant($num_page,$num_colonne) {
        global $DB;
        $req = $DB->query("SELECT * FROM `articles` WHERE `id_page` =$num_page AND colonne='$num_colonne' AND `constant` =1");
        return $req->rowCount();
    }
    
    public static function get_titreById($id_page) {
        global $DB;
        $sql = "SELECT titre_page FROM `menu_" . LANGUE . "` WHERE `id` =" . $id_page;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->titre_page;
    }
    
    public static function get_titrelien($num_page = NUM_PAGE) {
        global $DB;
        $sql = "SELECT titre FROM `menu_" . LANGUE . "` WHERE `num_page` =" . $num_page;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->titre;
    }
    /**
     * Function qui retourne le lien de la page dont le numéro est mis en parametre.
     * Exemple :
     * <code>
     * // Retourne le lien de la page 5
     * echo Fonction_Page::get_lien(5);
     * </code>
     * @global object $DB Variable de connection à la base de données
     * @param int $num_page optional Numéro de la page sur laquelle porte la demande
     * @return string Retourne le lien de la page
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function get_lien($num_page = NUM_PAGE) {
        global $DB;
        $sql = "SELECT lien FROM `menu_" . LANGUE . "` WHERE `num_page` =" . $num_page;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->lien.'.html';
    }

    /**
     * Function permettant de vérifier l'existance d'une page dans la base de données
     * @global object $DB Variable de connection à la base de données
     * @param string $lien Lien de la page à vérifier
     * @param string $langue Langue de la page 
     * @return bool Retourne true ou false
     * @example exemple : echo existe("contact.php","fr")
     * @since Version 1.0
     */
    public static function existe($lien, $langue = LANGUE) {
        global $DB;
        global $Config;
        $ConfExt     = trim($Config->Extension_File);
        $Gestion_Ext = (!empty($ConfExt)) ? " AND extension_lien='".$ConfExt."'" : "" ;
        $req = $DB->query("SELECT lien FROM `menu_" . $langue . "` WHERE lien ='" . $lien . "' $Gestion_Ext");
        if ($req->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function permettant de recupérer le numéro d'une page dont le le lien est passé en paramètre
     * @global object $DB Variable de connection à la base de données
     * @param string $lien Lien de la page
     * @param string $langue Langue de la page 
     * @return bool Retourne le numéro de page ou 0 si celle-ci n'a pas été trouvée
     * @example exemple : echo numpage_by_lien("contact.php","fr")
     * @since Version 1.0
     */
    public static function numpage_by_lien($lien, $langue = LANGUE) {
        global $DB;
        $req = $DB->query("SELECT num_page FROM `menu_" . $langue . "` WHERE lien ='" . $lien . "'");
        $data = $req->fetch(PDO::FETCH_OBJ);
        if ($req->rowCount() == 1) {
            return $data->num_page;
        } else {
            return 0;
        }
    }

    /**
     * Fonction d'interrogation de la table menu_$code_langue  pour le champ spécifié.
     * @global object $DB variable de connection à la base de données
     * @param string $champ Nom du champ de la base de données
     * @return string retourne la valeur du champ
     * @example exemple : echo info("titre",6)
     * @since Version 1.0
     */
    public static function info($champ, $num_page = NUM_PAGE) {
        global $DB;
        if($num_page!=0){
            $sql = "SELECT " . $champ . " FROM `menu_" . LANGUE . "` WHERE `num_page` =$num_page";
            $req = $DB->query($sql);
            $data = $req->fetch(PDO::FETCH_OBJ);
            return $data->$champ;
        }else{
            return "";
        }
    }

    /**
     * Fonction permentant de savoir si une page est une page en savoir plus.
     * @global object $DB Variable de connection à la base de données
     * @param int $numpage Numéro de la page sur laquelle porte la demande
     * @return bool Retourne true ou false
     * @example exemple : echo ensavoirplus(6)
     * @since Version 1.0
     */
    public static function ensavoirplus($numpage) {
        global $DB;
        $req = $DB->query("SELECT ensavoirplus FROM `articles_niveau` WHERE ensavoirplus=" . $numpage);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Fonction d'interrogation de base pour savoir si une page est une page jumelle ou pas.
     * @global object $DB Variable de connection à la base de données
     * @param int $numpage Numéro de la page sur laquelle porte la demande
     * @param string $code_langue Langue de la table menu à explorer
     * @return bool Retourne true ou false
     * @example exemple : echo jumelle(6,'fr')
     * @since Version 1.0
     */
    public static function jumelle($numpage, $code_langue) {
        global $DB;
        $req = $DB->query("SELECT page_jumelle FROM `menu_" . $code_langue . "` WHERE num_page =" . $numpage);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if ($data->page_jumelle == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fonction permettant de Récuperer le prochaine numéro de page
     * @global object $DB Variable de connection à la base de données
     * @param text $langue Langue du menu 'fr' ou 'en'
     * @return int Retourne le prochaine numéro de page
     * @example exemple : echo prochain_numero_page('fr')
     * @since Version 1.0
     */
    public static function prochain_numero_page($langue, $where="") {
        global $DB;
        $req = $DB->query("SELECT max(num_page)+1 as nextpage FROM `menu_$langue` $where order by num_page DESC limit 1");
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->nextpage;
    }
    public static function get_ESP_autorise($num_page) {
        global $DB;
        global $User;
        $req = $DB->query("SELECT ensavoirplus_autorise FROM `menu_$User->langue_user` WHERE num_page=".$num_page);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->ensavoirplus_autorise;
    }

    /**
     * Fonction permettant de transforme un texte en nom de fichier 
     * @param string $NomPage texte de la page à transformer en nom de fichier
     * @return string Retourne le nom du fichier
     * @example exemple : echo nom_dynamique('nom de la nouvelle page')
     * @since Version 1.0
     * @version Version 2.0
     */
    public static function OLD_nom_dynamique($NomPage) {
        $NomPage = trim($NomPage);
        $NomPage = mb_strtolower($NomPage, 'UTF-8');
        $NomPage = preg_replace("#[][;,.°:/()<>{}|_=+*?!\~^]#", "-", $NomPage);
        $NomPage = preg_replace('#[$£µ¤]#', "-", $NomPage);
        $NomPage = strtolower(preg_replace('#[[:space:]\'"%]#', "-", $NomPage));
        $NomPage = preg_replace('#-{2,3}#', "-", $NomPage);
        $NomPage = preg_replace('#\##', "-", $NomPage);
        $normalizeChars = array(
            'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
            'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
            'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
            'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
            'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f', "'" => '-'
        );
        $NomPage = str_replace('&', '-et-', $NomPage);
        $NomPage = str_replace(' ', '-', $NomPage);
        $NomPage = str_replace('--', '-', $NomPage);
        $NomPage = strtr($NomPage, $normalizeChars);
        if (substr($NomPage, -1) == '-') { # On verifie le caractere à droite
            return substr($NomPage, 0, strlen($NomPage) - 1);
        } else {
            return $NomPage;
        }
    }
        public static function nom_dynamique($NomPage) {
            $NomPage = trim(strtolower($NomPage));
            $NomPage = str_replace('&', 'et', $NomPage);
            $NomPage = htmlentities($NomPage, ENT_NOQUOTES, 'utf-8');
            $NomPage = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $NomPage);
            $NomPage = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $NomPage); // pour les ligatures e.g. '&oelig;'
            $NomPage = preg_replace('#&[^;]+;#', '', $NomPage); // supprime les autres caractères
            $NomPage = preg_replace('/[^\w]/', '-', $NomPage);
            $NomPage = str_replace('_', '-', $NomPage);
            while (strpos($NomPage,'--')!==false) {
                $NomPage = str_replace('--', '-', $NomPage);
            }
            $NomPage = (substr($NomPage, -1) == '-') ? substr($NomPage, 0, strlen($NomPage) - 1) : $NomPage; 
            $NomPage = (substr($NomPage, 0, 1) == '-') ? substr($NomPage,1,strlen($NomPage) - 1) : $NomPage;
            $NomPage = trim(strtolower($NomPage));
            
            return $NomPage;
        }

    /**
     * Fonction permettant de Récuperer le prochain numéro d'un champ de la table menu
     * @global object $DB Variable de connection à la base de données
     * @param string $champ Nom du champ (exemple : menu, sous_menu, sous_sous_menu)
     * @param string $SLangue Langue du menu 'fr' ou 'en'
     * @param string $ChaineWhere Chaine where (optionelle)
     * @return int Retourne le prochaine numéro de menu, sous_menu, sous_sous_menu
     * @example exemple : echo prochain_numero('')
     * @since Version 1.0
     * @todo a supprimer
     */
    public static function prochain_numero($champ, $SLangue, $ChaineWhere = '') {
        global $DB;
        $req = $DB->query("SELECT max($champ)+1 as nextrec FROM `menu_$SLangue` $ChaineWhere order by $champ DESC limit 1");
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->nextrec;
    }

    public static function set_ESP($ensavoirplus, $id_article) {
        global $DB;
        global $User;
        if (($ensavoirplus == '1') && (Fonction_Article::get_pageensavoirplus($id_article) == 0)) { # On met 'EnSavoirPlus' mais il ne l'était pas avant
            # On recupere le dernier numero de page et on met +1
            $NumNouvellePage = self::prochain_numero('num_page', $User->langue_user);

            //$ensavoirplus = $NumNouvellePage;
            # On crée la nouvelle page dans la Table de Menu_'langue'

            $NomNouvellePage = Fonction_Filter::NomFichier(Fonction_Article::get_titre($id_article)) . "_r" . $NumNouvellePage;
            
            
            $d = array($NumNouvellePage, 999, $NumNouvellePage, Fonction_Article::get_titre($id_article), Fonction_Article::get_titre($id_article), $NomNouvellePage, 1, 1, 0.5, 1, 1,1);
            $sql = 'INSERT INTO `menu_' . $User->langue_user . '` (`num_page`, `num_menu` , `menu` ,`titre` ,`titre_page`,`lien` , `administrable`, `rss`, `priority` ,`propriete` ,`recherche_autorisee`,`page_ensavoirplus`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?);';
            $reqI = $DB->prepare($sql);
            $reqI->execute($d);
            
            # On récupere le numéro de page de l'article qui donne l'ordre
            $NumPageArticle = Fonction_Article::get_numpage($id_article);
            # On fait un while de toutes les structures autorisées sur la page
            $req = $DB->query("SELECT * FROM `structure_niveau` WHERE `id_page`=$NumPageArticle");
            while ($data = $req->fetch(PDO::FETCH_OBJ)) {
                # Pour cette page, il faut créer les autorisations pour utiliser certains type de structure
                $d = array($data->id_structure, $NumNouvellePage, $data->id_colonne, $data->ordre);
                $sql = 'INSERT INTO `structure_niveau` (`id_structure`, `id_page` , `id_colonne` , `ordre`) VALUES (?,?,?,?);';
                $reqI = $DB->prepare($sql);
                $reqI->execute($d);
            }
            
            
            
            


            // Demande de mise en cache de la nouvelle page EnSavoirPlus
            Fonction_Cache::mise_en_cache($NumNouvellePage, $User->langue_user);

            $d = array($NumNouvellePage, $id_article, $User->langue_user);
            $reqU = $DB->prepare('UPDATE `articles_niveau` SET ensavoirplus=? WHERE id_article=? and langue=?  LIMIT 1');
            $reqU->execute($d);
        }
        if (($ensavoirplus == '1') && (Fonction_Article::get_pageensavoirplus($id_article) != 0)) {
            
        }
        if (($ensavoirplus == '') && (Fonction_Article::get_pageensavoirplus($id_article) == 0)) {
            
        }
        #on retire le EnSavoirPlus
        if (($ensavoirplus == '') && (Fonction_Article::get_pageensavoirplus($id_article) != 0)) { 
            # Numéro de la page EnSavoirPlus
            $Num_Page_ESP = Fonction_Article::get_pageensavoirplus($id_article);
            # On met 0 pour l'article pour détacher la page ensavoirplus	
            Fonction_Article::set_ensavoirplus($id_article, 0);
            # On détruit la page en savoir plus
            self::detruire($Num_Page_ESP);
        }
    }

    
    public static function detruire($num_page) {
        global $DB;
        global $User;
        # Destruction de tous les articles de la page à supprimer
        $sql = "SELECT id FROM `articles_vue` WHERE `id_page`=$num_page AND langue='" . $User->langue_user . "'";
        $reqS = $DB->query($sql);
        while ($data = $reqS->fetch(PDO::FETCH_OBJ)) {
            Fonction_Article::detruire($data->id);
        }
        $sql = "DELETE FROM `menu_$User->langue_user` WHERE `num_page` =$num_page LIMIT 1 ;";
        $nb = $DB->exec($sql);
        $sql = "DELETE FROM `structure_niveau` WHERE `id_page` =$num_page;";
        $nb = $DB->exec($sql);
    }
    
    
    /**
     * Fonction permettant de connaitre le nombre d'article inactifs sur une page
     * @global object $DB
     * @global type $User
     * @param type $NumPage
     * @return type 
     */
    public static function ArticlesInactifs($NumPage) {
        global $DB;
        global $User;
        $sql = "SELECT id,id_page,titre,article,colonne,ensavoirplus FROM `articles_vue` WHERE `langue`='" . $User->langue_user . "' AND `id_page`=" . $NumPage . "  AND `actif` =0 AND `corbeille` =0 order by `id`";
        $reqS = $DB->query($sql);
        return $reqS->rowCount();
    }
    /**
     *
     * @global object $DB
     * @global object $User 
     */
    public static function set_propriete($num_page,$data) {
        global $DB;
        global $User;
        $reqU = $DB->prepare('UPDATE `menu_' . $User->langue_user . '` SET `titre_page` = ?,
                                                                        description_page = ?,
                                                                        mots_clefs_page  = ?,
                                                                        titre_accroche_page = ? 
                                                                        WHERE `num_page` ='.$num_page.' LIMIT 1 ;');
        $reqU->execute($data);
        Fonction_Cache::mise_en_cache($num_page, $User->langue_user);
        
    }
    public static function get_propriete($numero_page) {
        global $DB;
        global $User;
        $sql = "SELECT titre_page,description_page,mots_clefs_page,titre_accroche_page FROM `menu_" . $User->langue_user . "` where num_page=" . $numero_page;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->titre_page . "#|#" . $data->description_page . "#|#" . $data->mots_clefs_page . "#|#" . $data->titre_accroche_page . "#|#" . Fonction_Page::ensavoirplus($numero_page);
    }
    /**
     * Verifie si une structure est autorisée pour une page
     * @global object $DB
     * @param int $numero_page numéro de page
     * @param int $id_structure Numéro de la structure
     * @return boolean
     */
    public static function get_structure_page($numero_page, $id_structure, $id_colonne=2) {
        global $DB;
        $req = $DB->query("SELECT * FROM `structure_niveau` where id_structure=$id_structure AND id_page=$numero_page AND id_colonne=$id_colonne");
        if ($req->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }
    public static function set_structure_page($numero_page, $id_structure, $value, $id_colonne=2) {
        global $DB;
        if($value==0){
            $sql = "DELETE FROM `structure_niveau` WHERE `id_structure` =$id_structure AND id_page=$numero_page AND id_colonne=$id_colonne LIMIT 1 ;";
            $nb = $DB->exec($sql);
        }else{
            $d = array($id_structure, $numero_page, $id_colonne);
            $sql = 'INSERT INTO `structure_niveau` (`id_structure`, `id_page` , `id_colonne`) VALUES (?,?,?);';
            $reqI = $DB->prepare($sql);
            $reqI->execute($d);
        }
        
    }
    /**
     * 
     * @global object $DB
     * @param mixed $numero_page
     * @return type
     */
    public static function get_article_lastupdate($numero_page, $mask = "d/m/Y H:i:s") {
        global $DB;
        //$mask = (trim($mask) == '') ? "" : $mask;
        $num_page = (is_array($numero_page)) ? implode(",", $numero_page) : $numero_page ;
        $sql  = "SELECT lastupdate FROM `articles_vue` where id_page in ($num_page) AND actif=1 AND corbeille=0 AND langue='".LANGUE."' ORDER BY lastupdate DESC LIMIT 1";
        $sql = "SELECT lastupdate  FROM `articles_vue` WHERE `id_page` =$num_page AND actif=1 AND corbeille=0 AND langue='".LANGUE."' UNION SELECT datepubli FROM `articles_vue` WHERE `id_page` =$num_page AND actif=1 AND corbeille=0 AND langue='".LANGUE."' ORDER BY `lastupdate` DESC LIMIT 1";
        $req  = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        
        
        if((trim($mask) == '')){
            return (is_null($data->lastupdate)) ? null : $data->lastupdate;
        }else{
            return (is_null($data->lastupdate)) ? null : date($mask, $data->lastupdate);
        }   
    }
    public static function isUse($numero_page) {
        global $DB;
        $d = array($numero_page);
        $reqU = $DB->prepare('UPDATE `menu_fr` SET `use`=1 WHERE num_page=? LIMIT 1');
        $reqU->execute($d);
        
    }
}

