<?php

// Set the directory path
$directory = getcwd();

// Open the directory
if (is_dir($directory)) {
    if ($dh = opendir($directory)) {

        // Loop through all files in the directory
        while (($file = readdir($dh)) !== false) {

            // Check if the file is a PHP file
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {

                // Output the file name (optional)
                echo "\n\nRunning $file...\n\n";

                // Include the file once to execute it, avoiding duplicate class declarations
                include_once($directory . '/' . $file);
                
                // Or use require_once if you need to ensure the file is included
                // require_once($directory . '/' . $file);
            }
        }

        // Close the directory
        closedir($dh);
    }
} else {
    echo "Directory does not exist.";
}

?>
