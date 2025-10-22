<?php
/**
 * \file  email.class.php
 * @brief Fichier de la classe d'envoi de mail avec phpmailer
 */
/**
 * @brief CLASSE D'ENVOI MAIL
 *
 * @version 2.2002 - Ajout de la fonction de test de mail
 * @version 2.1902 - Ajout de la résolution des certificats
 * @version 2.1812 - Ajout de la fonction setCopiesCacheesA()
 * @version 2.1804 - Utilisation de la classe statique Config
 * @version 1.1803 - Utilisation de PHPMailer 6.0
 *
 * @author David SENSOLI - <dsensoli@gmail.com>
 * @copyright (c) 2018, www.DavidSENSOLI.com
 */

namespace Appy\Src;

/**
 * Import PHPMailer classes into the global namespace
 * These must be at the top of your script, not inside a function 
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{
    static private $arr_destinataires;
    static private $arr_copies;
    static private $arr_copies_cachees;
    static private $subject;
    static private $html;

    static public function envoi($debug = 0)
    {

        try {

            // Mode démo: pas d'envoi réel si mail inactif
            if (!Config::mailActive()) {
                // En mode démo, on simule l'envoi pour l'UX et on loggue un message explicite
                if (Config::DEBUG) {
                    error_log("MODE DEMO - Envoi d'email désactivé. Aucun mail n'a été expédié.");
                }
                return true;
            }

            if (Config::mailActive()) {

                $mail = new PHPMailer(true);                  // Passing `true` enables exceptions
                // To load the French version
                $mail->setLanguage('fr', 'vendor/phpmailer/phpmailer/language/');

                /** Server settings */
                $mail->SMTPDebug  = 0;                        // Enable verbose debug output
                $mail->CharSet    = 'UTF-8';
                $mail->isSMTP();                              // Set mailer to use SMTP
                $mail->Host       = Config::mailHost();       // Specify main and backup SMTP servers
                $mail->SMTPAuth   = true;                     // Enable SMTP authentication
                $mail->Username   = Config::mailUsername();   // SMTP username
                $mail->Password   = Config::mailPassword();   // SMTP password
                $mail->SMTPSecure = Config::mailSecure();     // Enable TLS encryption, `ssl` also accepted
                $mail->Port       = Config::mailPort();       // TCP port to connect to

                /** To solve certificate problem (tried with and without)  */
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true)
                );

                /** Recipients */
                $mail->setFrom(Config::mailFrom(), Config::APPLI_NOM);

                foreach (self::$arr_destinataires as $destinataire) {
                    $mail->AddAddress($destinataire[0], $destinataire[1]);
                }

                if (!empty(self::$arr_copies)) {
                    foreach (self::$arr_copies as $copie) {
                        $mail->AddCC($copie[0], $copie[1]);
                    }
                }

                if (!empty(self::$arr_copies_cachees)) {
                    foreach (self::$arr_copies_cachees as $copie) {
                        $mail->addBCC($copie[0], $copie[1]);
                    }
                }

                /** Attachments */
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // Optional name

                /** Content */
                $mail->IsHTML(true); // Set email format to HTML
                $mail->Body    = self::$html;
                $mail->Subject = self::$subject;

                /** Envoi du mail */
                if (!$mail->send()) {
                    throw new Exception("Erreur dans la fonction send() de PHPMailer : \n".$mail->ErrorInfo);
                }

                return TRUE;
            }
        } catch (phpmailerException $e) {
            throw new Exception("Erreur dans la classe Email : \n".$e->errorMessage());
        }
    }

    static public function setHtml($html)
    {
        self::$html = $html;
    }

    static public function setSubject($subject)
    {
        self::$subject = $subject;
    }

    /**
     * Définit les destinataires du mail
     *
     * Besoin d'un array au format array(array("E-mail1", "NOM1 Prénom1"), array("E-mail2", "NOM2 Prénom2"))
     *
     * @param Array $arr_destinataires
     */
    static public function setDestinataires($arr_destinataires)
    {
        self::$arr_destinataires = $arr_destinataires;
    }

    /**
     * Définit les "Copies A" du mail
     *
     * Besoin d'un array au format array(array("E-mail1", "NOM1 Prénom1"), array("E-mail2", "NOM2 Prénom2"))
     *
     * @param Array $arr_copies
     */
    static public function setCopiesA($arr_copies)
    {
        self::$arr_copies = $arr_copies;
    }

    /**
     * Définit les "Copies cachées à" du mail
     *
     * Besoin d'un array au format array(array("E-mail1", "NOM1 Prénom1"), array("E-mail2", "NOM2 Prénom2"))
     *
     * @param Array $arr_copies_cachees
     */
    static public function setCopiesCacheesA($arr_copies_cachees)
    {
        self::$arr_copies_cachees = $arr_copies_cachees;
    }

    static public function test()
    {
        try {

            self::setDestinataires([
                ["dsensoli@gmail.com", "David SENSOLI"],
            ]);

            self::setHtml("Essai de mail !");
            self::setSubject("Sujet du mail");

            if (self::envoi(4)) {
                echo "Le mail de test est parti !";
            } else {
                echo "Le mail de test n'a pas pu être envoyé...";
            }
        } catch (Exception $e) {
            throw new Exception("Erreur dans la fonction ".__METHOD__." de la classe ".__CLASS__." !<br/>".$e->getMessage()."");
        }
    }
}
