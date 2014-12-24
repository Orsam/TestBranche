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
 * @subpackage Include
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Include{
    public static function css_fichier($_fichier){
        if(true===file_exists(CSS_PATH . DIRECTORY_SEPARATOR . $_fichier)){
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/".$_fichier."\" media=\"screen\" />\n";
        }else{
            throw new Exception("Le fichier CSS $_fichier n\'existe pas."); 
        }
    }
    public static function css_fichier_admin($_fichier){
        //if(true===file_exists(CSS_PATH . DIRECTORY_SEPARATOR . $_fichier)){
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../css_administration/".$_fichier."\" media=\"screen\" />\n";
        //}else{
        //    throw new Exception("Le fichier CSS $_fichier n\'existe pas."); 
        //}
    }
    public static function css_exception($_fichier,$chaineLTE){
        if(true===file_exists(CSS_PATH . DIRECTORY_SEPARATOR . $_fichier)){
            echo "<!--[$chaineLTE]>\n";
            echo "<style type=\"text/css\" media=\"screen\">\n";
            echo "<!--@import url(/css/$_fichier);-->\n";
            echo "</style>\n";
            echo "<![endif IE]-->\n";
        }else{
            throw new Exception("Le fichier CSS $_fichier n\'existe pas."); 
        }
    }
    public static function css_print($_fichier){
        if(true===file_exists(CSS_PATH . DIRECTORY_SEPARATOR . $_fichier)){
            echo '<style type="text/css" media="print">';
            echo "<!--@import url(/css/$_fichier);-->";
            echo "</style>\n";
        }
    }
    public static function css_javascript($_fichier){
        if(true===file_exists(JAVASCRIPT_PATH . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR. $_fichier)){
            echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/javascript/css/".$_fichier."\" media=\"screen\" />\n";
        }
    }           
    /**
     * Fonction permettant d'inclure les fichiers Javascript avec tous les attributs strictes
     * @param text $nom_du_fichier Nom du fichier Javascript à inclure
     * @return text retourne la chaine javascript 
     */
    public static function javascript($_fichier,$param=array()) {
        if(true===file_exists(JAVASCRIPT_PATH . DIRECTORY_SEPARATOR . $_fichier)){
            $fichier = substr($_fichier,0,  strlen($_fichier)-3); 
            $CParam = '';
            Fonction_Include::css_javascript($fichier.'.css');
            if (count($param)!=0){
                foreach ($param as $key => $value) {
                    $CParam == '' ? $sepa='?' : $sepa='&amp;';
                    $CParam .= $sepa . $key . '=' . $value; 
                }
            }
            echo "<script src=\"/javascript/$_fichier$CParam\"  type=\"text/javascript\" language=\"javascript\" charset=\"utf-8\"></script>\n";
        }else{
            throw new Exception("Le fichier Javascript $_fichier n'existe pas."); 
        }
    }
    /**
     * Fonction permettant d'inclure les fichiers Javascript avec tous les attributs strictes
     * @param text $nom_du_fichier Nom du fichier Javascript à inclure
     * @return text retourne la chaine javascript 
     */
    public static function javascript_UI($_fichier,$param=array()) {
        if(true===file_exists(JAVASCRIPT_PATH . DIRECTORY_SEPARATOR .'ui'.DIRECTORY_SEPARATOR. $_fichier)){
            $fichier = substr($_fichier,0,  strlen($_fichier)-3); 
            $CParam = '';
            Fonction_Include::css_javascript($fichier.'.css');
            if (count($param)!=0){
                foreach ($param as $key => $value) {
                    $CParam == '' ? $sepa='?' : $sepa='&amp;';
                    $CParam .= $sepa . $key . '=' . $value; 
                }
            }
            echo "<script src=\"/javascript/ui/$_fichier$CParam\"  type=\"text/javascript\" language=\"javascript\" charset=\"utf-8\"></script>\n";
        }else{
            throw new Exception("Le fichier Javascript UI $_fichier n'existe pas."); 
        }
    }
}
?>
