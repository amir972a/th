<?php
class ViewHelper {
    public static function getHeaderConfig() {
        require_once __DIR__ . '/../Models/SettingModel.php';
        $settingModel = new SettingModel();
        $config = $settingModel->get('header_config');
        if ($config) {
            return json_decode($config, true);
        }

        // Defaults
        return [
            'welcome_msg' => 'به <span class="text-primary">پرشین</span>‌وید خوش آمدید - بهترین مکان برای ویدیوهای شما',
            'logo_text_1' => 'PERSIAN',
            'logo_text_2' => 'VID',
            'logo_url' => '',
            'search_enabled' => true,
            'auth_enabled' => true,
            'upload_enabled' => true,
            'menu' => [
                ['label' => 'خانه', 'url' => '/', 'enabled' => true],
                ['label' => 'ویدیوها', 'url' => '#', 'enabled' => true],
                ['label' => 'پلی‌لیست‌ها', 'url' => '#', 'enabled' => true],
                ['label' => 'عکس‌ها', 'url' => '#', 'enabled' => true],
                ['label' => 'هوش مصنوعی 🔥', 'url' => '#', 'enabled' => true],
                ['label' => '☰ دسته‌بندی‌ها', 'url' => '#', 'enabled' => true],
                ['label' => 'انجمن', 'url' => '#', 'enabled' => true],
            ]
        ];
    }

    public static function getFooterConfig() {
        require_once __DIR__ . '/../Models/SettingModel.php';
        $settingModel = new SettingModel();
        $config = $settingModel->get('footer_config');
        if ($config) {
            return json_decode($config, true);
        }

        // Defaults
        return [
            'copyright_text' => '&copy; {year} ' . APP_NAME . '. تمامی حقوق محفوظ است.',
            'description' => 'پلتفرم اشتراک ویدیو ایرانی',
            'columns' => [
                [
                    'title' => 'دسترسی سریع',
                    'enabled' => true,
                    'links' => [
                        ['label' => 'درباره ما', 'url' => '#', 'enabled' => true],
                        ['label' => 'تماس با ما', 'url' => '#', 'enabled' => true],
                        ['label' => 'قوانین و مقررات', 'url' => '#', 'enabled' => true]
                    ]
                ]
            ],
            'social_links' => [
                ['icon' => 'instagram', 'url' => '#', 'enabled' => true],
                ['icon' => 'telegram', 'url' => '#', 'enabled' => true]
            ],
            'logo_url' => ''
        ];
    }
}
