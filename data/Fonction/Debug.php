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
 * @subpackage Debug
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Debug {
    
    public static function trace($texte)    {
//        if (file_exists(TEMPO_PATH . DIRECTORY_SEPARATOR .'trace.txt') == 1) {
//           chmod(TEMPO_PATH . DIRECTORY_SEPARATOR .'trace.txt',0777);
//           unlink(TEMPO_PATH . DIRECTORY_SEPARATOR .'trace.txt');
//        }
        echo TEMPO_PATH . DIRECTORY_SEPARATOR .'trace6.txt';
        $pointeur = @fopen(TEMPO_PATH . DIRECTORY_SEPARATOR .'trace6.txt', 'a+');
        @fwrite($pointeur,$texte.chr(10));
        @fclose($pointeur);
        chmod(TEMPO_PATH . DIRECTORY_SEPARATOR .'trace6.txt',0777);
    }
    
}

?>
