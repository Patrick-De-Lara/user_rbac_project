<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var array $allRoles   All roles from sys_user_role
 * @var array $allUsers   All users from sys_user joined with er_person
 * @var array $flash      Flash messages ['success' => ..., 'error' => ...]
 */

// Fallback to empty arrays if not passed (safety)
$allRoles = $allRoles ?? [];
$allUsers = $allUsers ?? [];
$flash    = $flash    ?? [];

$userId = $getUserId->getUserIdFromSession() ?? 0;
$roleActions = array_column($actionChecker->UserModuleChecker('role', $userId),'action_code');
$roleActions = array_flip($roleActions);

?>

<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Role Management</h1>
            <p class="mt-1 text-sm text-slate-500">Assign and manage roles for users.</p>
        </div>
        <?php if (isset($roleActions['create_role'])): ?>
        <a
            href="/role-list/create"
            class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-green-500"
        >
            + New Role
        </a>
        <?php endif; ?>
    </div>

    <!-- Flash Messages -->
    <?php if (!empty($flash['success'])): ?>
        <div class="rounded-lg border border-green-700 bg-green-900/30 px-4 py-3 text-sm text-green-300">
            <?= Html::encode($flash['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($flash['error'])): ?>
        <div class="rounded-lg border border-red-700 bg-red-900/30 px-4 py-3 text-sm text-red-300">
            <?= Html::encode($flash['error']) ?>
        </div>
    <?php endif; ?>


    <!-- =========================================================
         ROLES TABLE
         List all roles with edit/delete actions.
         ========================================================= -->
    <div class="overflow-hidden rounded-2xl border border-slate-300 shadow-lg">

        <div class="border-b border-slate-300 px-6 py-5">
            <h2 class="text-lg font-semibold text-black">All Roles</h2>
            <p class="mt-1 text-sm text-slate-600">All roles defined in the system.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-100 text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">

                    <?php if ($allRoles === []): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-slate-400">
                                No roles found. <a href="/role-list/create" class="text-blue-600 underline">Create one</a>.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($allRoles as $role): ?>
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-800">
                                <?= Html::encode((string) $role['code']) ?>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <?= Html::encode((string) $role['description']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ((int) $role['is_active'] === 1): ?>
                                    <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Active</span>
                                <?php else: ?>
                                    <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="/role-list/edit?id=<?= Html::encode((string) $role['id']) ?>"
                                        class="rounded-lg border border-blue-500 bg-blue-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-blue-800/50"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="/role-list/delete"
                                        onsubmit="return confirm('Delete role <?= Html::encode((string) $role['code']) ?>? This cannot be undone.')"
                                        class="inline"
                                    >
                                        <input type="hidden" name="id" value="<?= Html::encode((string) $role['id']) ?>">
                                        <button
                                            type="submit"
                                            class="rounded-lg border border-red-500 bg-red-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-red-800/50"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>


    <!-- =========================================================
         USER ROLE ASSIGNMENT
         Select a user, then drag roles between Available/Assigned.
         ========================================================= -->
    <div class="overflow-hidden rounded-2xl border border-slate-300 shadow-lg">

        <div class="border-b border-slate-300 px-6 py-5">
            <h2 class="text-lg font-semibold text-black">Assign Roles to User</h2>
            <p class="mt-1 text-sm text-slate-600">Select a user then add or remove roles.</p>
        </div>

        <div class="p-6">

            <!-- User selector -->
            <div class="flex flex-col gap-4 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="userSearch" class="mb-2 block text-sm font-medium text-slate-700">
                        User
                    </label>

                    <input
                        id="userSearch"
                        list="userList"
                        type="text"
                        placeholder="Search by username or name..."
                        autocomplete="off"
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white placeholder-slate-500 outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
                    >

                    <datalist id="userList">
                        <?php foreach ($allUsers as $u): ?>
                            <option
                                value="<?= Html::encode((string) $u['username']) ?>"
                                data-user-id="<?= Html::encode((string) $u['id']) ?>"
                            >
                                <?= Html::encode(
                                    ($u['firstName'] ?? '') . ' ' .
                                    ($u['middleName'] ?? '') . ' ' .
                                    ($u['lastName'] ?? '')
                                ) ?>
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <button
                    type="button"
                    id="chooseUser"
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-blue-500"
                >
                    Load Roles
                </button>
            </div>
        </div>
    </div>


    <!-- Account Information (shown after user is chosen) -->
    <div id="accountInfo" class="hidden overflow-hidden rounded-2xl border border-slate-400 shadow-xl">

        <div class="border-b border-slate-400 px-6 py-5">
            <h2 class="text-lg font-semibold text-black">Account Information</h2>
            <p class="mt-1 text-sm text-slate-600">Information for the currently selected user.</p>
        </div>

        <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">First Name</label>
                <div id="infoFirstName" class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white">—</div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Middle Name</label>
                <div id="infoMiddleName" class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white">—</div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Last Name</label>
                <div id="infoLastName" class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white">—</div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Username</label>
                <div id="infoUsername" class="rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white">—</div>
            </div>
        </div>
    </div>


    <!-- Role Assignment Panels (shown after user is chosen) -->
    <div id="rolePanel" class="hidden grid grid-cols-1 gap-6 lg:grid-cols-2">

        <!-- Available Roles -->
        <div class="overflow-hidden rounded-2xl border border-slate-700 bg-slate-900 shadow-xl">
            <div class="border-b border-slate-700 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Available Roles</h2>
                        <p class="mt-1 text-sm text-slate-400">Roles that can be assigned to this user.</p>
                    </div>
                    <span id="availableRoleCount" class="rounded-full bg-slate-800 px-3 py-1 text-xs font-medium text-slate-300">0</span>
                </div>

                <div class="relative mt-4">
                    <input
                        id="availableRoleSearch"
                        type="text"
                        placeholder="Search available roles..."
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
                    >
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-700 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="availableRoles" class="divide-y divide-slate-700/50">
                        <!-- Populated by JS after user is selected -->
                    </tbody>
                </table>
                <div id="availableEmpty" class="hidden px-6 py-10 text-center text-sm text-slate-500">
                    No available roles.
                </div>
            </div>
        </div>

        <!-- Assigned Roles -->
        <div class="overflow-hidden rounded-2xl border border-slate-700 bg-slate-900 shadow-xl">
            <div class="border-b border-slate-700 px-6 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Assigned Roles</h2>
                        <p class="mt-1 text-sm text-slate-400">Roles currently assigned to this user.</p>
                    </div>
                    <span id="assignedRoleCount" class="rounded-full bg-slate-800 px-3 py-1 text-xs font-medium text-slate-300">0</span>
                </div>

                <div class="relative mt-4">
                    <input
                        id="assignedRoleSearch"
                        type="text"
                        placeholder="Search assigned roles..."
                        class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-500 outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
                    >
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="border-b border-slate-700 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="assignedRoles" class="divide-y divide-slate-700/50">
                        <!-- Populated by JS after user is selected -->
                    </tbody>
                </table>
                <div id="assignedEmpty" class="hidden px-6 py-10 text-center text-sm text-slate-500">
                    No roles assigned.
                </div>
            </div>
        </div>
    </div>


    <!-- Save -->
    <div id="saveArea" class="hidden flex justify-end">
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

    // =============================================================
    // 1. DATA — embedded from PHP
    // =============================================================
    const ALL_ROLES = <?= json_encode(array_values($allRoles), JSON_UNESCAPED_UNICODE) ?>;
    const ALL_USERS = <?= json_encode(array_values($allUsers), JSON_UNESCAPED_UNICODE) ?>;

    // =============================================================
    // 2. DOM REFERENCES
    // =============================================================
    const dom = {
        userSearchInput:    document.getElementById('userSearch'),
        chooseBtn:          document.getElementById('chooseUser'),
        accountInfo:        document.getElementById('accountInfo'),
        rolePanel:          document.getElementById('rolePanel'),
        saveArea:           document.getElementById('saveArea'),
        saveBtn:            document.getElementById('saveRoles'),
        availableRolesTbody: document.getElementById('availableRoles'),
        assignedRolesTbody:  document.getElementById('assignedRoles'),
        availableCount:     document.getElementById('availableRoleCount'),
        assignedCount:      document.getElementById('assignedRoleCount'),
        availableSearch:    document.getElementById('availableRoleSearch'),
        assignedSearch:     document.getElementById('assignedRoleSearch'),
        availableEmpty:     document.getElementById('availableEmpty'),
        assignedEmpty:      document.getElementById('assignedEmpty'),
    };

    // =============================================================
    // 3. STATE
    // =============================================================
    let selectedUserId = null;

    // =============================================================
    // 4. HELPERS
    // =============================================================

    /**
     * Normalize any id (string or number) to an int for safe comparison.
     * DB drivers often return numeric columns as strings (e.g. "2"),
     * while embedded JS data may already be numbers — always compare
     * using the same type to avoid silent === mismatches.
     */
    function toId(value) {
        return parseInt(value, 10);
    }

    // =============================================================
    // 5. USER SELECTION
    // =============================================================

    function findUserByUsername(username) {
        return ALL_USERS.find(u => u.username === username);
    }

    function populateAccountInfo(user) {
        document.getElementById('infoFirstName').textContent  = user.firstName  || '—';
        document.getElementById('infoMiddleName').textContent = user.middleName || '—';
        document.getElementById('infoLastName').textContent   = user.lastName   || '—';
        document.getElementById('infoUsername').textContent   = user.username   || '—';
        dom.accountInfo.classList.remove('hidden');
    }

    function loadRolesForUser(userId) {
        fetch('/role-list/user-roles?user_id=' + userId)
            .then(r => r.json())
            .then(data => {
                const assignedIds = (data.assignedRoleIds || []).map(toId);
                buildRolePanels(assignedIds);
                dom.rolePanel.classList.remove('hidden');
                dom.saveArea.classList.remove('hidden');
            })
            .catch(() => {
                alert('Failed to load roles. Please try again.');
            });
    }

    dom.chooseBtn.addEventListener('click', function () {
        const username = dom.userSearchInput.value.trim();

        if (username === '') {
            alert('Please select a user first.');
            return;
        }

        const user = findUserByUsername(username);

        if (!user) {
            alert('User not found. Please select from the list.');
            return;
        }

        selectedUserId = user.id;
        populateAccountInfo(user);
        loadRolesForUser(user.id);
    });

    // =============================================================
    // 6. ROLE PANELS — build / render
    // =============================================================

    function buildRolePanels(assignedIds) {
        dom.availableRolesTbody.innerHTML = '';
        dom.assignedRolesTbody.innerHTML  = '';

        ALL_ROLES.forEach(function (role) {
            const isAssigned = assignedIds.includes(toId(role.id));
            const row = buildRoleRow(role, isAssigned);

            (isAssigned ? dom.assignedRolesTbody : dom.availableRolesTbody).appendChild(row);
        });

        updateCounts();
        updateEmptyStates();
    }

    // -----------------------------------------------------------
    // Build a single table row for a role
    // -----------------------------------------------------------
    function buildRoleRow(role, isAssigned) {
        const tr = document.createElement('tr');
        tr.className = 'role-row transition hover:bg-slate-800/50';
        tr.dataset.roleId   = role.id;
        tr.dataset.roleName = (role.code || '').toLowerCase();

        const nameTd = document.createElement('td');
        nameTd.className = 'px-6 py-4 font-medium text-white';
        nameTd.textContent = role.code;

        const actionTd = document.createElement('td');
        actionTd.className = 'px-6 py-4 text-right';

        const btn = document.createElement('button');
        btn.type = 'button';

        if (isAssigned) {
            btn.className = 'remove-role rounded-lg border border-red-700 bg-red-900/30 px-3 py-2 text-xs font-medium text-red-300 transition hover:bg-red-800/50';
            btn.textContent = 'Remove';
        } else {
            btn.className = 'add-role rounded-lg border border-emerald-700 bg-emerald-900/30 px-3 py-2 text-xs font-medium text-emerald-300 transition hover:bg-emerald-800/50';
            btn.textContent = '+ Add';
        }

        btn.dataset.roleId = role.id;
        actionTd.appendChild(btn);
        tr.appendChild(nameTd);
        tr.appendChild(actionTd);
        return tr;
    }

    // =============================================================
    // 7. ADD / REMOVE ROLE (move rows between panels)
    // =============================================================

    function addRole(button) {
        const row = button.closest('.role-row');
        if (!row) return;

        button.className = 'remove-role rounded-lg border border-red-700 bg-red-900/30 px-3 py-2 text-xs font-medium text-red-300 transition hover:bg-red-800/50';
        button.textContent = 'Remove';

        dom.assignedRolesTbody.appendChild(row);
        updateCounts();
        updateEmptyStates();
    }

    function removeRole(button) {
        const row = button.closest('.role-row');
        if (!row) return;

        button.className = 'add-role rounded-lg border border-emerald-700 bg-emerald-900/30 px-3 py-2 text-xs font-medium text-emerald-300 transition hover:bg-emerald-800/50';
        button.textContent = '+ Add';

        dom.availableRolesTbody.appendChild(row);
        updateCounts();
        updateEmptyStates();
    }

    // Event delegation for Add / Remove buttons
    document.addEventListener('click', function (event) {
        const addButton    = event.target.closest('.add-role');
        const removeButton = event.target.closest('.remove-role');

        if (addButton)    { addRole(addButton);       return; }
        if (removeButton) { removeRole(removeButton); return; }
    });

    // =============================================================
    // 8. SAVE — POST assigned role ids to backend
    // =============================================================

    function saveRoleAssignments() {
        if (!selectedUserId) {
            alert('No user selected.');
            return;
        }

        const assignedRows = dom.assignedRolesTbody.querySelectorAll('.role-row');
        const roleIds = [...assignedRows].map(row => toId(row.dataset.roleId));

        fetch('/role-list/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': <?= json_encode($csrf->getToken()) ?>,
            },
            body: JSON.stringify({ user_id: selectedUserId, role_ids: roleIds }),
        })
            .then(r => r.json())
            .then(data => {
                alert(data.message || (data.success ? 'Roles saved successfully.' : 'Failed to save roles.'));
            })
            .catch(() => {
                alert('Network error. Please try again.');
            });
    }

    dom.saveBtn.addEventListener('click', saveRoleAssignments);

    // =============================================================
    // 9. SEARCH / FILTER
    // =============================================================

    function filterRoles(tbody, searchInput) {
        const term = searchInput.value.trim().toLowerCase();
        tbody.querySelectorAll('.role-row').forEach(function (row) {
            const name = (row.dataset.roleName || '').toLowerCase();
            row.style.display = name.includes(term) ? '' : 'none';
        });
    }

    dom.availableSearch.addEventListener('input', () => filterRoles(dom.availableRolesTbody, dom.availableSearch));
    dom.assignedSearch.addEventListener('input',  () => filterRoles(dom.assignedRolesTbody,  dom.assignedSearch));

    // =============================================================
    // 10. COUNTS / EMPTY STATES
    // =============================================================

    function updateCounts() {
        dom.availableCount.textContent = dom.availableRolesTbody.querySelectorAll('.role-row').length;
        dom.assignedCount.textContent  = dom.assignedRolesTbody.querySelectorAll('.role-row').length;
    }

    function updateEmptyStates() {
        dom.availableEmpty.classList.toggle('hidden', dom.availableRolesTbody.querySelectorAll('.role-row').length > 0);
        dom.assignedEmpty.classList.toggle('hidden',  dom.assignedRolesTbody.querySelectorAll('.role-row').length > 0);
    }
});
</script>