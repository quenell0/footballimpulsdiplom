<?php
session_start();
require_once './assets/classes/dbd.php';
$db = new Dbh();
$pdo = $db->connect_pdo();

require_once 'blade/header.php';

if (isset($_SESSION['user_login'])) {
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        require_once 'blade/menuadmin.php';
    } else {
        require_once 'blade/menuuser.php';
    }
} else {
    require_once 'blade/menu.php';
}

if (!isset($_GET['id'])) {
    echo "<h2 style='color:white;text-align:center;'>Матч не выбран.</h2>";
    require_once 'blade/footer.php';
    exit();
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT Matches.*, COALESCE(Comands.name_comand, Matches.home_team) AS home_team_name FROM Matches LEFT JOIN Comands ON Matches.home_team = Comands.comand_id WHERE Matches.id_match = ?");
$stmt->execute([$id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    echo "<h2 style='color:white;text-align:center;'>Матч не найден.</h2>";
    require_once 'blade/footer.php';
    exit();
}

$matchDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $match['date_match'] . ' ' . $match['time_match']);
$now = new DateTime();
$isMatchFuture = $matchDateTime && $matchDateTime > $now;

$userEmail = '';
if (isset($_SESSION['user_id'])) {
    $stmtUser = $pdo->prepare("SELECT email FROM User WHERE user_id = ?");
    $stmtUser->execute([$_SESSION['user_id']]);
    $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if ($userData) {
        $userEmail = $userData['email'];
    }
}

$statusMessage = '';
$statusClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $statusMessage = 'Войдите, чтобы купить билет.';
        $statusClass = 'error-message';
    } elseif (!$isMatchFuture) {
        $statusMessage = 'Покупка билетов на этот матч уже недоступна.';
        $statusClass = 'error-message';
    } else {
        $sector = strtoupper(trim($_POST['sector'] ?? ''));
        $row = (int) ($_POST['row'] ?? 0);
        $place = (int) ($_POST['place'] ?? 0);
        $emailTicket = trim($_POST['email_ticket'] ?? '');

        if (!in_array($sector, ['A', 'B', 'C', 'D'], true)) {
            $statusMessage = 'Выберите корректный сектор.';
            $statusClass = 'error-message';
        } elseif ($row < 1 || $row > 10) {
            $statusMessage = 'Выберите корректный ряд от 1 до 10.';
            $statusClass = 'error-message';
        } elseif ($place < 1 || $place > 25) {
            $statusMessage = 'Выберите корректное место от 1 до 25.';
            $statusClass = 'error-message';
        } elseif (!filter_var($emailTicket, FILTER_VALIDATE_EMAIL)) {
            $statusMessage = 'Введите корректный email.';
            $statusClass = 'error-message';
        } else {
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM Tickets WHERE id_match = ? AND sector = ? AND `row` = ? AND place = ?");
            $stmtCheck->execute([$id, $sector, $row, $place]);
            $isTaken = (int) $stmtCheck->fetchColumn();

            if ($isTaken > 0) {
                $statusMessage = 'Это место уже занято. Выберите другое.';
                $statusClass = 'error-message';
            } else {
                $ticketId = (int) $pdo->query("SELECT COALESCE(MAX(ticket_id), 0) + 1 FROM Tickets")->fetchColumn();
                $ticketNum = null;
                $attempts = 0;
                do {
                    $ticketNum = random_int(100000, 999999);
                    $stmtNum = $pdo->prepare("SELECT COUNT(*) FROM Tickets WHERE num_ticket = ?");
                    $stmtNum->execute([$ticketNum]);
                    $exists = (int) $stmtNum->fetchColumn();
                    $attempts++;
                } while ($exists > 0 && $attempts < 10);

                if ($exists > 0) {
                    $statusMessage = 'Не удалось сгенерировать номер билета. Повторите позже.';
                    $statusClass = 'error-message';
                } else {
                    $stmtInsert = $pdo->prepare("INSERT INTO Tickets (ticket_id, id_match, sector, `row`, place, user_id, email_ticket, num_ticket) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $success = $stmtInsert->execute([$ticketId, $id, $sector, $row, $place, $_SESSION['user_id'], $emailTicket, $ticketNum]);

                    if ($success) {
                        $statusMessage = 'Покупка прошла успешно! Номер билета: ' . htmlspecialchars($ticketNum);
                        $statusClass = 'success-message';
                    } else {
                        $statusMessage = 'Не удалось сохранить билет. Попробуйте ещё раз.';
                        $statusClass = 'error-message';
                    }
                }
            }
        }
    }
}

