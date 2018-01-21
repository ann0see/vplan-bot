<?php
session_start();
include '../includes/head.php';
?>
  <title> Index </title>
</head>

<body>
  <h1> LOGIN: </h1>
  <div>
    <form action="login.php" method="post">
      <label for="Username"> Benutzername: </label>
      <input id="username" type="text" name="user" />
      <label for="password"> Passwort: </label>
      <input id="password" type="password" name="pw" />
      <input type="Submit" value="Login" />
    </form>
  </div>
  
<?php
include '../includes/footer.php';
?>
