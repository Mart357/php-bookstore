<?php

require_once('./connection.php');

// Get the book ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>Error: Book ID not provided.</p>";
    exit;
}

// If the form is submitted, update the book details
if (isset($_POST['submit_book']) && $_POST['submit_book'] == 'Save') {
    // Prepare the SQL statement to update the book details
    $stmt = $pdo->prepare('UPDATE books SET 
        title = :title,
        release_date = :release_date,
        language = :language,
        summary = :summary,
        price = :price,
        stock_saldo = :stock_saldo,
        pages = :pages,
        type = :type
        WHERE id = :id');
    
    // Execute the update query
    $stmt->execute([
        'id' => $_POST['id'],
        'title' => $_POST['title'],
        'release_date' => $_POST['release_date'],
        'language' => $_POST['language'],
        'summary' => $_POST['summary'],
        'price' => $_POST['price'],
        'stock_saldo' => $_POST['stock_saldo'],
        'pages' => $_POST['pages'],
        'type' => $_POST['type']
    ]);
    
    // Add new authors if selected
    if (!empty($_POST['authors'])) {
        foreach ($_POST['authors'] as $author_id) {
            $stmt = $pdo->prepare('INSERT INTO book_authors (book_id, author_id) VALUES (:book_id, :author_id)');
            $stmt->execute(['book_id' => $_POST['id'], 'author_id' => $author_id]);
        }
    }

    // Add a manually typed author if provided
    if (!empty($_POST['author_name'])) {
        // Insert the author into the authors table
        $stmt = $pdo->prepare('INSERT INTO authors (first_name) VALUES (:first_name)');
        $stmt->execute(['first_name' => $_POST['author_name']]);
        
        // Get the newly inserted author ID
        $author_id = $pdo->lastInsertId();
        
        // Link this new author to the book
        $stmt = $pdo->prepare('INSERT INTO book_authors (book_id, author_id) VALUES (:book_id, :author_id)');
        $stmt->execute(['book_id' => $_POST['id'], 'author_id' => $author_id]);
    }

    // Redirect after saving
    header('Location: ./book.php?id=' . $_POST['id']);
    exit();
}

// If an author is being removed
if (isset($_GET['remove_author'])) {
    $author_id = $_GET['remove_author'];
    $stmt = $pdo->prepare('DELETE FROM book_authors WHERE book_id = :book_id AND author_id = :author_id');
    $stmt->execute(['book_id' => $id, 'author_id' => $author_id]);
    header('Location: ./edit.php?id=' . $id); // Refresh page after removal
    exit();
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

// Prepare the SQL to fetch authors of the book
$authorStmt = $pdo->prepare('
    SELECT authors.id, CONCAT(authors.first_name, " ", authors.last_name) AS full_name 
    FROM authors 
    JOIN book_authors ON authors.id = book_authors.author_id 
    WHERE book_authors.book_id = :id
');
$authorStmt->execute(['id' => $id]);
$authors = $authorStmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare the SQL to fetch all authors for the add option
$allAuthorsStmt = $pdo->prepare('SELECT id, CONCAT(first_name, " ", last_name) AS full_name FROM authors');
$allAuthorsStmt->execute();
$allAuthors = $allAuthorsStmt->fetchAll(PDO::FETCH_ASSOC);

// Format release_date to YYYY-MM-DD
$release_date = date('Y-m-d', strtotime($book['release_date']));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book Details</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            font-size: 2.5em;
        }

        label {
            font-size: 16px;
            display: block;
            margin-top: 10px;
        }

        input[type="text"], input[type="number"], input[type="date"], textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .action-links {
            text-align: center;
            margin-top: 20px;
        }

        a {
            color: #4CAF50;
            font-size: 18px;
            text-decoration: none;
        }

        a:hover {
            color: #45a049;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 5px 0;
        }

        .remove-link {
            color: red;
            text-decoration: none;
        }

        .remove-link:hover {
            color: darkred;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Book Details</h1>

    <form action="./edit.php?id=<?= $book['id']; ?>" method="post">
        <input type="hidden" name="id" value="<?= $book['id']; ?>">

        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($book['title']); ?>" required>

        <label for="release_date">Release Date:</label>
        <!-- Show the correctly formatted release date -->
        <input type="date" name="release_date" id="release_date" value="<?= htmlspecialchars($release_date); ?>" required>

        <label for="language">Language:</label>
        <input type="text" name="language" id="language" value="<?= htmlspecialchars($book['language']); ?>" required>

        <label for="summary">Summary:</label>
        <textarea name="summary" id="summary" required><?= htmlspecialchars($book['summary']); ?></textarea>

        <label for="price">Price ($):</label>
        <input type="number" name="price" id="price" value="<?= htmlspecialchars($book['price']); ?>" step="0.01" required>

        <label for="stock_saldo">Stock:</label>
        <input type="number" name="stock_saldo" id="stock_saldo" value="<?= htmlspecialchars($book['stock_saldo']); ?>" required>

        <label for="pages">Pages:</label>
        <input type="number" name="pages" id="pages" value="<?= htmlspecialchars($book['pages']); ?>" required>

        <label for="type">Type:</label>
        <input type="text" name="type" id="type" value="<?= htmlspecialchars($book['type']); ?>" required>

        <!-- Add authors -->
        <label for="authors">Add Authors:</label>
        <select name="authors[]" id="authors" multiple>
            <?php foreach ($allAuthors as $author): ?>
                <option value="<?= $author['id']; ?>"><?= htmlspecialchars($author['full_name']); ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Add manually typed author -->
        <label for="author_name">Or Add New Author:</label>
        <input type="text" name="author_name" id="author_name" placeholder="Enter author name">

        <h3>Current Authors:</h3>
        <ul>
            <?php foreach ($authors as $author): ?>
                <li>
                    <?= htmlspecialchars($author['full_name']); ?>
                    <a href="./edit.php?id=<?= $book['id']; ?>&remove_author=<?= $author['id']; ?>" class="remove-link">Remove</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <input type="submit" name="submit_book" value="Save">
    </form>

    <div class="action-links">
        <a href="./book.php?id=<?= htmlspecialchars($book['id']); ?>">Cancel</a>
    </div>
</div>

</body>
</html>