$bookedSeats = [];
$stmtSeats = $pdo->prepare("SELECT sector, `row`, place FROM Tickets WHERE id_match = ?");
$stmtSeats->execute([$id]);
while ($row = $stmtSeats->fetch(PDO::FETCH_ASSOC)) {
    $bookedSeats[] = [
        'sector' => $row['sector'],
        'row' => (int) $row['row'],
        'place' => (int) $row['place'],
    ];
}

?>

<title>Покупка билета</title>

<div class="wrapper">
    <div class="container">
        <div class="header-with-search" style="justify-content:flex-start; gap: 12px; margin-bottom: 24px;">
            <a href="matchpage.php?id=<?= $match['id_match'] ?>" class="back-button">← Вернуться к матчу</a>
            <h1 style="margin:0; color: var(--text);">Покупка билета</h1>
        </div>

        <div class="ticket-page">

            <?php if ($statusMessage): ?>
                <div class="ticket-status <?= $statusClass ?>">
                    <?= $statusMessage ?>
                </div>
            <?php endif; ?>

            <?php if ($isMatchFuture && isset($_SESSION['user_id'])): ?>
                <form method="post" class="ticket-form">
                    <div class="form-row">
                        <h2>Матч</h2>
                <p><strong><?= htmlspecialchars($match['title_match']) ?></strong></p>
                <p>Дата: <?= htmlspecialchars($match['date_match']) ?> | <?= htmlspecialchars($match['time_match']) ?></p>
                <p>Команды: <?= htmlspecialchars($match['home_team_name'] ?? $match['home_team']) ?> vs <?= htmlspecialchars($match['away_team']) ?></p>
                <p>Цена билета: <?= htmlspecialchars($match['price_ticket']) ?>₽</p>
                <?php if (!$isMatchFuture): ?>
                    <div class="ticket-badge finished">Покупка билетов недоступна — матч завершён</div>
                <?php endif; ?>
                    </div>
                    <div class="form-row">
                        <label for="sector">Сектор</label>
                        <select id="sector" name="sector" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="row">Ряд</label>
                        <select id="row" name="row" required>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="place">Место</label>
                        <select id="place" name="place" required></select>
                    </div>
                    <div class="form-row">
                        <label for="email_ticket">Email</label>
                        <input id="email_ticket" type="email" name="email_ticket" value="<?= htmlspecialchars($userEmail) ?>" required>
                    </div>
                    <button type="submit" class="ticket-btn" style="margin-top: 10px;">Купить билет</button>
                </form>
            <?php elseif ($isMatchFuture): ?>
                <p style="color: var(--text);">Войдите, чтобы купить билет.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const reservedSeats = <?= json_encode($bookedSeats, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const sectorSelect = document.getElementById('sector');
    const rowSelect = document.getElementById('row');
    const placeSelect = document.getElementById('place');

    function updatePlaces() {
        const sector = sectorSelect.value;
        const row = Number(rowSelect.value);
        const taken = reservedSeats
            .filter(item => item.sector === sector && item.row === row)
            .map(item => item.place);

        placeSelect.innerHTML = '';
        let availableCount = 0;

        for (let i = 1; i <= 25; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            if (taken.includes(i)) {
                option.disabled = true;
                option.textContent = i + ' — занято';
            } else {
                availableCount++;
            }
            placeSelect.appendChild(option);
        }

        if (availableCount === 0) {
            placeSelect.innerHTML = '<option disabled selected>Нет свободных мест в этом ряду</option>';
            placeSelect.disabled = true;
        } else {
            placeSelect.disabled = false;
        }
    }

    if (sectorSelect && rowSelect && placeSelect) {
        sectorSelect.addEventListener('change', updatePlaces);
        rowSelect.addEventListener('change', updatePlaces);
        updatePlaces();
    }
</script>

<?php require_once 'blade/footer.php'; ?>
