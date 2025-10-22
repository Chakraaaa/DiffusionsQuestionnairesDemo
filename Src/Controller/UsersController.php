<?php

namespace Appy\Src\Controller;

use Appy\Src\Core\Appy;
use Appy\Src\Entity\User;
use Appy\Src\Entity\Groupe;
use Appy\Src\Repository\GroupesRepository;
use Appy\Src\Repository\UsersRepository;

class UsersController extends \Appy\Src\Core\Controller
{
    public function recherche()
    {
        $msg_erreur = [];
        $url = WEB_PATH . 'users.html';
        $urlCreateUser = WEB_PATH . 'users.html/createUser';
        $urlCreateUsers = WEB_PATH . 'users.html/createUsers';
        $urlEditUser = WEB_PATH . 'users.html/editUser';
        $urlAddGroup = WEB_PATH . 'groups.html/addGroup';
        $urlImport = WEB_PATH . 'users.html/ImportUsers';
        $urlReset = WEB_PATH . 'users.html/ResetUsers';
        $critereRecherche = [];
        if (isset($_POST['recherche_EmailNomPrenomIdentifiant'])) {
            $recherche = filter_input(INPUT_POST, 'recherche_EmailNomPrenomIdentifiant', FILTER_DEFAULT);
            $_SESSION['recherche']['EmailNomPrenomIdentifiant'] = $recherche; // Stockage dans la session
        }
        if (isset($_POST['groupe_id'])) {
            $groupeId = filter_input(INPUT_POST, 'groupe_id', FILTER_SANITIZE_NUMBER_INT); // Nettoyage de l'entrée
            $_SESSION['recherche']['groupe'] = $groupeId; // Stockage dans la session
        }
        if (isset($_SESSION['recherche']['EmailNomPrenomIdentifiant'])) {
            $critereRecherche['EmailNomPrenomIdentifiant'] = $_SESSION['recherche']['EmailNomPrenomIdentifiant'];
        }
        if (isset($_SESSION['recherche']['groupe'])) {
            $critereRecherche['groupe'] = $_SESSION['recherche']['groupe'];
        } else {
            $critereRecherche['groupe'] = '';
        }

        if (empty($critereRecherche)) {
            $critereRecherche['role'] = '5';
        }
        $order = $_SESSION['recherche']['order-by'] ?? 'U.created_at DESC';
        $usersRepository = new UsersRepository();
        $utilisateurs = $usersRepository->getAllRepondants($critereRecherche, $order);

        $groupesRepository = new GroupesRepository();
        $groupes = $groupesRepository->getAllGroupes();
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'users';
        self::showVue([
            'utilisateurs' => $utilisateurs,
            'msg_erreur' => $msg_erreur,
            'url' => $url,
            'urlCreateUser' => $urlCreateUser,
            'urlCreateUsers' => $urlCreateUsers,
            'groupes' => $groupes,
            'urlAddGroup' => $urlAddGroup,
            'urlImport' => $urlImport,
            'urlEdit' => $urlEditUser,
            'urlReset' => $urlReset,
        ]);
    }

