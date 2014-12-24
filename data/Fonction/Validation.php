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
 * @subpackage Validation
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Validation {
    /**
     * Fonction Alias, pour l'appel des differentes Validation Zend_Framework
     * Exemple :
     * <code>
     * // Recupere la valeur du champ prenom dans la table membre
     * Fonction_Validation::Alnum($texte);
     * Fonction_Validation::Alpha($texte);
     * Fonction_Validation::Digits($texte);
     * </code>
     * @param string $name Nom de la fonction
     * @param array $args Paramètres passés à la fonction
     * @return boolean Renvoi True ou False
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function __callStatic($name, $arg) {
        $tab = explode("_", $name);
        ini_set('include_path', DATA_PATH . DIRECTORY_SEPARATOR);

        if(!isset($tab[1])){
            $nom = $tab[0];
        }else{
            $nom = $tab[1];
        }
        require_once(DATA_PATH . DIRECTORY_SEPARATOR . 'Zend/Validate.php');
        require_once(DATA_PATH . DIRECTORY_SEPARATOR . 'Zend/Validate/'.$nom.'.php');
        require_once(DATA_PATH . DIRECTORY_SEPARATOR . 'Zend/Locale.php');
        $Valideur = 'Zend_Validate_' . $nom;
        $valid = new $Valideur();
        if ($valid->isValid($arg[0])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function permetant de verifier si la valeur passée en paramètre est une chaine comprise entre une longueur min et max.
     * @param int|string $var variable qui sera vérifiée
     * @param int $min longueur minimum de la chaine
     * @param int $max longueur maximum de la chaine
     * @return bool return true ou false
     */
    public static function Valid_String($var, $len_min, $len_max) {
        ini_set('include_path', DATA_PATH . DIRECTORY_SEPARATOR);
        require_once(DATA_PATH . DIRECTORY_SEPARATOR . 'Zend/Validate.php');
        require_once(DATA_PATH . DIRECTORY_SEPARATOR . 'Zend/Validate/StringLength.php');
        $validator = new Zend_Validate_StringLength(array('min' => $len_min, 'max' => $len_max));
        if ($validator->isValid($var)) {
            return true;
        } else {
            return false;
        }
    }

    public static function Valid_Mail($email) {
        ini_set('include_path', DATA_PATH . DIRECTORY_SEPARATOR);
        require_once(DATA_PATH . DIRECTORY_SEPARATOR . 'Zend/Validate.php');
        require_once(DATA_PATH . DIRECTORY_SEPARATOR . 'Zend/Validate/EmailAddress.php');
        $validator = new Zend_Validate_EmailAddress();
        if ($validator->isValid($email)) {
            return true;
        } else {
            return false;
        }
    }

    public static function Valid_MD5($texte) {
        $filtre = '#^[0-9A-Fa-f]{32}$#';
        if (preg_match($filtre, $texte)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function Valid_champs($texte) {
        $texte = Fonction_Texte::Unaccent($texte);
        $result = trim(preg_replace('/[\x00-\x08\x0B-\x1F]/', '', $texte)); 
        $pattern = "/[0-9a-zA-Z-\']+/";
        $result = trim(preg_replace($pattern, "", html_entity_decode($result, ENT_QUOTES)));
        
        if(empty($result)){
            return true;
        }else{
            return false;
        }
    }

}

?>
