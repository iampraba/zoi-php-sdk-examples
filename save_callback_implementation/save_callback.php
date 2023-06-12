<?php
// Handle POST request
echo 'Post Request Received';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if a file was uploaded in parameter name "content". 
    // If different key is used, change the below paramater value with that key.
    $fileParameterName = "content";

    if (isset($_FILES[$fileParameterName]) && $_FILES[$fileParameterName]['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES[$fileParameterName];
        $filename = $uploadedFile['name'];
        $tempFilePath = $uploadedFile['tmp_name'];

        // Move the temporary file to a desired location. 
        //Change this location or read the file and store in your applicaton file store.
        $destinationPath = __DIR__ . "/output_" . $filename;

        echo $destinationPath;

        move_uploaded_file($tempFilePath, $destinationPath);

        // Return success response
        $response = [
            'status' => 'success',
            'message' => 'File uploaded successfully.'
        ];
    } else {
        // Return error response
        $response = [
            'status' => 'error',
            'message' => 'No file uploaded or an error occurred.'
        ];
    }

    // Send the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Handle unsupported HTTP methods
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    echo 'Method Not Allowed';
}
?>
