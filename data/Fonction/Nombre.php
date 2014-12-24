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
 * Description de Nombre
 *
 * @author Olivier
 */
class Fonction_Nombre {

    /*
     * Retire les zéros inutiles
     */
    // cutzero("4.7600" );    // returns 4.76 
    // cutzero("4.7604" )      // returns 4.7604
    // cutzero("4.7000" );    // returns 4.7
    // cutzero("4.0000" );    // returns 4
    public static function cutzero($value) {
        return preg_replace("/(\.\d+?)0+$/", "$1", $value) * 1;
    }

}
