<?php

namespace Appy\Modules\Membres\Modeles;

class Auth
{
    private $options = [
        'restriction_msg' => "Accès interdit",
        'redirected_url'  => "login.php"
    ];
    private $session;
    private $ip;

    public function __construct($session, $options = [])
    {
        $this->options = array_merge($this->options, $options);
        $this->session = $session;
    }

    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function register($email)
    {
        $token = \Appy\Src\Str::random(60);
        \Appy\Src\Connexionbdd::query("INSERT INTO `users` SET `email`= ?, `confirmation_token` = ?", [
            $email,
            $token
        ]);

        $user_id = \Appy\Src\Connexionbdd::lastInsertId();
        $lien    = $this->getLien($user_id, $token, "confirm");

        \Appy\Src\Email::setDestinataires(array(array($_POST['email'], " ")));
        \Appy\Src\Email::setSubject("Confirmation de votre compte");
        \Appy\Src\Email::setHtml("<p>Afin de valider votre compte merci de cliquer sur le lien suivant : <a href='$lien'>$lien</a></p>");
        \Appy\Src\Email::envoi();
        return $user_id;
    }

    public function confirm($user_id, $token)
    {
        $user = \Appy\Src\Connexionbdd::query('SELECT * FROM users WHERE id = ?', [$user_id])->fetch();
        if ($user && $user->confirmation_token == $token) {
            \Appy\Src\Connexionbdd::query('UPDATE users SET confirmed_at = NOW() WHERE id = ?', [$user_id]);
            $this->session->write('utilisateur', $user);
            return true;
        }
        return false;
    }

    public function restrict()
    {
        if (!$this->user()) {
            $this->session->write('page_demandee', $_SERVER["REQUEST_URI"]);
            //$this->session->setFlash('danger', $this->options['restriction_msg']);
            \Appy\Src\Core\Appy::redirigeVers($this->options['redirected_url']);
        }
    }

    public function user()
    {
        if (!$this->session->read('utilisateur')) {
            return false;
        }

        $user = $this->session->read('utilisateur');

        return $user;
    }

    public function connect($user)
    {
        $this->session->write('utilisateur', $user);
    }

    public function connectFromCookie()
    {
        if (isset($_COOKIE['remember']) AND!$this->user()) {
            $cookie  = $_COOKIE['remember'];
            $parts   = explode("==", $cookie);
            $user_id = $parts[0];
            $user    = \Appy\Src\Connexionbdd::query("SELECT * FROM users WHERE id = ?", [$user_id])->fetch();
            if ($user) {
                $expected = $user->id."==".$user->remember_token.sha1($user->email."webeoSolution.fr");
                if ($expected == $cookie) {
                    $this->connect($user);
                }
            } else {
                setcookie("remember", NULL, time() - 10, "/");
            }
        }
    }

    public function login($email, $password, $remember = false)
    {
        $user = \Appy\Src\Connexionbdd::query('SELECT * FROM users WHERE email = :email AND confirmed_at IS NOT NULL', ['email' => $email])->fetch();
        if ($user AND password_verify($password, $user->password)) {

            // Gestion IP
            $this->saveIP($user);

            $this->connect($user);

            if ($remember) {
                $this->remember($user);
            }

            return $user;
        } else {
            return false;
        }
    }

    public function logout()
    {
        // Suppression sécurisée du cookie remember
        if (isset($_COOKIE['remember'])) {
            setcookie('remember', '', time() - 3600, "/");
        }
        $this->session->delete('utilisateur');
    }

    public function resetPassword($email)
    {
        $user = \Appy\Src\Connexionbdd::query('SELECT * FROM users WHERE email = ? AND confirmed_at IS NOT NULL', [$email])->fetch();
        if ($user) {
            $reset_token = \Appy\Src\Str::random(60);
            $lien        = $this->getLien($user->id, $reset_token, "reset");
            \Appy\Src\Connexionbdd::query('UPDATE users SET reset_token = ?, reset_at = NOW() WHERE id = ?', [$reset_token, $user->id]);

            \Appy\Src\Email::setDestinataires(array(array($_POST['email'], " ")));
            \Appy\Src\Email::setCopiesCacheesA(array(array("vdepeyre@webeosolution.fr", " ")));
            \Appy\Src\Email::setSubject("Réinitiatilisation de votre mot de passe");
            \Appy\Src\Email::setHtml("<p>Afin de réinitialiser votre mot de passe merci de cliquer sur le lien suivant : <a href='$lien'>$lien</a></p>");
            \Appy\Src\Email::envoi();

            return $user;
        }
        return false;
    }

    public function checkResetToken($user_id, $token)
    {
        return \Appy\Src\Connexionbdd::query('SELECT * FROM users WHERE id = ? AND reset_token IS NOT NULL AND reset_token = ? AND reset_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)', [$user_id, $token])->fetch();
    }

    private function getLien($user_id, $token, $action)
    {

        $array_path = explode("/", $_SERVER['REQUEST_URI']);
        array_pop($array_path);
        $path       = implode("/", $array_path);

        $serverName = $_SERVER['SERVER_NAME'];
        $serverPort = "";
        $httpOrhttps = "";
        if(\Appy\Src\Config::ENV == "DEV") {
            $serverPort = ":".$_SERVER['SERVER_PORT'];
            $httpOrhttps = "http://";
        } else {
            $httpOrhttps = "https://";
        }

        return $httpOrhttps.$serverName.$serverPort.$path."/$action?id=".$user_id."&token=$token";
    }

    /**
     * Créé un token pour se rappeler de l'utilisateur
     *
     * @param Object $user
     */
    private function remember($user)
    {
        $remember_token = \Appy\Src\Str::random(90);
        \Appy\Src\Connexionbdd::query('UPDATE users SET remember_token = ? WHERE id = ?', [$remember_token, $user->id]);
        // COOKIE
        $expire         = mktime(0, 0, 0, date("m"), date("d") + 60, date('Y')); // Le cookie expire à minuit
        setcookie("remember", $user->id."==".$remember_token.sha1($user->email."webeoSolution.fr"), $expire, '/');
    }

    private function saveIP($user)
    {
        // Récupération sécurisée de l'adresse IP
        $this->ip = $_SERVER['REMOTE_ADDR'] ?? null;
        
        // Validation de l'adresse IP
        if ($this->ip && filter_var($this->ip, FILTER_VALIDATE_IP)) {
            \Appy\Src\Connexionbdd::query("UPDATE users SET last_ip = ? WHERE id = ?", [$this->ip, $user->id]);
        }
    }
        
    public function setLastConnexion($user){
        $userId = $user->id;
        \Appy\Src\Connexionbdd::query('UPDATE users SET last_connection_at = NOW() WHERE id = ?', [$userId]);
        $this->session->write('utilisateur', $user);
    }
}
