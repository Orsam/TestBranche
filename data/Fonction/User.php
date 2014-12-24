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
 * @subpackage User
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de Membre
 *
 * @author Olivier
 */
class Fonction_User {

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
        $data->table = 'auth_users';
        $data->id = 'id';
        $data->class = __CLASS__;
        return Model_Autoclass::dispatcher($name, $args, $data);
    }

    public static function get_id($login) {
        global $DB;

        $sql = "SELECT id FROM auth_users where `login`='$login'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if ($req->rowCount() > 0) {
            return $data->id;
        } else {
            return 0;
        }
    }

    public static function add($data = array()) {
        
    }

    public static function remove() {
        
    }

    public static function find_role($UserLogin, $role) {
        global $DB;
        $sql = "SELECT role FROM `auth_users_roles` WHERE role ='$role' and login='$UserLogin'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function ChangeLogin($oldLogin, $New_Login) {
        global $DB;
        global $User;
        $oldLogin = trim($oldLogin);
        $New_Login = trim($New_Login);
        if ($oldLogin == $New_Login) {
            return false;
        }
        $sql = "SELECT id FROM `auth_users` WHERE login ='$New_Login'";
        $req = $DB->query($sql);
        if ($req->rowCount() != 0) {
            return false; // Le login existe deja
        } else {
            $d = array($New_Login, $oldLogin);
            $reqU = $DB->prepare("UPDATE `auth_users` SET login=? WHERE login=? LIMIT 1");
            $reqU->execute($d);
            $reqU = $DB->prepare("UPDATE `cotisation` SET login=? WHERE login=?");
            $reqU->execute($d);
            $reqU = $DB->prepare("UPDATE `sessions` SET login=? WHERE login=? LIMIT 1");
            $reqU->execute($d);
            //Fonction_Session::update("login", $New_Login);
            $ControlerIdentité = Fonction_Cryptage::crypte($User->id_user . $User->login);
            //Fonction_Session::update("controle", $ControlerIdentité);
//            Fonction_Logue::trace_log('données personnelles', "Changement de login $oldLogin=>$New_Login");
            return $New_Login;
        }
    }

    public static function add_role($loginId, $role) {
        global $DB;

        if (Fonction_Validation::Valid_Int($loginId)) {
            $UserLogin = Fonction_User::getLogin_ById($loginId);
        }else{
            $UserLogin = $loginId;
        }

        if (Fonction_User::find_role($UserLogin, $role)) {
            //throw new Exception( 'Role déjà attribué'); 
            return false;
        }

        if (!Fonction_User::existe($UserLogin)) {
            throw new Exception('Utilisateur inexistant');
        }
        if (!Fonction_Role::existe($role)) {
            throw new Exception('Role inexistant');
        }

        if (Fonction_User::existe($UserLogin) && Fonction_Role::existe($role)) {
            //$User->$role = true;
            $d = array($UserLogin, $role);
            $sql2 = "INSERT INTO `auth_users_roles` (`login`,`role`) VALUES (?,?)";
            $reqI = $DB->prepare($sql2);
            $reqI->execute($d);
            //Fonction_Session::update($role, true);
        }
    }

    public static function remove_role($loginId, $role) {
        global $DB;
        if (Fonction_Validation::Valid_Int($loginId)) {
            $UserLogin = Fonction_User::getLogin_ById($loginId);
        }else{
            $UserLogin = $loginId;
        }

        if (!self::find_role($UserLogin, $role)) {
            return false;
        }

        if (!self::existe($UserLogin)) {
            throw new Exception('Utilisateur inexistant');
        }
        if (!Fonction_Role::existe($role)) {
            throw new Exception('Role inexistant');
        }

        if (self::existe($UserLogin) && Fonction_Role::existe($role)) {
            $sql = "DELETE FROM auth_users_roles WHERE role='$role' and login='$UserLogin' LIMIT 1"; //on supprime un role
            return $DB->exec($sql);
        }
    }

    public static function add_role_Super($UserLogin, $role_remove = array()) {
        global $DB;
        $roles_exclus = array();
        
        if (!self::existe($UserLogin)) {
            throw new Exception('Utilisateur inexistant');
        }
        if (!empty($role_remove)) {
            foreach ($role_remove as &$value) {
                $roles_exclus[] = "'".$value."'";
            }
            
            $liste_role_supp = implode(',', $roles_exclus);
            
            $sql = "DELETE  FROM `auth_users_roles` WHERE `login` LIKE '$UserLogin' and role in ($liste_role_supp)"; //on supprime un role
            $DB->exec($sql);

            $d = array($UserLogin, 'super_administrateur');
            $sql2 = "INSERT INTO `auth_users_roles` (`login`,`role`) VALUES (?,?)";
            $reqI = $DB->prepare($sql2);
            $reqI->execute($d);
        }else{
            return false;
        }
    }

    public static function remove_All_roles($loginId) {
        global $DB;

        if (Fonction_Validation::Valid_Int($loginId)) {
            $login = Fonction_User::getLogin_ById($loginId);
        }else{
            $login = $loginId;
        }

        if (!self::existe($login)) {
            throw new Exception('Utilisateur inexistant');
        }
        if (self::existe($login)) {
            $sql = "DELETE FROM auth_users_roles WHERE login='$login'"; //on supprime tous les roles
            return $DB->exec($sql);
        }
    }

    public static function Add_InBureau($login, $inBureau = true) {
        global $DB;
        $d = array($login);
        $SqlInBureau = ($inBureau) ? 1 : 0;
        $reqU = $DB->prepare("UPDATE `auth_users_suppl` SET membre_bureau=$SqlInBureau WHERE login=? LIMIT 1");
        $reqU->execute($d);
    }
    public static function set_DateCreation($login, $annee_creation) {
        global $DB;
        $date_creation = "01/01/".$annee_creation;
        $d = array($date_creation,$annee_creation,$login);
        $reqU = $DB->prepare("UPDATE `auth_users_suppl` SET date_creation=?, annee_creation=? WHERE login=? LIMIT 1");
        $reqU->execute($d);
    }

    
    
    public static function Add_InCA($login, $inCA = true) {
        global $DB;
        $d = array($login);
        $SqlInCa = ($inCA) ? 1 : 0;
        $reqU = $DB->prepare("UPDATE `auth_users_suppl` SET membre_CA=$SqlInCa WHERE login=? LIMIT 1");
        $reqU->execute($d);
    }

    # Fait automatiquement Par la base
