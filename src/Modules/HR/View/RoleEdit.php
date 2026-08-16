<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var string $title        'Create Role' or 'Edit Role'
 * @var string $submitLabel  'Create Role' or 'Save Changes'
 * @var string $actionUrl    '/role-list/create' or '/role-list/edit?id=X'
 * @var array  $fields       ['code' => '', 'description' => '', 'is_active' => 1]
 * @var array  $errors       ['code' => 'msg', 'description' => 'msg']
 * @var array  $flash        ['success' => '...', 'error' => '...']
 * @var array  $allActions   All rows from sys_user_action (only passed on edit)
 * @var array  $roleActions  Assigned action IDs for this role (only passed on edit)
 */

$fields     = $fields     ?? ['code' => '', 'description' => '', 'is_active' => 1];
$errors     = $errors     ?? [];
$flash      = $flash      ?? [];
$allActions = $allActions ?? [];
$roleActions = $roleActions ?? [];

// Detect mode
$isEdit = isset($actionUrl) && str_contains($actionUrl, 'edit');


// Extract role id from actionUrl for forms that need it
$roleId = 0;

if ($isEdit) {
    $query = parse_url($actionUrl ?? '', PHP_URL_QUERY) ?? '';
    parse_str($query, $params);

    $roleId = isset($params['id']) ? (int) $params['id'] : 0;
}

// var_dump($roleId);
// exit;

// Build a set of assigned action IDs for fast lookup
$assignedActionIds = array_map('intval', array_column($roleActions, 'action_id'));
?>

