<footer class="site-footer">
<div class="footer-content2">
    <h2>✆Телефон</h2>
    <div class="fc2p">
    <p>Поддержка: +7(965)836-45-30</p>
    <p>Офис «Импульс»: +7(952)527-47-67</p>
    <p>Сотрудничество: +7(952)528-48-68</p>
    </div>
    </div>
    <div class="footer-cont">
    <div class="fca">
    <div class="footer-content">
    <a href="info_about.php">О нас</a>
    <a href="news.php">Новости</a>
    <a href="matches.php">Матчи</a>
    </div>
    <div class="footer-content">
    <a href="players.php">Футболисты</a>
    <a href="info_teams.php">Команды</a>
    <a href="stadiums.php">Стадионы</a>
    </div>
    </div>
    <div class="social-icon">
            <h3>Социальные сети</h3>
            <div class="icons">
            <a href="https://vk.com/Импульс_football" target="_blank">
                <img src="assets/img/vk-v2.svg" alt="ВКонтакте">
            </a>
            <a href="https://rutube.ru" target="_blank">
                <img src="assets/img/rutube-sign-logo.svg" alt="RuTube">
            </a>
            <a href="https://dzen.ru" target="_blank">
                <img src="assets/img/dzen-icon-logo.svg" alt="Яндекс.Дзен">
            </a>
            </div>
        </div>
    <div class="footer-content">
    <p>&copy; 2026 ФК «Импульс». Все права защищены.</p>
    </div>
    </div>
    <div class="footer-content2">
        <h2>✉Почта</h2>
        <div class="fc2p">
        <p>Поддержка: impulssupport@gmail.com</p>
        <p>Офис «Импульс»: officeimpuls@gmail.com</p>
        <p>Сотрудничество: impulssotr@gmail.com</p>
        </div>
    </div>
</footer>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.site-nav__toggle').forEach(function (toggle) {
      toggle.addEventListener('click', function () {
        var nav = toggle.closest('.site-nav');
        if (!nav) return;
        var isOpen = nav.classList.toggle('menu-open');
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    });
  });
</script>
</body>
</html>