<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    protected $email;
    protected $nombre;
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
         //crear el objeto de email
        $mail = new PHPMailer();

        //configurar SMTP
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '730d47b8d588a8';
        $mail->Password = 'd03af919db757d';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'UpTask.com');
        $mail->Subject = "Confirmación de cuenta";

        //set html
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p>Hola <strong>" . $this->nombre . "</strong>. Has creado una cuenta en UpTask, solo debes confirmarla presionando el siguiente enlace.</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/confirmar?token=" . $this->token . "'>Confirmar cuenta</a></p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar este mensaje</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;

        //enviar mail
        $mail->send();
    }

    public function enviarInstrucciones() {
         //crear el objeto de email
        $mail = new PHPMailer();

        //configurar SMTP
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '730d47b8d588a8';
        $mail->Password = 'd03af919db757d';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'UpTask.com');
        $mail->Subject = "Reestable tu contraseña | UpTask";

        //set html
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p>Hola <strong>" . $this->nombre . "</strong>. Has solicitado reestablecer tu contraseña en UpTask, solo debes hacerlo presionando el siguiente enlace.</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/reestablecer?token=" . $this->token . "'>Reestablecer Contraseña</a></p>";
        $contenido .= "<p>Si tu no solicitaste reestablecer contraseña, puedes ignorar este mensaje</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;

        //enviar mail
        $mail->send();
    }
}