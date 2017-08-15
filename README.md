# Easy Google Drive API PHP   



Fast and easy to use Google Drive API for PHP.

  - Include class file.
  - Complete the basic setup.
  - Magic

#### Features (v0.1)

  - Easy to setup;
  - Create Folders,Upload files,List files or folders by just calling simple function.

#### (v0.2) (15/08/2017)

  - Refresh Token added for long lived session
  - isLoggedIn() method added to check access token is available without accessing session variables


### Installation

Clone this repo.

Install the dependencies of GoogleAPI by using composer.

```sh
$ composer install
```

### How to use

Open your favorite Terminal and run these commands.

Open GoogleClient.php and set your credintial details
```php
protected $client_id = '<CLIENT_ID>';
protected $client_secret = '<CLIENT_SECRET>';
protected $redirect_uri = '<REDIRECTED_URI>';
```

## Authentication:

```php
require_once 'google.php'; // Include Class File
session_start(); //Start the session
$google = new GoogleClient(); //Create GoogleClient Object

// Check if the user is logged in
if(!$google->isLoggedIn()){ 
    // Go to Google Login Page
	header('Location: '.$google->getAuthURL());
	exit;
}

// If the page is redirected from google authentication
if(isset($_GET['code'])){
    // Authenticate and start the session
	$google->authenticate($_GET['code']);
	# your code ..
}
```
### Drive Operations


Always initlize drive before using Drive operation
```php
$google->initDriveService();
```

### Create Folder
```php
/**
 *  Create folder at google drive
 *  @param string $parentId folder id in which folder will be created
 *  @param string $folderName folder name to create
 *  @return string id of created folder
 */
$folderId = $google->createFolder($parentFolderId,$folderName);
```

### Upload File
```php
 /**
  *  Upload file to given folder
  *  @param string $parentId parent folder in which folder will be upload
  *  @param string $filePath file local path of file which will be upload
  *  @param string $fileName file name of the uploaded copy at google drive
  *  @return string id of uploaded file
 */
    $fileId uploadFile($parentId,$filePath,$fileName="none");
```
### List Files and Folders

```php
/**
 *  Get the list of files or folders or both from given folder or root
 *  @param string $search complete or partial name of file or folder to search
 *  @param string $parentId parent folder id or root from which the list of files or folders or both will be generated
 *  @param string $type='all' file or folder (files,folders or all)
 *  @return array list of files or folders or both from given parent directory
 */
$list = listFilesFolders($search,$parentId,$type='all');
```


#### Todos
  - More ease to use API.
  - Different options of credentials either by initilizing variables or secred.json file.
  - Features like moving and copying one folder to another folder.

