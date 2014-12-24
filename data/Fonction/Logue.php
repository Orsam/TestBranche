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
 * @subpackage Logue
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Logue {
    public static function trace_log($section,$message,$erreur=0){
        global $DB;
        global $User;
        //$message = print_r($User, true);
        if(empty($User)){
            $User = Fonction_Session::read(session_id());
        }
        $user_name = "$User->nom $User->prenom ($User->titre)";
        try {
            $d = array($User->login,$User->id_user,$user_name,$section, left(Fonction_Cryptage::crypte(session_id()),20),ucfirst($message),$User->langue_user,time(),$_SERVER["REMOTE_ADDR"],getenv("HTTP_USER_AGENT"),$erreur);
            $reqI = $DB->prepare('INSERT INTO `trace_log` (`user` ,`id_user`,`user_name`,`section`,`id_session` , `action`, `version` , `quand`, `ip`, `navigateur`,`erreur`) VALUES (?,?,?,?,?,?,?,?,?,?,?);');
            $reqI->execute($d);
        } catch (Exception $exc) {
            $d = array($message,$exc->getTraceAsString());
            $reqI = $DB->prepare('INSERT INTO `trace_log_error` (`message` ,`error`) VALUES (?,?);');
            $reqI->execute($d);
        }
    }
    public static function trace_log_nonConnect($login,$id_user,$section,$message,$erreur=1){
        global $DB;
        $user_name = Fonction_User::NomPrenomById($id_user);
        try {
            $d = array($login,$id_user,$user_name,$section,ucfirst($message),"fr",time(),$_SERVER["REMOTE_ADDR"],getenv("HTTP_USER_AGENT"),$erreur);
            $reqI = $DB->prepare('INSERT INTO `trace_log` (`user` ,`id_user`,`user_name`,`section`, `action`, `version` , `quand`, `ip`, `navigateur`,`erreur`) VALUES (?,?,?,?,?,?,?,?,?,?);');
            $reqI->execute($d);
            
        } catch (Exception $exc) {
            $d = array($message,$exc->getTraceAsString());
            $reqI = $DB->prepare('INSERT INTO `trace_log_error` (`message` ,`error`) VALUES (?,?);');
            $reqI->execute($d);
        }
    }
    /**
     * Retourne le nombre de visiteurs sur le site
     * @return int 0 
     */
    public static function nombre_visiteurs() {
        global $DB;
        $sql = "SELECT * FROM `sessions`";
//        $sql = "SELECT * FROM `trace_visites`";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $req->rowCount();
    }
    /**
     * Retourne le nombre de visiteurs sur le site
     * @return int 0 
     */
    public static function trace_visiteurs() {
        global $DB;
        global $Config;

            
        $expire     = strtotime('+1 hours'); //calcul de l'expiration de la session
        $expire_txt = date("d/m/Y H:i:s",strtotime('+1 hours'));
        
        $sql  = "DELETE FROM `trace_visites` WHERE `expire`<".time().";";
        $nb   = $DB->exec($sql);
        
        $sql  = "SELECT id FROM `trace_visites` WHERE `ip`='".$_SERVER['REMOTE_ADDR']."' AND source='".$_SERVER['HTTP_USER_AGENT']."'";
        $reqS = $DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);
        if ($reqS->rowCount() == 0) {
            
            $d = array($_SERVER['REMOTE_ADDR'],date("d/m/Y H:i:s"),$expire,$expire_txt,$_SERVER['HTTP_USER_AGENT']);
            $reqI = $DB->prepare('INSERT INTO `trace_visites` (`ip`  ,`arrive` ,`expire` ,`expire_txt` ,`source`) VALUES (?,?,?,?,?);');
            $reqI->execute($d);
            
        }else{    
            $d = array(date("d/m/Y H:i:s"), $expire,$expire_txt, $_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT']);
            $reqU = $DB->prepare("UPDATE `trace_visites` SET `arrive` = ?,`expire` = ?,`expire_txt` = ? WHERE `ip` =? AND source=? LIMIT 1 ;");
            $reqU->execute($d);
        } 
    }
    
    public static function trace_Master($class,$fonction,$message="") {
        global $DB;
        if($_SERVER['REMOTE_ADDR']===IP_OLR){
            $d = array($class,$fonction,$message);
            $reqI = $DB->prepare('INSERT INTO `trace_logMaster` (`class`,`fonction`,`message`) VALUES (?,?,?);');
            $reqI->execute($d);
        }
    }
    
} 