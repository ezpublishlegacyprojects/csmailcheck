<?

echo "Starting expired hash cleanup...\n";

$db = eZDB::instance(); 
$db->query('DELETE FROM csmailcheck WHERE expires < '.time());

echo "Finished clearing old check mail hashes...\n"
?>