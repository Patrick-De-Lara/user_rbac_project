<?php

declare(strict_types=1);

$isUpdate = $isUpdate ?? false;
$userId = $userId ?? null;

$formAction = $isUpdate
    ? '/update-user/' . $userId
    : '/create-user';

//var firstname
//var lastname
//var middlename
//date birthday
//var sex
//var birthplace
//var password
//var username

?>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <?php
        $value = static fn (string $field): string =>
            htmlspecialchars((string) ($formData[$field] ?? ''), ENT_QUOTES, 'UTF-8');
    ?>

<form
    class="w-full max-w-4xl mx-auto space-y-5 px-1 sm:px-0"
    method="POST"
    action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>"
>

    <?= $csrf->hiddenInput() ?>

    <!-- =========================================================
         PERSONAL INFORMATION
         ========================================================= -->

    <fieldset
        class="bg-white rounded-lg shadow-sm p-4"
    >

        <h1
            class="text-base font-semibold text-gray-800 "
        >
            Personal Information
    </h1>   

        <h1 class="text-sm text-gray-600 mb-3">
            Please fill in the following personal information:
        </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
            
            <!-- First Name -->

            <div>
                <label
                    for="firstname"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    First Name
                </label>

                <input
                    type="text"
                    id="firstname"
                    name="firstname"
                    value="<?= $value('firstName') ?>"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Last Name -->

            <div>
                <label
                    for="lastname"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Last Name
                </label>

                <input
                    type="text"
                    value="<?= $value('lastName') ?>"
                    id="lastname"
                    name="lastname"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <!-- Middle Name -->

            <div>
                <label
                    for="middlename"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Middle Name
                </label>

                <input
                    type="text"
                    id="middlename"
                    name="middlename"
                    value="<?= $value('middleName') ?>"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

            </div>

            <!-- Birthday -->

            <div>
                <label
                    for="birthday"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Birthday
                </label>

                <input
                    type="date"
                    value="<?= $value('birthday') ?>"
                    id="birthday"
                    name="birthday"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

            </div>

            <!-- Sex -->
            <div>
                <label
                    for="sex"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Sex
                </label>

                <select
                    id="sex"
                    name="sex"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >

                    <option
                        value=""
                        disabled
                        selected
                    >
                        Select...
                    </option>

                    <option 
                        value="male" 
                        <?= ($formData['sex'] ?? '') === 'male' ? 'selected' : '' ?>>
                        Male
                    </option>

                    <option 
                        value="female" 
                        <?= ($formData['sex'] ?? '') === 'female' ? 'selected' : '' ?>
                        >Female
                    </option>

                </select>

            </div>

            <!-- Birthplace -->
            <div>
                <label
                    for="birthplace"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Birthplace
                </label>

                <input
                    type="text"
                    value="<?= $value('birthPlace') ?>"
                    id="birthplace"
                    name="birthplace"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

        </div>

    </fieldset>



    <!-- =========================================================
         ACCOUNT INFORMATION
         ========================================================= -->

    <?php if(!$isUpdate): ?>
    <fieldset
        class="bg-white rounded-lg shadow-sm p-4"
    >
        <h1 class="text-base font-semibold text-gray-800 mb-3">
            Account Information
        </h1>

        <div class="space-y-3">
            <!-- Username -->
            <div>
                <label
                    for="username"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Username
                </label>
                <input
                    type="text"
                    value="<?= $value('username') ?>"
                    id="username"
                    name="username"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <!-- Password -->
            <div>

                <label
                    for="password"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
            <!-- Confirm Password -->
            <div>

                <label
                    for="password_confirm"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    required
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

            </div>

        </div>

    </fieldset>
    <?php endif; ?>

    <!-- =========================================================
         SUBMIT
         ========================================================= -->

    <button type="submit" 
            class="w-full bg-blue-500 text-white py-2.5 px-4 text-sm rounded-md hover:bg-blue-600 active:bg-blue-700 transition-colors duration-300 font-medium">
        <?= $isUpdate ? 'Update User' : 'Create User' ?>
    </button>


</form>