    public function ImportUsers()
    {
        $msg_erreur_import = [];
        $groupeId = $_POST['groupe_id_import'];
        $userRepository = new UsersRepository();

        // Validation du fichier
        if (!isset($_FILES['fichier_excel']) || $_FILES['fichier_excel']['error'] !== UPLOAD_ERR_OK) {
            $msg_erreur_import[] = "Erreur lors de l'upload du fichier.";
        } else {
            $fileName = $_FILES['fichier_excel']['name'];
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            if (strtolower($extension) !== 'xlsx') {
                $msg_erreur_import[] = "Le fichier doit être au format .xlsx.";
            } else {
                $pathFile = $_FILES['fichier_excel']['tmp_name'];
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setReadDataOnly(true);

                try {
                    $spreadsheet = $reader->load($pathFile);
                    $worksheet = $spreadsheet->getSheet(0);
                    $rows = $worksheet->toArray();
                    $headers = $rows[0];
                    $normalizedHeaders = array_map('trim', array_map('strtolower', $headers));
                    $expectedHeader = ['nom', 'prenom', 'email'];
                    $expectedHeaderWithAccent = ['nom', 'prénom', 'email'];
                    $expectedHeaderWithAccentMajuscule = ['Nom', 'Prénom', 'Email'];
                    $expectedHeaderMajuscule = ['Nom', 'Prenom', 'Email'];

                    if (($normalizedHeaders == $expectedHeader) ||
                        ($normalizedHeaders == $expectedHeaderWithAccent) ||
                        ($normalizedHeaders == $expectedHeaderWithAccentMajuscule) ||
                        ($normalizedHeaders == $expectedHeaderMajuscule)
                        ) {
                        foreach ($rows as $key => $row) {
                            if ($key === 0) continue; // Ignorer l'en-tête

                            $email = trim($row[2]);
                            if (empty($email)) {
                                $msg_erreur_import[] = "L'email est obligatoire à la ligne " . ($key + 1);
                            } elseif ($userRepository->emailExistsInGroup($email, $groupeId)) {
                                $msg_erreur_import[] = "L'email '$email' existe déjà dans ce groupe à la ligne " . ($key + 1);
                            } else {
                                $user = new User();
                                $nom = trim($row[0]);
                                $prenom = trim($row[1]);
                                $user->lastname = $nom;
                                $user->firstname = $prenom;
                                $user->email = $email;
                                $user->role = '5';
                                $user->groupId = $groupeId;
                                $userRepository->createUser($user);
                            }
                        }
                    } else {
                        $msg_erreur_import[] = "Les colonnes doivent être NOM, PRENOM, EMAIL dans cet ordre.";
                    }
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    $msg_erreur_import[] = "Erreur de lecture du fichier Excel : " . $e->getMessage();
                }
            }
        }

        $messageMail = '';
        if (empty($msg_erreur_import)) {
            $messageMail = "Import de toutes les lignes avec succès.";
        } else {
            $messageMail = "Erreurs rencontrées lors de l'import :<br>";
            foreach ($msg_erreur_import as $erreur) {
                $messageMail .= "- " . htmlspecialchars($erreur) . "<br>";
            }
        }

        $userEmail = $_SESSION['utilisateur']->email;
        $userName = $_SESSION['utilisateur']->lastname . ' ' . $_SESSION['utilisateur']->firstname;

        // Configuration et envoi du mail
        \Appy\Src\Email::setDestinataires([[$userEmail, $userName]]);
        \Appy\Src\Email::setSubject("Rapport d'import d'utilisateurs");
        \Appy\Src\Email::setHtml($messageMail);
        //\Appy\Src\Email::envoi();

        // Redirection après l'envoi du mail
        $session = \Appy\Src\Core\Session::getInstance();
        $session->setFlash("success", "Import des données effectué avec succès");
        Appy::redirigeVers(WEB_PATH . 'users.html');
    }





    public function delete()
    {
        if (isset($_GET['userId'])) {
            $userId = $_GET['userId'];
            $usersRepository = new UsersRepository();
            $usersRepository->deleteUser($userId);
            echo "L'utilisateur a été supprimé avec succès.";
            $url = WEB_PATH . 'users.html';
            Appy::redirigeVers($url);
        } else {
            echo "Identifiant de l'utilisateur manquant.";
            Appy::redirigeVers(WEB_PATH . 'users.html');
        }
    }

    public function deleteMultipleUsers(){
        $IdUsers = $_GET['usersIds'];
        $idUsersArray = explode(',', $IdUsers);
        $usersRepository = new UsersRepository();
        foreach ($idUsersArray as $idUser) {
            $usersRepository->deleteUser($idUser);
        }
        Appy::redirigeVers(WEB_PATH . 'users.html');

    }

    public function groupeMultipleUsers(){
        $IdUsers = $_GET['usersIds'];
        $IdGroup = $_GET['grouIdMultiple'];
        $idUsersArray = explode(',', $IdUsers);
        $usersRepository = new UsersRepository();
        foreach ($idUsersArray as $idUser) {
            $usersRepository->updateGroup($idUser, $IdGroup);
        }
        Appy::redirigeVers(WEB_PATH . 'users.html');

    }