<div class="space-y-6">

    <!-- =========================================================
         PAGE HEADER
         ========================================================= -->
    <div class="flex items-center gap-4">

        <a
            href="/role-list"
            class="
                flex h-9 w-9 items-center justify-center
                rounded-lg border border-slate-300
                text-slate-500
                transition hover:border-slate-400 hover:text-slate-700
            "
            aria-label="Back to roles"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                <?= Html::encode($title ?? 'Role') ?>
            </h1>
            <p class="mt-0.5 text-sm text-slate-500">
                <?= $isEdit
                    ? 'Update role details and manage its permissions.'
                    : 'Fill in the details to create a new role.' ?>
            </p>
        </div>

    </div>


    <!-- =========================================================
         FLASH MESSAGES
         ========================================================= -->
    <?php if (!empty($flash['success'])): ?>
        <div
            id="successFlash"
            role="status"
            class="fixed bottom-5 right-5 z-50 flex max-w-sm items-center gap-3 rounded-lg border border-green-700 bg-green-900 px-4 py-3 text-sm text-green-100 shadow-xl transition duration-300"
        >
            <span class="text-lg" aria-hidden="true">✓</span>
            <span><?= Html::encode($flash['success']) ?></span>
        </div>

        <script>
        window.setTimeout(function () {
            const successFlash = document.getElementById('successFlash');
            if (!successFlash) return;

            successFlash.classList.add('translate-y-2', 'opacity-0');
            window.setTimeout(() => successFlash.remove(), 300);
        }, 5000);
        </script>
    <?php endif; ?>

    <?php if (!empty($flash['error'])): ?>
        <div class="rounded-lg border border-red-700 bg-red-900/30 px-4 py-3 text-sm text-red-300">
            <?= Html::encode($flash['error']) ?>
        </div>
    <?php endif; ?>


    <!-- =========================================================
         ROLE DETAILS FORM
         ========================================================= -->
    <div class="overflow-hidden rounded-2xl border border-slate-300 shadow-lg">

        <div class="border-b border-slate-300 px-6 py-5">
            <h2 class="text-lg font-semibold text-black">Role Details</h2>
            <p class="mt-1 text-sm text-slate-600">
                <?= $isEdit ? 'Edit the role information below.' : 'Enter the role information below.' ?>
            </p>
        </div>

        <form method="POST" action="<?= Html::encode($actionUrl ?? '') ?>" novalidate>

        <?= $csrf->hiddenInput() ?>

            <div class="space-y-6 p-6">

                <!-- Code -->
                <div>
                    <label for="code" class="mb-2 block text-sm font-medium text-slate-700">
                        Role Code <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="code"
                        name="code"
                        type="text"
                        maxlength="20"
                        autocomplete="off"
                        placeholder="e.g. Administrator, HR Manager"
                        value="<?= Html::encode((string) ($fields['code'] ?? '')) ?>"
                        class="w-full rounded-lg border px-4 py-3 text-sm outline-none transition
                            <?= isset($errors['code'])
                                ? 'border-red-600 bg-red-950/30 text-white placeholder-red-400 focus:border-red-500 focus:ring-1 focus:ring-red-500'
                                : 'border-slate-700 bg-slate-950 text-white placeholder-slate-500 focus:border-slate-500 focus:ring-1 focus:ring-slate-500'
                            ?>"
                    >

                    <?php if (isset($errors['code'])): ?>
                        <p class="mt-1.5 text-xs text-red-400"><?= Html::encode($errors['code']) ?></p>
                    <?php else: ?>
                        <p class="mt-1.5 text-xs text-slate-500">Short unique identifier. Max 20 characters.</p>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-slate-700">
                        Description <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        maxlength="100"
                        placeholder="Describe what this role allows users to do..."
                        class="w-full resize-none rounded-lg border px-4 py-3 text-sm outline-none transition
                            <?= isset($errors['description'])
                                ? 'border-red-600 bg-red-950/30 text-white placeholder-red-400 focus:border-red-500 focus:ring-1 focus:ring-red-500'
                                : 'border-slate-700 bg-slate-950 text-white placeholder-slate-500 focus:border-slate-500 focus:ring-1 focus:ring-slate-500'
                            ?>"
                    ><?= Html::encode((string) ($fields['description'] ?? '')) ?></textarea>

                    <?php if (isset($errors['description'])): ?>
                        <p class="mt-1.5 text-xs text-red-400"><?= Html::encode($errors['description']) ?></p>
                    <?php else: ?>
                        <p class="mt-1.5 text-xs text-slate-500">Max 100 characters.</p>
                    <?php endif; ?>
                </div>

                <!-- Is Active -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                    <label class="inline-flex cursor-pointer items-center gap-3">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            <?= ((int) ($fields['is_active'] ?? 1)) === 1 ? 'checked' : '' ?>
                            class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-blue-600 focus:ring-blue-500"
                        >
                        <span class="text-sm text-slate-700">
                            Active — this role can be assigned to users
                        </span>
                    </label>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-6 py-4">

                <a
                    href="/role-list"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:border-slate-400 hover:text-slate-800"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="rounded-lg <?= $isEdit ? 'bg-blue-600 hover:bg-blue-500' : 'bg-green-600 hover:bg-green-500' ?> px-6 py-2.5 text-sm font-medium text-white shadow-sm transition"
                >
                    <?= Html::encode($submitLabel ?? 'Submit') ?>
                </button>

            </div>

        </form>
    </div>


    <?php if ($isEdit): ?>

    <!-- =========================================================
         ACTION / PERMISSION ASSIGNMENT
         Only shown in edit mode — you need a role ID to assign actions.
         Left:  sys_user_action  (available actions)
         Right: sys_user_role_has_action (actions assigned to this role)
         ========================================================= -->
    <div class="overflow-hidden rounded-2xl border border-slate-300 shadow-lg">

        <div class="border-b border-slate-300 px-6 py-5">
            <h2 class="text-lg font-semibold text-black">Role Permissions</h2>
            <p class="mt-1 text-sm text-slate-600">
                Manage which actions are granted to the
                <span class="font-medium text-slate-800"><?= Html::encode((string) ($fields['code'] ?? '')) ?></span>
                role.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-0 lg:grid-cols-2 lg:divide-x lg:divide-slate-200">


            <!-- =================================================
                 LEFT — Available Actions (sys_user_action)
                 ================================================= -->
            <div class="flex flex-col">

                <div class="border-b border-slate-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800">Available Actions</h3>
                            <p class="mt-0.5 text-xs text-slate-500">All actions defined in the system.</p>
                        </div>
                        <span id="availableActionCount" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">
                            0
                        </span>
                    </div>

                    <div class="mt-3">
                        <input
                            id="availableActionSearch"
                            type="text"
                            placeholder="Search available actions..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-slate-400"
                        >
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Action</th>
                                <th class="px-5 py-3 text-right">Add</th>
                            </tr>
                        </thead>
                        <tbody id="availableActions" class="divide-y divide-slate-100 bg-white">

                            <?php foreach ($allActions as $action):
                                $isAssigned = in_array((int) $action['id'], $assignedActionIds, true);
                                if ($isAssigned) continue; // starts in right panel
                            ?>
                                <tr
                                    class="action-row transition hover:bg-slate-50"
                                    data-action-id="<?= Html::encode((string) $action['id']) ?>"
                                    data-action-name="<?= Html::encode(strtolower((string) $action['code'])) ?>"
                                >
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800">
                                            <?= Html::encode((string) $action['code']) ?>
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            <?= Html::encode((string) $action['description']) ?>
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <button
                                            type="button"
                                            class="add-action rounded-lg border border-emerald-600 bg-emerald-900/20 px-3 py-1.5 text-xs font-medium text-emerald-400 transition hover:bg-emerald-800/40"
                                        >
                                            + Add
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>

                    <div id="availableActionsEmpty" class="hidden px-6 py-8 text-center text-sm text-slate-400">
                        No available actions. All actions are assigned.
                    </div>
                </div>
            </div>


            <!-- =================================================
                 RIGHT — Assigned Actions (sys_user_role_has_action)
                 ================================================= -->
            <div class="flex flex-col">

                <div class="border-b border-slate-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800">Assigned Actions</h3>
                            <p class="mt-0.5 text-xs text-slate-500">Actions currently granted to this role.</p>
                        </div>
                        <span id="assignedActionCount" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-500">
                            0
                        </span>
                    </div>

                    <div class="mt-3">
                        <input
                            id="assignedActionSearch"
                            type="text"
                            placeholder="Search assigned actions..."
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-slate-400"
                        >
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Action</th>
                                <th class="px-5 py-3 text-right">Remove</th>
                            </tr>
                        </thead>
                        <tbody id="assignedActions" class="divide-y divide-slate-100 bg-white">

                            <?php foreach ($allActions as $action):
                                $isAssigned = in_array((int) $action['id'], $assignedActionIds, true);
                                if (!$isAssigned) continue; // only assigned ones here
                            ?>
                                <tr
                                    class="action-row transition hover:bg-slate-50"
                                    data-action-id="<?= Html::encode((string) $action['id']) ?>"
                                    data-action-name="<?= Html::encode(strtolower((string) $action['code'])) ?>"
                                >
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800">
                                            <?= Html::encode((string) $action['code']) ?>
                                        </p>
                                        <p class="text-xs text-slate-400">
                                            <?= Html::encode((string) $action['description']) ?>
                                        </p>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <button
                                            type="button"
                                            class="remove-action rounded-lg border border-red-600 bg-red-900/20 px-3 py-1.5 text-xs font-medium text-red-400 transition hover:bg-red-800/40"
                                        >
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>

                    <div id="assignedActionsEmpty" class="hidden px-6 py-8 text-center text-sm text-slate-400">
                        No actions assigned to this role.
                    </div>
                </div>
            </div>

        </div>

        <!-- Save Actions Footer -->
        <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-6 py-4">
            <p class="text-xs text-slate-500">
                Changes are saved immediately when you click Save Permissions.
            </p>
            <button
                type="button"
                id="saveActions"
                data-role-id="<?= $roleId ?>"
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-blue-500"
            >
                Save Permissions
            </button>
        </div>

    </div>


    <!-- =========================================================
         DANGER ZONE — edit mode only
         ========================================================= -->
    <div class="overflow-hidden rounded-2xl border border-red-300 shadow-lg">

        <div class="border-b border-red-300 bg-red-50 px-6 py-5">
            <h2 class="text-lg font-semibold text-red-700">Danger Zone</h2>
            <p class="mt-1 text-sm text-red-500">Destructive actions. These cannot be undone.</p>
        </div>

        <div class="flex items-center justify-between p-6">
            <div>
                <p class="text-sm font-medium text-slate-800">Delete this role</p>
                <p class="mt-0.5 text-sm text-slate-500">
                    Permanently removes the role. Blocked if any users are still assigned to it.
                </p>
            </div>

            <form
                method="POST"
                action="/role-list/delete"
                onsubmit="return confirm('Delete this role? This cannot be undone.')"
            >
                <input type="hidden" name="id" value="<?= $roleId ?>">
                <button
                    type="submit"
                    class="rounded-lg border border-red-600 bg-red-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-red-700"
                >
                    Delete Role
                </button>
            </form>
        </div>
    </div>

    <?php endif; ?>

