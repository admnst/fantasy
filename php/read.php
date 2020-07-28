<?php
header('Access-Control-Allow-Origin:*');
header('Content-type: text/html; charset=utf-8');
include 'config.php';
$a = FormitDollor($_GET["a"]);
$_id = FormitDollor($_GET["id"]);
$limit = !empty($_GET["limit"]) && preg_match("/^[\d0-9]{1,3}+$/", $_GET["limit"]) ? (int) $_GET["limit"] : 1000;
$skip = !empty($_GET["skip"]) && preg_match("/^[\d0-9]{1,9}+$/", $_GET["skip"]) ? (int) $_GET["skip"] : 0;

if (!empty($_id)) {
    $_ids = explode(',', trim($_id, '[]'));
    $arr = [];
    foreach ($_ids as $_id) {
        if (preg_match("/^[\d0-9a-f]{24}+$/", $_id)) {
            $arr[] = new MongoDB\BSON\ObjectID($_id);
        }
    }
    $filter = ['_id' => ['$in' => $arr]];
    $options = ['sort' => ['_id' => -1]];
    $query = new MongoDB\Driver\Query($filter, $options);
    echo json_encode($manager->executeQuery($db_name.'.'.$db_document, $query)->toArray());
    exit;
} else if (!empty($a)) {
    $cmd = new MongoDB\Driver\Command([
        'aggregate' => $db_document,
        'pipeline' => [
            ['$match' => ['a' => $a]],
            ['$group' => ['_id' => '$b', 'id_temp' => ['$first' => '$_id'], 'count' => ['$sum' => 1]]],
            ['$sort' => ['id_temp' => 1]],
            ['$skip' => $limit * $skip],
            ['$limit' => $limit],
            // ['$project' => ['_id' => '$id_temp', 'a' => '$_id']]
            ['$project' => ['a' => '$_id']]
        ],
        'cursor' => new stdClass,
    ]);
    try {
        echo json_encode($manager->executeCommand($db_name, $cmd)->toArray());
        exit;
    } catch (MongoDB\Driver\Exception $e) {
        echo $e->getMessage(), "\n";
        exit;
    }
} else {
    $cmd = new MongoDB\Driver\Command([
        'aggregate' => $db_document,
        'pipeline' => [
            ['$match' => ['a' => ['$exists' => true], 'b' => ['$exists' => true, '$nin' =>  ['', [], [''], [[]]]]]],
            ['$group'=>['_id'=>'$b', 'a' =>  ['$first' => '$a'], 'id_temp' => ['$first' => '$_id']]],
            ['$sort' => ['id_temp' => -1]],
            ['$skip' => $limit * $skip],
            ['$limit' => $limit],
            // ['$project' => ['_id' => '$id_temp', 'a' => '$a', 'b' => '$_id']]
            ['$project' => ['a' => '$a', 'b' => '$_id']]
        ],
        'cursor' => new stdClass,
    ]);
    try {
        $json=json_encode($manager->executeCommand($db_name, $cmd)->toArray());
        echo $json;
        exit;
    } catch (MongoDB\Driver\Exception $e) {
        echo $e->getMessage(), "\n";
        exit;
    }
}
