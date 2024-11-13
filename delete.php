<?php
require_once('./connection.php');

// Get the book ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<p>Error: Book ID not provided.</p>";
    exit;
}

// First, delete the related orders from the orders table
$orderStmt = $pdo->prepare('DELETE FROM orders WHERE book_id = :id');
$orderStmt->execute(['id' => $id]);

// Then, delete the related entries from the book_authors table (if any)
$authorStmt = $pdo->prepare('DELETE FROM book_authors WHERE book_id = :id');
$authorStmt->execute(['id' => $id]);

// Finally, delete the book from the books table
$stmt = $pdo->prepare('DELETE FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);

// Redirect back to the main page (index.php) after the deletion
header('Location: index.php');
exit();
?>
