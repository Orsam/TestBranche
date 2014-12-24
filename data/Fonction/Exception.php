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
 * @subpackage Exception
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de Exception
 *
 * @author Olivier
 */
class Fonction_Exception extends Exception {

    /**
     * Lancement d'une Exception écrivant les erreurs dans la table _Exception
     * @global string $User Utilisateur en cours
     * @param string $message Message de l'erreur
     * @param string $class Nom de la class qui revoie l'erreur
     * @param string $fonction Nom de la fonction qui revoie l'erreur
     * @param array $numargs nombre d'argument passé à la fonction
     * @param array $arg_list liste des arguments passés à la fonction
     * @throws Fonction_Exception Exception
     */
    public static function except($message,$class,$fonction="", $numargs="",$arg_list="") {
        $erreur = array();
        if (!isset($User)) {
            global $User;
        }
        $erreur["login"]    = $User->login;
        $erreur["message"]  = $message;
        $erreur["class"]    = $class;
        if($fonction!=""){
            $erreur["fonction"] = $fonction;
        }
        if($numargs!=""){
            for ($i = 0; $i < $numargs; $i++) {
                $erreur['param'.$i] = $arg_list[$i];
            }
        }
        throw new Fonction_Exception($erreur, 5);
    }

// Redéfinissez l'exception ainsi le message n'est pas facultatif
    public function __construct($message, $code = 0, Exception $previous = null) {
        $message = json_encode($message);
        // traitement personnalisé que vous voulez réaliser ...
        // assurez-vous que tout a été assigné proprement
        parent::__construct($message, $code, $previous);
    }

    // chaîne personnalisée représentant l'objet


    public function __toString() {
        global $DB;
        $PR = json_decode($this->message);
        
        
        $d = array($PR->class, $_SERVER['REMOTE_ADDR'], $this->message);
        $sql2 = "INSERT INTO `_exception` (`class`,`ip`,`data`) VALUES (?,?,?)";
        $reqI = $DB->prepare($sql2);
        $reqI->execute($d);
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function customFunction() {
        echo "Une fonction personnalisée pour ce type d'exception\n";
    }

}

?>
