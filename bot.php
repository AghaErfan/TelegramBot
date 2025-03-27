<?php
// توکن ربات - باید با توکن از بات فادر توی تلگرام بگیری اینجا جایگزین شود
define('BOT_TOKEN', 'inja copy kon');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

// دیتابیس ساده برای ذخیره کاربران (در پروژه واقعی از دیتابیس واقعی استفاده کنید)
$users = [];

// تابع برای ارسال درخواست به API تلگرام
function apiRequestWebhook($method, $parameters) {
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = [];
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    $parameters["method"] = $method;

    header("Content-Type: application/json");
    echo json_encode($parameters);
    return true;
}

// تابع برای دریافت آپدیت‌ها
function getUpdate() {
    $update = file_get_contents("php://input");
    return json_decode($update, true);
}

// پردازش پیام‌ها
function processMessage($message) {
    global $users;
    
    // شناسه چت و کاربر
    $chat_id = $message['chat']['id'];
    $user_id = $message['from']['id'];
    $first_name = $message['from']['first_name'];
    
    // ذخیره اطلاعات کاربر (در حالت واقعی باید در دیتابیس ذخیره شود)
    if (!isset($users[$user_id])) {
        $users[$user_id] = [
            'first_name' => $first_name,
            'step' => 'start'
        ];
    }
    
    // بررسی نوع پیام
    if (isset($message['text'])) {
        $text = $message['text'];
        
        if (strpos($text, "/start") === 0) {
            // پیام خوش‌آمدگویی
            $welcome_text = "سلام {$first_name} عزیز! 👋\nبه ربات حرفه ای ما خوش آمدید.\n\nلطفا یکی از گزینه‌های زیر را انتخاب کنید:";
            
            // ساخت منوهای اینلاین
            $keyboard = [
                [
                    ['text' => 'منوی 1️⃣', 'callback_data' => 'menu1'],
                    ['text' => 'منوی 2️⃣', 'callback_data' => 'menu2']
                ],
                [
                    ['text' => 'منوی 3️⃣', 'callback_data' => 'menu3'],
                    ['text' => 'منوی 4️⃣', 'callback_data' => 'menu4']
                ],
                [
                    ['text' => 'منوی 5️⃣', 'callback_data' => 'menu5'],
                    ['text' => 'منوی 6️⃣', 'callback_data' => 'menu6']
                ],
                [
                    ['text' => 'منوی 7️⃣', 'callback_data' => 'menu7'],
                    ['text' => 'منوی 8️⃣', 'callback_data' => 'menu8']
                ],
                [
                    ['text' => 'منوی 9️⃣', 'callback_data' => 'menu9'],
                    ['text' => 'منوی 🔟', 'callback_data' => 'menu10']
                ],
                [
                    ['text' => '📊 وضعیت حساب', 'callback_data' => 'account_status'],
                    ['text' => '⚙️ تنظیمات', 'callback_data' => 'settings']
                ]
            ];
            
            apiRequest("sendMessage", [
                'chat_id' => $chat_id,
                'text' => $welcome_text,
                'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
            ]);
        } elseif ($text === "/help") {
            apiRequest("sendMessage", [
                'chat_id' => $chat_id,
                'text' => "راهنمای ربات:\n\n/start - شروع کار با ربات\n/help - نمایش راهنما\n/settings - تنظیمات ربات"
            ]);
        } else {
            apiRequest("sendMessage", [
                'chat_id' => $chat_id,
                'text' => "دستور نامعتبر! لطفا از منوی ربات استفاده کنید."
            ]);
        }
    } elseif (isset($message['callback_query'])) {
        // پردازش کلیک روی دکمه‌های اینلاین
        $callback_data = $message['callback_query']['data'];
        $callback_id = $message['callback_query']['id'];
        $callback_chat_id = $message['callback_query']['message']['chat']['id'];
        $callback_user_id = $message['callback_query']['from']['id'];
        
        // پاسخ به کال‌بک (برای جلوگیری از نمایش ساعت انتظار در تلگرام)
        apiRequest("answerCallbackQuery", [
            'callback_query_id' => $callback_id
        ]);
        
        // پردازش منوهای مختلف
        switch ($callback_data) {
            case 'menu1':
                sendMenuResponse($callback_chat_id, "شما منوی 1 را انتخاب کردید.");
                break;
            case 'menu2':
                sendMenuResponse($callback_chat_id, "شما منوی 2 را انتخاب کردید.");
                break;
            case 'menu3':
                sendMenuResponse($callback_chat_id, "شما منوی 3 را انتخاب کردید.");
                break;
            case 'menu4':
                sendMenuResponse($callback_chat_id, "شما منوی 4 را انتخاب کردید.");
                break;
            case 'menu5':
                sendMenuResponse($callback_chat_id, "شما منوی 5 را انتخاب کردید.");
                break;
            case 'menu6':
                sendMenuResponse($callback_chat_id, "شما منوی 6 را انتخاب کردید.");
                break;
            case 'menu7':
                sendMenuResponse($callback_chat_id, "شما منوی 7 را انتخاب کردید.");
                break;
            case 'menu8':
                sendMenuResponse($callback_chat_id, "شما منوی 8 را انتخاب کردید.");
                break;
            case 'menu9':
                sendMenuResponse($callback_chat_id, "شما منوی 9 را انتخاب کردید.");
                break;
            case 'menu10':
                sendMenuResponse($callback_chat_id, "شما منوی 10 را انتخاب کردید.");
                break;
            case 'account_status':
                sendMenuResponse($callback_chat_id, "وضعیت حساب شما:\n\n👤 نام: {$users[$callback_user_id]['first_name']}\n🆔 شناسه کاربری: {$callback_user_id}\n📅 تاریخ عضویت: ".date("Y-m-d"));
                break;
            case 'settings':
                sendSettingsMenu($callback_chat_id);
                break;
            default:
                apiRequest("sendMessage", [
                    'chat_id' => $callback_chat_id,
                    'text' => "دستور نامعتبر!"
                ]);
        }
    }
}

