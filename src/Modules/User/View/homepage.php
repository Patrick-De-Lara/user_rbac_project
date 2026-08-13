<?php

declare(strict_types=1);
?>


    <h2 class="text-2xl font-bold text-center mt-10">Welcome, <?= htmlspecialchars($user['username']) ?>! to the user RBAC Project</h2>

    <h3 class="text-lg text-center mt-4">Updated: <?= htmlspecialchars($user['date_updated']) ?> </h3>