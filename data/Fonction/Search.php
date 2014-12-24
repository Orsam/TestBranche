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
 * @subpackage Search
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de Search
 *
 * @author Olivier
 */
class Fonction_Search {

    private static $_instance = null;
    public $_result = array();
    public $_nbr_result = 0;
    private static $_ChaineSearch;
    private $_index;
    //private $_indexname = 'my_index';

    public static function getInstance($chaineSearch) {
        if (is_null(self::$_instance) || ($chaineSearch != self::$_ChaineSearch)) {
            self::$_instance = new Fonction_Search($chaineSearch);
            self::$_ChaineSearch = $chaineSearch;
        }
        return self::$_instance;
    }

    public function __construct($chaineSearch) {
        $this->_ChaineSearch = $chaineSearch;
        $this->_index = Zend_Search_Lucene::open(self::FichierIndex());
        Zend_Search_Lucene::setDefaultSearchField(NULL);
        Zend_Search_Lucene::setResultSetLimit(0);
        Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');

        // Filtrage de mots
//        require_once DATA_PATH.'/Zend/Search/Lucene/Analysis/TokenFilter/StopWords.php';
//        $stopWords = array('une', 'à', 'la', 'le', 'et', 'de', 'is', 'am');
//        $stopWordsFilter = new Zend_Search_Lucene_Analysis_TokenFilter_StopWords($stopWords);
//        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive();
//        $analyzer->addFilter($stopWordsFilter);
//        Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);        
        //$this->find();
    }

    public static function FichierIndex() {
        return CACHE_PATH . '/index/my_index_'.LANGUE;
    }

    public static function index() {
        return Zend_Search_Lucene::open(self::FichierIndex());
    }
    public static function recherche_exclude() {
        global $Config;
        $pageExclude = explode(",", $Config->page_recherche_exclude);
        $notIn = array();
        foreach ($pageExclude as $value) {
            $notIn[] = Fonction_Page::numpage_by_lien($value);
        }
        return implode(',', $notIn);
    }

    public static function find_unit($id_article) {
        $hits = self::index()->find('pk:' . $id_article);
        foreach ($hits as $hit) {
            $resultSetEntry = array();
            $resultSetEntry['id']         = $hit->id;
            $resultSetEntry['id_article'] = $hit->pk;
            $resultSetEntry['score']      = $hit->score;
            $resultSetEntry['titre']      = $hit->titre;
            $resultSetEntry['contents']   = $hit->contents;
//            $resultSetEntry['contents']   = $query->highlightMatches($hit->contents);
            $result[] = $resultSetEntry;
        }
        return $result;
    }

    public static function find($chaineSearch) {
        $query = Zend_Search_Lucene_Search_QueryParser::parse($chaineSearch, 'utf-8');
        $hits = self::index()->find($query);
        $result = array();
        foreach ($hits as $hit) {
            $resultSetEntry = array();
            $resultSetEntry['id']         = $hit->id;
            $resultSetEntry['id_article'] = $hit->pk;
            $resultSetEntry['score']      = $hit->score;
            $resultSetEntry['titre']      = $hit->titre;
            $resultSetEntry['contents']   = $hit->contents;
//            $resultSetEntry['contents']   = $query->highlightMatches($hit->contents);
            $result[] = $resultSetEntry;
        }
        return $result;
    }

    public static function update($id_article) {
        self::delete($id_article);
        self::add($id_article);
    }

    public static function delete($id_article) {
        $hits = self::index()->find('pk:' . $id_article);
        foreach ($hits as $hit) {
            self::index()->delete($hit->id);
        }
        self::optimize();
    }

    public static function add($id_article) {
        global $DB;
        $req = $DB->query("SELECT id, titre,article FROM `articles_vue` where id=$id_article AND id_page not in (".self::recherche_exclude().")");
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::keyword('pk', $data->id, 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('id_article', $data->id, 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('titre', $data->titre, 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('contents', strip_tags($data->article)), 'utf-8');

            $req2 = $DB->query('SELECT * FROM `images_survol` where id_article=' . $id_article . " ORDER BY num_image");
            while ($data_img = $req2->fetch(PDO::FETCH_OBJ)) {
                $ref = $id_article . "_" . $data_img->num_image;
                $doc->addField(Zend_Search_Lucene_Field::Text("num_image_"    . $ref, $data_img->num_image), 'utf-8');
                $doc->addField(Zend_Search_Lucene_Field::Text("survol_"      . $ref, $data_img->survol), 'utf-8');
                $doc->addField(Zend_Search_Lucene_Field::Text("legende_"     . $ref, $data_img->legende), 'utf-8');
                $doc->addField(Zend_Search_Lucene_Field::Text("geolocation_" . $ref, $data_img->geolocation), 'utf-8');
            }
            self::index()->addDocument($doc);
        }
        self::index()->commit();
        self::optimize();
    }

    public static function create_index() {
        global $DB;
        $index = Zend_Search_Lucene::create(self::FichierIndex());
        $req = $DB->query("SELECT id, titre,article FROM `articles_vue` WHERE langue='fr' and corbeille=0 and actif=1 AND id_page not in (".self::recherche_exclude().")");
        while ($data = $req->fetch(PDO::FETCH_OBJ)) {
            $doc = new Zend_Search_Lucene_Document();
            $doc->addField(Zend_Search_Lucene_Field::keyword('pk', $data->id, 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('id_article', $data->id, 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('titre', $data->titre, 'utf-8'));
            $doc->addField(Zend_Search_Lucene_Field::Text('contents', strip_tags($data->article)), 'utf-8');
                    
            $req2 = $DB->query('SELECT * FROM `images_survol` where id_article=' . $data->id . " ORDER BY num_image");
            while ($data_img = $req2->fetch(PDO::FETCH_OBJ)) { 
                $ref = $data->id . "_" . $data_img->num_image;
                $doc->addField(Zend_Search_Lucene_Field::Text("num_image_"    . $ref, $data_img->num_image), 'utf-8');
                $doc->addField(Zend_Search_Lucene_Field::Text("survol_"      . $ref, $data_img->survol), 'utf-8');
                $doc->addField(Zend_Search_Lucene_Field::Text("legende_"     . $ref, $data_img->legende), 'utf-8');
                $doc->addField(Zend_Search_Lucene_Field::Text("geolocation_" . $ref, $data_img->geolocation), 'utf-8');
            }

            $index->addDocument($doc);
        }
        self::optimize();

    }
    public static function optimize() {
        self::index()->optimize();
    }
    public static function delete_index($NomIndex=null) {
        
        $dossier = (is_null($NomIndex)) ? self::FichierIndex() : $NomIndex;

        $ouverture = @opendir($dossier);
        if (!$ouverture) {
            return;
        }
        while ($fichier = readdir($ouverture)) {
            if ($fichier == '.' || $fichier == '..') {
                continue;
            }
            if (is_dir($dossier . "/" . $fichier)) {
                $r = del_repertoire($dossier . "/" . $fichier);
                if (!$r) {
                    return false;
                }
            } else {
                chmod($dossier . "/" . $fichier, 0777);
                $r = @unlink($dossier . "/" . $fichier);
                if (!$r) {
                    return false;
                }
            }
        }
        closedir($ouverture);
        $r = @rmdir($dossier);
        if (!$r) {
            return false;
        }
        return true;
    }

}

?>
