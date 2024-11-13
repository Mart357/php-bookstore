<?php
require_once("./connection.php");

// Get the search query from the form submission (if any)
$search = $_GET['search'] ?? '';

// Prepare the SQL query with a WHERE clause to filter based on the search term
if ($search) {
    $stmt = $pdo->prepare('SELECT id, title FROM books WHERE title LIKE :search');
    $stmt->execute(['search' => '%' . $search . '%']);
} else {
    $stmt = $pdo->query('SELECT id, title FROM books');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book List</title>

    <!-- Inline CSS for styling -->
    <style>
        body {
            background-image: url('https://as2.ftcdn.net/v2/jpg/08/97/90/21/1000_F_897902120_OEXsPolkAXSeixlhNK1PghPkbu1VlO5E.jpg'); /* Replace with actual background image */
            font-family: 'Georgia', serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 50px;
            color: seashell;
            font-size: 3em;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.6);
        }

        .search-container {
            text-align: center;
            margin: 30px auto;
        }

        .search-container input[type="text"] {
            padding: 12px;
            font-size: 18px;
            width: 250px;
            margin-right: 10px;
            border: 2px solid #4CAF50;
            border-radius: 5px;
            background-color: whitesmoke;
            color: #333;
        }

        .search-container button {
            padding: 12px 20px;
            font-size: 18px;
            background-color: olivedrab;
            color: white;
            border: 2px solid #4CAF50;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #45a049;
        }

        ul {
            padding: 20px;
            list-style-type: none;
            background-color: rgba(30,20,10,5);
            margin: 20px auto;
            width: 70%;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        li {
            margin: 15px 0;
            padding: 12px;
            background-color: brown;
            border-left: 5px solid #4CAF50;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        li:hover {
            background-color: olive;
        }

        a {
            text-decoration: none;
            color: whitesmoke;
            font-size: 18px;
        }

        a:hover {
            color: white;
        }

        body {
            padding: 20px;
        }

        /* Optionally, add a soft book texture to the background */
        .container {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            padding: 20px;
        }
    </style>

</head>
<body>

<h1>Book List</h1>

<!-- Search Form -->
<div class="search-container">
    <form method="get" action="index.php">
        <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Search for a book..." />
        <button type="submit">Search</button>
    </form>
</div>

<!-- Book List -->
<ul>
    <?php
        while ($book = $stmt->fetch()) { ?>
        <li>
            <a href="./book.php?id=<?= $book['id']; ?>">
                <?= htmlspecialchars($book['title']); ?>
            </a>
        </li>
    <?php }
    ?>
</ul>

</body>
</html>
