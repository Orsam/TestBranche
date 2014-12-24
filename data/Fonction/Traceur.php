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
 * @subpackage Traceur
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Traceur {
    
    public static function libre($num_page){
        global $DB;
        global $User;
        $sql = "SELECT user FROM `trace_traceur` WHERE num_page=$num_page AND `id_user`<>".$User->id_user;
        $reqS = $DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);
        if ($reqS->rowCount() > 0) {
            return $data->user;
        }else{
            return null;
        }
    }
    // Qui est en train d'administrer cette page
    public static function Who_Admin($num_page){
        global $DB;
        $sql = "SELECT user FROM `trace_traceur` WHERE num_page=$num_page";
        $reqS = $DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);
        if ($reqS->rowCount() > 0) {
            return $data->user;
        }else{
            return null;
        }
    }
    
    
    public static function trace($num_page=null){
        global $DB;
        global $User;
        global $Config;
        if ($num_page===null){
            $sql  = "DELETE FROM `trace_traceur` WHERE `expire`<".time().";";
            $nb   = $DB->exec($sql);
            return false;
        }

        $expire     = strtotime('+'.$Config->deconnection_automatique.' minutes'); //calcul de l'expiration de la session
        $expire_txt = date("d/m/Y H:i:s",strtotime('+'.$Config->deconnection_automatique.' minutes'));
        
        $sql  = "SELECT user FROM `trace_traceur` WHERE `id_user`=".$User->id_user;
        $reqS = $DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);
        if ($reqS->rowCount() == 0) {
            $d = array($User->login,$User->id_user,$num_page,NOM_PAGE, $expire,$expire_txt);
            $reqI = $DB->prepare('INSERT INTO `trace_traceur` (`user` ,`id_user`, `num_page` , `nom_page` , `expire`,`expire_txt`) VALUES (?,?,?,?,?,?);');
            $reqI->execute($d);
        }else{
            if(!PAGE_IN_ADM){
                $d = array($num_page,NOM_PAGE, $expire,$expire_txt, $User->id_user);
            }else{
                $d = array(0,"Page $num_page en cours admin", $expire,$expire_txt, $User->id_user);
            }
            $reqU = $DB->prepare('UPDATE trace_traceur SET `num_page` = ?,`nom_page` = ?,`expire` = ?,`expire_txt` = ? WHERE `id_user` =? LIMIT 1 ;');
            $reqU->execute($d);
        }
    }
    
    // L'administrateur se déconnecte.
    // Suppression de ce dernier dans trace_traceur
    public static function liberation_traceur($LoginUser) {
        global $DB;
        if(empty($LoginUser)){
            return false;
        }
        $sql  = "DELETE FROM `trace_traceur` WHERE `user`='$LoginUser';";
        $nb   = $DB->exec($sql);
    }
}