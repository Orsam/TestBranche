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
 * @subpackage Db
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 5.0
 */
class Fonction_Db {

    /**
     * Function permettant de récuperer le nombre d'enregistrements dans la table et le Where spécifié
     * @global text $Connect variable de connection à la base de données
     * @param text $NomTable Nom de la table à consulter
     * @param text $Where Condition à affecter à la requete
     * @return int retourne le nombre d'enregistrement de la table
     */
    public static function nombre_ligne($NomTable, $Where = "") {
        global $DB;
        $sql = "SELECT * FROM `" . $NomTable . "` " . $Where;
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $req->rowCount();
    }

    public static function donnee_existe($NomDuChamp, $NomTable, $donnee_a_trouver) {
        global $DB;
        $sql = "SELECT " . $NomDuChamp . " FROM `" . $NomTable . "` WHERE " . $NomDuChamp . "='" . $donnee_a_trouver . "'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if ($req->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function permettant d'épurer les tables de la base de données
     * @global string $Connect
     * @param string $table Nom de la table à épurer
     * @param string $champ_date non du champ DateTime
     * @param int $duree Temps en jour 
     * : Seconde = 1, Minute = 60, Heure=3 600, Jour=86 400, Semaine=604 800 ,Mois= 2 419 200, Année=29 030 400
     */
    public static function epuration($table, $champ_date, $dureeEnJour = 0, $exprime_en = 'jour') {
        global $DB;

        switch (strtolower($exprime_en)) {
            case 'seconde':
                $duree = $dureeEnJour * 1;
                break;
            case 'minute':
                $duree = $dureeEnJour * 60;
                break;
            case 'heure':
                $duree = $dureeEnJour * 3600;
                break;
            case 'jour':
                $duree = $dureeEnJour * 86400;
                break;
            case 'semaine':
                $duree = $dureeEnJour * 604800;
                break;
            case 'mois':
                $duree = $dureeEnJour * 2419200;
                break;
            case 'annee':
                $duree = $dureeEnJour * 29030400;
                break;
        }

        $sql = "DELETE FROM `" . $table . "` WHERE `" . $champ_date . "` < '" . (date("Y-n-j H:i:s", time() - $duree)) . "'";
        echo "$sql<br>";
        $req = $DB->exec($sql);
    }

    /*
     *
     */

    public static function epuration_newsletter($duree) {
        global $DB;
        $sql = "DELETE FROM `newsletter_inscrits` WHERE `inscription_le` < '" . (date("Y-n-j H:i:s", time() - $duree)) . "' and valide=0";
        $req = $DB->exec($sql);
    }
    /**
     * Fonction de connexion aux bases de données
     * Exemple :
     * <code>
     * $DB  = Fonction_Db::connexion();
     * $DB2 = Fonction_Db::connexion(2);
     * </code>
     * @global object $Config Objet Config 
     * @param int $numero_DB numéro de la base de données
     * @return object Retourne un object DB
     * @since Version 1.0
     * @version Version 2.0
     */
    public static function connexion($numero_DB='1') {
        global $Config;
        $b = "basename".$numero_DB;
        $NomBase = $Config->$b;
        try {
            $DB = new PDO('mysql:host=' . $Config->host . ';dbname=' . $NomBase, $Config->user, $Config->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $DB;
        } catch (PDOException $e) {
            echo 'erreur de connection ';
        }
    }

//    public static function connexion_boutique() {
//        global $Config;
//        try {
//            $DBoutique = new PDO('mysql:host=' . $Config->host . ';dbname=' . $Config->basename_boutique, $Config->user, $Config->password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
//            $DBoutique->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//            return $DBoutique;
//        } catch (PDOException $e) {
//            echo 'erreur de connection ';
//        }
//    }

    /**
     * Fonction de récuperartion de la liste des champs d'une table
     * @param text $nom_table nom de la table
     * @return array retourne un objet contenant la liste des champs, le type, etc
     * @version 1.0
     */
    public static function liste_champs($Nom_Table) {
        global $DB;
        $Nom_Champ = array();
        $sql = "SHOW COLUMNS FROM `$Nom_Table`";
        $req = $DB->query($sql);
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $Nom_Champ[] = $data->Field;
        }
        return $Nom_Champ;
    }
    /**
     * Fonction de récuperartion de la liste des vues de la base de données
     * @global object $DB Connection de la base de données en cours
     * @global object $Config Objet de configuration du site
     * @param text $DataBaseName nom de la base de données si elle est differente de celle en cours
     * @return array retourn la liste des vues de la base de données
     */
    public static function liste_vues($DataBaseName='') {
        global $DB;
        global $Config;
        $Nom_Champ = array();
        if(empty($DataBaseName)){$DataBaseName = $Config->basename;}
        $sql = "SELECT table_name AS vues FROM INFORMATION_SCHEMA.VIEWS WHERE table_schema = '$DataBaseName'";
        $req = $DB->query($sql);
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $Nom_Champ[] = $data->vues;
        }
        return $Nom_Champ;
    }
    /**
     * Fonction de récuperartion de la liste des tables de la base de données
     * @global object $DB Connection de la base de données en cours
     * @global object $Config Objet de configuration du site
     * @param text $DataBaseName nom de la base de données si elle est differente de celle en cours
     * @return array retourn la liste des tables de la base de données
     */
    public static function liste_tables($DataBaseName='') {
        global $DB;
        global $Config;
        $Nom_Champ = array();
        if(empty($DataBaseName)){$DataBaseName = $Config->basename;}
        $sql = "SELECT table_name AS tables FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = '$DataBaseName'";
        $req = $DB->query($sql);
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $Nom_Champ[] = $data->tables;
        }
        return $Nom_Champ;
    }
    /**
     * Fonction permettant de recuperer la taille d'un champ VARCHAR dans la base de données
     * Exemple :
     * <code>
     * echo Fonction_Db::len_champ(Nom_De_La_Table, Nom_Du_Champ);
     * </code>
     * @global object $DB Objet de connection à la base de données
     * @global object $Config Objet Config 
     * @param string $TableName Nom de la table dans laquelle se trouve le champ
     * @param string $NomChamp Nom du champ dont-on veux récupérer la taille
     * @return mixed Retourne la longueur du champs si elle est trouvée autrement, retourne false
     * @since Version 5.0
     * @version Version 1.0
     */
    public static function len_champ($TableName,$NomChamp) {
        global $DB;
        global $Config;
        $sql = "SELECT CHARACTER_MAXIMUM_LENGTH AS longueur FROM INFORMATION_SCHEMA.COLUMNS WHERE `TABLE_SCHEMA` = '$Config->basename1' AND `TABLE_NAME` = '$TableName' AND `DATA_TYPE` = 'varchar' AND COLUMN_NAME = '$NomChamp'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return (is_null($data->longueur) || empty($data->longueur)) ? false : $data->longueur;
    }
    
    
    
}

?>
