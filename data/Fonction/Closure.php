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
 * @subpackage Closure
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description of Closure
 *
 * @author Olivier
 */
class Fonction_Closure {

    function closure() {
        $fichier = dirname($_SERVER['DOCUMENT_ROOT']) . '/web/javascript/jquery.fenetre_popup.js';
        $MonCodeJS = file_get_contents($fichier);

        $r = new HttpRequest("http://closure-compiler.appspot.com/compile", HttpRequest::METH_POST);

        $data = array("js_code" => $MonCodeJS, "compilation_level" => "WHITESPACE_ONLY", "output_info" => "compiled_code");
        $r->addPostFields($data);
        $r->send();
        if ($r->getResponseCode() == 200) {
            $_html = $r->getResponseBody();
            echo $_html;
        } else {
            echo "erreur";
        }
        $pointeur = @fopen(dirname($_SERVER['DOCUMENT_ROOT']) . '/web/media/tempo/jquery.fenetre_popup.min.js', 'w');
        @fwrite($pointeur, $_html);
        @fclose($pointeur);
    }

}

?>
