<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * # MythicalSystems License v2.0
 *
 * ## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman
 *
 * Breaking any of the following rules will result in a permanent ban from the MythicalSystems community and all of its services.
 */

use MythicalClient\App;
use MythicalClient\Chat\User\Session;
use MythicalClient\Chat\Services\ServiceTagLines;
use MythicalClient\Chat\Services\ServiceCategories;

$router->add('/api/user/services/categories', function () {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    new Session($appInstance);
    $categories = ServiceCategories::getAll();
    $appInstance->OK('Here are all the categories and their tag lines!', ['categories' => $categories]);
});

$router->add('/api/user/services/category/(.*)/info', function (int $id) {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    new Session($appInstance);
    if (!is_numeric($id)) {
        $appInstance->BadRequest('Invalid category ID', ['error' => 'ERR_INVALID_CATEGORY_ID']);

        return;
    }
    $category = ServiceCategories::getById($id);
    if (!$category) {
        $appInstance->NotFound('Category not found', ['error' => 'ERR_CATEGORY_NOT_FOUND']);

        return;
    }
    $tagLines = ServiceTagLines::getByCategoryId($id);
    $category['futures'] = $tagLines;
    $appInstance->OK('Here is the category and its tag lines!', ['category' => $category]);
});
