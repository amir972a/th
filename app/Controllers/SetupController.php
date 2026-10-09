<?php
class SetupController extends Controller {
    public function index() {
        $db = Database::getInstance()->getConnection();

        // Check if an admin already exists. If yes, disable this endpoint.
        $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        if ($stmt->fetchColumn() > 0) {
            Session::setFlash('error', 'مدیر کل قبلا تنظیم شده است. دسترسی به این بخش امکان‌پذیر نیست.');
            $this->redirect('/');
        }

        if ($this->isPost()) {
            if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
                Session::setFlash('error', 'توکن نامعتبر.');
                $this->redirect('/setup');
            }

            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $errors = [];

            if (empty($username) || !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
                $errors[] = 'نام کاربری باید شامل ۳ تا ۲۰ حرف انگلیسی یا عدد باشد.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'ایمیل معتبر نیست.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'رمز عبور باید حداقل ۸ کاراکتر باشد.';
            }
            if ($password !== $password_confirm) {
                $errors[] = 'رمز عبور و تکرار آن مطابقت ندارند.';
            }

            if (empty($errors)) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, display_name, role) VALUES (:username, :email, :password_hash, :display_name, 'admin')");
                if ($stmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => $hash,
                    'display_name' => 'مدیر کل'
                ])) {
                    Session::setFlash('success', 'حساب مدیر کل با موفقیت ساخته شد. اکنون میتوانید وارد شوید.');
                    $this->redirect('/login');
                } else {
                    $errors[] = 'خطا در ثبت اطلاعات.';
                }
            }

            if (!empty($errors)) {
                foreach ($errors as $err) {
                    Session::setFlash('error', $err);
                }
            }
        }

        $pageTitle = 'راه‌اندازی اولیه - ساخت مدیر کل';
        $contentView = __DIR__ . '/../Views/setup.php';
        require_once __DIR__ . '/../Views/layout.php';
    }
}
