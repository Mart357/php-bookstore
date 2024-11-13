<?php
    // var_dump($_GET);    //muutujate sisu vaadata

    $firstName = $_GET["first_name"];
    $lastName = $_GET["last_name"];
    
    ?>
    
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
  </head>
  <body>
  <form action="./hello1.php" method="get">
    <input type="text" name="first_name">
    <input type="text" name="last_name">
    <input type="submit" name="action" value="Saada">
  </form>
    <?php echo "Hello,{$firstName} {$lastName}!";?>
  </body>
  </html>