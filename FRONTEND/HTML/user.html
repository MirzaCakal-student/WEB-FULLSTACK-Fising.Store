<?php
require_once __DIR__ . '/../../backend/rest/DAO/UserDAO.php';
$userDAO = new UserDAO();
$user_id = 2; // Example logged user

$user = $userDAO->getById($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['username'];
  $email = $_POST['email'];
  $password = !empty($_POST['password']) ? $_POST['password'] : null;
  $userDAO->update($user_id, $name, $email, $password);
  $user = $userDAO->getById($user_id);
  $msg = "Profile updated successfully!";
}
?>

<h3>User Profile</h3>
<div id="userCard" class="card" style="max-width:680px;">
  <div class="card-body">
    <form method="POST">
      <div class="d-flex align-items-center gap-3 mb-3">
        <img src="https://i.pravatar.cc/120?u=<?= $user_id ?>" class="rounded-circle" width="80" height="80" alt="">
        <div class="flex-grow-1">
          <div class="row g-2">
            <div class="col-md-6"><input name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>"></div>
            <div class="col-md-6"><input name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>"></div>
            <div class="col-md-6"><input name="password" type="password" class="form-control" placeholder="New password (optional)"></div>
          </div>
        </div>
      </div>
      <button class="btn btn-primary mt-3" id="saveUser">Save</button>
      <?php if (!empty($msg)): ?><div class="alert alert-success mt-2"><?= $msg ?></div><?php endif; ?>
    </form>
  </div>
</div>
