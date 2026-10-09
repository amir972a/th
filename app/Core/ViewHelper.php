<?php
class ViewHelper {
    public static function getHeaderConfig() {
        require_once __DIR__ . '/../Models/SettingModel.php';
        $settingModel = new SettingModel();
        $config = $settingModel->get('header_config');
        if ($config) {
            $parsed = json_decode($config, true);
            return self::resolveDynamicUrls($parsed);
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
            $parsed = json_decode($config, true);
            return self::resolveDynamicUrls($parsed);
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

    private static function resolveDynamicUrls($config) {
        $db = Database::getInstance()->getConnection();

        $resolveUrl = function($url) use ($db) {
            if (preg_match('/^category:(\d+)$/', $url, $matches)) {
                $stmt = $db->prepare("SELECT slug FROM categories WHERE id = :id");
                $stmt->execute(['id' => $matches[1]]);
                $slug = $stmt->fetchColumn();
                return $slug ? BASE_URL . '/category/' . $slug : '#';
            } elseif (preg_match('/^page:(\d+)$/', $url, $matches)) {
                $stmt = $db->prepare("SELECT slug FROM pages WHERE id = :id");
                $stmt->execute(['id' => $matches[1]]);
                $slug = $stmt->fetchColumn();
                return $slug ? BASE_URL . '/page/' . $slug : '#';
            }
            // For static routes we still prepend BASE_URL if they start with /
            if (strpos($url, '/') === 0) {
                return BASE_URL . $url;
            }
            return $url;
        };

        if (isset($config['menu'])) {
            foreach ($config['menu'] as &$item) {
                if (isset($item['url'])) {
                    $item['url'] = $resolveUrl($item['url']);
                }
            }
        }

        if (isset($config['columns'])) {
            foreach ($config['columns'] as &$col) {
                if (isset($col['links'])) {
                    foreach ($col['links'] as &$link) {
                        if (isset($link['url'])) {
                            $link['url'] = $resolveUrl($link['url']);
                        }
                    }
                }
            }
        }

        return $config;
    }
}