//    public static function remove_Allroles($UserLogin) {
//        global $DB;
//        if (!self::existe($UserLogin)){
//            throw new Exception( 'Utilisateur inexistant'); 
//        }
//        
//        $sql = "DELETE FROM auth_users_roles WHERE login='$UserLogin'"; //on supprime un role
//        $DB->exec($sql);
//    }

    public static function existe($UserLogin) {
        global $DB;
        $sql = "SELECT id FROM `auth_users` WHERE login ='$UserLogin'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function GetLastUpdateFiche($login) {
        global $DB;
        $sql = "SELECT modif_fiche FROM `auth_users_suppl` WHERE login ='$login'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return ($data->modif_fiche == 0) ? 'Aucune mise à jour' : date("d/m/Y H:i:s", $data->modif_fiche);
    }
    // Qui a modifié la fiche User
    public static function GetLastUpdateFicheUser($login) {
        global $DB;
        $sql = "SELECT modif_fiche_user FROM `auth_users_suppl` WHERE login ='$login'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return (empty($data->modif_fiche_user)) ? "" : " par ".$data->modif_fiche_user ;
    }

    public static function SetLastUpdateFiche($loginId) {
        global $DB;
        global $User;
        $login = (Fonction_Validation::Valid_Int($loginId)) ? Fonction_User::getLogin_ById($loginId) : $loginId; 
        $d = array(Fonction_User::NomPrenomByLogin($User->login), $login);
        $reqU = $DB->prepare("UPDATE `auth_users_suppl` SET modif_fiche=" . time() . ",modif_fiche_user=?  WHERE login=? LIMIT 1");
        $reqU->execute($d);
    }

    public static function idUser_existe($id_user) {
        global $DB;
        $sql = "SELECT id FROM `auth_users` WHERE id =$id_user";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function getLogin_ById($id_user) {
        global $DB;
        $sql = "SELECT login FROM `auth_users` WHERE id =$id_user";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return $data->login;
        }
    }

    public static function delete_user($id) {
        global $DB;
        $DB->exec("DELETE FROM auth_users WHERE id=$id");
        $DB->exec("DELETE FROM all_options WHERE `table`='membres' and id_element=$id");
    }

    public static function delete_AllUsers() {
        global $DB;

        //$DB->exec("DELETE FROM all_options WHERE `table`='membres'");
    }

    // A supprimer est remplacé par SaveDataUser
    public static function ChangePassword($UserLogin, $NewPassWord) {
        global $DB;
        $d = array(md5($NewPassWord), $UserLogin);
        $reqU = $DB->prepare('UPDATE `auth_users` SET pass=? WHERE login=? LIMIT 1');
        $reqU->execute($d);
    }

    public static function Nbr_MembreActif() {
        global $DB;
        $sql = "SELECT count(*) as nombre FROM `All_Users_Actif`";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->nombre;
    }

    public static function isInBureau($UserLogin) {
        global $DB;
        $sql = "SELECT id FROM `auth_users_vue` WHERE login='$UserLogin' and actif = 1 and membre_bureau = 1";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }
    public static function isTresorier($UserLogin) {
        global $DB;
        $sql = "SELECT * FROM `auth_users_roles` WHERE login='$UserLogin' and role ='tresorier'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }
    public static function isSuperAdministrateur($UserLogin) {
        global $DB;
        $sql = "SELECT * FROM `auth_users_roles` WHERE login='$UserLogin' and role ='super_administrateur'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }
    public static function isNonMembre($loginId) {
        global $DB;
        if (Fonction_Validation::Valid_Int($loginId)) {
            $login = Fonction_User::getLogin_ById($loginId);
        }else{
            $login = $loginId;
        }
        
        $sql = "SELECT * FROM `auth_users_suppl` WHERE login='$login' and non_membre=1";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }
    public static function isNeuro($UserLogin) {
        global $DB;
        $sql = "SELECT * FROM `auth_users_roles` WHERE login='$UserLogin' and role ='neuro'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }
    public static function is_UserAvecPouvoir($UserLogin) {
        if(Fonction_User::isNeuro($UserLogin) || Fonction_User::isSuperAdministrateur($UserLogin) || Fonction_User::is_Administrateur($UserLogin) || Fonction_User::isInBureau($UserLogin) || Fonction_User::isInCA($UserLogin) || Fonction_User::isTresorier($UserLogin)){
            return true;
        }else{
            return false;
        }
    }

    public static function is_Administrateur($UserLogin) {
        global $DB;
        $sql = "SELECT id FROM `administrateurs` WHERE login='$UserLogin'";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function isInCA($UserLogin) {
        global $DB;
        $sql = "SELECT id FROM `auth_users_vue` WHERE login='$UserLogin' and actif = 1 and membre_ca = 1";
        $req = $DB->query($sql);
        if ($req->rowCount() == 0) {
            return false;
        } else {
            return true;
        }
    }

    public static function LibelleFonction($code_fonction) {
        global $DB;
        $sql = "SELECT fonction FROM `auth_user_fonctions` where code_fonction='$code_fonction'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->fonction;
    }

    public static function LibelleRoleMembreBureau($UserLogin) {
        global $DB;
        $sql = "SELECT fonction FROM `auth_users_suppl` where login='$UserLogin'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return self::LibelleFonction($data->fonction);
    }

    public static function idUserByLogin($UserLogin) {
        global $DB;
        if (self::existe($UserLogin)) {
            $sql = "SELECT id FROM `auth_users_vue` WHERE `login` ='$UserLogin'";
            $req = $DB->query($sql);
            $data = $req->fetch(PDO::FETCH_OBJ);
            return $data->id;
        } else {
            return 0;
        }
    }

    public static function new_login($nom, $prenom) {
        $new_login = '';
        $LenBase = 10;
        $nom = (empty($nom)) ? '' : strtolower(str_replace('-', '', Fonction_Page::nom_dynamique($nom)));
        $prenom = (empty($prenom)) ? '' : strtolower(str_replace('-', '', Fonction_Page::nom_dynamique($prenom)));

        $new_login .= (empty($prenom)) ? '' : Left($prenom, 1);
        $new_login .= (empty($nom)) ? '' : Left($nom, 9);
        $len_login = strlen($new_login);
        $code = Left(mt_rand(), ($LenBase - $len_login));
        return ($len_login < $LenBase) ? $new_login . $code : $new_login;
    }

    public static function SaveDataUser($id_user, $champTable, $value) {
        global $DB;
        $d = array($value, $id_user);
        try {
            $reqI = $DB->prepare("UPDATE `auth_users` SET `$champTable`=? WHERE `id`=?;");
            $reqI->execute($d);
        } catch (Exception $ex) {
            $reqI = $DB->prepare("UPDATE `auth_users_suppl` SET `$champTable`=? WHERE `id_user`=?;");
            $reqI->execute($d);
        }
    }

    public static function ReadDataUser($id_user, $champTable) {
        global $DB;
        $sql = "SELECT $champTable FROM `auth_users_vue` WHERE id=$id_user";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return ($champTable == '*') ? $data : $data->$champTable;
    }

    public static function NomPrenomByLogin($login) {
        global $DB;
        $sql = "SELECT titre,nom,prenom FROM `auth_users_vue` WHERE login='$login'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return "$data->titre $data->nom $data->prenom";
    }

    public static function NomPrenomById($id_user) {
        global $DB;
        
        if (Fonction_Validation::Valid_Int($id_user)) {
            $FiltreSql = " WHERE id=".$id_user;
        }else{
            $FiltreSql = " WHERE login='$id_user'";
        }
        $sql = "SELECT titre,nom,prenom FROM auth_users_vue $FiltreSql";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return "$data->nom $data->prenom ($data->titre)";
    }

    public static function FindListeUsersByRole($role) {
        global $DB;
        $Liste = array();
        $role = str_replace("-", "_", Fonction_Page::nom_dynamique($role));
        $sql = "SELECT login FROM `auth_users_roles` WHERE `role` LIKE '$role'";
        $req = $DB->query($sql);
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $Liste[] = $data->login;
        }
        return $Liste;
    }
    public static function FindListeMailsByRole($role) {
        global $DB;
        $Liste = array();
        $role = str_replace("-", "_", Fonction_Page::nom_dynamique($role));
        $sql = "SELECT login FROM `auth_users_roles` WHERE `role` LIKE '$role'";
        $req = $DB->query($sql);
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $Liste[] = Fonction_User::get_mail_User($data->login);
        }
        return $Liste;
    }

    public static function get_mail_User($loginId) {
        global $DB;
        if (Fonction_Validation::Valid_Int($loginId)) {
            $FiltreSql = " WHERE id=".$loginId;
        }else{
            $FiltreSql = " WHERE login='$loginId'";
        }
        $sql = "SELECT mail FROM All_Users $FiltreSql";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        return $data->mail;
    }
    
    public static function AdressePerso($loginId) {
        global $DB;
        if (Fonction_Validation::Valid_Int($loginId)) {
            $FiltreSql = " WHERE id=".$loginId;
        }else{
            $FiltreSql = " WHERE login='$loginId'";
        }
        $sql = "SELECT perso_adresse,perso_ville,perso_pays,mail FROM All_Users $FiltreSql";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        $test = trim($data->perso_adresse) . trim($data->perso_ville) . trim($data->perso_pays) . trim($data->mail);
        if ($test===""){
            return false;
        }else{
            return true;
        }
        
    }
    public static function AdressePro($loginId) {
        global $DB;
        if (Fonction_Validation::Valid_Int($loginId)) {
            $FiltreSql = " WHERE id=".$loginId;
        }else{
            $FiltreSql = " WHERE login='$loginId'";
        }
        $sql = "SELECT etab_adresse,etab_ville,etab_pays,mail FROM All_Users $FiltreSql";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        $test = trim($data->etab_adresse) . trim($data->etab_ville) . trim($data->etab_pays) . trim($data->mail);
        if ($test===""){
            return false;
        }else{
            return true;
        }
    }
    
}
