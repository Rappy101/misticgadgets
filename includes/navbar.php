

<nav class="navbar navbar-expand-lg bg-light shadow">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="icon1.ico" alt="Icon" class="navbar-icon" width="50" height="50">
      Mistic Gadgets
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto" >
        <li class="nav-item">
          <a class="nav-link active" href="admin/index.php">Home</a>
        </li>
        <?php if(isset($_SESSION['loggedIn'])): ?>
        <li class="nav-item">
          <a class="nav-link" href="#"><?= $_SESSION['loggedInUser']['name']; ?></a>
        </li>
        <li class="nav-item">
    <form method="post" action="logout.php">
        <button type="submit" class="btn btn-danger" name="logout">Logout</button>
    </form>
</li>

        <?php else: ?> 
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
