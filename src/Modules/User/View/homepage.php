<?php

declare(strict_types=1);
?>

<h1 id="clock" class="text-3xl font-bold text-center mt-10"></h1>


<h2 class="text-2xl font-bold text-center mt-10">Welcome, <?= htmlspecialchars($user['username']) ?>! to the user RBAC Project</h2>

<h3 class="text-lg text-center mt-4">Updated: <?= htmlspecialchars($user['date_updated']) ?> </h3>



<script>
function updateClock() {
    const now = new Date();

    const formatted = now.getFullYear() + '-' +
        String(now.getMonth() + 1).padStart(2, '0') + '-' +
        String(now.getDate()).padStart(2, '0') + ' ' +
        String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0') + ':' +
        String(now.getSeconds()).padStart(2, '0');

    document.getElementById('clock').textContent = 'Time: ' + formatted;
}

updateClock();
setInterval(updateClock, 1000);
</script>