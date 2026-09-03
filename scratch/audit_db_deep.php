<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$dbName = config('database.connections.mysql.database');

echo "=== DATABASE QA FORENSIC EXTRACTION ===\n";
echo "Database: " . $dbName . "\n\n";

// 1. Tables
$tables = DB::select("SELECT TABLE_NAME, ENGINE, TABLE_ROWS, DATA_LENGTH, INDEX_LENGTH, TABLE_COLLATION 
                      FROM information_schema.TABLES 
                      WHERE TABLE_SCHEMA = ? 
                      ORDER BY TABLE_NAME", [$dbName]);

$tableNames = array_map(fn($t) => $t->TABLE_NAME, $tables);
echo "1. TOTAL TABLES IN DB: " . count($tables) . "\n";
foreach ($tables as $t) {
    echo "  - {$t->TABLE_NAME} (Engine: {$t->ENGINE}, Rows: {$t->TABLE_ROWS})\n";
}

// 2. Columns
$columns = DB::select("SELECT TABLE_NAME, COLUMN_NAME, ORDINAL_POSITION, COLUMN_DEFAULT, IS_NULLABLE, DATA_TYPE, 
                              CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, NUMERIC_SCALE, COLUMN_TYPE, COLUMN_KEY, EXTRA 
                       FROM information_schema.COLUMNS 
                       WHERE TABLE_SCHEMA = ? 
                       ORDER BY TABLE_NAME, ORDINAL_POSITION", [$dbName]);

$tableColumns = [];
foreach ($columns as $c) {
    $tableColumns[$c->TABLE_NAME][] = $c;
}

// 3. Foreign Keys
$foreignKeys = DB::select("SELECT 
    kcu.TABLE_NAME,
    kcu.COLUMN_NAME,
    kcu.CONSTRAINT_NAME,
    kcu.REFERENCED_TABLE_NAME,
    kcu.REFERENCED_COLUMN_NAME,
    rc.UPDATE_RULE,
    rc.DELETE_RULE
FROM information_schema.KEY_COLUMN_USAGE kcu
JOIN information_schema.REFERENTIAL_CONSTRAINTS rc 
    ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME 
    AND kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
WHERE kcu.TABLE_SCHEMA = ? AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY kcu.TABLE_NAME, kcu.COLUMN_NAME", [$dbName]);

echo "\n2. TOTAL FOREIGN KEYS: " . count($foreignKeys) . "\n";
foreach ($foreignKeys as $fk) {
    echo "  - {$fk->TABLE_NAME}.{$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME} [ON DELETE {$fk->DELETE_RULE}, ON UPDATE {$fk->UPDATE_RULE}]\n";
}

// 4. Indexes
$indexes = DB::select("SELECT TABLE_NAME, INDEX_NAME, NON_UNIQUE, SEQ_IN_INDEX, COLUMN_NAME, COLLATION, CARDINALITY, INDEX_TYPE 
                       FROM information_schema.STATISTICS 
                       WHERE TABLE_SCHEMA = ? 
                       ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX", [$dbName]);

$tableIndexes = [];
foreach ($indexes as $idx) {
    $tableIndexes[$idx->TABLE_NAME][$idx->INDEX_NAME][] = $idx->COLUMN_NAME;
    $idxMeta[$idx->TABLE_NAME][$idx->INDEX_NAME] = [
        'non_unique' => $idx->NON_UNIQUE,
        'type' => $idx->INDEX_TYPE
    ];
}

echo "\n3. INDEXES SUMMARY:\n";
foreach ($tableIndexes as $tbl => $idxs) {
    echo "  Table [{$tbl}]:\n";
    foreach ($idxs as $idxName => $cols) {
        $unique = $idxMeta[$tbl][$idxName]['non_unique'] == 0 ? 'UNIQUE' : 'INDEX';
        echo "    * {$idxName} ({$unique}): (" . implode(', ', $cols) . ")\n";
    }
}

// Save to JSON for deep analysis
$dump = [
    'database' => $dbName,
    'tables' => $tables,
    'columns' => $tableColumns,
    'foreign_keys' => $foreignKeys,
    'indexes' => $tableIndexes,
    'index_meta' => $idxMeta ?? []
];

file_put_contents(__DIR__ . '/db_schema_actual.json', json_encode($dump, JSON_PRETTY_PRINT));
echo "\nSaved full schema to scratch/db_schema_actual.json\n";
