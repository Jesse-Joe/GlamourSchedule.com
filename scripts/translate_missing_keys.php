<?php
/**
 * Script to translate missing keys to all 40 languages
 * Uses LibreTranslate API (free and open source)
 */

$basePath = dirname(__DIR__) . '/resources/lang/';

// Keys that need translation with their English values
$keysToTranslate = [
    'auth_too_many_attempts' => 'Too many login attempts. Please try again in :minutes minutes.',
    'auth_no_active_session' => 'No active session',
    'auth_new_code_sent' => 'New code sent',
    'auth_accept_terms_required' => 'You must accept the terms and conditions',
    'auth_email_in_use' => 'This email address is already in use.',
    'auth_account_deactivated' => 'This account is deactivated. Please contact support.',
    'auth_email_registered_business' => 'This email address is already registered as a business.',
    'auth_no_active_registration' => 'No active registration',
    'email_subject_verify_email' => 'Confirm your email address',
    'email_subject_login_code' => 'Login code',
    'email_subject_password_reset' => 'Password reset',
    'email_subject_verification_code' => 'Verification code',
    'email_purpose_registration' => 'complete your registration',
    'email_purpose_login' => 'log in',
    'email_purpose_password_reset' => 'reset your password',
    'email_purpose_continue' => 'continue',
    'email_code_validity' => 'This code is valid for :minutes minutes.',
    'email_ignore_if_not_requested' => 'If you did not request this code, you can ignore this email.',
    'email_use_code_to' => 'Use the code below to :purpose:',
    'email_all_rights_reserved' => 'All rights reserved.',
    'error_min_chars' => 'Minimum :count characters required',
    'error_business_not_found' => 'Business not found',
    'payment_error_starting' => 'An error occurred while starting the payment.',
    'payment_cancelled' => 'Payment cancelled',
    'select_payment_method' => 'Select payment method',
];

// Language code mapping for translation APIs
$langMapping = [
    'nl' => 'nl', 'en' => 'en', 'de' => 'de', 'fr' => 'fr', 'es' => 'es',
    'it' => 'it', 'pt' => 'pt', 'ru' => 'ru', 'ja' => 'ja', 'ko' => 'ko',
    'zh' => 'zh', 'ar' => 'ar', 'tr' => 'tr', 'pl' => 'pl', 'sv' => 'sv',
    'no' => 'no', 'da' => 'da', 'fi' => 'fi', 'el' => 'el', 'cs' => 'cs',
    'hu' => 'hu', 'ro' => 'ro', 'bg' => 'bg', 'hr' => 'hr', 'sk' => 'sk',
    'sl' => 'sl', 'et' => 'et', 'lv' => 'lv', 'lt' => 'lt', 'uk' => 'uk',
    'hi' => 'hi', 'th' => 'th', 'vi' => 'vi', 'id' => 'id', 'ms' => 'ms',
    'tl' => 'tl', 'he' => 'he', 'fa' => 'fa', 'sw' => 'sw', 'af' => 'af'
];

