<?
// No time limit
set_time_limit(0);

define("ERR_WARNING", 1);
define("ERR_PANIC", 0);

class GetController extends Controller {
  private $musicStorage;
  private $lastFm;
  private $dataStorage;

  public function Main() {

    if(!isset($_GET['offset'])) return AJAXResponse::error('`offset` is undefined.');

    try {

        // Get requested tag
        $offset = intval($_GET['offset']);
        Session::set('offset', $offset + 1);
        // Find item
        // $item = $this->musicStorage->getTrackByTag($tag);

        $item = $this->musicStorage->getItems(1, $offset);
        

        if(count($item) == 0) return AJAXResponse::error("No item for offset $offset");
        $item = $item[0];

        $tag  = $item['TAG'];

        // If search result is empty
        if(!count($item)) return AJAXResponse::error("[$tag:$offset] Item not found", ERR_PANIC);


        $track = $this->lastFm->getTrack($item['Artist1'], $item['Title']);

        if( count($track) == 0 ) return AJAXResponse::error("[$tag:$offset] No information from Last.FM available");

        // Get data and put into db
        $this->saveMetaData($tag, $track);

        // Download images
        if( $this->fetchImages($item, $track) > 0 ) {
          $this->markAsDone($item);
          return AJAXResponse::reply("[$tag:$offset] Success");
        } else {
          return AJAXResponse::error("[$tag:$offset] No Arts Available", ERR_WARNING);
        }

        
        
    } catch (Exception $ex) {
      return AJAXResponse::error($_GET['offset'].((string) $ex) );
    }
  }


  private function saveMetaData($tag, $data) {
    $track = $this->lastFm->extractTrackInfo($data, $tag);

    if(count($track) == 0) return false;

    $mbid   = $track['mbid'];
    $length = (isset($track['duration'])) ? $track['duration'] : 0;
    $artist = $track['artist'];
    $album  = $track['album'];
    $tags   = $track['tags'];

    
    // Write artist
    if (!$this->dataStorage->artistExists($artist['mbid']) ) {
      $this->dataStorage->createArtist($artist['mbid'], $artist['name']);
    }

    // Write album
    if( (count($track['album']) > 0) && isset($track['album']['mbid']) ) {
      $album = $track['album'];

      if (!$this->dataStorage->albumExists($album['mbid']) ) {
        $hasArt = ( isset($album['image']) && (count($album['image']) > 0) );

        $this->dataStorage->createAlbum($album['mbid'], $album['title'], $artist['mbid'], $hasArt);
      }
    }

    // Write track
    if(!$this->dataStorage->trackExists($mbid)) {
      $albumId = (isset($album['mbid'])) ? $album['mbid'] : null;
      $this->dataStorage->createTrack($tag, $mbid, $length, $artist['mbid'], $albumId);
    }

    // Write tags
    foreach($tags as $tag) {
      if(!$this->dataStorage->tagExists($mbid, $tag)) {
        $this->dataStorage->createTag($mbid, $tag);
      }
    }

  }

  private function fetchImages($data, $lastFmData) {

    $grpPath  = $this->musicStorage->getGroupPath($data);
    $arts     = $this->lastFm->getTrackAlbumArt($lastFmData);

    foreach($arts as $type => $url) {
      $this->downloadImage($type, $grpPath, $url);
    }

    return count($arts);
  }

  private function markAsDone($item) {
    $tag = $item['TAG'];
    return $this->musicStorage->hasAlbumArt($tag, 1);
  }

  private function downloadImage($type, $location, $url, $ext='png') {

    // Create directory recursive
    if(!Dir::Exists($location)) Dir::Create($location, true);

    $saveto = $location.DS.$type.'.'.$ext;

    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);

  }
  private function idownloadImage($type, $location, $url, $ext='png') {

    // No time limit
    set_time_limit(0);

    // Create directory recursive
    if(!Dir::Exists($location)) Dir::Create($location, true);

    $target = $location.DS.$type.'.'.$ext;
    $file = fopen($target, 'w+');

    $curl   = curl_init($url);

    // Update as of PHP 5.4 array() can be written []
    curl_setopt_array($curl, [
        CURLOPT_URL            => $url,
        CURLOPT_BINARYTRANSFER => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FILE           => $file,
        CURLOPT_TIMEOUT        => 50,
        CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
    ]);

    $response = curl_exec($curl);

    if($response === false) {
        // Update as of PHP 5.3 use of Namespaces Exception() becomes \Exception()
        throw new \Exception('Curl error: ' . curl_error($curl));
    }

    return ($response !== false);

  }

  public function __construct() {
    // Dependency
    Extensions::request('ajaxResponse');

    // Get models
    $this->musicStorage = Model::Get('MusicStorage');
    $this->lastFm       = Model::Get('LastFmProvider');
    $this->dataStorage  = Model::Get('MusicData');

  }

}