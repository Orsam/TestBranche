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
 * Description de Filter
 *
 * @author Olivier
 */
class Fonction_Filter {

    /**
     * Fonction Alias, pour l'appel des differents filtres Zend_Framework
     * Exemple :
     * <code>
     * // Recupere la valeur du champ prenom dans la table membre
     * Fonction_Filter::Alnum($texte);
     * Fonction_Filter::Alpha($texte);
     * Fonction_Filter::Digits($texte);
     * Fonction_Filter::NomFichier($texte);
     * Fonction_Filter::AlphaSansAccents($texte);
     * </code>
     * @param string $name Nom de la fonction
     * @param array $args Paramètres passés à la fonction
     * @return mixed Renvoi une la chaine passée en paramètre filtrée
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function __callStatic($name, $arg) {
        ini_set('include_path', DATA_PATH . DIRECTORY_SEPARATOR);
        require_once(DATA_PATH . DIRECTORY_SEPARATOR . 'Zend/Filter.php');
        require_once(DATA_PATH . DIRECTORY_SEPARATOR . 'Zend/Filter/' . $name . '.php');
        $Filtre = 'Zend_Filter_' . $name;
        if (isset($arg[2])) {
            $retour = new $Filtre($arg[1], $arg[2]);
        } else {
            $retour = new $Filtre($arg[1]);
        }
        return $retour->filter($arg[0]);
    }

    public static function champs($texte) {
        $texte = Fonction_Texte::Unaccent($texte);
        $pattern = "/[^0-9a-zA-Z-\' ]+/";
        $result = trim(preg_replace($pattern, "", html_entity_decode($texte, ENT_QUOTES)));
        return $result;
    }

    /**
     * Ulilisation = Fonction_Filter::StripTagsContent($text, '<strong><p><br>')
     * @param string $text
     * @param string $tags
     * @return string
     */
    public static function StripTagsContent($text, $tags = '') {
        return strip_tags(self::strip_tags_content($text, $tags), $tags);
    }

    /**
     * Fonction utilisée par StripTagsContent
     * @param type $text
     * @param type $tags
     * @param type $invert
     * @return type
     */
    function strip_tags_content($text, $tags = '', $invert = FALSE) {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);
        if (is_array($tags) AND count($tags) > 0) {
            if ($invert == FALSE) {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif ($invert == FALSE) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }

    public static function FiltreNomPrenom($ChaineNomPrenom) {
        if(!empty($ChaineNomPrenom)){
            $ChaineNomPrenom = str_replace("(", " ", $ChaineNomPrenom);
            $ChaineNomPrenom = str_replace(")", " ", $ChaineNomPrenom);
        }
        
        $filter = new Zend_Filter_PregReplace();
        $filter->setMatchPattern(array('/[^\p{L}\'\s-]/u'))
                ->setReplacement(array(''));
        $NomTransforme = mb_strtolower(trim($ChaineNomPrenom));
        if (strpos($NomTransforme, "-")) {
            $filtre1 = $filter->filter($NomTransforme);
            $filtre2 = str_replace(" - ", "-", $filtre1);
            $tab = explode("-", $filtre2);
            $NewTab = array();
            foreach ($tab as $v) {
                $NewTab[] = mb_ucwords($v);
            }
            $filtre3 = implode("-", $NewTab);
            return $filtre3;
        } else if (strpos($NomTransforme, " ")) {
            $filtre1 = $filter->filter($NomTransforme);
            $filtre2 = str_replace("  ", " ", $filtre1);
            return mb_ucwords($filtre2);
        } else {
            $filtre1 = $filter->filter($NomTransforme);
            return mb_ucfirst($filtre1);
        }
    }
    public static function FiltreAdressePostale($Adresse) {
        $filter = new Zend_Filter_PregReplace();
        $filter->setMatchPattern(array('/[^\p{L}\s0-9\'-]/u'))
                ->setReplacement(array(''));
        return $filter->filter(trim($Adresse));
    }
    public static function PhoneNumber($PhoneNumber) {
        $filter = new Zend_Filter_PregReplace();
        $filter->setMatchPattern(array('/[^0-9+.\s-]/u'))
                ->setReplacement(array(''));
        return $filter->filter($PhoneNumber);
    }
    
}