// تابع برای ارسال پاسخ منوها
function sendMenuResponse($chat_id, $text) {
    $keyboard = [
        [
            ['text' => '🔙 بازگشت به منوی اصلی', 'callback_data' => 'back_to_main']
        ]
    ];
    
    apiRequest("sendMessage", [
        'chat_id' => $chat_id,
        'text' => $text,
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
    ]);
}

// تابع برای ارسال منوی تنظیمات
function sendSettingsMenu($chat_id) {
    $keyboard = [
        [
            ['text' => 'تغییر زبان', 'callback_data' => 'change_language'],
            ['text' => 'اعلانات', 'callback_data' => 'notifications']
        ],
        [
            ['text' => 'حریم خصوصی', 'callback_data' => 'privacy'],
            ['text' => 'پشتیبانی', 'callback_data' => 'support']
        ],
        [
            ['text' => '🔙 بازگشت به منوی اصلی', 'callback_data' => 'back_to_main']
        ]
    ];
    
    apiRequest("sendMessage", [
        'chat_id' => $chat_id,
        'text' => '⚙️ تنظیمات ربات:',
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard])
    ]);
}

// تابع عمومی برای ارسال درخواست به API تلگرام
function apiRequest($method, $parameters) {
    if (!is_string($method)) {
        error_log("Method name must be a string\n");
        return false;
    }

    if (!$parameters) {
        $parameters = [];
    } else if (!is_array($parameters)) {
        error_log("Parameters must be an array\n");
        return false;
    }

    foreach ($parameters as $key => &$val) {
        if (is_array($val)) {
            $val = json_encode($val);
        }
    }
    
    $url = API_URL.$method.'?'.http_build_query($parameters);
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($handle, CURLOPT_TIMEOUT, 60);
    $response = curl_exec($handle);
    
    if ($response === false) {
        error_log(curl_error($handle));
        curl_close($handle);
        return false;
    }
    
    curl_close($handle);
    return json_decode($response, true);
}

// نقطه ورود اصلی برنامه
$update = getUpdate();
if ($update) {
    if (isset($update['message'])) {
        processMessage($update['message']);
    } elseif (isset($update['callback_query'])) {
        processMessage($update['callback_query']);
    }
}
?>
