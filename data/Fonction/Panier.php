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
 * @subpackage Panier
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
/**
 * Description of Panier
 *
 * @author Olivier
 */

    class Fonction_Panier {

        public static function __callStatic($name, $args) {
            return self::data_search($name, $args[0]);
        }

        function data_search($name, $args) {
            if (!empty($args)) {
                $cSQL = " WHERE 1=1";
                foreach ($args as $key => $value) {
                    $cSQL .= " AND $key='$value'";
                }
            }
            global $DBoutique;
            $req = $DBoutique->query("SELECT * FROM `$name` $cSQL");
            return $req->fetch(\PDO::FETCH_OBJ);
        }

        /**
         * Fonction permettant d'ajouter des éléments dans le panier 
         * @param int $id_article Id de l'article à ajouter dans le panier 
         * @param int $quantite Quantité d'article à ajouter dans le panier
         * @param array $ArrayOption Tableau associatif contenant des options éventuelles (exemple : couleurs, taille d'un article)  
         */
        public static function _Init() {
            if (!isset($_SESSION)) {
                session_start();
            }
            if (!isset($_SESSION['panier'])) {
                $_SESSION['panier'] = array();
            }
        }

        public static function ajout_old($id_article, $quantite, $ArrayOption = array()) {
            // Vérifier que l'article n'existe pas déjà. Dans ce cas, cela devient une modification. Ou un ajout de quantité
            global $DBoutique;

            $data_Article = Fonction_Article::get_prix($id_article);

            $prix_ut = $data_Article->prix_ht;
            $total_ht = $quantite * $prix_ut;
            $tva = $quantite * $data_Article->prix_tva;
            $total_ttc = $quantite * $data_Article->prix_ttc;

            $d = array($id_article, $prix_ut, $quantite, $total_ht, $tva, $total_ttc);
            $reqI = $DBoutique->prepare('INSERT INTO `panier` (`id_article` ,`prix_uht` ,`quantite` ,`total_ht` ,`tva` ,`total_ttc`) VALUES (?,?,?,?,?,?);');
            $reqI->execute($d);

            $id_panier = $DBoutique->lastInsertId('id');

            $ArrayOption = array("Fase 1" => 15, "Sans Frites" => "Oui");
            foreach ($ArrayOption as $key => $value) {
                $d = array($id_panier, Fonction_Filter::NomFichier($key), $value);
                $reqI = $DBoutique->prepare('INSERT INTO `panier_options` (`id_panier` ,`nom_option`,`valeur_option`) VALUES (?,?,?);');
                $reqI->execute($d);
            }
        }

        public static function info_produit($id_produit, $code_article = 1) {
            Fonction_Panier::_Init();
            $ObjPanier = new stdClass();
            $ObjPanier->titre = Fonction_Article::get_titre($id_produit);
            $ObjPanier->quantite = $_SESSION['panier'][$id_produit][$code_article];
            $ObjPanier->prix_ht = number_format(Fonction_Article::get_prix($id_produit, $code_article)->prix_ht, 2, ',', ' ');
            $ObjPanier->prix_ttc = number_format(Fonction_Article::get_prix($id_produit, $code_article)->prix_ttc, 2, ',', ' ');
            $ObjPanier->prix_tva = number_format(Fonction_Article::get_prix($id_produit, $code_article)->prix_tva, 2, ',', ' ');
            $ObjPanier->code_tva = Fonction_Article::get_prix($id_produit)->code_tva;
            $ObjPanier->total_ht = number_format($ObjPanier->quantite * Fonction_Article::get_prix($id_produit, $code_article)->prix_ht, 2, ',', ' ');
            $ObjPanier->total_tva = number_format($ObjPanier->quantite * Fonction_Article::get_prix($id_produit, $code_article)->prix_tva, 2, ',', ' ');
            $ObjPanier->total_ttc = number_format($ObjPanier->quantite * Fonction_Article::get_prix($id_produit, $code_article)->prix_ttc, 2, ',', ' ');
            $ObjPanier->libelle = self::libelle_option($id_produit, $code_article);
            return $ObjPanier;
        }

        public static function recalc() {
            Fonction_Panier::_Init();
            foreach ($_SESSION['panier'] as $product_id => $quantity) {
                if (isset($_POST['panier']['quantity'][$product_id])) {
                    $_SESSION['panier'][$product_id] = $_POST['panier']['quantity'][$product_id];
                }
            }
        }

        public function count() {
            Fonction_Panier::_Init();
            $total = 0;
            foreach ($_SESSION['panier'] as $key => $value) {
                $total += array_sum($_SESSION['panier'][$key]);
            }
            return $total;
        }

        public static function add($product_id, $code_article = 1) {
            Fonction_Panier::_Init();
            if (!isset($_SESSION['panier'][$product_id])) {
                $_SESSION['panier'][$product_id] = array();
                if (!isset($_SESSION['panier'][$product_id][$code_article])) {
                    $_SESSION['panier'][$product_id][$code_article] = 1;
                } else {
                    $_SESSION['panier'][$product_id][$code_article]++;
                }
            } else {
                $_SESSION['panier'][$product_id][$code_article]++;
            }
        }

        public static function del($product_id, $code_article = 1) {
            Fonction_Panier::_Init();
            unset($_SESSION['panier'][$product_id][$code_article]);
        }

        function query($sql, $data = array()) {
            global $DBoutique;
            $req = $DBoutique->prepare($sql);
            $req->execute($data);
            return $req->fetchAll(PDO::FETCH_OBJ);
        }

        public static function prix($id_article, $code_article = 1, $champ = "prix_ttc") {
            $prix = self::query("SELECT id_article,$champ FROM articles_prix WHERE id_article=$id_article AND code_article=$code_article");
            return $prix[0]->$champ;
        }

        public static function total($champ = "prix_ttc") {
            Fonction_Panier::_Init();
            $total = 0;
            $ids = array_keys($_SESSION['panier']);
            foreach ($_SESSION['panier'] as $key => $value) {
                foreach ($_SESSION['panier'][$key] as $key2 => $value2) {
                    $total += self::prix($key, $key2, $champ) * $value2;
                }
            }
            return number_format($total, 2, ',', ' ');
        }

        public static function Nombre_option($id_article) {
            global $DBoutique;
            $sql = "SELECT id FROM `articles_prix` WHERE id_article=$id_article";
            $req = $DBoutique->query($sql);
            return $req->rowCount();
        }

        public static function libelle_option($id_article, $code_article = 1) {
            $libelle = self::query("SELECT libelle FROM articles_prix WHERE id_article=$id_article AND code_article=$code_article");
            return (empty($libelle[0]->libelle)) ? "" : '- <span style="color:grey">' . $libelle[0]->libelle . '</span>';
        }

    }

?>
