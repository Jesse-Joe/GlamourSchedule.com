<?php
/**
 * Script to translate page titles in all languages
 */

$basePath = dirname(__DIR__) . '/resources/lang/';

$pageTitles = [
    'ja' => [
        'page_login' => 'ログイン',
        'page_register' => '新規登録',
        'page_verification' => '認証',
        'page_email_verification' => 'メール認証',
        'page_not_found' => 'ページが見つかりません',
        'page_my_bookings' => '予約一覧',
        'page_settings' => '設定',
        'page_security' => 'セキュリティ',
    ],
    'ko' => [
        'page_login' => '로그인',
        'page_register' => '회원가입',
        'page_verification' => '인증',
        'page_email_verification' => '이메일 인증',
        'page_not_found' => '페이지를 찾을 수 없음',
        'page_my_bookings' => '내 예약',
        'page_settings' => '설정',
        'page_security' => '보안',
    ],
    'zh' => [
        'page_login' => '登录',
        'page_register' => '注册',
        'page_verification' => '验证',
        'page_email_verification' => '邮箱验证',
        'page_not_found' => '页面未找到',
        'page_my_bookings' => '我的预订',
        'page_settings' => '设置',
        'page_security' => '安全',
    ],
    'ar' => [
        'page_login' => 'تسجيل الدخول',
        'page_register' => 'التسجيل',
        'page_verification' => 'التحقق',
        'page_email_verification' => 'التحقق من البريد الإلكتروني',
        'page_not_found' => 'الصفحة غير موجودة',
        'page_my_bookings' => 'حجوزاتي',
        'page_settings' => 'الإعدادات',
        'page_security' => 'الأمان',
    ],
    'tr' => [
        'page_login' => 'Giriş Yap',
        'page_register' => 'Kayıt Ol',
        'page_verification' => 'Doğrulama',
        'page_email_verification' => 'E-posta Doğrulama',
        'page_not_found' => 'Sayfa Bulunamadı',
        'page_my_bookings' => 'Rezervasyonlarım',
        'page_settings' => 'Ayarlar',
        'page_security' => 'Güvenlik',
    ],
    'ru' => [
        'page_login' => 'Вход',
        'page_register' => 'Регистрация',
        'page_verification' => 'Проверка',
        'page_email_verification' => 'Подтверждение email',
        'page_not_found' => 'Страница не найдена',
        'page_my_bookings' => 'Мои бронирования',
        'page_settings' => 'Настройки',
        'page_security' => 'Безопасность',
    ],
    'it' => [
        'page_login' => 'Accedi',
        'page_register' => 'Registrati',
        'page_verification' => 'Verifica',
        'page_email_verification' => 'Verifica Email',
        'page_not_found' => 'Pagina non trovata',
        'page_my_bookings' => 'Le mie prenotazioni',
        'page_settings' => 'Impostazioni',
        'page_security' => 'Sicurezza',
    ],
    'pt' => [
        'page_login' => 'Entrar',
        'page_register' => 'Registar',
        'page_verification' => 'Verificação',
        'page_email_verification' => 'Verificação de Email',
        'page_not_found' => 'Página não encontrada',
        'page_my_bookings' => 'Minhas reservas',
        'page_settings' => 'Configurações',
        'page_security' => 'Segurança',
    ],
    'pl' => [
        'page_login' => 'Logowanie',
        'page_register' => 'Rejestracja',
        'page_verification' => 'Weryfikacja',
        'page_email_verification' => 'Weryfikacja e-mail',
        'page_not_found' => 'Strona nie znaleziona',
        'page_my_bookings' => 'Moje rezerwacje',
        'page_settings' => 'Ustawienia',
        'page_security' => 'Bezpieczeństwo',
    ],
    'sv' => [
        'page_login' => 'Logga in',
        'page_register' => 'Registrera',
        'page_verification' => 'Verifiering',
        'page_email_verification' => 'E-postverifiering',
        'page_not_found' => 'Sidan hittades inte',
        'page_my_bookings' => 'Mina bokningar',
        'page_settings' => 'Inställningar',
        'page_security' => 'Säkerhet',
    ],
    'no' => [
        'page_login' => 'Logg inn',
        'page_register' => 'Registrer',
        'page_verification' => 'Verifisering',
        'page_email_verification' => 'E-postverifisering',
        'page_not_found' => 'Siden ble ikke funnet',
        'page_my_bookings' => 'Mine bestillinger',
        'page_settings' => 'Innstillinger',
        'page_security' => 'Sikkerhet',
    ],
    'da' => [
        'page_login' => 'Log ind',
        'page_register' => 'Registrer',
        'page_verification' => 'Bekræftelse',
        'page_email_verification' => 'E-mailbekræftelse',
        'page_not_found' => 'Siden blev ikke fundet',
        'page_my_bookings' => 'Mine bookinger',
        'page_settings' => 'Indstillinger',
        'page_security' => 'Sikkerhed',
    ],
    'fi' => [
        'page_login' => 'Kirjaudu',
        'page_register' => 'Rekisteröidy',
        'page_verification' => 'Vahvistus',
        'page_email_verification' => 'Sähköpostivahvistus',
        'page_not_found' => 'Sivua ei löytynyt',
        'page_my_bookings' => 'Varaukseni',
        'page_settings' => 'Asetukset',
        'page_security' => 'Turvallisuus',
    ],
    'el' => [
        'page_login' => 'Σύνδεση',
        'page_register' => 'Εγγραφή',
        'page_verification' => 'Επαλήθευση',
        'page_email_verification' => 'Επαλήθευση Email',
        'page_not_found' => 'Η σελίδα δεν βρέθηκε',
        'page_my_bookings' => 'Οι κρατήσεις μου',
        'page_settings' => 'Ρυθμίσεις',
        'page_security' => 'Ασφάλεια',
    ],
    'cs' => [
        'page_login' => 'Přihlášení',
        'page_register' => 'Registrace',
        'page_verification' => 'Ověření',
        'page_email_verification' => 'Ověření e-mailu',
        'page_not_found' => 'Stránka nenalezena',
        'page_my_bookings' => 'Moje rezervace',
        'page_settings' => 'Nastavení',
        'page_security' => 'Zabezpečení',
    ],
    'hu' => [
        'page_login' => 'Bejelentkezés',
        'page_register' => 'Regisztráció',
        'page_verification' => 'Ellenőrzés',
        'page_email_verification' => 'E-mail ellenőrzés',
        'page_not_found' => 'Az oldal nem található',
        'page_my_bookings' => 'Foglalásaim',
        'page_settings' => 'Beállítások',
        'page_security' => 'Biztonság',
    ],
    'ro' => [
        'page_login' => 'Autentificare',
        'page_register' => 'Înregistrare',
        'page_verification' => 'Verificare',
        'page_email_verification' => 'Verificare email',
        'page_not_found' => 'Pagină negăsită',
        'page_my_bookings' => 'Rezervările mele',
        'page_settings' => 'Setări',
        'page_security' => 'Securitate',
    ],
    'bg' => [
        'page_login' => 'Вход',
        'page_register' => 'Регистрация',
        'page_verification' => 'Верификация',
        'page_email_verification' => 'Потвърждение на имейл',
        'page_not_found' => 'Страницата не е намерена',
        'page_my_bookings' => 'Моите резервации',
        'page_settings' => 'Настройки',
        'page_security' => 'Сигурност',
    ],
    'hr' => [
        'page_login' => 'Prijava',
        'page_register' => 'Registracija',
        'page_verification' => 'Verifikacija',
        'page_email_verification' => 'Potvrda e-pošte',
        'page_not_found' => 'Stranica nije pronađena',
        'page_my_bookings' => 'Moje rezervacije',
        'page_settings' => 'Postavke',
        'page_security' => 'Sigurnost',
    ],
    'sk' => [
        'page_login' => 'Prihlásenie',
        'page_register' => 'Registrácia',
        'page_verification' => 'Overenie',
        'page_email_verification' => 'Overenie e-mailu',
        'page_not_found' => 'Stránka nenájdená',
        'page_my_bookings' => 'Moje rezervácie',
        'page_settings' => 'Nastavenia',
        'page_security' => 'Bezpečnosť',
    ],
    'sl' => [
        'page_login' => 'Prijava',
        'page_register' => 'Registracija',
        'page_verification' => 'Preverjanje',
        'page_email_verification' => 'Potrditev e-pošte',
        'page_not_found' => 'Stran ni najdena',
        'page_my_bookings' => 'Moje rezervacije',
        'page_settings' => 'Nastavitve',
        'page_security' => 'Varnost',
    ],
    'et' => [
        'page_login' => 'Sisselogimine',
        'page_register' => 'Registreerimine',
        'page_verification' => 'Kinnitamine',
        'page_email_verification' => 'E-posti kinnitamine',
        'page_not_found' => 'Lehte ei leitud',
        'page_my_bookings' => 'Minu broneeringud',
        'page_settings' => 'Seaded',
        'page_security' => 'Turvalisus',
    ],
    'lv' => [
        'page_login' => 'Pieteikties',
        'page_register' => 'Reģistrēties',
        'page_verification' => 'Verificēšana',
        'page_email_verification' => 'E-pasta apstiprināšana',
        'page_not_found' => 'Lapa nav atrasta',
        'page_my_bookings' => 'Manas rezervācijas',
        'page_settings' => 'Iestatījumi',
        'page_security' => 'Drošība',
    ],
    'lt' => [
        'page_login' => 'Prisijungimas',
        'page_register' => 'Registracija',
        'page_verification' => 'Patvirtinimas',
        'page_email_verification' => 'El. pašto patvirtinimas',
        'page_not_found' => 'Puslapis nerastas',
        'page_my_bookings' => 'Mano rezervacijos',
        'page_settings' => 'Nustatymai',
        'page_security' => 'Saugumas',
    ],
    'uk' => [
        'page_login' => 'Увійти',
        'page_register' => 'Реєстрація',
        'page_verification' => 'Підтвердження',
        'page_email_verification' => 'Підтвердження email',
        'page_not_found' => 'Сторінку не знайдено',
        'page_my_bookings' => 'Мої бронювання',
        'page_settings' => 'Налаштування',
        'page_security' => 'Безпека',
    ],
    'hi' => [
        'page_login' => 'लॉग इन',
        'page_register' => 'पंजीकरण',
        'page_verification' => 'सत्यापन',
        'page_email_verification' => 'ईमेल सत्यापन',
        'page_not_found' => 'पृष्ठ नहीं मिला',
        'page_my_bookings' => 'मेरी बुकिंग',
        'page_settings' => 'सेटिंग्स',
        'page_security' => 'सुरक्षा',
    ],
    'th' => [
        'page_login' => 'เข้าสู่ระบบ',
        'page_register' => 'ลงทะเบียน',
        'page_verification' => 'การยืนยัน',
        'page_email_verification' => 'ยืนยันอีเมล',
        'page_not_found' => 'ไม่พบหน้า',
        'page_my_bookings' => 'การจองของฉัน',
        'page_settings' => 'การตั้งค่า',
        'page_security' => 'ความปลอดภัย',
    ],
    'vi' => [
        'page_login' => 'Đăng nhập',
        'page_register' => 'Đăng ký',
        'page_verification' => 'Xác minh',
        'page_email_verification' => 'Xác minh email',
        'page_not_found' => 'Không tìm thấy trang',
        'page_my_bookings' => 'Đặt chỗ của tôi',
        'page_settings' => 'Cài đặt',
        'page_security' => 'Bảo mật',
    ],
    'id' => [
        'page_login' => 'Masuk',
        'page_register' => 'Daftar',
        'page_verification' => 'Verifikasi',
        'page_email_verification' => 'Verifikasi Email',
        'page_not_found' => 'Halaman tidak ditemukan',
        'page_my_bookings' => 'Pemesanan saya',
        'page_settings' => 'Pengaturan',
        'page_security' => 'Keamanan',
    ],
    'ms' => [
        'page_login' => 'Log Masuk',
        'page_register' => 'Daftar',
        'page_verification' => 'Pengesahan',
        'page_email_verification' => 'Pengesahan E-mel',
        'page_not_found' => 'Halaman tidak dijumpai',
        'page_my_bookings' => 'Tempahan saya',
        'page_settings' => 'Tetapan',
        'page_security' => 'Keselamatan',
    ],
    'tl' => [
        'page_login' => 'Mag-login',
        'page_register' => 'Magrehistro',
        'page_verification' => 'Beripikasyon',
        'page_email_verification' => 'Beripikasyon ng Email',
        'page_not_found' => 'Hindi natagpuan ang pahina',
        'page_my_bookings' => 'Aking mga booking',
        'page_settings' => 'Mga Setting',
        'page_security' => 'Seguridad',
    ],
    'he' => [
        'page_login' => 'התחברות',
        'page_register' => 'הרשמה',
        'page_verification' => 'אימות',
        'page_email_verification' => 'אימות אימייל',
        'page_not_found' => 'הדף לא נמצא',
        'page_my_bookings' => 'ההזמנות שלי',
        'page_settings' => 'הגדרות',
        'page_security' => 'אבטחה',
    ],
    'fa' => [
        'page_login' => 'ورود',
        'page_register' => 'ثبت نام',
        'page_verification' => 'تأیید',
        'page_email_verification' => 'تأیید ایمیل',
        'page_not_found' => 'صفحه یافت نشد',
        'page_my_bookings' => 'رزروهای من',
        'page_settings' => 'تنظیمات',
        'page_security' => 'امنیت',
    ],
    'sw' => [
        'page_login' => 'Ingia',
        'page_register' => 'Jisajili',
        'page_verification' => 'Uthibitisho',
        'page_email_verification' => 'Uthibitisho wa Barua Pepe',
        'page_not_found' => 'Ukurasa haujapatikana',
        'page_my_bookings' => 'Nafasi zangu',
        'page_settings' => 'Mipangilio',
        'page_security' => 'Usalama',
    ],
    'af' => [
        'page_login' => 'Meld aan',
        'page_register' => 'Registreer',
        'page_verification' => 'Verifikasie',
        'page_email_verification' => 'E-pos Verifikasie',
        'page_not_found' => 'Bladsy nie gevind nie',
        'page_my_bookings' => 'My besprekings',
        'page_settings' => 'Instellings',
        'page_security' => 'Sekuriteit',
    ],
];

function updateLanguageFile($langCode, $translations, $basePath) {
    $filePath = $basePath . $langCode . '/messages.php';

    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return false;
    }

    $content = file_get_contents($filePath);
    $updated = false;

    foreach ($translations as $key => $value) {
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
        echo "✓ Updated: $langCode\n";
        return true;
    }

    return false;
}

echo "Translating page titles for 35 languages...\n\n";

foreach ($pageTitles as $langCode => $langTranslations) {
    updateLanguageFile($langCode, $langTranslations, $basePath);
}

echo "\n✅ Done! Page titles are now translated in all languages.\n";
