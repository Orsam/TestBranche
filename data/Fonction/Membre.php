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
 * @subpackage Membre
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de Membre
 *
 * @author Olivier
 */
class Fonction_Membre {
    /**
     * Fonction qui permet la génération dynamique des fonction get, set, option
     * Si celle-ci n'existe pas.
     * Exemple :
     * <code>
     * // Recupere la valeur du champ prenom dans la table membre
     * Fonction_Membre::get_prenom($id_membre);
     * // Affecte une valeur au champ prenom dans la table membre
     * Fonction_Membre::set_prenom($id_membre,"Olivier");
     * // Gestion des options
     * Fonction_Membre::option_add($id_membre, $nom_option, $valeur_option);
     * Fonction_Membre::option_find($id_membre, $nom_option);
     * Fonction_Membre::option_existe($id_membre, $nom_option);
     * Fonction_Membre::option_update($id_membre, $nom_option, $valeur_option);
     * Fonction_Membre::option_delete($id_membre, $nom_option);
     * </code>
     * @param string $name Nom de la fonction non trouvée
     * @param array $args Paramètres passés à la fonction
     * @return mixed Renvoi une valeur ou un boolean
     * @since Version 4.0
     * @version Version 1.0
     */
    public static function __callStatic($name, $args) {
        $data = new stdClass();
        $data->table = 'membres';
        $data->id    = 'id_membres';
        $data->class = __CLASS__;
        return Model_Autoclass::dispatcher($name, $args, $data);
    }
    public static function add() {
        
    }
    public static function remove() {
        
    }    
    
    
    /**
     * Fonction de rechrche dans les informations Json de la base de données
     * @global type $DB
     * @param type $champ
     * @param type $valeur
     * @return type
     */
    public static function recherche($champ,$valeur) {
        global $DB;
        $Result = array();
        $sql = "SELECT id, valeur FROM all_options WHERE valeur REGEXP '\"$champ\":\"([^\"]*)$valeur([^\"]*)\"'";
        $req = $DB->query($sql);
        while ($data = $req->fetch(PDO::FETCH_OBJ)){
            $Js = json_decode($data->valeur); 
            $Result[] = $data->id; 
        }
        $Result = implode(",",$Result); 
        return $Result;
    }
    
    public static function info_ByLogin($login) {
        global $DB;
        $sql = "SELECT * FROM auth_users_vue WHERE login='$login'";
        $req = $DB->query($sql);
        return $req->fetch(PDO::FETCH_OBJ);
    }
    
    
}