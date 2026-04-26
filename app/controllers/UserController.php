<?php
require_once "../app/helpers/Validator.php";
require_once "../app/models/User.php";

class UserController extends Controller
{
    public $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }

    // READ ALL
    public function index()
    {
        $users = $this->userModel->getAllUsers();
        $this->view("users/index", ['users' => $users]);
    }

    // SHOW ONE USER
    public function show($id)
    {
        $user = $this->userModel->getUserById($id);
        $this->view("users/show", ['user' => $user]);
    }

    // SHOW CREATE FORM
    public function create()
    {
        $this->view("users/create");
    }

    // STORE NEW USER
    public function store()
    {
        $validator = new Validator();

        $name  = $_POST['name'];
        $age   = $_POST['age'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $userType = "Student";


        // Validation rules
        $validator->required('name', $name);
        $validator->required('age', $age);
        $validator->email('email', $email);
        $validator->minLength('password', $password, 8);

        if ($validator->passes()) {
            // Save to DB
            $this->userModel->createUser($name, $age, $email, $password, $userType);
            header("Location: " . BASE_URL . "User/index");
        } else {
            // Return errors to view
            $this->view("users/create", [
                'errors' => $validator->getErrors(),
                'old'    => $_POST
            ]);
        }
    }

    // EDIT FORM
    public function edit($id)
    {
        $user = $this->userModel->getUserById($id);
        $this->view("users/edit", ['user' => $user]);
    }

    // UPDATE USER
    public function update($id)
    {
        $name  = $_POST['name'];
        $age   = $_POST['age'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $userType = "Student";

        $this->userModel->updateUser($id, $name, $age, $email, $password, $userType);

        header("Location: " . BASE_URL . "User/index");
    }

    // DELETE USER
    public function delete($id)
    {
        $this->userModel->deleteUser($id);

        header("Location: " . BASE_URL . "User/index");
    }
}
