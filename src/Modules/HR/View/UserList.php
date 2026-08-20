<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Modules\Service\ActionChecker;

$userId = $getUserId->getUserIdFromSession() ?? 0;
$userActions = array_column($actionChecker->UserModuleChecker('user', $userId),'action_code');
$roleActions = array_column($actionChecker->UserModuleChecker('role', $userId),'action_code');

?>

<?php require __DIR__ . '/../../../Web/Shared/View/flashToast.php'; ?>

<div class="overflow-hidden rounded-2xl border border-slate-700 bg-slate-900 shadow-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-700 text-left text-sm text-slate-200">
                <thead class="bg-slate-950 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <tr>
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Username</th>
                    <th class="px-6 py-4">Date Updated</th>
                    <th class="px-6 py-4">Is Active</th>
                    <?php if (in_array('update_employee_user', $userActions, true)): ?>
                    <th class="px-6 py-4 text-right">Actions</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-700">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-slate-800/70">
                        <td class="px-6 py-4 font-medium text-white">
                            <?= Html::encode((string) ($user['id'])) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?= Html::encode((string) ($user['username'] ?? '-')) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?= Html::encode((string) ($user['date_updated'] ?? '-')) ?>
                        </td>
                        <td class="px-6 py-4">
                            <?= Html::encode((string) ($user['is_active'] ?? '-')) ?>
                        </td>
                        <?php if (in_array('update_employee_user', $userActions, true)): ?>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a
                                        href="/update-user/<?= Html::encode((string) $user['id']) ?>"
                                        class="rounded-lg border border-slate-600 px-3 py-2 font-medium text-white bg-blue-500 transition hover:bg-blue-800"
                                    >
                                    Edit
                                </a>
                                <a
                                    href="/delete-user/<?= Html::encode((string) $user['id']) ?>"
                                    class="rounded-lg border border-slate-500 px-3 py-2 font-medium text-white bg-amber-500 transition hover:bg-amber-700"
                                >
                                    Role
                                </a>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>