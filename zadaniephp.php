<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'przestepstwa';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $imie_przestepcy = $_POST['imie_przestepcy'];
    $lokalizacja = $_POST['lokalizacja'];
    $kolor_skory = $_POST['kolor_skory'];

    $stmt = $conn->prepare("INSERT INTO przestepstwa (imie_przestepcy, lokalizacja, kolor_skory) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $imie_przestepcy, $lokalizacja, $kolor_skory);
    $stmt->execute();
    $stmt->close();
}

// Modyfikowanie rekordu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $imie_przestepcy = $_POST['imie_przestepcy'];
    $lokalizacja = $_POST['lokalizacja'];
    $kolor_skory = $_POST['kolor_skory'];

    $stmt = $conn->prepare("UPDATE przestepstwa SET imie_przestepcy = ?, lokalizacja = ?, kolor_skory = ? WHERE id = ?");
    $stmt->bind_param("sssi", $imie_przestepcy, $lokalizacja, $kolor_skory, $id);
    $stmt->execute();
    $stmt->close();
}

// Usuwanie rekordu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM przestepstwa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Wyszukiwanie rekordów
$search = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $search = $_GET['search'];
}

$query = "SELECT * FROM przestepstwa";
if (!empty($search)) {
    $query .= " WHERE imie_przestepcy LIKE '%$search%' OR lokalizacja LIKE '%$search%' OR kolor_skory LIKE '%$search%'";
}
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny</title>
</head>
<body>
    <h1>Panel Administracyjny</h1>

    <!-- Formularz dodawania rekordu -->
    <h2>Dodaj rekord</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <label>
            Imię przestępcy:
            <input type="text" name="imie_przestepcy" required>
        </label>
            </br></br>
        <label>
            Lokalizacja:
            <input type="text" name="lokalizacja" required>
        </label>
            </br></br>
        <label>
            Kolor skóry przestępcy:
            <select name="kolor_skory">
                <option value="Niewinny">Niewinny</option>
                <option value="czarny">Czarny</option>
                <option value="inny">Inny</option>
            </select>
        </label>
        <button type="submit">Dodaj</button>
    </form>

    <!-- Wyszukiwarka -->
    <h2>Wyszukaj rekordy</h2>
    <form method="GET">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Szukaj</button>
    </form>

    <!-- Tabela z rekordami -->
    <h2>Lista rekordów</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Imię przestępcy</th>
            <th>Lokalizacja</th>
            <th>Kolor Skóry</th>
            <th>Akcje</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['imie_przestepcy']) ?></td>
            <td><?= htmlspecialchars($row['lokalizacja']) ?></td>
            <td><?= htmlspecialchars($row['kolor_skory']) ?></td>
            <td>
                <!-- Formularz modyfikacji rekordu -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="text" name="imie_przestepcy" value="<?= htmlspecialchars($row['imie_przestepcy']) ?>" required>
                    <input type="text" name="lokalizacja" value="<?= htmlspecialchars($row['lokalizacja']) ?>" required>
                    <input type="text" name="kolor_skory" value="<?= htmlspecialchars($row['kolor_skory']) ?>">
                    <button type="submit">Zapisz</button>
                </form>

                <!-- Formularz usuwania rekordu -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit">Usuń</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>