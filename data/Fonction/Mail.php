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
 * @subpackage Mail
 * @author     Olivier Regard
 * @copyright  Copyright (c) 2005-2012 Neuro-Graph. (http://www.neuro-graph.fr)
 * @version    Version 4.0
 */

/**
 * Description de Mail
 *
 * @author Olivier
 */
class Fonction_Mail extends Plugin_Mail_PHPMailer {

    
    public static function send_mail($ConfMail, $CorpsMail) {
        global $Config;
        $Template = '';
        $ConfMail = array2object($ConfMail);
        $IsHTML   = (!isset($ConfMail->IsHTML)) ? true : $ConfMail->IsHTML;
        $ExtFile  = ($IsHTML) ? "tpl" : "txt";
        
        $inF = fopen(TEMPLATES_PATH . "/$ConfMail->fichier_tpl.$ExtFile", "r");
        while (!feof($inF)) {
            $Template .= fgets($inF, 4096);
        }
        fclose($inF);
        foreach ($CorpsMail as $key => $value) {
            $Template = str_replace("|$key|", $value, $Template);
        }
        
        if(!$IsHTML){
            $Template = str_replace('<br/>', chr(10), $Template);
            $Template = str_replace('<br>', chr(10), $Template);
            $Template = str_replace('<strong>', '', $Template);
            $Template = str_replace('</strong>', '', $Template);
            $Template = strip_tags($Template,'<a>');
        }
        
        // Nettoyage des balise non interpretées
        $Template = preg_replace('/\|[\w]+\|/', '', $Template);     
        $mail = new Plugin_Mail_PHPMailer();
        $mail->IsSMTP();
        $mail->IsHTML($IsHTML);
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = false;
        $NomDeExpediteur = (!isset($ConfMail->nom_expediteur) || trim($ConfMail->nom_expediteur) == '') ? trim($Config->nom_expediteur) : trim($ConfMail->nom_expediteur);

        $mail->FromName = $NomDeExpediteur; //"Support Stephane Lehr"; // nom expediteur
        $mail->Username = $Config->user_mail; //"form@concept-et-realisation.fr"; //user mail
        $mail->Password = $Config->password_mail; //"neuro"; // password_mail
        $mail->Host     = $Config->host_mail; //'smtp.concept-et-realisation.fr'; //host_mail

        $MailExpediteur = (!isset($ConfMail->mail_expediteur) || trim($ConfMail->mail_expediteur) == '') ? trim($Config->mail_expediteur) : trim($ConfMail->mail_expediteur);
        $mail->From     = $MailExpediteur; //"no-reply@neuro-graph.com";  // mail expediteur
        
        if(isset($ConfMail->ConfirmReadingTo)){
            $mail->ConfirmReadingTo = $MailExpediteur;
        }
        if(isset($ConfMail->ReplyTo)){
            $mail->ReplyTo = $Config->ReplyTo;
        }
        $mail->Subject  = "[" . $Config->prefixe_objet . "] " . $ConfMail->objet_mail;
        if(isset($ConfMail->files)){
            foreach ($ConfMail->files as $value) {
                $ArrayAttac = explode("|", $value);
                $mail->AddAttachment($ArrayAttac[0], $ArrayAttac[1]);
            }
        }
        
        if (!empty($ConfMail->mails_destinataires)) {
            if (is_array($ConfMail->mails_destinataires)) {
                foreach ($ConfMail->mails_destinataires as $value) {
                    $mail->AddAddress($value); // Destinataire    
                }
            } else {
                $mail->AddAddress($ConfMail->mails_destinataires); // Destinataire    
            }
        }
        if (!empty($ConfMail->mails_destinataires_cc)) {
            if (is_array($ConfMail->mails_destinataires_cc)) {
                foreach ($ConfMail->mails_destinataires_cc as $value) {
                    $mail->AddCC($value); // Destinataire    
                }
            } else {
                $mail->AddCC($ConfMail->mails_destinataires_cc); // Destinataire    
            }
        }
        if (!empty($ConfMail->mails_destinataires_cci)) {
            if (is_array($ConfMail->mails_destinataires_cci)) {
                foreach ($ConfMail->mails_destinataires_cci as $value) {
                    $mail->AddBCC($value); // Destinataire    
                }
            } else {
                $mail->AddBCC($ConfMail->mails_destinataires_cci); // Destinataire    
            }
        }

        $mail->Body = stripslashes($Template);
        if (!$mail->Send()) {
            $mail->SmtpClose();
            unset($Template);
            return false;
        } else {
            $mail->SmtpClose();
            unset($Template);
            return true;
        }
    }

}
