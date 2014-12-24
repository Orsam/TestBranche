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
 * @subpackage Facture
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */
$Path = dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'Class';
$Path.= DIRECTORY_SEPARATOR.'Outils'.DIRECTORY_SEPARATOR.'PDF'.DIRECTORY_SEPARATOR.'Invoice.php';
require_once($Path);

class Fonction_Facture extends Invoice {
    private $_PdfPath;  
    private $_pdf; 
    private $_y = 109;
    private $_size;
    private $_num_page;
    
    private $_societe_nom;
    private $_societe_adresse;
    
    private $_num_facture_intitule;
    private $_num_facture_num_facture;
    
    private $_date_facture_date;
    
    private $_TarifJour;
    private $_client_societe;
    private $_client_code;
    private $_DB;
    
    
    function __construct() {
        $this->_PdfPath = dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'Class';
        $this->_PdfPath .= DIRECTORY_SEPARATOR.'Outils'.DIRECTORY_SEPARATOR.'PDF'.DIRECTORY_SEPARATOR;
        require_once($this->_PdfPath.'fpdf.php');
        require_once($this->_PdfPath.'Invoice.php');
        $host     = 'mysql.neuro-graph.fr';
        $basename = 'neurographfr1';
        $user     = 'neurograph';
        $password = 'moKK2mvVEWKK';
        $this->_DB = new PDO('mysql:host=' . $host . ';dbname=' . $basename, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        $this->_DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->_pdf = new Invoice( 'P', 'mm', 'A4' );

        // Couleurs du cadre, du fond et du texte
        $this->_pdf->SetDrawColor($this->_color_r,$this->_color_g,$this->_color_b);
        $this->_pdf->SetFillColor($this->_color_r,$this->_color_g,$this->_color_b);
        $this->_pdf->SetTextColor($this->_color_r,$this->_color_g,$this->_color_b);
    }
    public function addpage(){
        $this->_num_page += 1;
        $this->_pdf->AddPage();
        self::MyHeader();
    }
    function TauxTVA($taux){
        $this->_pdf->_TauxTVA = $taux;
    }
    function TarifJour($tarif){
        $this->_TarifJour = $tarif; 
    }
    public function societe($nom, $adresse) {
        $this->_societe_nom      = $nom;
        $this->_societe_adresse = $adresse;
        $this->_pdf->addSociete($nom,$adresse);
    }
    public function type_document($intitule){
        //201201-02
      if($intitule=='Facture'){
          $vue = "vue_factures";
      }else{
          $vue = "vue_devis";
      }
        $sql = "SELECT SUBSTRING_INDEX( `numero` , '-', -1 )+1 AS NextFacture FROM `$vue` ORDER BY NextFacture DESC LIMIT 1";
        $reqS = $this->_DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);

        if ($reqS->rowCount() > 0) {

            $num_facture = date("Ym", time()).'-'.str_pad($data->NextFacture, 2, '0', STR_PAD_LEFT);;
        }else{
            $num_facture = date("Ym", time()).'-01';
        }        
        
        
        $num_facture = strtoupper(substr($intitule, 0, 1)).$num_facture;
        $this->_num_facture_intitule         = $intitule;
        $this->_num_facture_num_facture = $num_facture;
        $this->_pdf->fact_dev( $intitule, $num_facture );

        try{
            $d = array($num_facture, $this->_client_code);
            $reqI = $this->_DB->prepare('INSERT INTO `Factures` (`numero`,`client_code`) VALUES (?,?);');
            $reqI->execute($d);
        }  catch (Exception $e){
            echo "$num_facture : cette facture existe déjà";
            exit();
        }
        
    }
    public function filigrane($texte){
        $this->_pdf->temporaire($texte);
    }
    public function date_facture($date){
        $this->_date_facture_date = $date;
        $this->_pdf->addDate($date);
    }
    public function num_page($num_page){
        $this->_pdf->addPageNumber($num_page);
    }
    public function client($societe, $adresse){
        $code_client = strtoupper(substr(str_replace(' ','',$societe), 0, 5));

        $sql = "SELECT * FROM `Clients` WHERE `client_code`='" . $code_client."'";
        $reqS = $this->_DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);
         if ($reqS->rowCount() > 0) {
             echo "Erreur, le code client existe deja";
             exit();
        }else{
            $societe = strtoupper($societe);
            $this->_client_societe=$societe;
            $this->_pdf->addClient($code_client);
            $this->_client_code=$code_client;
            $this->_pdf->addClientAdresse($societe,$adresse);
            
            $d = array($societe,  $code_client, $adresse);
            $reqI = $this->_DB->prepare('INSERT INTO `Clients` (`client_societe` ,`client_code` ,`client_adresse`) VALUES (?,?,?);');
            $reqI->execute($d);
            
        }

        
    }
    public function code_client($code_client){
        $code_client = strtoupper(substr(str_replace(' ','',$code_client), 0, 5));
        $sql = "SELECT * FROM `Clients` WHERE `client_code`='" . $code_client."'";
        $reqS = $this->_DB->query($sql);
        $data = $reqS->fetch(PDO::FETCH_OBJ);
        $this->_client_societe=$data->client_societe;
        $this->_client_code=$code_client;
        $this->_pdf->addClientAdresse($data->client_societe,$data->client_adresse);
        $this->_pdf->addClient($code_client);
    }
    
    public function autre(){
        $this->_pdf->addPageNumber($this->_num_page);
        $this->_pdf->addReglement("Chèque à réception de facture");
        $this->_pdf->addVirement("Chèque à réception de facture");
        $this->_pdf->addEcheance("03/12/2003");
        $this->_pdf->addNumTVA("FR888777666");
        $this->_pdf->addReference("Dévis ... du ....");
        $cols=array("DESIGNATION"  => 112, "JOUR" => 22, "P.U. HT" => 26, "MONTANT H.T." => 30);
        $this->_pdf->addCols($cols);
        $cols=array("DESIGNATION"  => "L", "JOUR" => "R", "P.U. HT" => "R", "MONTANT H.T." => "R");
        $this->_pdf->addLineFormat($cols);
        $this->_pdf->addLineFormat($cols);
    }
    public function designation($quantite,$libelle){
        if ($this->_y>=244){
            $this->_y = 109;
            $this->addpage();
            self::societe($this->_societe_nom,  $this->_societe_adresse);
            self::type_document($this->_num_facture_intitule);
            self::date_facture($this->_date_facture_date);
            self::autre();
        }
        
        $PUHT = $this->_TarifJour;
        $MHT  = $quantite*$PUHT;
        $line = array( "DESIGNATION"  => $libelle,  "JOUR"     => $quantite);
        $line['P.U. HT'] = number_format($PUHT, 2 , "," , " " );
        $line['MONTANT H.T.'] = number_format($MHT, 2 , "," , " " );
        $this->_TotalHT += $MHT;
        
 
        $this->_size = $this->_pdf->addLine($this->_y, $line );
        $this->_y   += $this->_size + 2;
        
        $d = array($this->_num_facture_num_facture,  $libelle, $quantite, $PUHT, $MHT);
        $reqI = $this->_DB->prepare('INSERT INTO `Factures_Detail` (`facture_numero` ,`designation` ,`quantite` ,`puht` ,`tht`) VALUES (?,?,?,?,?);');
        $reqI->execute($d);
        

    }
    public function remise($pourcentage,$libelle='Remise'){
        
        $MHT  = ($pourcentage/100)*$this->_TotalHT;
        $this->_TotalHT -= $MHT;
        $this->_TotalTTC  = $this->_TotalHT*(($this->_TauxTVA/100)+1);
        $this->_TotalTVA = $this->_TotalHT*($this->_TauxTVA/100);
        
        $line = array( "DESIGNATION"  => $libelle,  "JOUR" => $pourcentage.' %', 'P.U. HT'=>0, 'MONTANT H.T.'=>0);
        $line['MONTANT H.T.'] = '-'.number_format($MHT, 2 , "," , " " );
        $this->_size = $this->_pdf->addLine($this->_y, $line);
        $this->_y   += $this->_size + 2;

        $d = array($this->_num_facture_num_facture,$libelle,  -$MHT);
        $reqI = $this->_DB->prepare('INSERT INTO `Factures_Detail` (`facture_numero` ,`designation`,`tht`) VALUES (?,?,?);');
        $reqI->execute($d);

        // F F --Ligne Droite -- Droite Hauteur
//        $this->_pdf->Line(170, $this->_y-2, 200, $this->_y-2); 
//        $line = array( "DESIGNATION"  => 0,  "QUANTITE" => 0, 'P.U. HT'=>0, 'MONTANT H.T.'=>0);
//        $line['MONTANT H.T.'] = number_format($this->_TotalHT, 2 , "," , " " );
//        $this->_size = $this->_pdf->addLine($this->_y, $line );
//        $this->_y   += $this->_size + 2;
        
    }
    
    function lignevide(){
        $line = array( "DESIGNATION"  => 0,  "JOUR" => 0, 'P.U. HT'=>0, 'MONTANT H.T.'=>0);
        $this->_size = $this->_pdf->addLine($this->_y, $line );
        $this->_y   += $this->_size + 2;
    } 
    
    function MyHeader(){
        // Logo
        $this->_pdf->Image('logoNeuro1.png',10,6);
    }
    
    
    function __destruct() {
        $chemin = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'tempo'.DIRECTORY_SEPARATOR;
        
        //$this->_pdf->MyFooter($remarque);
        $this->_pdf->addCadreEurosFrancs($this->_TotalHT);
        $this->_pdf->MyFooter('Olivier Regard\nLa ville du bois - 18160 Saint Baudel - Téléphone : 02.46.65.03.16 - Mail : olivier.regard@neuro-graph.com');

//        $d = array($this->_date_facture_date,  $this->_client_code, $this->_num_facture_intitule,$this->_num_facture_num_facture,$this->_TotalHT,$this->_pdf->_TotalTVA,$this->_pdf->_TotalTTC);
//        $reqI = $this->_DB->prepare('INSERT INTO `Factures` (`date` ,`client_code` ,`type` ,`numero` ,`TotalHT` ,`TotalTVA` ,`TotalTTC`) VALUES (?,?,?,?,?,?,?);');
//        $reqI->execute($d);

        $d = array($this->_date_facture_date,  $this->_client_code, $this->_num_facture_intitule,$this->_TotalHT,$this->_pdf->_TotalTVA,$this->_pdf->_TotalTTC,$this->_num_facture_num_facture);
        $reqU = $this->_DB->prepare('UPDATE `Factures` SET date=?,client_code=?, type=?, TotalHT=?,TotalTVA=?, TotalTTC=?  WHERE numero=? LIMIT 1');
        $reqU->execute($d);
        
//        $d = array($this->_client_societe,  $this->_client_code, "M. Philippe Facon\n29 / 31, boulevard de la Muette\n95140 Garges les Gonesse");
//        $reqI = $this->_DB->prepare('INSERT INTO `Clients` (`client_societe` ,`client_code` ,`client_adresse`) VALUES (?,?,?);');
//        $reqI->execute($d);
//
        
        $chemin= dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'olivier';
        $chemin .= DIRECTORY_SEPARATOR;
        
        
    
        
     //$this->_pdf->Output($chemin.$this->_num_facture_num_facture.'.pdf');
        $this->_pdf->Output();
        
    }
        
}

?>
