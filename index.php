<?php
    require './vendor/autoload.php';

    $redis = new Predis\Client();

    $key = 'PRODUCTS';
    $cachedEntry = $redis->get($key);

    if ($cachedEntry) {
        $source = 'Redis Server';
        $products = unserialize($redis->get($key));
    }
    else {
        $source = 'MySQL Server';
        $database_name     = 'presto';
        $database_user     = 'root';
        $database_password = '';
        $mysql_host        = 'localhost';

        $pdo = new PDO('mysql:host=' . $mysql_host . '; dbname=' . $database_name, $database_user, $database_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql  = "SELECT * FROM pcontacts limit 2";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[$row['idpContact']] = $row;
        }

        $redis->set($key, serialize($products));
        // $redis->expire($key, 10);
    }
    
echo $source . ': <br>';
echo "<pre>";
print_r($products);
echo "</pre>";
?>