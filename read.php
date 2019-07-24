<?php
header('Access-Control-Allow-Origin:*');
include 'config.php';
$a = FormitDollor($_GET["a"]);
$_id = FormitDollor($_GET["id"]);
<<<<<<< HEAD
$limit = !empty($_GET["limit"]) && preg_match("/^[\d0-9]{1,3}+$/", $_GET["limit"]) ? (int) $_GET["limit"] : 100;
$skip = !empty($_GET["skip"]) && preg_match("/^[\d0-9]{1,9}+$/", $_GET["skip"]) ? (int) $_GET["skip"] : 0;
=======
$limit = !empty($_GET["limit"]) && preg_match("/^[\d0-9]{1,3}+$/", $_GET["limit"]) ? (int)$_GET["limit"] : 100;
$skip = !empty($_GET["skip"]) && preg_match("/^[\d0-9]{1,9}+$/", $_GET["skip"]) ? (int)$_GET["skip"] : 0;
>>>>>>> f05d2682d46871768c30ee95ad302e8db5df26c4
// if (strstr($a, '$')) {
//     file_get_contents('https://buguoheng.com/create.php?a=dollarlink&b=['.$a.']');
// }
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
    echo json_encode($manager->executeQuery($db_document, $query)->toArray());
    exit;
} else if (!empty($a)) {
    $cmd = new MongoDB\Driver\Command([
        'aggregate' => 'a',
        'pipeline' => [
            ['$match' => ['a' => $a]],
            ['$group' => ['_id' => '$b', 'id' => ['$first' => '$_id'], 'count' => ['$sum' => 1]]],
<<<<<<< HEAD
            ['$sort' => ['count' => -1]],
            ['$skip' => $limit * $skip],
            ['$limit' => $limit],
            ['$project' => ['_id' => '$id', 'a' => '$_id']]
=======
            ['$sort' => ['count' => -1, 'id' => -1]],
            ['$skip' => $limit * $skip],
            ['$limit' => $limit],
            ['$project' => ['_id' => '$id', 'a' => '$_id', 'count' => '$count']]
>>>>>>> f05d2682d46871768c30ee95ad302e8db5df26c4
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
        'aggregate' => 'a',
        'pipeline' => [
<<<<<<< HEAD
            ['$match' => ['a' => ['$exists' => true], 'b' => ['$exists' => true, '$nin' =>  ['', [], [''], [[]]]]]],
            ['$sort' => ['_id' => -1]],
            ['$skip' => $limit * $skip],
            ['$limit' => $limit],
            ['$project' => ['_id' => '$_id', 'a' => '$a', 'b' => '$b']]
=======
            ['$match' => ['a' => ['$exists' => true]]],
            ['$group' => ['_id' => '$a', 'id' => ['$first' => '$_id'], 'count' => ['$sum' => 1]]],
            ['$sort' => ['count' => -1, 'id' => -1]],
            ['$skip' => $limit * $skip],
            ['$limit' => $limit],
            ['$project' => ['_id' => '$id', 'a' => '$_id', 'count' => '$count']]
>>>>>>> f05d2682d46871768c30ee95ad302e8db5df26c4
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
}
<<<<<<< HEAD
=======
?>
 
>>>>>>> f05d2682d46871768c30ee95ad302e8db5df26c4
