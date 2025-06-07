<?php
class EmailSender {
    public static function enviar($destinatario, $assunto, $mensagem) {
        $headers = "From: loja@exemplo.com\r\nContent-Type: text/html; charset=UTF-8";
        return mail($destinatario, $assunto, $mensagem, $headers);
    }
}

