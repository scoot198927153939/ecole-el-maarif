<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeSetPassword extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        if ($notifiable->locale === 'fr') {
            return (new MailMessage)
                ->subject('Définissez votre mot de passe')
                ->greeting('Bonjour ' . $notifiable->name . ',')
                ->line('Cliquez sur le bouton ci-dessous pour définir le mot de passe de votre compte sur ' . config('app.name') . '.')
                ->action('Définir mon mot de passe', $url)
                ->line('Ce lien expirera dans 60 minutes.')
                ->line('Si vous ne vous attendiez pas à ce message, vous pouvez l\'ignorer.');
        }

        return (new MailMessage)
            ->subject('تعيين كلمة مرور حسابك')
            ->greeting('مرحباً ' . $notifiable->name . '،')
            ->line('اضغط الزر أدناه لتعيين كلمة مرور حسابك في ' . config('app.name') . '.')
            ->action('تعيين كلمة المرور', $url)
            ->line('هذا الرابط صالح لمدة 60 دقيقة.')
            ->line('إن لم تكن تتوقع هذه الرسالة، يمكنك تجاهلها.');
    }
}
