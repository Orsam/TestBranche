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
 * @subpackage Translate
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description of Translate
 *
 * @author Olivier
 */
class Fonction_Translate {
    public static function traduit() {
        $Chaine = "L'equipe, vous remercie...Votre mot de passe est incorrect, Merci de le modifier.. ";
        $Chaine = urlencode($Chaine);
        $Chaine = str_replace('+', '%20', $Chaine);
        $url = "http://translate.google.fr/translate_a/t?client=t&text=" . $Chaine . "&hl=fr&sl=fr&tl=en&multires=1&ssel=0&tsel=0&sc=1";
        $r = new HttpRequest($url, HttpRequest::METH_GET);
        try {
            $r->send();
            if ($r->getResponseCode() == 200) {
                $VariableXML = $r->getResponseBody();
                $VariableXML = str_replace('[', '', $VariableXML);
                $VariableXML = str_replace(']', '', $VariableXML);
                $VariableXML = str_replace('"', '|', $VariableXML);
                $Array = explode('|', $VariableXML);
                return $Array[0] . $Array[1];
            }
        } catch (HttpException $ex) {
            
        }
    }

}

?>
