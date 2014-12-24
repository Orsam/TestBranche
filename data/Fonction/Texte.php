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
 * @subpackage Texte
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Texte {

    /**
     * Function permettant de fermer les balises d'une chaine
     * exemple : la chaine '<adresse><ville><pays class="pays">' retournera </pays></ville></adresse>
     * @param string $chaine chaine de caractère balisée
     * @return string retourne la chaine avec les balises fermées 
     */
    public static function fermeture_balises($chaine) {
        preg_match_all("/<([a-z0-9]+)/i", $chaine, $matches);
        foreach (array_reverse($matches[1], TRUE) as $cle => $valeur) {
            $Sortie .= "</" . $valeur . ">";
        }
        return $Sortie;
    }

    public static function nettoye($value) {
        $value = str_replace(chr(10), " ", $value);
        $value = str_replace(chr(13), " ", $value);
        return str_replace("  ", " ", $value);
    }
    
    public static function normalise($NomPage) {
        $NomPage = trim(strtolower($NomPage));
        $NomPage = str_replace('&', 'et', $NomPage);
        $NomPage = htmlentities($NomPage, ENT_NOQUOTES, 'utf-8');
        $NomPage = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $NomPage);
        $NomPage = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $NomPage); // pour les ligatures e.g. '&oelig;'
        $NomPage = preg_replace('#&[^;]+;#', '', $NomPage); // supprime les autres caractères
        $NomPage = preg_replace('/[^\w]/', '-', $NomPage);
        $NomPage = str_replace('_', '-', $NomPage);
        while (strpos($NomPage,'--')!==false) {
            $NomPage = str_replace('--', '-', $NomPage);
        }
        $NomPage = (substr($NomPage, -1) == '-') ? substr($NomPage, 0, strlen($NomPage) - 1) : $NomPage; 
        $NomPage = (substr($NomPage, 0, 1) == '-') ? substr($NomPage,1,strlen($NomPage) - 1) : $NomPage;
        $NomPage = trim(strtolower($NomPage));

        return $NomPage;
    }
    
    
    /**
     * Fonction permettant la coupure propre d'une chaine de caracteres
     * @param string $chaine
     * @param int $max 
     * @param string $fin_de_chaine Indique les caractère après la chaine exemple '...'
     * @return string 
     */
    public static function tronque($chaine, $max, $fin_de_chaine = '...') {
        $max -= strlen($fin_de_chaine);
        $chaine = trim(strip_tags($chaine));
        if (strlen($chaine) >= $max) {
            // Met la portion de chaine dans $chaine
            $chaine = substr($chaine, 0, $max);
            // position du dernier espace
            $espace = strrpos($chaine, " ");
            // test si il ya un espace
            if ($espace)
            // si ya 1 espace, coupe de nouveau la chaine
                $chaine = substr($chaine, 0, $espace);
            // Ajoute ... à la chaine
            $chaine .= $fin_de_chaine;
        }
        return $chaine;
    }

    /**
     * tronque_html
     * Coupe une chaine en gardant le formatage HTML
     *
     * @param string $texte Texte à couper
     * @param integer $nbreCar Longueur à garder en nbre de caractères
     * @return string
     */
    public static function tronque_html($texte, $nbreCar, $fin_de_chaine = '...') {
        $LongueurTexteBrutSansHtml = strlen(strip_tags($texte));
        if ($LongueurTexteBrutSansHtml < $nbreCar)
            return $texte;
        $MasqueHtmlSplit = '#</?([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?>#';
        $MasqueHtmlMatch = '#<(?:/([a-zA-Z1-6]+)|([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?)>#';
        $texte .= ' ';
        $BoutsTexte = preg_split($MasqueHtmlSplit, $texte, -1, PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $NombreBouts = count($BoutsTexte);
        if ($NombreBouts == 1) {
            $longueur = strlen($texte);
            return substr($texte, 0, strpos($texte, ' ', $longueur > $nbreCar ? $nbreCar : $longueur));
        }
        $longueur = 0;
        $indexDernierBout = $NombreBouts - 1;
        $position = $BoutsTexte[$indexDernierBout][1] + strlen($BoutsTexte[$indexDernierBout][0]) - 1;
        $indexBout = $indexDernierBout;
        $rechercheEspace = true;
        foreach ($BoutsTexte as $index => $bout) {
            $longueur += strlen($bout[0]);
            if ($longueur >= $nbreCar) {
                $position_fin_bout = $bout[1] + strlen($bout[0]) - 1;
                $position = $position_fin_bout - ($longueur - $nbreCar);
                if (($positionEspace = strpos($bout[0], ' ', $position - $bout[1])) !== false) {
                    $position = $bout[1] + $positionEspace;
                    $rechercheEspace = false;
                }
                if ($index != $indexDernierBout)
                    $indexBout = $index + 1;
                break;
            }
        }
        if ($rechercheEspace === true) {
            for ($i = $indexBout; $i <= $indexDernierBout; $i++) {
                $position = $BoutsTexte[$i][1];
                if (($positionEspace = strpos($BoutsTexte[$i][0], ' ')) !== false) {
                    $position += $positionEspace;
                    break;
                }
            }
        }
        $texte = substr($texte, 0, $position);
        preg_match_all($MasqueHtmlMatch, $texte, $retour, PREG_OFFSET_CAPTURE);
        $BoutsTag = array();
        foreach ($retour[0] as $index => $tag) {
            if (isset($retour[3][$index][0])) {
                continue;
            }
            if ($retour[0][$index][0][1] != '/') {
                array_unshift($BoutsTag, $retour[2][$index][0]);
            } else {
                array_shift($BoutsTag);
            }
        }
        if (!empty($BoutsTag)) {
            foreach ($BoutsTag as $tag) {
                $texte .= '</' . $tag . '>';
            }
        }
        if ($LongueurTexteBrutSansHtml > $nbreCar) {
            $texte .= ' [......]';

            $texte = str_replace('</p> [......]', '... </p>', $texte);
            $texte = str_replace('</ul> [......]', '... </ul>', $texte);
            $texte = str_replace('</div> [......]', '... </div>', $texte);
            $texte = str_replace(' [......]', $fin_de_chaine, $texte);
            $texte = str_replace('[......]', $fin_de_chaine, $texte);
        }
        return $texte;
    }
    public static function Unaccent($string) {
        return preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
    }
   
    public static function Strip_accents($str) {
        $str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&[^;]+;#', '', $str); // supprime les autres caractères
        return $str;
    }


}

?>
