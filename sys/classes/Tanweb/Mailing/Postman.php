<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Mailing;

use Tanweb\Mailing\EmailConfig as EmailConfig;
use Tanweb\Mailing\Email as Email;
use Tanweb\Mailing\PostmanException as PostmanException;

/**
 * Adapter of PHPMailer and class that allows easly send emails, configure in config.ini
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Postman {
    private EmailConfig $config;
    private array $emails;
    private PHPMailer $mailer;
    
    public function __construct() {
        $this->config = new EmailConfig();
        $this->emails = array();
        $this->initMailer();
    }
    
    private function initMailer() : void{
        $mailer = new PHPMailer();
        $mailer->IsSMTP();
        $mailer->CharSet= "UTF-8";
        $mailer->Mailer = "smtp";
        
        $mailer->SMTPAuth   = TRUE;
        $mailer->SMTPSecure = "tls";
        $mailer->Port       = $this->config->getPort();
        $mailer->Host       = $this->config->getHost();
        $mailer->Username   = $this->config->getUser();
        $mailer->Password   = $this->config->getPass();
        $this->mailer = $mailer;
    }
    
    public function addEmail(Email $email) : void{
        $this->emails[] = $email;
    }
    
    public function send() : array{
        $responses = array();
        foreach ($this->emails as $mail){
            $msg = $this->sendMail($mail);
            $responses[] = $msg;
        }
        return $responses;
    }
    
    private function sendMail(Email $mail) : string{
        $this->prepareMailer($mail);
        
        if(!$this->mailer->Send()) {
            $this->throwException('Error in sending mail to: ' . $mail->getAddress());
        } else {
            return "Email sent successfully";
        }
    }
    
    private function prepareMailer(Email $mail) : void{
        $this->mailer->clearAllRecipients();
        
        $isHTML = $mail->getIsHTML();
        $this->mailer->IsHTML($isHTML);
        
        $address = $mail->getAddress();
        $this->mailer->AddAddress($address);
        
        $sourceMail = $this->config->getAddress();
        $display = $this->config->getDisplayUser();
        $this->mailer->SetFrom($sourceMail, $display);
        
        $title = $mail->getTitle();
        $this->mailer->Subject = $title;
        
        $contents = $mail->getContents();
        $this->mailer->Body  = $contents;
    }
    
    
    private function throwException($msg){
        throw new PostmanException($msg);
    }
}
