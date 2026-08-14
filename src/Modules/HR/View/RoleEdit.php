<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/*
 * TEMPORARY FRONTEND DATA
 *
 * These will eventually come from the controller/database.
 */
$user = $user ?? [
    'firstName' => 'Patrick',
    'middleName' => 'Martin',
    'lastName' => 'De Lara',
    'username' => 'patrick',
];

$availableRoles = [
    ['id' => 1, 'name' => 'Administrator'],
    ['id' => 2, 'name' => 'HR Manager'],
    ['id' => 3, 'name' => 'Project Manager'],
    ['id' => 4, 'name' => 'Employee'],
    ['id' => 5, 'name' => 'Supervisor'],
    ['id' => 6, 'name' => 'Finance'],
];

$assignedRoles = [
    ['id' => 7, 'name' => 'User'],
];
?>

<div class="space-y-6">

    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-black">
            Role Management
        </h1>

        <p class="mt-1 text-sm text-slate-700">
            Manage the roles assigned to this user.
        </p>
    </div>


    <!-- Account Information -->
    <div class="overflow-hidden rounded-2xl border border-slate-700 bg-slate-900 shadow-xl">

        <div class="border-b border-slate-700 px-6 py-5">
            <h2 class="text-lg font-semibold text-white">
                Account Information
            </h2>

            <p class="mt-1 text-sm text-slate-400">
                Basic information about the account.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-4">

            <!-- First Name -->
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-400">
                    First Name
                </label>

                <div class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white">
                    <?= Html::encode((string) ($user['firstName'] ?? '-')) ?>
                </div>
            </div>

            <!-- Middle Name -->
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-400">
                    Middle Name
                </label>

                <div class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white">
                    <?= Html::encode((string) ($user['middleName'] ?? '-')) ?>
                </div>
            </div>

            <!-- Last Name -->
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-400">
                    Last Name
                </label>

                <div class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white">
                    <?= Html::encode((string) ($user['lastName'] ?? '-')) ?>
                </div>
            </div>

            <!-- Username -->
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-400">
                    Username
                </label>

                <div class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white">
                    <?= Html::encode((string) ($user['username'] ?? '-')) ?>
                </div>
            </div>

        </div>
    </div>


    <!-- Role Management -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- Available Roles -->
        <div class="overflow-hidden rounded-2xl border border-slate-700 bg-slate-900 shadow-xl">

            <div class="border-b border-slate-700 px-6 py-5">

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">
                            Available Roles
                        </h2>

                        <p class="mt-1 text-sm text-slate-400">
                            Roles that can be assigned to this user.
                        </p>
                    </div>

                    <span
                        id="availableRoleCount"
                        class="rounded-full bg-slate-800 px-3 py-1 text-xs font-medium text-slate-300"
                    >
                        <?= count($availableRoles) ?>
                    </span>
                </div>

                <!-- Search -->
                <div class="relative mt-4">
                    <input
                        id="availableRoleSearch"
                        type="text"
                        placeholder="Search available roles..."
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
                    >
                </div>

            </div>


            <!-- Table -->
            <div class="overflow-x-auto">

                <table class="min-w-full text-left text-sm">

                    <thead class="bg-slate-950 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-6 py-4">
                            Role
                        </th>

                        <th class="px-6 py-4 text-right">
                            Action
                        </th>
                    </tr>
                    </thead>

                    <tbody
                        id="availableRoles"
                        class="divide-y divide-slate-700"
                    >

                    <?php foreach ($availableRoles as $role): ?>

                        <tr
                            class="role-row hover:bg-slate-800/70"
                            data-role-id="<?= Html::encode((string) $role['id']) ?>"
                            data-role-name="<?= Html::encode(strtolower((string) $role['name'])) ?>"
                        >

                            <td class="px-6 py-4 font-medium text-white">
                                <?= Html::encode((string) $role['name']) ?>
                            </td>

                            <td class="px-6 py-4 text-right">

                                <button
                                    type="button"
                                    class="add-role rounded-lg border border-emerald-700 bg-emerald-900/30 px-3 py-2 text-xs font-medium text-emerald-300 transition hover:bg-emerald-800/50"
                                    data-role-id="<?= Html::encode((string) $role['id']) ?>"
                                >
                                    + Add
                                </button>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

                <div
                    id="availableEmpty"
                    class="hidden px-6 py-10 text-center text-sm text-slate-500"
                >
                    No available roles found.
                </div>

            </div>
        </div>


        <!-- Assigned Roles -->
        <div class="overflow-hidden rounded-2xl border border-slate-700 bg-slate-900 shadow-xl">

            <div class="border-b border-slate-700 px-6 py-5">

                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">
                            Assigned Roles
                        </h2>

                        <p class="mt-1 text-sm text-slate-400">
                            Roles currently assigned to this user.
                        </p>
                    </div>

                    <span
                        id="assignedRoleCount"
                        class="rounded-full bg-slate-800 px-3 py-1 text-xs font-medium text-slate-300"
                    >
                        <?= count($assignedRoles) ?>
                    </span>
                </div>

                <!-- Search -->
                <div class="relative mt-4">
                    <input
                        id="assignedRoleSearch"
                        type="text"
                        placeholder="Search assigned roles..."
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
                    >
                </div>

            </div>


            <!-- Table -->
            <div class="overflow-x-auto">

                <table class="min-w-full text-left text-sm">

                    <thead class="bg-slate-950 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-6 py-4">
                            Role
                        </th>

                        <th class="px-6 py-4 text-right">
                            Action
                        </th>
                    </tr>
                    </thead>

                    <tbody
                        id="assignedRoles"
                        class="divide-y divide-slate-700"
                    >

                    <?php foreach ($assignedRoles as $role): ?>

                        <tr
                            class="role-row hover:bg-slate-800/70"
                            data-role-id="<?= Html::encode((string) $role['id']) ?>"
                            data-role-name="<?= Html::encode(strtolower((string) $role['name'])) ?>"
                        >

                            <td class="px-6 py-4 font-medium text-white">
                                <?= Html::encode((string) $role['name']) ?>
                            </td>

                            <td class="px-6 py-4 text-right">

                                <button
                                    type="button"
                                    class="remove-role rounded-lg border border-red-700 bg-red-900/30 px-3 py-2 text-xs font-medium text-red-300 transition hover:bg-red-800/50"
                                    data-role-id="<?= Html::encode((string) $role['id']) ?>"
                                >
                                    Remove
                                </button>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

                <div
                    id="assignedEmpty"
                    class="hidden px-6 py-10 text-center text-sm text-slate-500"
                >
                    No roles assigned.
                </div>

            </div>
        </div>

    </div>


    <!-- Save Area -->
    <div class="flex justify-end">
        <button
            type="button"
            id="saveRoles"
            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-500"
        >
            Save Changes
        </button>
    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const availableRoles = document.getElementById('availableRoles');
        const assignedRoles = document.getElementById('assignedRoles');

        const availableSearch = document.getElementById('availableRoleSearch');
        const assignedSearch = document.getElementById('assignedRoleSearch');

        const availableCount = document.getElementById('availableRoleCount');
        const assignedCount = document.getElementById('assignedRoleCount');

        const availableEmpty = document.getElementById('availableEmpty');
        const assignedEmpty = document.getElementById('assignedEmpty');


        /*
         * Move a role from Available → Assigned
         */
        function addRole(button) {

            const row = button.closest('.role-row');

            if (!row) {
                return;
            }

            const removeButton = document.createElement('button');

            removeButton.type = 'button';
            removeButton.className =
                'remove-role rounded-lg border border-red-700 bg-red-900/30 px-3 py-2 text-xs font-medium text-red-300 transition hover:bg-red-800/50';

            removeButton.textContent = 'Remove';

            removeButton.dataset.roleId = row.dataset.roleId;

            const actionCell = row.querySelector('td:last-child');

            actionCell.innerHTML = '';
            actionCell.appendChild(removeButton);

            assignedRoles.appendChild(row);

            updateCounts();
            updateEmptyStates();
        }


        /*
         * Move a role from Assigned → Available
         */
        function removeRole(button) {

            const row = button.closest('.role-row');

            if (!row) {
                return;
            }

            const addButton = document.createElement('button');

            addButton.type = 'button';
            addButton.className =
                'add-role rounded-lg border border-emerald-700 bg-emerald-900/30 px-3 py-2 text-xs font-medium text-emerald-300 transition hover:bg-emerald-800/50';

            addButton.textContent = '+ Add';

            addButton.dataset.roleId = row.dataset.roleId;

            const actionCell = row.querySelector('td:last-child');

            actionCell.innerHTML = '';
            actionCell.appendChild(addButton);

            availableRoles.appendChild(row);

            updateCounts();
            updateEmptyStates();
        }


        /*
         * Search roles
         */
        function filterRoles(container, searchInput) {

            const searchTerm = searchInput.value
                .trim()
                .toLowerCase();

            const rows = container.querySelectorAll('.role-row');

            let visibleRows = 0;

            rows.forEach(function (row) {

                const roleName = row.dataset.roleName || '';

                const matches = roleName.includes(searchTerm);

                row.style.display = matches ? '' : 'none';

                if (matches) {
                    visibleRows++;
                }
            });

            return visibleRows;
        }


        /*
         * Update role counters
         */
        function updateCounts() {

            availableCount.textContent =
                availableRoles.querySelectorAll('.role-row').length;

            assignedCount.textContent =
                assignedRoles.querySelectorAll('.role-row').length;

            filterRoles(availableRoles, availableSearch);
            filterRoles(assignedRoles, assignedSearch);
        }


        /*
         * Show "no roles" messages
         */
        function updateEmptyStates() {

            const availableRows =
                availableRoles.querySelectorAll('.role-row').length;

            const assignedRows =
                assignedRoles.querySelectorAll('.role-row').length;

            availableEmpty.classList.toggle(
                'hidden',
                availableRows > 0
            );

            assignedEmpty.classList.toggle(
                'hidden',
                assignedRows > 0
            );
        }


        /*
         * Event delegation for Add / Remove buttons
         */
        document.addEventListener('click', function (event) {

            const addButton =
                event.target.closest('.add-role');

            const removeButton =
                event.target.closest('.remove-role');


            if (addButton) {
                addRole(addButton);
                return;
            }


            if (removeButton) {
                removeRole(removeButton);
            }
        });


        /*
         * Search events
         */
        availableSearch.addEventListener('input', function () {
            filterRoles(availableRoles, availableSearch);
        });


        assignedSearch.addEventListener('input', function () {
            filterRoles(assignedRoles, assignedSearch);
        });


        /*
         * Temporary save button.
         *
         * Backend will be connected later.
         */
        document.getElementById('saveRoles')
            .addEventListener('click', function () {

                const assigned =
                    [...assignedRoles.querySelectorAll('.role-row')]
                        .map(row => ({
                            id: row.dataset.roleId,
                            name: row.querySelector('td').textContent.trim()
                        }));

                console.log('Roles that would be saved:', assigned);

                alert(
                    'Frontend is working. Backend saving will be connected next.'
                );
            });


        updateCounts();
        updateEmptyStates();
    });
</script>