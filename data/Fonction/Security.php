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
 * @subpackage Security
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Security{
    public static function intrusion() {
        global $DB;
        $sql  = "SELECT * FROM `_liste_noire` WHERE adresse_ip = '".$_SERVER['REMOTE_ADDR']."'";
        $req  = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if($req->rowCount()==0){
            $d = array($_SERVER['REMOTE_ADDR'],1);
            $reqI = $DB->prepare('INSERT INTO `_liste_noire` (`adresse_ip` ,`num_erreur`) VALUES (?,?);');
            $reqI->execute($d);

        }else{
            $NombreErreur = $data->num_erreur;
            $d = array($NombreErreur+1,$_SERVER['REMOTE_ADDR']);
            $reqU = $DB->prepare('UPDATE `_liste_noire` SET num_erreur=? WHERE adresse_ip=?');
            $reqU->execute($d);
        }
        $d = array($_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_REFERER'].' '.$_SERVER['PHP_SELF'],date("Y-n-j H:i:s"));
        $reqI = $DB->prepare('INSERT INTO `_liste_noire_log` (`adresse_ip` ,`referer` ,`quand`) VALUES (?,?,?);');
        $reqI->execute($d);
    }
    
    public static function in_listenoir() {
        global $DB;
        $sql  = "SELECT * FROM `_liste_noire` WHERE adresse_ip = '".$_SERVER['REMOTE_ADDR']."'";
        $req  = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if($req->rowCount()==1){
            if ($data->num_erreur>3){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public static function set_listenoire() {
        global $DB;
        $sql  = "SELECT * FROM `_liste_noire` WHERE adresse_ip = '".$_SERVER['REMOTE_ADDR']."'";
        $req  = $DB->query($sql);
        if($req->rowCount()==0){
            $d = array($_SERVER['REMOTE_ADDR'],4);
            $reqI = $DB->prepare('INSERT INTO `_liste_noire` (`adresse_ip` ,`num_erreur`) VALUES (?,?);');
            $reqI->execute($d);

        }else{
            $d = array(4,$_SERVER['REMOTE_ADDR']);
            $reqU = $DB->prepare('UPDATE `_liste_noire` SET num_erreur=? WHERE adresse_ip=?');
            $reqU->execute($d);
        }
        $d = array($_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_REFERER'].' '.$_SERVER['PHP_SELF'],date("Y-n-j H:i:s"));
        $reqI = $DB->prepare('INSERT INTO `_liste_noire_log` (`adresse_ip` ,`referer` ,`quand`) VALUES (?,?,?);');
        $reqI->execute($d);
        
    }    
    
    
    
    
//    public static function verification_ip() {
//        if (isset($_SESSION[$field])) {
//            return $_SESSION[$field];
//        } else {
//            return false;
//        }
//    }
    
}


?>
