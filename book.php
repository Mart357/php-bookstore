<?php

require_once('./connection.php');

// Get the book ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>Error: Book ID not provided.</p>";
    exit;
}

// Prepare the SQL statement to fetch the book details
$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

// Check if a book was found
if (!$book) {
    echo "<p>Error: Book not found.</p>";
    exit;
}

// Prepare a SQL statement to fetch all authors for this book
$authorStmt = $pdo->prepare('
    SELECT CONCAT(authors.first_name, " ", authors.last_name) AS full_name 
    FROM authors 
    JOIN book_authors ON authors.id = book_authors.author_id 
    WHERE book_authors.book_id = :id
');
$authorStmt->execute(['id' => $id]);
$authors = $authorStmt->fetchAll(PDO::FETCH_COLUMN);

// Ensure $authors is an array (if no authors, it will be an empty array)
$authors = $authors ?? [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']); ?></title>

    <!-- Inline CSS for styling -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://i.pinimg.com/1200x/f2/58/14/f258140582ce9fb7dfa524cd06a8b290.jpg'); /* Replace with your library background image URL */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #333;
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .container {
            width: 80%;
            margin: 20px auto;
        }

        h1 {
            color: brown;
            font-size: 2.5em;
            margin-top: 0;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        strong {
            color: #333;
        }

        .action-links {
            margin-top: 20px;
            text-align: center;
        }

        a {
            color: brown;
            font-size: 18px;
            text-decoration: none;
            margin: 0 10px;
        }

        a:hover {
            color: maroon;
        }

        /* Center the cover photo */
        .cover-img {
            display: block;
            max-width: 200px;  /* Adjust the size of the cover */
            margin: 20px auto; /* Center the image horizontally */
            border-radius: 5px;
        }

        button {
            color: brown;
            font-size: 18px;
            cursor: pointer;
            margin: 0 10px;
        }

        button:hover {
            color: maroon;
        }
    </style>
</head>
<body>

<div class="container">
    <h1><?= htmlspecialchars($book['title']); ?></h1>

    <p><strong>Authors:</strong> 
        <?= htmlspecialchars(implode(', ', $authors)); ?>
    </p>
    <p><strong>Release Date:</strong> <?= htmlspecialchars($book['release_date']); ?></p>
    <p><strong>Language:</strong> <?= htmlspecialchars($book['language']); ?></p>
    <p><strong>Summary:</strong> <?= htmlspecialchars($book['summary']); ?></p>
    <p><strong>Price:</strong> $<?= htmlspecialchars($book['price']); ?></p>
    <p><strong>Stock:</strong> <?= htmlspecialchars($book['stock_saldo']); ?> units</p>
    <p><strong>Pages:</strong> <?= htmlspecialchars($book['pages']); ?></p>
    <p><strong>Type:</strong> <?= htmlspecialchars($book['type']); ?></p>

    <?php if (!empty($book['cover_path'])): ?>
        <p><strong>Cover:</strong></p>
        <img src="<?= htmlspecialchars($book['cover_path']); ?>" alt="<?= htmlspecialchars($book['title']); ?>" class="cover-img">
    <?php endif; ?>

    <div class="action-links">
        <a href="./edit.php?id=<?= htmlspecialchars($book['id']); ?>">Edit</a> | 
        <a href="./delete.php?id=<?= htmlspecialchars($book['id']); ?>" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a> | 
        <a href="./index.php?id=<?= htmlspecialchars($book['id']); ?>">Back</a>
    </div>
</div>

</body>
</html>
