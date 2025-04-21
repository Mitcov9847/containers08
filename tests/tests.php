<?php

require_once __DIR__ . '/testframework.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modules/database.php';
require_once __DIR__ . '/../modules/page.php';

$testFramework = new TestFramework();

function testDbConnection()
{
    global $config;
    try {
        $db = new Database($config["db"]["path"]);
        return assertExpression($db !== null, "Database connection established", "Failed to connect to database");
    } catch (Exception $e) {
        return assertExpression(false, "", "Database connection failed: " . $e->getMessage());
    }
}

function testDbCount()
{
    global $config;
    $db = new Database($config["db"]["path"]);
    $count = $db->Count("page");
    return assertExpression($count >= 3, "Table count returned $count", "Table count failed");
}

function testDbCreate()
{
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = ['title' => 'Test Page', 'content' => 'Test Content'];
    $id = $db->Create("page", $data);
    return assertExpression($id > 0, "Created record with ID $id", "Create operation failed");
}

function testDbRead()
{
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = $db->Read("page", 1);
    return assertExpression(
        isset($data['title']) && $data['title'] === 'Page 1',
        "Read operation successful",
        "Read operation failed"
    );
}

function testDbUpdate()
{
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = ['title' => 'Updated Page', 'content' => 'Updated Content'];
    $result = $db->Update("page", 1, $data);
    $updated = $db->Read("page", 1);
    return assertExpression(
        $updated['title'] === 'Updated Page',
        "Update operation successful",
        "Update operation failed"
    );
}

function testDbDelete()
{
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = ['title' => 'Delete Test', 'content' => 'Delete Content'];
    $id = $db->Create("page", $data);
    $result = $db->Delete("page", $id);
    $deleted = $db->Read("page", $id);
    return assertExpression(
        $deleted === false,
        "Delete operation successful",
        "Delete operation failed"
    );
}

function testPageRender()
{
    $page = new Page(__DIR__ . '/../templates/index.tpl');
    $data = ['title' => 'Test', 'content' => 'Test Content'];
    $output = $page->Render($data);
    return assertExpression(
        strpos($output, 'Test Content') !== false,
        "Page render successful",
        "Page render failed"
    );
}

$testFramework->add('Database connection', 'testDbConnection');
$testFramework->add('Table count', 'testDbCount');
$testFramework->add('Data create', 'testDbCreate');
$testFramework->add('Data read', 'testDbRead');
$testFramework->add('Data update', 'testDbUpdate');
$testFramework->add('Data delete', 'testDbDelete');
$testFramework->add('Page render', 'testPageRender');

$testFramework->run();
echo $testFramework->getResult();
