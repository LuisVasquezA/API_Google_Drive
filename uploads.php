<?php
require_once __DIR__ . '/vendor/autoload.php';
/**
* Upload a file to a Google Drive accounts
* @param Google_Service_Drive $GoogleDriveClient is the client of a google account to use
* @param string $fileName is the name of the file
* @param string $filePath is the ubication of the file in the system
* @param string $fileType is the file MIME type. For example the type of a jpg image is "image/jpge"
*/
 function upload($GoogleDriveClient,$filePath,$fileType){
$services = new Google_Service_Drive($GoogleDriveClient);
$info = pathinfo($filePath);
$fileMetadata = new Google_Service_Drive_DriveFile(array(
  'name' => $info['basename']));
$content = file_get_contents($filePath);
$file = $services->files->create($fileMetadata,array(
    'data' => $content,
    'mimeType' => $fileType,
    'uploadType' => 'multipart',
    'fields' => 'id'));

printf("File ID: %s\n", $file->id);
}



/*POST https: 'www.googleapis.com/upload/drive/v3?uploadType=media HTTP/1.1';
Content-Type: image/jpeg;
Content-Length: [NUMBER_OF_BYTES_IN_FILE];
Authorization: Bearer [YOUR_AUTH_TOKEN];

[JPEG_DATA]*/


//Delete FilesystemIterator
function deleteFile ($services, $FileID){
  $service = new Google_Service_Drive($services);
  try {
    $service->files->delete($FileID);
  } catch (Exception $e) {
    print "An error ocurred: " . $e->getMessage();
  }

}

?>