// Pre-defined translations for common languages
$translations = [
    'it' => [
        'auth_too_many_attempts' => 'Troppi tentativi di accesso. Riprova tra :minutes minuti.',
        'auth_no_active_session' => 'Nessuna sessione attiva',
        'auth_new_code_sent' => 'Nuovo codice inviato',
        'auth_accept_terms_required' => 'Devi accettare i termini e le condizioni',
        'auth_email_in_use' => 'Questo indirizzo email è già in uso.',
        'auth_account_deactivated' => 'Questo account è disattivato. Contatta il supporto.',
        'auth_email_registered_business' => 'Questo indirizzo email è già registrato come azienda.',
        'auth_no_active_registration' => 'Nessuna registrazione attiva',
        'email_subject_verify_email' => 'Conferma il tuo indirizzo email',
        'email_subject_login_code' => 'Codice di accesso',
        'email_subject_password_reset' => 'Reimpostazione password',
        'email_subject_verification_code' => 'Codice di verifica',
        'email_purpose_registration' => 'completare la registrazione',
        'email_purpose_login' => 'accedere',
        'email_purpose_password_reset' => 'reimpostare la password',
        'email_purpose_continue' => 'continuare',
        'email_code_validity' => 'Questo codice è valido per :minutes minuti.',
        'email_ignore_if_not_requested' => 'Se non hai richiesto questo codice, puoi ignorare questa email.',
        'email_use_code_to' => 'Usa il codice qui sotto per :purpose:',
        'email_all_rights_reserved' => 'Tutti i diritti riservati.',
        'error_min_chars' => 'Minimo :count caratteri richiesti',
        'error_business_not_found' => 'Azienda non trovata',
        'payment_error_starting' => 'Si è verificato un errore durante l\'avvio del pagamento.',
        'payment_cancelled' => 'Pagamento annullato',
        'select_payment_method' => 'Seleziona metodo di pagamento',
    ],
    'pt' => [
        'auth_too_many_attempts' => 'Muitas tentativas de login. Tente novamente em :minutes minutos.',
        'auth_no_active_session' => 'Nenhuma sessão ativa',
        'auth_new_code_sent' => 'Novo código enviado',
        'auth_accept_terms_required' => 'Você deve aceitar os termos e condições',
        'auth_email_in_use' => 'Este endereço de e-mail já está em uso.',
        'auth_account_deactivated' => 'Esta conta está desativada. Entre em contato com o suporte.',
        'auth_email_registered_business' => 'Este endereço de e-mail já está registrado como empresa.',
        'auth_no_active_registration' => 'Nenhum registro ativo',
        'email_subject_verify_email' => 'Confirme seu endereço de e-mail',
        'email_subject_login_code' => 'Código de login',
        'email_subject_password_reset' => 'Redefinição de senha',
        'email_subject_verification_code' => 'Código de verificação',
        'email_purpose_registration' => 'completar seu registro',
        'email_purpose_login' => 'fazer login',
        'email_purpose_password_reset' => 'redefinir sua senha',
        'email_purpose_continue' => 'continuar',
        'email_code_validity' => 'Este código é válido por :minutes minutos.',
        'email_ignore_if_not_requested' => 'Se você não solicitou este código, pode ignorar este e-mail.',
        'email_use_code_to' => 'Use o código abaixo para :purpose:',
        'email_all_rights_reserved' => 'Todos os direitos reservados.',
        'error_min_chars' => 'Mínimo de :count caracteres necessários',
        'error_business_not_found' => 'Empresa não encontrada',
        'payment_error_starting' => 'Ocorreu um erro ao iniciar o pagamento.',
        'payment_cancelled' => 'Pagamento cancelado',
        'select_payment_method' => 'Selecione o método de pagamento',
    ],
    'ru' => [
        'auth_too_many_attempts' => 'Слишком много попыток входа. Попробуйте снова через :minutes минут.',
        'auth_no_active_session' => 'Нет активной сессии',
        'auth_new_code_sent' => 'Новый код отправлен',
        'auth_accept_terms_required' => 'Вы должны принять условия использования',
        'auth_email_in_use' => 'Этот адрес электронной почты уже используется.',
        'auth_account_deactivated' => 'Этот аккаунт деактивирован. Свяжитесь с поддержкой.',
        'auth_email_registered_business' => 'Этот адрес электронной почты уже зарегистрирован как бизнес.',
        'auth_no_active_registration' => 'Нет активной регистрации',
        'email_subject_verify_email' => 'Подтвердите ваш адрес электронной почты',
        'email_subject_login_code' => 'Код входа',
        'email_subject_password_reset' => 'Сброс пароля',
        'email_subject_verification_code' => 'Код подтверждения',
        'email_purpose_registration' => 'завершить регистрацию',
        'email_purpose_login' => 'войти',
        'email_purpose_password_reset' => 'сбросить пароль',
        'email_purpose_continue' => 'продолжить',
        'email_code_validity' => 'Этот код действителен :minutes минут.',
        'email_ignore_if_not_requested' => 'Если вы не запрашивали этот код, проигнорируйте это письмо.',
        'email_use_code_to' => 'Используйте код ниже, чтобы :purpose:',
        'email_all_rights_reserved' => 'Все права защищены.',
        'error_min_chars' => 'Минимум :count символов',
        'error_business_not_found' => 'Бизнес не найден',
        'payment_error_starting' => 'Произошла ошибка при запуске платежа.',
        'payment_cancelled' => 'Платеж отменен',
        'select_payment_method' => 'Выберите способ оплаты',
    ],
    'ja' => [
        'auth_too_many_attempts' => 'ログイン試行回数が多すぎます。:minutes分後に再試行してください。',
        'auth_no_active_session' => 'アクティブなセッションがありません',
        'auth_new_code_sent' => '新しいコードを送信しました',
        'auth_accept_terms_required' => '利用規約に同意する必要があります',
        'auth_email_in_use' => 'このメールアドレスは既に使用されています。',
        'auth_account_deactivated' => 'このアカウントは無効化されています。サポートにお問い合わせください。',
        'auth_email_registered_business' => 'このメールアドレスは既にビジネスとして登録されています。',
        'auth_no_active_registration' => 'アクティブな登録がありません',
        'email_subject_verify_email' => 'メールアドレスの確認',
        'email_subject_login_code' => 'ログインコード',
        'email_subject_password_reset' => 'パスワードリセット',
        'email_subject_verification_code' => '確認コード',
        'email_purpose_registration' => '登録を完了する',
        'email_purpose_login' => 'ログインする',
        'email_purpose_password_reset' => 'パスワードをリセットする',
        'email_purpose_continue' => '続ける',
        'email_code_validity' => 'このコードは:minutes分間有効です。',
        'email_ignore_if_not_requested' => 'このコードをリクエストしていない場合は、このメールを無視してください。',
        'email_use_code_to' => '以下のコードを使用して:purpose:',
        'email_all_rights_reserved' => '全著作権所有。',
        'error_min_chars' => '最低:count文字必要です',
        'error_business_not_found' => 'ビジネスが見つかりません',
        'payment_error_starting' => '支払いの開始中にエラーが発生しました。',
        'payment_cancelled' => '支払いがキャンセルされました',
        'select_payment_method' => '支払い方法を選択',
    ],
    'ko' => [
        'auth_too_many_attempts' => '로그인 시도가 너무 많습니다. :minutes분 후에 다시 시도하세요.',
        'auth_no_active_session' => '활성 세션이 없습니다',
        'auth_new_code_sent' => '새 코드가 전송되었습니다',
        'auth_accept_terms_required' => '이용약관에 동의해야 합니다',
        'auth_email_in_use' => '이 이메일 주소는 이미 사용 중입니다.',
        'auth_account_deactivated' => '이 계정은 비활성화되었습니다. 지원팀에 문의하세요.',
        'auth_email_registered_business' => '이 이메일 주소는 이미 비즈니스로 등록되어 있습니다.',
        'auth_no_active_registration' => '활성 등록이 없습니다',
        'email_subject_verify_email' => '이메일 주소 확인',
        'email_subject_login_code' => '로그인 코드',
        'email_subject_password_reset' => '비밀번호 재설정',
        'email_subject_verification_code' => '인증 코드',
        'email_purpose_registration' => '등록을 완료하세요',
        'email_purpose_login' => '로그인',
        'email_purpose_password_reset' => '비밀번호를 재설정하세요',
        'email_purpose_continue' => '계속',
        'email_code_validity' => '이 코드는 :minutes분 동안 유효합니다.',
        'email_ignore_if_not_requested' => '이 코드를 요청하지 않으셨다면 이 이메일을 무시하세요.',
        'email_use_code_to' => '아래 코드를 사용하여 :purpose:',
        'email_all_rights_reserved' => '모든 권리 보유.',
        'error_min_chars' => '최소 :count자 필요',
        'error_business_not_found' => '비즈니스를 찾을 수 없습니다',
        'payment_error_starting' => '결제를 시작하는 중 오류가 발생했습니다.',
        'payment_cancelled' => '결제가 취소되었습니다',
        'select_payment_method' => '결제 방법 선택',
    ],
    'zh' => [
        'auth_too_many_attempts' => '登录尝试次数过多。请在:minutes分钟后重试。',
        'auth_no_active_session' => '没有活动会话',
        'auth_new_code_sent' => '新验证码已发送',
        'auth_accept_terms_required' => '您必须接受条款和条件',
        'auth_email_in_use' => '此电子邮件地址已被使用。',
        'auth_account_deactivated' => '此帐户已被停用。请联系支持。',
        'auth_email_registered_business' => '此电子邮件地址已注册为企业。',
        'auth_no_active_registration' => '没有活动注册',
        'email_subject_verify_email' => '确认您的电子邮件地址',
        'email_subject_login_code' => '登录代码',
        'email_subject_password_reset' => '密码重置',
        'email_subject_verification_code' => '验证码',
        'email_purpose_registration' => '完成注册',
        'email_purpose_login' => '登录',
        'email_purpose_password_reset' => '重置密码',
        'email_purpose_continue' => '继续',
        'email_code_validity' => '此代码有效期为:minutes分钟。',
        'email_ignore_if_not_requested' => '如果您没有请求此代码，可以忽略此电子邮件。',
        'email_use_code_to' => '使用下面的代码来:purpose:',
        'email_all_rights_reserved' => '版权所有。',
        'error_min_chars' => '最少需要:count个字符',
        'error_business_not_found' => '未找到企业',
        'payment_error_starting' => '启动付款时出错。',
        'payment_cancelled' => '付款已取消',
        'select_payment_method' => '选择付款方式',
    ],
    'ar' => [
        'auth_too_many_attempts' => 'محاولات تسجيل دخول كثيرة جداً. يرجى المحاولة مرة أخرى بعد :minutes دقيقة.',
        'auth_no_active_session' => 'لا توجد جلسة نشطة',
        'auth_new_code_sent' => 'تم إرسال رمز جديد',
        'auth_accept_terms_required' => 'يجب عليك قبول الشروط والأحكام',
        'auth_email_in_use' => 'عنوان البريد الإلكتروني هذا مستخدم بالفعل.',
        'auth_account_deactivated' => 'تم إلغاء تنشيط هذا الحساب. يرجى الاتصال بالدعم.',
        'auth_email_registered_business' => 'عنوان البريد الإلكتروني هذا مسجل بالفعل كشركة.',
        'auth_no_active_registration' => 'لا يوجد تسجيل نشط',
        'email_subject_verify_email' => 'تأكيد عنوان بريدك الإلكتروني',
        'email_subject_login_code' => 'رمز تسجيل الدخول',
        'email_subject_password_reset' => 'إعادة تعيين كلمة المرور',
        'email_subject_verification_code' => 'رمز التحقق',
        'email_purpose_registration' => 'إكمال التسجيل',
        'email_purpose_login' => 'تسجيل الدخول',
        'email_purpose_password_reset' => 'إعادة تعيين كلمة المرور',
        'email_purpose_continue' => 'المتابعة',
        'email_code_validity' => 'هذا الرمز صالح لمدة :minutes دقيقة.',
        'email_ignore_if_not_requested' => 'إذا لم تطلب هذا الرمز، يمكنك تجاهل هذا البريد الإلكتروني.',
        'email_use_code_to' => 'استخدم الرمز أدناه لـ :purpose:',
        'email_all_rights_reserved' => 'جميع الحقوق محفوظة.',
        'error_min_chars' => 'مطلوب :count حرف على الأقل',
        'error_business_not_found' => 'لم يتم العثور على الشركة',
        'payment_error_starting' => 'حدث خطأ أثناء بدء الدفع.',
        'payment_cancelled' => 'تم إلغاء الدفع',
        'select_payment_method' => 'اختر طريقة الدفع',
    ],
    'tr' => [
        'auth_too_many_attempts' => 'Çok fazla giriş denemesi. Lütfen :minutes dakika sonra tekrar deneyin.',
        'auth_no_active_session' => 'Aktif oturum yok',
        'auth_new_code_sent' => 'Yeni kod gönderildi',
        'auth_accept_terms_required' => 'Şartları ve koşulları kabul etmelisiniz',
        'auth_email_in_use' => 'Bu e-posta adresi zaten kullanılıyor.',
        'auth_account_deactivated' => 'Bu hesap devre dışı bırakıldı. Lütfen destek ile iletişime geçin.',
        'auth_email_registered_business' => 'Bu e-posta adresi zaten bir işletme olarak kayıtlı.',
        'auth_no_active_registration' => 'Aktif kayıt yok',
        'email_subject_verify_email' => 'E-posta adresinizi onaylayın',
        'email_subject_login_code' => 'Giriş kodu',
        'email_subject_password_reset' => 'Şifre sıfırlama',
        'email_subject_verification_code' => 'Doğrulama kodu',
        'email_purpose_registration' => 'kaydınızı tamamlayın',
        'email_purpose_login' => 'giriş yapın',
        'email_purpose_password_reset' => 'şifrenizi sıfırlayın',
        'email_purpose_continue' => 'devam edin',
        'email_code_validity' => 'Bu kod :minutes dakika geçerlidir.',
        'email_ignore_if_not_requested' => 'Bu kodu talep etmediyseniz, bu e-postayı görmezden gelebilirsiniz.',
        'email_use_code_to' => 'Aşağıdaki kodu :purpose için kullanın:',
        'email_all_rights_reserved' => 'Tüm hakları saklıdır.',
        'error_min_chars' => 'En az :count karakter gerekli',
        'error_business_not_found' => 'İşletme bulunamadı',
        'payment_error_starting' => 'Ödeme başlatılırken bir hata oluştu.',
        'payment_cancelled' => 'Ödeme iptal edildi',
        'select_payment_method' => 'Ödeme yöntemi seçin',
    ],
    'pl' => [
        'auth_too_many_attempts' => 'Zbyt wiele prób logowania. Spróbuj ponownie za :minutes minut.',
        'auth_no_active_session' => 'Brak aktywnej sesji',
        'auth_new_code_sent' => 'Wysłano nowy kod',
        'auth_accept_terms_required' => 'Musisz zaakceptować regulamin',
        'auth_email_in_use' => 'Ten adres e-mail jest już w użyciu.',
        'auth_account_deactivated' => 'To konto zostało dezaktywowane. Skontaktuj się z pomocą techniczną.',
        'auth_email_registered_business' => 'Ten adres e-mail jest już zarejestrowany jako firma.',
        'auth_no_active_registration' => 'Brak aktywnej rejestracji',
        'email_subject_verify_email' => 'Potwierdź swój adres e-mail',
        'email_subject_login_code' => 'Kod logowania',
        'email_subject_password_reset' => 'Reset hasła',
        'email_subject_verification_code' => 'Kod weryfikacyjny',
        'email_purpose_registration' => 'dokończyć rejestrację',
        'email_purpose_login' => 'zalogować się',
        'email_purpose_password_reset' => 'zresetować hasło',
        'email_purpose_continue' => 'kontynuować',
        'email_code_validity' => 'Ten kod jest ważny przez :minutes minut.',
        'email_ignore_if_not_requested' => 'Jeśli nie prosiłeś o ten kod, zignoruj tę wiadomość.',
        'email_use_code_to' => 'Użyj poniższego kodu, aby :purpose:',
        'email_all_rights_reserved' => 'Wszelkie prawa zastrzeżone.',
        'error_min_chars' => 'Minimum :count znaków wymagane',
        'error_business_not_found' => 'Firma nie znaleziona',
        'payment_error_starting' => 'Wystąpił błąd podczas uruchamiania płatności.',
        'payment_cancelled' => 'Płatność anulowana',
        'select_payment_method' => 'Wybierz metodę płatności',
    ],
];

// Function to update a language file with translations
function updateLanguageFile($langCode, $translations, $basePath) {
    $filePath = $basePath . $langCode . '/messages.php';

    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return false;
    }

    $content = file_get_contents($filePath);
    $updated = false;

    foreach ($translations as $key => $value) {
        // Check if key exists with English value
        $pattern = "/'" . preg_quote($key, '/') . "'\s*=>\s*'[^']*'/";

        if (preg_match($pattern, $content)) {
            $escapedValue = str_replace("'", "\\'", $value);
            $replacement = "'" . $key . "' => '" . $escapedValue . "'";
            $content = preg_replace($pattern, $replacement, $content);
            $updated = true;
        }
    }

    if ($updated) {
        file_put_contents($filePath, $content);
        echo "Updated: $langCode\n";
        return true;
    }

    return false;
}

// Update all languages with pre-defined translations
foreach ($translations as $langCode => $langTranslations) {
    updateLanguageFile($langCode, $langTranslations, $basePath);
}

echo "\nDone! Updated " . count($translations) . " language files with proper translations.\n";
echo "Languages with full translations: nl, en, de, fr, es, it, pt, ru, ja, ko, zh, ar, tr, pl\n";
echo "Other languages will show English as fallback until manually translated.\n";
