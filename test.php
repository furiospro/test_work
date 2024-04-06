<?php

$cli = ($_SERVER['argv'][1] ?? null) == '--console';

$id = (is_numeric($_SERVER['argv'][1] ?? null)) ? $_SERVER['argv'][1] : null;

if ($cli) {
    $res = '';
    $res_arr = [];

    // Исхожу из того, что массив имеет строгую структуру: набор объектов по типу записей из базы данных
    $arr = [
        [
            "internal_history_id"  => "2230893",
            "external_id"  => "8615",
            "external_commission"  =>"0.0005"
        ],
        [
            "internal_history_id" => "2230891",
            "external_id" => "2159",
            "external_commission" => "0.0200"
        ],
        [
            "internal_history_id" => "2230892",
            "external_id" => "5349",
            "external_commission" => "0.0060"
        ],
        [
            "internal_history_id" => "563089",
            "external_id" => "8659",
            "external_commission" => "0.0054"
        ]
    ];

    $keyNameArr = array_keys($arr[0]);

    foreach ($keyNameArr as $key) {
        $res_arr[$key] = array_column($arr, $key);
    }

    for ($j = 0; $j < count($keyNameArr); $j++) {
        $res .= sprintf("| %-30s", $keyNameArr[$j]);
    }

    $res .= PHP_EOL;

    array_map(function () use (&$res) {
        $args = func_get_args();
        for ($n = 0; $n < count($args); $n++) {
            $res .= sprintf("| %-30s", $args[$n]);
        }
        $res .= PHP_EOL;
    }, ...array_values($res_arr));

    echo $res;
} elseif ($id) {
    $sql = "with tmp_res as (select ar, cr from test_work where id = :id)
select id, epl, epc from test_work where ar = (select ar from tmp_res as t1) and cr = (select cr from tmp_res as t2) order by id desc limit 20;";
   echo print_r((new DB())->getQuery($sql, ['id' => $id]), 1);
}

class Db
{

    public $db = null;
    private $pdo = null;
    public $dbname = 'hh_test';
    public $dbuser = 'root';
    public $dbpass = 'root';
    public $dbhost = '127.0.0.1';
    public $charset = 'utf8';
    public $options = [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // по умолчанию ассоциативный массив
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,//ошибки бросают исключения
    ];


    public function init($dbname = null) {
        $this->dbname = $dbname ?? $this->dbname;
        $this->getPDO();
    }

    public function getPDO() {
        if(empty($this->pdo)) {
            $this->pdo = new \PDO("mysql:host={$this->dbhost};port=3306;dbname={$this->dbname}",
                $this->dbuser,
                $this->dbpass,
                $this->options);
        }

        return $this->pdo;

    }

    public function getQuery(string $query, array $params = []): array
    {
        $stmt = $this->getPDO()->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getQueryOne(string $query, array $params = []): ?array
    {
        return self::getQuery($query, $params)[0] ?? null;
    }
}