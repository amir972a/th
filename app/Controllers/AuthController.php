<?php
class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function login() {
        if (Session::get('user_id')) {
            $this->redirect('/');
        }

        if ($this->isPost()) {
            if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
                Session::setFlash('error', 'Invalid CSRF token');
                $this->redirect('/login');
            }

            $username = trim($_POST['username']);
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                Session::setFlash('error', 'لطفا نام کاربری و رمز عبور را وارد کنید.');
            } else {
                $user = $this->userModel->findByUsername($username);

                if ($user && password_verify($password, $user['password_hash'])) {
                    if ($user['status'] !== 'active') {
                        Session::setFlash('error', 'حساب کاربری شما مسدود شده است.');
                    } else {
                        // Prevent session fixation
                        session_regenerate_id(true);

                        Session::set('user_id', $user['id']);
                        Session::set('username', $user['username']);
                        Session::set('user_role', $user['role']);

                        $this->redirect('/profile');
                    }
                } else {
                    Session::setFlash('error', 'نام کاربری یا رمز عبور اشتباه است.');
                }
            }
        }

        $pageTitle = 'ورود';
        $contentView = __DIR__ . '/../Views/auth/login.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function register() {
        if (Session::get('user_id')) {
            $this->redirect('/');
        }

        if ($this->isPost()) {
            if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
                Session::setFlash('error', 'Invalid CSRF token');
                $this->redirect('/register');
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
            if (strlen($password) < 6) {
                $errors[] = 'رمز عبور باید حداقل ۶ کاراکتر باشد.';
            }
            if ($password !== $password_confirm) {
                $errors[] = 'رمز عبور و تکرار آن مطابقت ندارند.';
            }

            if (empty($errors)) {
                if ($this->userModel->findByUsername($username)) {
                    $errors[] = 'این نام کاربری قبلا ثبت شده است.';
                } elseif ($this->userModel->findByEmail($email)) {
                    $errors[] = 'این ایمیل قبلا ثبت شده است.';
                } else {
                    if ($this->userModel->create($username, $email, $password)) {
                        Session::setFlash('success', 'ثبت نام با موفقیت انجام شد. اکنون میتوانید وارد شوید.');
                        $this->redirect('/login');
                    } else {
                        $errors[] = 'خطا در ثبت نام. لطفا دوباره تلاش کنید.';
                    }
                }
            }

            if (!empty($errors)) {
                foreach ($errors as $err) {
                    Session::setFlash('error', $err);
                }
            }
        }

        $pageTitle = 'ثبت نام';
        $contentView = __DIR__ . '/../Views/auth/register.php';
        require_once __DIR__ . '/../Views/layout.php';
    }

    public function logout() {
        Session::destroy();
        $this->redirect('/');
    }
}
