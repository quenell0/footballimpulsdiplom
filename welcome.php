<?php
session_start();

// Проверяем, авторизован ли пользователь. Если нет, перенаправляем на страницу авторизации
if (!isset($_SESSION["user_login"])) {
    header("location: login.php");
    exit();
}

require_once 'assets/classes/dbd.php';
$db = new Dbh();
$pdo = $db->connect_pdo();

$user_id = $_SESSION["user_login"];
$statusMessage = '';
$statusClass = '';
$tickets = [];
$showEditForm = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['return_ticket'])) {
        $ticketId = (int) $_POST['return_ticket'];
        $stmtDelete = $pdo->prepare("DELETE FROM Tickets WHERE ticket_id = ? AND user_id = ?");
        if ($stmtDelete->execute([$ticketId, $user_id]) && $stmtDelete->rowCount() > 0) {
            $statusMessage = 'Билет успешно сдан.';
            $statusClass = 'success-message';
        } else {
            $statusMessage = 'Не удалось сдать билет. Повторите попытку.';
            $statusClass = 'error-message';
        }
    } elseif (isset($_POST['update_profile'])) {
        $showEditForm = true;
        $newUsername = trim($_POST['username'] ?? '');
        $newEmail = trim($_POST['email'] ?? '');

        if ($newUsername === '') {
            $statusMessage = 'Введите никнейм.';
            $statusClass = 'error-message';
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $statusMessage = 'Введите корректный email.';
            $statusClass = 'error-message';
        } else {
            $stmtUpdate = $pdo->prepare("UPDATE User SET username = ?, email = ? WHERE user_id = ?");
            if ($stmtUpdate->execute([$newUsername, $newEmail, $user_id])) {
                $statusMessage = 'Профиль обновлён.';
                $statusClass = 'success-message';
            } else {
                $statusMessage = 'Не удалось сохранить изменения. Попробуйте снова.';
                $statusClass = 'error-message';
            }
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT username, email, foto, role FROM User WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Ошибка: Пользователь не найден.";
        exit();
    }

    $username = $user['username'];
    $email = $user['email'];
    $avatar = $user['foto'];
    $role = $user['role'];

    $stmtTickets = $pdo->prepare(
        "SELECT Tickets.ticket_id, Tickets.num_ticket, Tickets.sector, Tickets.`row`, Tickets.place, Tickets.id_match,
                Matches.title_match, Matches.date_match, Matches.time_match
         FROM Tickets
         JOIN Matches ON Tickets.id_match = Matches.id_match
         WHERE Tickets.user_id = :user_id
         ORDER BY Tickets.ticket_id DESC"
    );
    $stmtTickets->execute([':user_id' => $user_id]);
    $tickets = $stmtTickets->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Ошибка базы данных: " . $e->getMessage();
    exit();
} finally {
    if ($pdo) {
        $db->close_connect();
    }
}

require_once 'blade/header.php';

if (isset($_SESSION['user_login'])) {
    $roleee = $_SESSION['user_role'] ?? 'user';

    if ($roleee === 'admin') {
        require_once 'blade/menuadmin.php';
    } else {
        require_once 'blade/menuuser.php';
    }
} else {
    require_once 'blade/menu.php';
}
?>


    <div class="wrapper profile-wrap">
        <div class="container"> 
            <div class="col-lg-12">  
                <center>   
                    <?php if (!empty($avatar)) : ?>  
                        <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Аватар пользователя" class="avatar">  
                    <?php else : ?>  
                        <p>У вас нет аватара.</p>  
                    <?php endif; ?>  
                    <p>Никнейм: <?php echo htmlspecialchars($username); ?></p>  
                    <p>Email: <?php echo htmlspecialchars($email); ?></p>  
                    <p>Роль: <?php echo htmlspecialchars($role); ?></p>  
                </center>  
            </div>  

            <div class="col-lg-12" style="margin-top: 32px;">   
                <button type="button" class="ticket-btn edit-profile-button" id="editProfileButton">Редактировать профиль</button>  
                <div id="profileEditSection" class="profile-edit-section<?php echo $showEditForm ? ' active' : ''; ?>">  
                    <form method="post" class="profile-edit-form">  
                        <input type="hidden" name="update_profile" value="1">  
                        <div class="form-row">  
                            <label for="username">Никнейм</label>  
                            <input id="username" type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>  
                        </div>  
                        <div class="form-row">  
                            <label for="email">Email</label>  
                            <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>  
                        </div>  
                        <button type="submit" class="ticket-btn" style="margin-top: 0;">Сохранить изменения</button>  
                    </form>  
                </div>  
            </div>  

            <div class="col-lg-12" style="margin-top: 32px;">  
                <h2>Мои билеты</h2>  

                <?php if ($statusMessage): ?>  
                    <div class="ticket-status <?php echo htmlspecialchars($statusClass); ?>">  
                        <?php echo htmlspecialchars($statusMessage); ?>  
                    </div>  
                <?php endif; ?>  

                <?php if (!empty($tickets)): ?>  
                    <div class="ticket-table-wrap">  
                    <table class="ticket-table">  
                        <thead>  
                            <tr>  
                                <th>Матч</th>  
                                <th>Дата</th>  
                                <th>Сектор</th>  
                                <th>Ряд</th>  
                                <th>Место</th>  
                                <th>Номер билета</th>  
                                <th>Действие</th>  
                            </tr>  
                        </thead>  
                        <tbody>  
                            <?php foreach ($tickets as $ticket): ?>  
                                <tr>  
                                    <td><?php echo htmlspecialchars($ticket['title_match']); ?></td>  
                                    <td><?php echo htmlspecialchars($ticket['date_match']); ?> <?php echo htmlspecialchars($ticket['time_match']); ?></td>  
                                    <td><?php echo htmlspecialchars($ticket['sector']); ?></td>  
                                    <td><?php echo htmlspecialchars($ticket['row']); ?></td>  
                                    <td><?php echo htmlspecialchars($ticket['place']); ?></td>  
                                    <td><?php echo htmlspecialchars($ticket['num_ticket']); ?></td>  
                                    <td>  
                                        <form method="post" onsubmit="return confirm('Вы уверены?');">  
                                            <input type="hidden" name="return_ticket" value="<?php echo htmlspecialchars($ticket['ticket_id']); ?>">  
                                            <button type="submit" class="ticket-btn">Сдать билет</button>  
                                        </form>  
                                    </td>  
                                </tr>  
                            <?php endforeach; ?>  
                        </tbody>  
                    </table>  
                    </div>  
                <?php else: ?>  
                    <p>У вас пока нет купленных билетов.</p>  
                <?php endif; ?>  
            </div>  
        </div>  
    </div>  

    <script>
        const editProfileButton = document.getElementById('editProfileButton');
        const profileEditSection = document.getElementById('profileEditSection');

        if (editProfileButton && profileEditSection) {
            editProfileButton.addEventListener('click', () => {
                profileEditSection.classList.toggle('active');
                editProfileButton.textContent = profileEditSection.classList.contains('active')
                    ? 'Скрыть редактирование'
                    : 'Редактировать профиль';
            });
        }
    </script>
    <?php include_once 'blade/footer.php'; ?>  
