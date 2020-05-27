<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Fusonic\Linq\Linq;
$files = glob("/tmp/*");

// Sort all files in a directory by filsize in descending order

### Plain PHP: ###
$data = [];
foreach($files as $file) {
    $currentSize = filesize($file);
    $data[] = ["name" => $file, "size" => $currentSize];
}

uasort($data, function($a, $b) {
   $as = $a['size'];
   $bs = $b['size'];
   if($as == $bs) { return 0; }
   else return $as < $bs ? 1 : -1;
});

foreach($data as $x)
{
    echo $x['name'] . " " . $x['size'] . "<br>";
}

### Linq: ###

echo "<br/><br> Linq: <br /><br>";

$linq = Linq::from($files)
    ->select(fn($ex) => ["name" => $x, "size" => filesize($x)])
    ->orderByDescending(fn($x) => $x['size'])
    ->each(fn($x) =>
        echo $x['name'] . " " . $x['size'] . "<br>";
    });