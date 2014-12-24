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
 * @subpackage Cryptage
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description of Cryptage
 *
 * @author Olivier
 */
class Fonction_Cryptage {

    /**
     * Function permettant de crypter des données
     * Exemple :
     * <code>
     * <?php
     * // Crytage de la chaine de caractère "neuro-graph"
     * 
     * ?>
     * </code>
     * @param string $data Chaine de caractères à crypter
     * @return string Retourne la chaine de caractère crypter 
     * @since Version 1.0
     */
    public static function crypte($data) {
        // Création de la clef de cryptage se huit caractères sur la base du code de serveur
        $tab = explode('/', dirname($_SERVER['DOCUMENT_ROOT']));
        $key = substr($tab[count($tab) - 1], (8 * -1));
        //$key = "secret";  // Clé de 8 caractères max
        $data = serialize($data);
        $td = mcrypt_module_open(MCRYPT_DES, "", MCRYPT_MODE_ECB, "");
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = base64_encode(mcrypt_generic($td, '!' . $data));
        mcrypt_generic_deinit($td);
        return urlencode($data);
    }

    /**
     * Function permettant de décrypter une chaine encryptée par la fonction "Crypte"
     * Exemple :
     * <code>
     * <?php
     * // Décryptage de la chaine de caractère "ehfjekhfkejf"
     * ?>
     * </code>
     * @param string $data Chaine de données à décrypter
     * @param bool $utilisation_url doit etre à True si la source du décryptage est une url
     * @return string Retourne la chaine décryptée
     * @since Version 1.0
     * @version Version 2.0
     */
    public static function decrypte($data, $utilisation_url=false) {
        if(empty($data) || is_null($data)){return null;}
        try {
            if (!$utilisation_url){
                $data = urldecode($data);
            }
            $tab = explode('/', dirname($_SERVER['DOCUMENT_ROOT']));
            $key = substr($tab[count($tab) - 1], (8 * -1));
            //$key = "secret";
            $td = mcrypt_module_open(MCRYPT_DES, "", MCRYPT_MODE_ECB, "");
            $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

            mcrypt_generic_init($td, $key, $iv);
            
            $data = mdecrypt_generic($td, base64_decode($data));
            mcrypt_generic_deinit($td);

            if (substr($data, 0, 1) != '!')
                return false;

            $data = substr($data, 1, strlen($data) - 1);
            return unserialize($data);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Fonction permettant de créer une URL avec donnés cryptées
     * Exemple :
     * <code>
     * <?php
     * // Exemple de cryptage d'url
     * echo Function_url_crypte("http://www.neuro-graph.fr/index.php", array('data1'=>3, 'data2'=>7));
     * // Retournera "http://neuro-graph.fr/index.php?data=Rr5RGorzo9Eb3GGXB"
     * ?>
     * </code>
     * @param string $url Adresse URL
     * @param array $ArrayData Tableau associatif contenant les données à crypter
     * @return string Retourne l'URL complète les données cryptées 
     * @since Version 1.0
     */
    public static function url_crypte($url, $ArrayData) {
        $count = 0;
        $Curl = '';
        foreach ($ArrayData as $key => $value) {
            $Curl .= ($count == 0) ? '' : '|';
            $Curl .= $key . '=' . $value;
            $count++;
        }
        return $url . '?data=' . Fonction_Cryptage::crypte($Curl);
    }

    /**
     * Fonction permettant de crypter une adresse mail afin quelle ne soit pas récupérable par les robots
     * Exemple :
     * <code>
     * <?php
     * // Exemple de cryptage d'adresse mail
     * ?>
     * </code>
     * @param string $mail Adresse mail à crypter 
     * @return string Retourne l'adresse mail cryptée
     * @since Version 1.0
     */
    public static function mail_crypte($mail) {
        $encoded = bin2hex($mail);
        $encoded = chunk_split($encoded, 2, '%');
        $encoded = '%' . substr($encoded, 0, strlen($encoded) - 1);
        return $encoded;
    }

}

?>