    public function create()
    {
        $url = WEB_PATH . 'users.html';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'users';
        $usersRepository = new UsersRepository();
        if ($_POST['form_type'] === 'single_user') {
            // Ajouter un seul utilisateur
            $user = new User();
            $user->setRoleRepondant();
            $user->lastname = $_POST['user_lastname'];
            $user->firstname = $_POST['user_firstname'];
            $user->email = $_POST['user_email'];
            $user->groupId = $_POST['groupe_id_single'];
            $usersRepository->createUser($user);
        } elseif ($_POST['form_type'] === 'multiple_users') {
            // Ajouter plusieurs utilisateurs
            $nbr_repondants = $_POST['nbr_repondants'];
            for ($i = 0; $i < $nbr_repondants; $i++) {
                $user = new User();
                $user->setRoleRepondant();
                $user->role = $_POST['RoleDesRepondants'];
                $user->lastname = null;
                $user->firstname = null;
                $user->email = null;
                $user->groupId = $_POST['groupe_id_multiple'];
                $usersRepository->createUser($user);
            }
        }

        Appy::redirigeVers($url);

    }

    public function rechercheGroupes()
    {
        $msg_erreur = [];
        $url = WEB_PATH . 'groups.html';
        $urlCreate = WEB_PATH . 'groups.html/createGroupe';
        $urlEditGroup = WEB_PATH . 'groups.html/editGroup';
        $groupesRepository = new GroupesRepository();
        $groupes = $groupesRepository->getAllGroupes();
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'groupes';
        self::showVue([
            'groupes' => $groupes,
            'msg_erreur' => $msg_erreur,
            'url' => $url,
            'urlCreate' => $urlCreate,
            'urlEditGroup' => $urlEditGroup,
        ]);
    }

    public function createGroupe()
    {
        $url = WEB_PATH . 'groups.html';
        self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'groupes';

        if (isset($_POST['btnValider'])) {
            $groupesRepository = new GroupesRepository();
            $groupe = new Groupe();
            $groupe->groupeName = $_POST['groupe_name'];
            $groupesRepository->createGroupe($groupe);
            Appy::redirigeVers($url);
        }
    }

    public function deleteGroupe()
    {
        if (isset($_GET['groupId'])) {
            $groupeId = $_GET['groupId'];
            $groupesRepository = new GroupesRepository();
            $groupesRepository->deleteGroupe($groupeId);
            echo "Le groupe a été supprimé avec succès.";
            $url = WEB_PATH . 'groups.html';
            Appy::redirigeVers($url);
        } else {
            echo "Identifiant de l'utilisateur manquant.";
            Appy::redirigeVers(WEB_PATH . 'groups.html');
        }
    }

    public function addGroup() {
        if (isset($_GET['NewGroupName'])) {
            $groupeRepository = new GroupesRepository();
            $groupe = new Groupe();
            $groupe->groupeName = $_GET['NewGroupName'];
            $groupe->createdAt = date('Y-m-d H:i:s');
            $idGroupe = $groupeRepository->createGroupe($groupe);
            $groupes = $groupeRepository->getAllGroupes();
            self::$gabarit = 'gabaritAjax';
            self::$vue = BASE_PATH . 'vues' . DS . 'default' . DS . 'SelectGroup';
            self::showVue([
                'id' => $idGroupe,
                'groupes' => $groupes

            ]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function EditUser()
    {
        $userRepository = new UsersRepository();
        $userId = $_POST['user_id'];
        $lastname = $_POST['user_lastname'];
        $firstname = $_POST['user_firstname'];
        $email = $_POST['user_email'];
        $groupId = $_POST['groupe-edit-id'];
        $user = new User();
        $user->id = $userId;
        $user->lastname = $lastname;
        $user->firstname = $firstname;
        $user->email = $email;
        $user->role = '5';
        $user->groupId = $groupId;
        $userRepository->UpdateUser($user);
        Appy::redirigeVers(WEB_PATH . 'users.html');
    }

    public function EditGroup()
    {
        $groupeRepository = new GroupesRepository();
        $groupeId = $_POST['groupe_id'];
        $groupeName = $_POST['new_groupe_name'];
        $groupe = new Groupe();
        $groupe->id = $groupeId;
        $groupe->groupeName = $groupeName;
        $groupeRepository->UpdateGroup($groupe);
        Appy::redirigeVers(WEB_PATH . 'groups.html');
    }

    public function ResetUsers(){
        $url = WEB_PATH . 'users.html';
        unset($_SESSION['recherche']);
        Appy::redirigeVers($url);
    }





}
