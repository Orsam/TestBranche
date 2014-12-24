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
 * Description de Epuration
 *
 * @author Olivier
 */
class Fonction_Epuration {
    public static function logues() {
        global $DB;
        global $Config;

        $temp = strtotime('-'.$Config->conservation_logues.' day', time());
        $sql = "DELETE FROM `trace_log` WHERE `quand` < $temp";
        $req = $DB->exec($sql);
    }
}

?>
