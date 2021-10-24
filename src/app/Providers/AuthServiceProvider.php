<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Team;
use App\Policies\TeamPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('驗證電子郵件')
                ->line('請點擊下方按鈕驗證您的電子郵件：')
                ->action('驗證電子郵件', $url)
                ->line('如果您未註冊帳號，請忽略此郵件。');
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            return URL::temporarySignedRoute(
                'api.verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            // $url = url(route('password.reset', [
            $url = url(route('view.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
            return (new MailMessage)
                ->subject('重設密碼通知')
                ->line('您收到此電子郵件是因為我們收到了您帳戶的密碼重設請求。')
                ->action('重設密碼', $url)
                ->line(Lang::get('這個重設密碼連結將會在 :count 分鐘後失效。', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
                ->line('如果您未註冊帳號，請忽略此郵件。');
        });
        //
    }
}
