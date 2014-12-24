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
 * @subpackage ExecutionTime
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de ExecutionTime
 *
 * @author Olivier
 */
class Fonction_ExecutionTime {
    public static function save($infopage='') {
        global $DB;
        $endTime = microtime(true);
        $time    = $endTime - START_TIME;
        $NomPage = (empty($infopage)) ? NOM_PAGE : NOM_PAGE.'-'.$infopage;
        
        $sql  = "SELECT * FROM `_Execution_Time` WHERE page = '".$NomPage."'";
        $req  = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if($req->rowCount()==0){
            $d = array($NomPage,LANGUE,$time,$time,$time);
            $reqI = $DB->prepare('INSERT INTO `_Execution_Time` (`page` ,`langue` ,`time` ,`min_time`,`max_time`) VALUES (?,?,?,?,?);');
            $reqI->execute($d);
        }else{
            $champ ="";
            if($time<$data->min_time){$champ = "min_time";}
            if($time>$data->max_time){$champ = "max_time";}
            if(!empty($champ)){
                $d = array($time, $time,$NomPage);
                $reqU = $DB->prepare("UPDATE `_Execution_Time` SET time=?, $champ=? WHERE page=?");
                $reqU->execute($d);
            }
        }
        
    }
}

?>
