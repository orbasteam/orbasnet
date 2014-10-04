<? 
function array_column($input, $column_key, $index_key = null)
{
    if ($index_key !== null) {
        // Collect the keys
        $keys = array();
        $i = 0; // Counter for numerical keys when key does not exist
        
        foreach ($input as $row) {
            if (array_key_exists($index_key, $row)) {
                // Update counter for numerical keys
                if (is_numeric($row[$index_key]) || is_bool($row[$index_key])) {
                    $i = max($i, (int) $row[$index_key] + 1);
                }
                
                // Get the key from a single column of the array
                $keys[] = $row[$index_key];
            } else {
                // The key does not exist, use numerical indexing
                $keys[] = $i++;
            }
        }
    }
    
    if ($column_key !== null) {
        // Collect the values
        $values = array();
        $i = 0; // Counter for removing keys
        
        foreach ($input as $row) {
            if (array_key_exists($column_key, $row)) {
                // Get the values from a single column of the input array
                $values[] = $row[$column_key];
                $i++;
            } elseif (isset($keys)) {
                // Values does not exist, also drop the key for it
                array_splice($keys, $i, 1);
            }
        }
    } else {
        // Get the full arrays
        $values = array_values($input);
    }
    
    if ($index_key !== null) {
        return array_combine($keys, $values);
    }
    
    return $values;
}
?>