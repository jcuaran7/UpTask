<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    protected $email;
    protected $nombre;
    protected $apellido;
    protected $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    private function configurarServidor(PHPMailer $mail) {
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
    }

    private function enviarEmail($asunto, $htmlCuerpo, $altCuerpo) {
        $mail = new PHPMailer(true);

        try {
            $this->configurarServidor($mail);

            // Configurar remitente y destinatario
            $mail->setFrom('jmcuaran.5618@gmail.com', 'UpTask');
            $mail->addAddress($this->email, $this->nombre); // El destinatario es el usuario

            $mail->Subject = $asunto;
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Body = $htmlCuerpo;
            $mail->AltBody = $altCuerpo;

            // Enviar el correo
            return $mail->send();
        } catch (Exception $e) {
            error_log("Error al enviar el correo: {$mail->ErrorInfo}");
            return false;
        }
    }

    public function enviarConfirmacion() {
        $asunto = 'Confirma tu Cuenta';
        $htmlCuerpo = "<html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        width: 90%;
                        max-width: 600px;
                        margin: 20px auto;
                        background: white;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                    }
                    .header {
                        text-align: center;
                        padding-bottom: 20px;
                        border-bottom: 2px solid #ddd;
                    }
                    .header h2 {
                        color: #333;
                    }
                    .content {
                        text-align: center;
                        padding: 20px;
                    }
                    .content p {
                        font-size: 16px;
                        color: #555;
                    }
                    .btn {
                        display: inline-block;
                        padding: 12px 20px;
                        margin-top: 20px;
                        font-size: 16px;
                        color: white !important;
                        background-color: #28a745;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                    .footer {
                        text-align: center;
                        padding-top: 20px;
                        font-size: 12px;
                        color: #777;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Confirma tu Cuenta</h2>
                    </div>
                    <div class='content'>
                        <p><strong>Hola " . $this->nombre . " " . $this->apellido ."</strong></p>
                        <p>Has creado tu cuenta en UpTask. Para activarla, confirma tu correo haciendo clic en el botón:</p>
                        <a class='btn' href='" . $_ENV['APP_URL'] . "/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a>
                        <p>Si no solicitaste esta cuenta, puedes ignorar este mensaje.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " Uptask. Todos los derechos reservados.</p>
                    </div>
                </div>
            </body>
        </html>";
    
        $altCuerpo = "Hola " . $this->nombre . ",\n\nHas creado tu cuenta en UpTask. Confirma tu correo en el siguiente enlace:\n" . $_ENV['APP_URL'] . "/confirmar-cuenta?token=" . $this->token . "\n\nSi no solicitaste esta cuenta, ignora este mensaje.";
    
        return $this->enviarEmail($asunto, $htmlCuerpo, $altCuerpo);
    }
    

    public function enviarInstrucciones() {
        $asunto = 'Restablece tu Contraseña';
        $htmlCuerpo = "<html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f4;
                        margin: 0;
                        padding: 0;
                    }
                    .container {
                        width: 90%;
                        max-width: 600px;
                        margin: 20px auto;
                        background: white;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                    }
                    .header {
                        text-align: center;
                        padding-bottom: 20px;
                        border-bottom: 2px solid #ddd;
                    }
                    .header h2 {
                        color: #333;
                    }
                    .content {
                        text-align: center;
                        padding: 20px;
                    }
                    .content p {
                        font-size: 16px;
                        color: #555;
                    }
                    .btn {
                        display: inline-block;
                        padding: 12px 20px;
                        margin-top: 20px;
                        font-size: 16px;
                        color: white !important;
                        background-color: #007BFF;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                    .footer {
                        text-align: center;
                        padding-top: 20px;
                        font-size: 12px;
                        color: #777;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Restablece tu Contraseña</h2>
                    </div>
                    <div class='content'>
                        <p><strong>Hola " . $this->nombre . "</strong>,</p>
                        <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente botón para hacerlo:</p>
                        <a class='btn' href='" . $_ENV['APP_URL'] . "/reestablecer?token=" . $this->token . "'>Restablecer Contraseña</a>
                        <p>Si no solicitaste este cambio, ignora este mensaje.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " UpTask. Todos los derechos reservados.</p>
                    </div>
                </div>
            </body>
        </html>";
    
        $altCuerpo = "Hola " . $this->nombre . ",\n\nHas solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para hacerlo:\n" . $_ENV['APP_URL'] . "/recuperar?token=" . $this->token . "\n\nSi no solicitaste este cambio, ignora este mensaje.";
    
        return $this->enviarEmail($asunto, $htmlCuerpo, $altCuerpo);
    }
    
}
