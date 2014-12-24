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
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2011 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 3.0
 */

/**
 * Description of Rss
 *
 * @author Olivier
 */
class Fonction_Rss {

    public static function lecteur($langue='fr',$Num_page='xx') {
        global $DB;
        global $Config;
        if ($Num_page=='xx'){$Xml_global=true;};
        $xml = '<?xml version="1.0" encoding="utf-8"?>' . chr(10);
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . chr(10);
        $xml .= '<channel>' . chr(10);
        $xml .= "<atom:link href=\"http://" . $_SERVER['HTTP_HOST'] . "/" . $langue . "_" . $Num_page . ".xml\" rel=\"self\" type=\"application/rss+xml\" />" . chr(10);
        if ($Xml_global) {
            $xml .= "<title>" . $Config->nom_site . " : <![CDATA[  ]]></title>" . chr(10);
        } else {
            $xml .= "<title>" . $Config->nom_site . " : <![CDATA[ " . Fonction_Page::get_titre($Num_page) . " ]]></title>" . chr(10);
        };
        $xml .= '<link>http://' . $_SERVER['HTTP_HOST'] . '</link>' . chr(10);
        $xml .= "<description>" . $Config->nom_site . "</description>" . chr(10);
        $xml .= '<language>' . $lng . '</language>' . chr(10);
        $xml .= '<copyright>Copyright ' . date("Y") . ' Neuro-Graph.com</copyright>' . chr(10);
        $xml .= '<lastBuildDate>' . date("r") . '</lastBuildDate>' . chr(10);
        $xml .= '<category>' . $category . '</category>' . chr(10);
        $xml .= '<managingEditor>collectif@neuro-graph.com (Neuro-graph)</managingEditor>' . chr(10);
        $xml .= '<webMaster>collectif@neuro-graph.com (Neuro-graph)</webMaster>' . chr(10);
        $xml .= '<ttl>60</ttl>' . chr(10);
        $xml .= '<image>' . chr(10);
        $xml .= '	<title>' . $Config->nom_site . '</title>' . chr(10);
        $xml .= '	<url>http://' . $_SERVER['HTTP_HOST'] . '/images_site/logo_client2.png</url>' . chr(10);
        $xml .= '	<link>http://' . $_SERVER['HTTP_HOST'] . '</link>' . chr(10);
        $xml .= '	<description>' . $Config->nom_site . '</description>' . chr(10);
        $xml .= '</image>' . chr(10);

        
        $Num_page=='xx' ? $WhereNumPage = "" : $WhereNumPage = " AND id_page=".$Num_page;
        
        
        $sql = "SELECT * FROM `articles_vue`  where `corbeille` = '0' AND actif=1 $WhereNumPage AND  langue='" . $langue . "' ORDER BY datepubli DESC LIMIT 0,30";
        $reqW = $DB->query($sql);
        while ($data = $reqW->fetch(PDO::FETCH_OBJ)) {
            $article = lien_internes($data->article,$data->lien);
            $xml .= '<item>' . chr(10);
            
            $CLSU = new CLSU($data->id);
            
            $xml .= '	<title>' . Fonction_Article::get_titre($data->id) . '</title>' . chr(10);
            if ($Xml_global) {
                $xml .= '	<link>http://' . $_SERVER['HTTP_HOST'] . '/' . Fonction_Page::get_lien($data->id_page) . '#' . $data->id . '</link>' . chr(10);
                $xml .= '	<guid>http://' . $_SERVER['HTTP_HOST'] . '/' . Fonction_Page::get_lien($data->id_page) . '#' . $data->id . '</guid>' . chr(10);
            } else {
                $xml .= '	<link>http://' . $_SERVER['HTTP_HOST'] . '/' . Fonction_Page::get_lien($data->id_page) . '#' . $data->id . '</link>' . chr(10);
                $xml .= '	<guid isPermaLink="false">http://' . $_SERVER['HTTP_HOST'] . '/' . Fonction_Page::get_lien($data->id_page) . '#' . $data->id . '</guid>' . chr(10);
            }
            $xml .= '	<description><![CDATA[ ' . $CLSU->retour() . ' ]]></description>' . chr(10);
            $xml .= '	<pubDate>' . gmdate("D, d M Y h:i:s", strtotime($data->datepubli)) . ' GMT</pubDate>' . chr(10);
            $xml .= '</item>' . chr(10); 
        }

        $xml .="</channel>" . chr(10);
        $xml .="</rss>" . chr(10);
        echo $entete_rss . $xml;
    }

}
function lien_internes($article,$lien){
        return str_replace($lien,"http://".$_SERVER['HTTP_HOST']."/".$lien,$article);
}

?>
