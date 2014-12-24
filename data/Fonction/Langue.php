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
 * @subpackage Langue
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de Langue
 *
 * @author Olivier
 */
class Fonction_Langue {

    public static function Langue($texte, $langue=null) {
        global $DB;
        $texte = trim($texte);
        $Code = strtolower(Fonction_Filter::NomFichier($texte));
        $lng  = (is_null($langue)) ? LANGUE : $langue;
        $sql  = "SELECT $lng FROM `neuro_table_langue` WHERE `code`='$Code'";
        $req  = $DB->query($sql);
        if($req->rowCount()==0){
            return self::add_translate($texte, $lng);
        }else{
            $data = $req->fetch(PDO::FETCH_OBJ);
            return trim($data->$lng);
        }
    }
    public static function add_translate($texte, $langue=null) {
        global $DB;
        $Code = Fonction_Filter::NomFichier($texte);
        
        $d = array(strtolower($Code), $texte);
        //echo "INSERT INTO `neuro_table_langue` (`code` ,`$langue`) VALUES (?,?);";
        $reqI = $DB->prepare("INSERT INTO `neuro_table_langue` (`code` ,`$langue`) VALUES (?,?);");
        $reqI->execute($d);
        return $texte;
        
    }
}

//Fonction_Langue::Langue("Etes-vous sûre de vouloir supprimer cet article", $langue);

?>
