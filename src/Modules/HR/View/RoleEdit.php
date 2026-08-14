<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * Variables passed from RoleAction::create() and RoleAction::edit()
 *
 * @var string $title        'Create Role' or 'Edit Role'
 * @var string $submitLabel  'Create Role' or 'Save Changes'
 * @var string $actionUrl    '/role-list/create' or '/role-list/edit?id=X'
 * @var array  $fields       ['code' => '', 'description' => '', 'is_active' => 1]
 * @var array  $errors       ['code' => 'msg', 'description' => 'msg']
 * @var array  $flash        ['success' => '...', 'error' => '...']
 */

$fields = $fields ?? ['code' => '', 'description' => '', 'is_active' => 1];
$errors = $errors ?? [];
$flash  = $flash  ?? [];

// Detect mode — if actionUrl contains 'edit' we are updating
$isEdit = isset($actionUrl) && str_contains($actionUrl, 'edit');
?>

<div class="space-y-6">

    <!-- Page Header -->
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
            <!-- Back arrow icon -->
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                <?= Html::encode($title ?? 'Role') ?>
            </h1>
            <p class="mt-0.5 text-sm text-slate-500">
                <?= $isEdit ? 'Update the details of this role.' : 'Fill in the details to create a new role.' ?>
            </p>
        </div>

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


    <!-- Form Card -->
    <div class="overflow-hidden rounded-2xl border border-slate-300 shadow-lg">

        <div class="border-b border-slate-300 px-6 py-5">
            <h2 class="text-lg font-semibold text-black">
                Role Details
            </h2>
            <p class="mt-1 text-sm text-slate-600">
                <?= $isEdit ? 'Edit the role information below.' : 'Enter the role information below.' ?>
            </p>
        </div>

        <form
            method="POST"
            action="<?= Html::encode($actionUrl ?? '') ?>"
            novalidate
        >
            <div class="space-y-6 p-6">

                <!-- Code -->
                <div>
                    <label
                        for="code"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Role Code
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="code"
                        name="code"
                        type="text"
                        maxlength="20"
                        autocomplete="off"
                        placeholder="e.g. Administrator, HR Manager"
                        value="<?= Html::encode((string) ($fields['code'] ?? '')) ?>"
                        class="
                            w-full rounded-lg border px-4 py-3 text-sm outline-none transition
                            <?= isset($errors['code'])
                                ? 'border-red-600 bg-red-950/30 text-white placeholder-red-400 focus:border-red-500 focus:ring-1 focus:ring-red-500'
                                : 'border-slate-700 bg-slate-950 text-white placeholder-slate-500 focus:border-slate-500 focus:ring-1 focus:ring-slate-500'
                            ?>
                        "
                    >

                    <?php if (isset($errors['code'])): ?>
                        <p class="mt-1.5 text-xs text-red-400">
                            <?= Html::encode($errors['code']) ?>
                        </p>
                    <?php else: ?>
                        <p class="mt-1.5 text-xs text-slate-500">
                            Short unique identifier. Max 20 characters.
                        </p>
                    <?php endif; ?>
                </div>


                <!-- Description -->
                <div>
                    <label
                        for="description"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Description
                        <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        maxlength="100"
                        placeholder="Describe what this role allows users to do..."
                        class="
                            w-full resize-none rounded-lg border px-4 py-3 text-sm outline-none transition
                            <?= isset($errors['description'])
                                ? 'border-red-600 bg-red-950/30 text-white placeholder-red-400 focus:border-red-500 focus:ring-1 focus:ring-red-500'
                                : 'border-slate-700 bg-slate-950 text-white placeholder-slate-500 focus:border-slate-500 focus:ring-1 focus:ring-slate-500'
                            ?>
                        "
                    ><?= Html::encode((string) ($fields['description'] ?? '')) ?></textarea>

                    <?php if (isset($errors['description'])): ?>
                        <p class="mt-1.5 text-xs text-red-400">
                            <?= Html::encode($errors['description']) ?>
                        </p>
                    <?php else: ?>
                        <p class="mt-1.5 text-xs text-slate-500">
                            Max 100 characters.
                        </p>
                    <?php endif; ?>
                </div>


                <!-- Is Active -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Status
                    </label>

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
            <div class="
                flex items-center justify-between
                border-t border-slate-200
                bg-slate-50
                px-6 py-4
            ">

                <a
                    href="/role-list"
                    class="
                        rounded-lg border border-slate-300
                        px-5 py-2.5
                        text-sm font-medium text-slate-600
                        transition hover:border-slate-400 hover:text-slate-800
                    "
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="
                        rounded-lg
                        <?= $isEdit ? 'bg-blue-600 hover:bg-blue-500' : 'bg-green-600 hover:bg-green-500' ?>
                        px-6 py-2.5
                        text-sm font-medium text-white
                        shadow-sm transition
                    "
                >
                    <?= Html::encode($submitLabel ?? 'Submit') ?>
                </button>

            </div>

        </form>

    </div>


    <?php if ($isEdit): ?>
    <!-- Danger Zone — only shown on edit -->
    <div class="overflow-hidden rounded-2xl border border-red-300 shadow-lg">

        <div class="border-b border-red-300 bg-red-50 px-6 py-5">
            <h2 class="text-lg font-semibold text-red-700">Danger Zone</h2>
            <p class="mt-1 text-sm text-red-500">
                Destructive actions. These cannot be undone.
            </p>
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
                <?php
                    // Extract id from actionUrl: '/role-list/edit?id=5' → 5
                    $roleIdForDelete = 0;
                    if (isset($actionUrl) && preg_match('/id=(\d+)/', $actionUrl, $m)) {
                        $roleIdForDelete = (int) $m[1];
                    }
                ?>
                <input type="hidden" name="id" value="<?= $roleIdForDelete ?>">

                <button
                    type="submit"
                    class="
                        rounded-lg border border-red-600
                        bg-red-600
                        px-5 py-2.5
                        text-sm font-medium text-white
                        transition hover:bg-red-700
                    "
                >
                    Delete Role
                </button>
            </form>

        </div>
    </div>
    <?php endif; ?>

</div>