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
 * @subpackage Corbeille
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Corbeille {
    /**
     * Fonction permentant de retourner le nombre d'article se trouvant dans la corbeille d'une page
     * Exemple :
     * <code>
     * // Retourner le nombre d'article se trouvant dans la corbeille de la page 1 en francais
     * echo nombre_article(1,'fr');
     * </code>
     * @global object $DB Variable de connection à la base de données
     * @global object $User Variable Utilisateur de la session actuelle
     * @param int $numpage Numéro de la page sur laquelle porte la demande
     * @return int retourne le nombre d'article presents dans la corbeille
     * @since Version 1.0
     * @version Version 1.0
     */
    public static function nombre_article($numpage = '', $langue = '') {
        global $DB;
        global $User;
        $langue = ($langue == '') ? $User->langue_user : $langue;
        $Sql_Numpage = ($numpage == '') ? '' : ' AND id_page=' . $numpage;
        
        $sql = "SELECT id FROM articles_vue WHERE `corbeille`=1 $Sql_Numpage AND langue='$langue'";
        $req = $DB->query($sql);
        return $req->rowCount();
    }

}

?>