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
 * Description de Date
 *
 * @author Olivier
 */
class Fonction_Date {
    /**
     * Converti au date JJ/MM/AAAA en YYYY/MM/DD
     * @param text $DateFr
     * @return text
     */
    public static function Date_FR_to_EN($DateFr) {
        return implode('-', array_reverse(explode('/', $DateFr)));
    }
    
    /**
     * Converti une date Française JJ/MM/AAAA en TimeStamp
     * @param text $date_heure
     * @return int
     */
    public static function dateFR2Time($date_heure){
        $date_heure = explode(' ', $date_heure);
        if(count($date_heure)==1){
            list($day, $month, $year) = explode('/', $date_heure[0]);
            return mktime(12, 0, 0, $month, $day, $year);
        }else{
            list($day, $month, $year) = explode('/', $date_heure[0]);
            list($h, $m, $s) = explode(':', $date_heure[1]);
            return mktime($h, $m, $s, $month, $day, $year);
        }
    }
    /**
     * Renvoi True si $DateSearch est conprise entre $DateDebut et $DateFin autrement False
     * @param text $DateDebut Date de Début format jj/mm/aaaa[ HH:MM:SS]
     * @param type $DateFin Date de Fin format jj/mm/aaaa[ HH:MM:SS]
     * @param type $DateSearch
     * @return boolean
     */
    public static function inRange($DateDebut,$DateFin, $DateSearch) {

        $DateDebut = explode(' ', $DateDebut);
        if(count($DateDebut)==1){
            list($day, $month, $year) = explode('/', $DateDebut[0]);
            $TimeDebut = mktime(0, 0, 0, $month, $day, $year);
        }else{
            list($day, $month, $year) = explode('/', $DateDebut[0]);
            list($h, $m, $s) = explode(':', $DateDebut[1]);
            $TimeDebut = mktime($h, $m, $s, $month, $day, $year);
        }
        
        $DateFin = explode(' ', $DateFin);
        if(count($DateFin)==1){
            list($day, $month, $year) = explode('/', $DateFin[0]);
            $TimeFin = mktime(0, 0, 0, $month, $day, $year);
        }else{
            list($day, $month, $year) = explode('/', $DateFin[0]);
            list($h, $m, $s) = explode(':', $DateFin[1]);
            $TimeFin = mktime($h, $m, $s, $month, $day, $year);
        }
        
        $DateSearch = explode(' ', $DateSearch);
        if(count($DateSearch)==1){
            list($day, $month, $year) = explode('/', $DateSearch[0]);
            $TimeSearch = mktime(0, 0, 0, $month, $day, $year);
        }else{
            list($day, $month, $year) = explode('/', $DateSearch[0]);
            list($h, $m, $s) = explode(':', $DateSearch[1]);
            $TimeSearch = mktime($h, $m, $s, $month, $day, $year);
        }
        
        if($TimeSearch>=$TimeDebut && $TimeSearch<$TimeFin){
            return true;
        }else{
            return false;
        }
        
    }
}
