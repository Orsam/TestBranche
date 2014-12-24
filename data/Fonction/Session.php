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
 * @subpackage Session
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
class Fonction_Session {

    public static function epure() {
        global $DB;
        $sql = "DELETE FROM sessions WHERE expire < " . time(); //on supprime les vieilles sessions 
        $nb = $DB->exec($sql);
    }

    public static function read($sid) {

        global $DB;
        $sql = "SELECT session_data FROM sessions WHERE session_id='" . $sid . "'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);
        if ($req->rowCount() > 0) {
            return json_decode($data->session_data);
        } else {
            return false;
        }
    }

    public function write($sid,$donnees) {
        global $DB;
        global $Config;
        //global $Who_page;
        if (!$donnees) {
            return false;
        }
        $donneesOrigin = $donnees;
        $donnees = json_encode($donnees);
        $expire  = strtotime('+'.$Config->deconnection_automatique.' minutes'); //calcul de l'expiration de la session
        
        
        
        
        $sql = "DELETE FROM `sessions` WHERE login = '$donneesOrigin->login' AND session_id<>'$sid'";
        $det = $DB->exec($sql);

        $sql = "SELECT * FROM `sessions` WHERE session_id = '" . $sid . "'";
        $req = $DB->query($sql);
        $data = $req->fetch(PDO::FETCH_OBJ);

        $ip = $_SERVER['REMOTE_ADDR'];

//        $PageEnCours = explode('.', basename($_SERVER['REQUEST_URI'], ".php"));
//        $Page = ($PageEnCours[0] != 'choix_type_article') ? $PageEnCours[0] : $data->page;
        $Page = print_r($_SERVER,TRUE);
        //$nom = explode(".", $Page);
        //$Page = $nom[0];
        //$Page = WHO_PAGE;
        
        if ($req->rowCount() > 0) {
            $d = array($ip, $Page, $donnees, $expire, $sid);
            $reqU = $DB->prepare('UPDATE `sessions` SET ip=?,page=?,session_data=?,expire=? WHERE session_id=?');
            $reqU->execute($d);
        } else {
            
            try{
                $d = array($ip, $donneesOrigin['login'], $Page, $sid, $donnees, $expire);
                $reqI = $DB->prepare('INSERT INTO `sessions` (`ip` ,`login` ,`page`,`session_id` ,`session_data`,`expire`) VALUES (?,?,?,?,?,?);');
                $reqI->execute($d);
            } catch (Exception $ex) {

            }
            
        }
    }

    public static function destroy($sid) {

        global $DB;
        $sql = "DELETE FROM `sessions` WHERE `session_id` ='" . $sid . "'";
        $nb = $DB->exec($sql);
        return $nb;
    }

    public static function destroybyIP() {
        global $DB;
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $d = array($ip);
        $sql = "DELETE FROM `sessions` WHERE `ip` ='" . $ip . "'";
        $nb = $DB->exec($sql);
        return $nb;
    }

    public static function gc() {
        global $DB;

        $sql = "DELETE FROM sessions WHERE expire < " . time(); //on supprime les vieilles sessions 
        $Nbr = $DB->exec($sql);
        $objSession = self::read(session_id());
        if (!$objSession){
            
//            echo "SESSSSEE <br>";
//            exit();

            header("Location: http://" . $_SERVER['SERVER_NAME']);//.$Config->adresse_site);
            exit();
        }
        self::write(session_id(), $objSession);
    }

    public static function update($field, $value) {
        global $User;

        $objSession = self::read(session_id());
        $objSession->$field = $value;
        self::write(session_id(),$objSession);
        $User=$objSession;
    }
    public static function delete($field) {
        
    }

}

?>