</div>


<?php if ($isEdit): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const availableTbody   = document.getElementById('availableActions');
    const assignedTbody    = document.getElementById('assignedActions');
    const availableCount   = document.getElementById('availableActionCount');
    const assignedCount    = document.getElementById('assignedActionCount');
    const availableSearch  = document.getElementById('availableActionSearch');
    const assignedSearch   = document.getElementById('assignedActionSearch');
    const availableEmpty   = document.getElementById('availableActionsEmpty');
    const assignedEmpty    = document.getElementById('assignedActionsEmpty');
    const saveBtn          = document.getElementById('saveActions');
    const roleId           = saveBtn.dataset.roleId;

    // Initial counts
    updateCounts();
    updateEmptyStates();

    // -------------------------------------------------------
    // Add action: Available → Assigned
    // -------------------------------------------------------
    function addAction(button) {
        const row = button.closest('.action-row');
        if (!row) return;

        button.className = 'remove-action rounded-lg border border-red-600 bg-red-900/20 px-3 py-1.5 text-xs font-medium text-red-400 transition hover:bg-red-800/40';
        button.textContent = 'Remove';

        assignedTbody.appendChild(row);
        updateCounts();
        updateEmptyStates();
    }

    // -------------------------------------------------------
    // Remove action: Assigned → Available
    // -------------------------------------------------------
    function removeAction(button) {
        const row = button.closest('.action-row');
        if (!row) return;

        button.className = 'add-action rounded-lg border border-emerald-600 bg-emerald-900/20 px-3 py-1.5 text-xs font-medium text-emerald-400 transition hover:bg-emerald-800/40';
        button.textContent = '+ Add';

        availableTbody.appendChild(row);
        updateCounts();
        updateEmptyStates();
    }

    // -------------------------------------------------------
    // Event delegation for add/remove buttons
    // -------------------------------------------------------
    document.addEventListener('click', function (e) {
        const addBtn    = e.target.closest('.add-action');
        const removeBtn = e.target.closest('.remove-action');
        if (addBtn)    { addAction(addBtn);       return; }
        if (removeBtn) { removeAction(removeBtn); return; }
    });

    // -------------------------------------------------------
    // Search filtering
    // -------------------------------------------------------
    function filterActions(tbody, input) {
        const term = input.value.trim().toLowerCase();
        tbody.querySelectorAll('.action-row').forEach(function (row) {
            const name = (row.dataset.actionName || '').toLowerCase();
            row.style.display = name.includes(term) ? '' : 'none';
        });
    }

    availableSearch.addEventListener('input', () => filterActions(availableTbody, availableSearch));
    assignedSearch.addEventListener('input',  () => filterActions(assignedTbody,  assignedSearch));

    // -------------------------------------------------------
    // Counts and empty states
    // -------------------------------------------------------
    function updateCounts() {
        availableCount.textContent = availableTbody.querySelectorAll('.action-row').length;
        assignedCount.textContent  = assignedTbody.querySelectorAll('.action-row').length;
    }

    function updateEmptyStates() {
        availableEmpty.classList.toggle('hidden', availableTbody.querySelectorAll('.action-row').length > 0);
        assignedEmpty.classList.toggle('hidden',  assignedTbody.querySelectorAll('.action-row').length > 0);
    }

    // -------------------------------------------------------
    // Save permissions via fetch → POST /role-list/save-actions
    // -------------------------------------------------------
    saveBtn.addEventListener('click', function () {
        const actionIds = [...assignedTbody.querySelectorAll('.action-row')]
            .map(row => Number(row.dataset.actionId));

        fetch('/role-list/save-actions', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': <?= json_encode($csrf->getToken()) ?> },
            body: JSON.stringify({ role_id: Number(roleId), action_ids: actionIds }),
        })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.redirect) {
                        window.location.assign(data.redirect);
                        return;
                    }

                    // Show inline success without a page reload
                    saveBtn.textContent = '✓ Saved';
                    saveBtn.classList.replace('bg-blue-600', 'bg-green-600');
                    saveBtn.classList.replace('hover:bg-blue-500', 'hover:bg-green-500');

                    setTimeout(() => {
                        saveBtn.textContent = 'Save Permissions';
                        saveBtn.classList.replace('bg-green-600', 'bg-blue-600');
                        saveBtn.classList.replace('hover:bg-green-500', 'hover:bg-blue-500');
                    }, 2500);
                } else {
                    alert(data.message || 'Failed to save permissions.');
                }
            })
            .catch(() => alert('Network error. Please try again.'));
    });
});
</script>
<?php endif; ?>
