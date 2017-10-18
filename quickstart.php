
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/uploads.php';

define('APPLICATION_NAME', 'Drive API Quickstart');
define('CREDENTIALS_PATH', __DIR__.'/client_secret.json');
define('CLIENT_SECRET_PATH', __DIR__ .'/client_id2.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/drive-php-quickstart.json
define('SCOPES', implode(' ', array(Google_Service_Drive::DRIVE_FILE)));

if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CREDENTIALS_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CLIENT_SECRET_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));

    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, json_encode($accessToken));
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Drive($client);

// Print the names and IDs for up to 10 files.
$optParams = array(
  'pageSize' => 10,
  'fields' => 'nextPageToken, files(id, name)'
);
$results = $service->files->listFiles($optParams);

if (count($results->getFiles()) == 0) {
  print "No files found.\n";
} else {
  print "Files:\n";
  foreach ($results->getFiles() as $file) {
    printf("%s (%s)\n", $file->getName(), $file->getId());
  }
}

$filePath = "//Applications/XAMPP/xamppfiles/htdocs/APIDRIVE/ContenedorDrive/ prueba nube.png";
$fileType = "image/pgn";
//$url = "//Applications/XAMPP/xamppfiles/htdocs/APIDRIVE/ContenedorDrive/photo.jpg";
//$archivo = basename($url);

upload(getClient(), $filePath, $fileType);
deleteFile(getClient(),'0B6oO7odOul8IV1dGZFJlX1otSUE');
