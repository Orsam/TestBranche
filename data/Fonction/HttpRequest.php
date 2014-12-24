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
 * @subpackage HttpRequest
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description of HttpRequest
 *
 * @author Olivier
 */
class Fonction_HttpRequest {

    /**
     * Fonction permettant de faire un envoi de données en HttpRequest en method POST
     * Exemple :
     * <code>
     * <?php
     * // Envoie des données data1, data2 contenant respectivement 2 et 6
     * Fonction_HttpRequest::send_data("http://www.neuro-graph.fr/mapage.php", array('data1'=>2, 'data2'=>6));
     * ?>
     * </code>
     * @param string $url Adresse du site destinataire
     * @param array $ArrayData Tableau associatif des données à envoyer
     * @return string Retourne 1 si les données ont été envoyées correctement, dans le cas contraire, 0 est retourné 
     * @since Version 1.0
     */
    public static function send_data($url, $ArrayData) {
        $r = new HttpRequest($url, HttpRequest::METH_POST);
        if (!empty($ArrayData)) {
            $r->addPostFields($ArrayData);
        }
        $r->send();
        if ($r->getResponseCode() == 200) {
            return '1';
        } else {
            return '0';
        }
    }

}

?>
