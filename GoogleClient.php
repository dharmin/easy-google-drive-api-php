<?php
require_once 'vendor/autoload.php';

class GoogleClient{
    protected $client_id = '<CLIENT_ID>';
    protected $client_secret = '<CLIENT_SECRET>';
    protected $redirect_uri = '<REDIRECTED_URI>';
    
    protected $scopes = array('https://www.googleapis.com/auth/drive',);

    protected $client;
    protected $service;

    /**
     *  Construct an easy to use API client.
     */
    public function __construct() {
        $this->client = new Google_Client();  
        $this->client->setClientId($this->client_id);
        $this->client->setClientSecret($this->client_secret);
        $this->client->setRedirectUri($this->redirect_uri);
        $this->client->setAccessType('offline');
        $this->client->setScopes($this->scopes);
        if(isset($_SESSION['GOOGLE_ACCESS_TOKEN'])){
            $this->client->setAccessToken($_SESSION['GOOGLE_ACCESS_TOKEN']);
        }
    }

    /**
     *  Authenticate the client and set access token after login
     *  @param string $code redirected code
     */
    public function authenticate($code) {
        $this->client->authenticate($_GET['code']);
        $_SESSION['GOOGLE_ACCESS_TOKEN'] = $this->client->getAccessToken();
    }

    /**
     *  To set access token explicitely
     *  @param string $accessToken access token
     */
    public function setAccessToken($accessToken){
        $this->client->setAccessToken($accessToken);
    }

    /**
     *  To get authentication URL if not in session
     *  @return string
     */
    public function getAuthURL(){
        return $this->client->createAuthUrl();
    }

    /**
     *  Returns the google client object
     *  @return Google_Client
     */
    public function getClient(){
        return $this->client;
    }

    /**
     *  Initilize drive services object
     */
    public function initDriveService(){
        $this->service = new Google_Service_Drive($this->client);
    }

    /**
     *  Create folder at google drive
     *  @param string $parentId parent folder id or root where folder will be created
     *  @param string $folderName folder name to create
     *  @return string id of created folder
     */
    public function createFolder($parentId,$folderName){
        $fileMetadata = new Google_Service_Drive_DriveFile(array(
            'name' => $folderName,
            'parents' => array($parentId),
            'mimeType' => 'application/vnd.google-apps.folder'));
        $file = $this->service->files->create($fileMetadata, array('fields' => 'id'));
        return $file->id;
    }

    /**
     *  Get the list of files or folders or both from given folder or root
     *  @param string $search complete or partial name of file or folder to search
     *  @param string $parentId parent folder id or root from which the list of files or folders or both will be generated
     *  @param string $type='all' file or folder
     *  @return array list of files or folders or both from given parent directory
     */
    public function listFilesFolders($search,$parentId,$type='all'){
        $query = '';
        $condition = $search!=''?'=':'contains';
        $query .= $parentId!='all'?"'".$parentId."' in parents":"";
        
        switch($type){
          case "files":   $query .= $query!=''?' and ':'';
          $query .= "mimeType != 'application/vnd.google-apps.folder' and name ".$condition." '".$search."'";
          break;
          case "folders": $query .= $query!=''?' and ':'';
          $query .= "mimeType = 'application/vnd.google-apps.folder' and name contains '".$search."'";
          break;
          default:        $query .= "";
          break;
      }
      $query .= $query!=''?' and trashed = false':'trashed = false';
      $optParams = array('q' => $query,'pageSize' => 1000);

      $results = $this->service->files->listFiles($optParams);

      if (count($results->getFiles()) == 0) {
          return false;
      }
      $result = array();
      foreach ($results->getFiles() as $file) {
          $result[$file->getId()] = $file->getName();
      }
      return $result;
  }



    /**
     *  Upload file to given folder
     *  @param string $parentId parent folder id or root where folder will be upload
     *  @param string $filePath file local path of file which will be upload
     *  @param string $fileName file name of the uploaded copy at google drive
     *  @return string id of uploaded file
     */
    public function uploadFile($parentId,$filePath,$fileName="none"){
        if($fileName=="none") $fileName = end(explode('/', $filePath));
            $fileMetadata = new Google_Service_Drive_DriveFile(array(
                'name' => $fileName,
                'parents' => array($parentId)
            ));
            $content = file_get_contents($filePath);
            $file = $this->service->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => 'image/jpeg',
                'uploadType' => 'multipart',
                'fields' => 'id'));
            return $file->id;
        }
    }
}