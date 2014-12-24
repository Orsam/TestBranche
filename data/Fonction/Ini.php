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
 * @subpackage Ini
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de Ini
 *
 * @author Olivier
 */
class Fonction_Ini {
    public static function CreeIniFile($fichier) {
        if(!file_exists($fichier)){
            $handle = fopen($fichier, "a+");
            fclose($handle);
            chmod($fichier, 0664);
        }
    }
    public static function add_value($fichier, $Groupe, $Item, $valeur){
        self::CreeIniFile($fichier);
        $_ObjIni = new Model_Ini();
        $_ObjIni->m_fichier($fichier);
        $_ObjIni->m_put($valeur, $Item, $Groupe);
        $_ObjIni->save();
    }
    public static function read($fichier) {
        self::CreeIniFile($fichier);
        $_ObjIni = new Model_Ini();
        $_ObjIni->m_fichier($fichier);
        return array2object($_ObjIni->fichier_ini);
    }
    public static function del_groupe($fichier, $nom_groupe) {
        self::CreeIniFile($fichier);
        $_ObjIni = new Model_Ini();
        $_ObjIni->m_fichier($fichier);
        $_ObjIni->groupe = $nom_groupe;
        $_ObjIni->s_groupe();
        $_ObjIni->save();
    }
    public static function del_item($fichier, $nom_groupe, $nom_item) {
        self::CreeIniFile($fichier);
        $_ObjIni = new Model_Ini();
        $_ObjIni->m_fichier($fichier);
        $_ObjIni->groupe = $nom_groupe;
        $_ObjIni->item = $nom_item;
        $_ObjIni->s_item();
        $_ObjIni->save();
    }
}

?>
