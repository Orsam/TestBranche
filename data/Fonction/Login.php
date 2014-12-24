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
 * Description de Login
 *
 * @author Olivier
 */
class Fonction_Login {
    public static function Create_Login($nom,$prenom) {
                global $DB;

        $nom          = Fonction_Texte::normalise(str_replace(" ", "", $nom));
        $prenom       = Fonction_Texte::normalise(str_replace(" ", "", $prenom));
        
        if(strlen($nom)< 6){
            $LenNom     = strlen($nom); // 3
            $DifLenNom  = (6 - $LenNom)+1;
            $prenom_nom = Fonction_Texte::normalise(left($prenom,$DifLenNom)." ".left($nom,6));
        }else{
            $prenom_nom = Fonction_Texte::normalise(left($prenom,1)." ".left($nom,6));
        }
        
        $informations = str_replace("-", "", $prenom_nom);
        $len_Pass     = strlen($informations);
        $login        = $informations;
        if($len_Pass==0){
            $login    = strtolower(NumeroClientAlpha(4)).NumeroClientInt(3);
            $len_Pass = strlen($login);
        }
        if($len_Pass<7){
            $calc  = (7 - $len_Pass);
            $suppl = NumeroClientInt($calc);
            $login = $informations.$suppl;
        }
        if($len_Pass>7){
            $login = left($informations,7);
        }
        
        if(self::IsExiste($login)){
            $i = 2;
            while (self::IsExiste($login . $i)) {
                $i++;
            }
            $login = $login . $i;
        }

        $d = array($login);
        $reqI = $DB->prepare('INSERT INTO `auth_login_archive` (`login`) VALUES (?);');
        $reqI->execute($d);
        return $login;
    }   
    
    public static function IsExiste($login) {
        global $DB;
        $req = $DB->query("SELECT * FROM `auth_users` WHERE login ='$login'");
        $UserTable = ($req->rowCount() > 0) ? true : false;
        
        $req = $DB->query("SELECT * FROM `auth_login_archive` WHERE login ='$login'");
        $ArchiveTable = ($req->rowCount() > 0) ? true : false;
        
        return ($UserTable || $ArchiveTable) ? true : false;
    }

    
    
    
}
