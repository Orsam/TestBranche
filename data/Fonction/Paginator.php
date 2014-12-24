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
 * @subpackage Paginator
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Fonction_Paginator est une classe qui permet la gestion de la pagination
 * Utilisation :
 * $pages = new Fonction_Paginator;
 * $pages->page_php       = $Nompage;
 * $pages->items_per_page = $Per_Page;
 * $pages->items_total    = Article::nbr_article_annee(NUM_PAGE,$AnneeActuelle);
 * $pages->mid_range      = 3;
 * $pages->paginate();
 * echo $pages->display_pages();

 * @author Olivier
 */

class Fonction_Paginator {
    /**
    *
    * @var type 
    */
    var $items_per_page;
    var $items_total;
    var $page_php;
    var $current_page;
    var $num_pages; // Nombre de page
    var $mid_range;
    var $low;
    var $high;
    var $limit;
    var $return;
    var $default_ipp = 10; // Default Item Per Page
    var $querystring;

    function Paginator() {
        $this->current_page = 1;
        $this->mid_range = 7;
        $this->items_per_page = (!empty($_GET['ipp'])) ? $_GET['ipp'] : $this->default_ipp;
    }

    function paginate() {
        if ($_GET['ipp'] == 'All') {
            $this->num_pages = ceil($this->items_total / $this->default_ipp);
            $this->items_per_page = $this->default_ipp;
        } else {
            if (!is_numeric($this->items_per_page) OR $this->items_per_page <= 0)
                $this->items_per_page = $this->default_ipp;
            $this->num_pages = ceil($this->items_total / $this->items_per_page);
        }
        $this->current_page = (int) $_GET['p']; // must be numeric > 0
        if ($this->current_page < 1 Or !is_numeric($this->current_page))
            $this->current_page = 1;
        if ($this->current_page > $this->num_pages)
            $this->current_page = $this->num_pages;
        $prev_page = $this->current_page - 1;
        $next_page = $this->current_page + 1;

        if ($_GET) {
            $args = explode("&", $_SERVER['QUERY_STRING']);
            foreach ($args as $arg) {
                $keyval = explode("=", $arg);
                if ($keyval[0] != "p" And $keyval[0] != "ipp")
                    $this->querystring .= "&" . $arg;
            }
        }

        if ($_POST) {
            foreach ($_POST as $key => $val) {
                if ($key != "p" And $key != "ipp")
                    $this->querystring .= "&$key=$val";
            }
        }

        if ($this->num_pages > 10) {
            $this->return = ($this->current_page != 1 And $this->items_total >= 10) ? "<li><a href=\"".$this->page_php."?p=$prev_page&ipp=$this->items_per_page$this->querystring\">&laquo; Previous</a></li>\n" : "\n";

            $this->start_range = $this->current_page - floor($this->mid_range / 2);
            $this->end_range = $this->current_page + floor($this->mid_range / 2);

            if ($this->start_range <= 0) {
                $this->end_range += abs($this->start_range) + 1;
                $this->start_range = 1;
            }
            if ($this->end_range > $this->num_pages) {
                $this->start_range -= $this->end_range - $this->num_pages;
                $this->end_range = $this->num_pages;
            }
            $this->range = range($this->start_range, $this->end_range);

            for ($i = 1; $i <= $this->num_pages; $i++) {
                if ($this->range[0] > 2 And $i == $this->range[0])
                    $this->return .= "<span class=\"couleur1\"> ... </span>";
                // loop through all pages. if first, last, or in range, display
                if ($i == 1 Or $i == $this->num_pages Or in_array($i, $this->range)) {
                    $this->return .= ($i == $this->current_page And $_GET['page'] != 'All') ? "<li class=\"couleur1\">[$i]</li> " : "<li><a title=\"Go to page $i of $this->num_pages\" href=\"".$this->page_php."?p=$i&ipp=$this->items_per_page$this->querystring\">[$i]</a></li>\n";
                }
                if ($this->range[$this->mid_range - 1] < $this->num_pages - 1 And $i == $this->range[$this->mid_range - 1])
                    $this->return .= "<span class=\"couleur1\"> ... </span>";
            }
            $this->return .= (($this->current_page != $this->num_pages And $this->items_total >= 10) And ($_GET['page'] != 'All')) ? "<li><a href=\"".$this->page_php."?p=$next_page&ipp=$this->items_per_page$this->querystring\">Next &raquo;</a></li>\n" : "\n";
            //$this->return .= ($_GET['page'] == 'All') ? "<a class=\"current\" style=\"margin-left:10px\" href=\"#\">All</a> \n" : "<a class=\"paginate\" style=\"margin-left:10px\" href=\"more-resources-videos.php?page=1&ipp=All$this->querystring\">All6</a> \n";
        }
        else {
            for ($i = 1; $i <= $this->num_pages; $i++) {
                $this->return .= ($i == $this->current_page) ? "<li class=\"couleur1\">[$i]</li>\n" : "<li><a href=\"".$this->page_php."?p=$i&ipp=$this->items_per_page$this->querystring\">[$i]</a></li>\n";
            }
            //$this->return .= "<a class=\"paginate\" href=\"".$this->page_php."?page=1&ipp=All$this->querystring\">All</a> \n";
        }
        $this->low = ($this->current_page - 1) * $this->items_per_page;
        $this->high = ($_GET['ipp'] == 'All') ? $this->items_total : ($this->current_page * $this->items_per_page) - 1;
        $this->limit = ($_GET['ipp'] == 'All') ? "" : " LIMIT $this->low,$this->items_per_page";
    }

    function display_items_per_page() {
        $items = '';
        $ipp_array = array(10, 25, 50, 100, 'All');
        foreach ($ipp_array as $ipp_opt)
            $items .= ($ipp_opt == $this->items_per_page) ? "<option selected value=\"$ipp_opt\">$ipp_opt</option>\n" : "<option value=\"$ipp_opt\">$ipp_opt</option>\n";
        return "<span class=\"paginate\">Items per page:</span><select class=\"paginate\" onchange=\"window.location='".$this->page_php."?p=1&ipp='+this[this.selectedIndex].value+'$this->querystring';return false\">$items</select>\n";
    }

    function display_jump_menu() {
        for ($i = 1; $i <= $this->num_pages; $i++) {
            $option .= ($i == $this->current_page) ? "<option value=\"$i\" selected>$i</option>\n" : "<option value=\"$i\">$i</option>\n";
        }
        return "<span class=\"paginate\">Page:</span><select class=\"paginate\" onchange=\"window.location='".$this->page_php."?p='+this[this.selectedIndex].value+'&ipp=$this->items_per_page$this->querystring';return false\">$option</select>\n";
    }

    function display_pages() {
        return ($this->num_pages==1) ? '' : $this->return;
    }

}