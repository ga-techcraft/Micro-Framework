<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// 例外を有効にして PHPMailer を起動します。
$mail = new PHPMailer(true);

try {
    // サーバの設定
    $mail->isSMTP();                                      // SMTPを使用するようにメーラーを設定します。
    $mail->Host       = 'smtp.gmail.com';                 // GmailのSMTPサーバ
    $mail->SMTPAuth   = true;                             // SMTP認証を有効にします。
    $mail->Username   = 'ga.techcraft@gmail.com';   // SMTPユーザー名
    $mail->Password   = 'nhpi kzca xkss tucu';                  // SMTPパスワード
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // 必要に応じてTLS暗号化を有効にします。
    $mail->CharSet    = 'UTF-8';
    $mail->Encoding   = 'base64';
    $mail->Port       = 587;                              // 接続先のTCPポート

    // 受信者
    $mail->setFrom('ga.techcraft@gmail.com', 'Micro-Frame-App'); // 送信者設定
    $mail->addAddress('ykjustin007@gmail.com', 'My User');          // 受信者を追加します。

    $mail->Subject = '【Micro-Frame-App】テストメールの送信確認です';
    
    // HTMLコンテンツ
    $mail->isHTML(true); // メール形式をHTMLに設定します。
    ob_start();
    include(__DIR__ .'/../Views/component/mail.php');
    $mail->Body = ob_get_clean();

    // $mail->Body = '<h1>こんにちは</h1><p>これはHTMLメールです。</p>';

    // 本文は、相手のメールプロバイダーがHTMLをサポートしていない場合に備えて、シンプルなテキストで構成されています。
    $mail->AltBody = file_get_contents(__DIR__ .'/../Views/component/mail.txt');

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}