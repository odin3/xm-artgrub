<?
class GetController extends Controller {
  private $musicStorage;
  private $lastFm;

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
        if(!count($item)) return AJAXResponse::error("[$tag] Item not found");



        if( $this->fetchImages($item) > 0 ) {
          $this->markAsDone($item);
          return AJAXResponse::reply("[$tag] Success");
        } else {
          return AJAXResponse::error("[$tag] No Arts Available");
        }

        
        
    } catch (Exception $ex) {
      return AJAXResponse::error((string) $ex);
    }
  }

  private function fetchImages($data) {

    $grpPath  = $this->musicStorage->getGroupPath($data);
    $arts     = $this->lastFm->getTrackAlbumArt($data['Artist1'], $data['Title']);

    foreach($arts as $type => $url) {
      $this->downloadImage($type, $grpPath, $url);
    }
    //var_dump($arts);
    return count($arts);
  }

  private function markAsDone($item) {
    $tag = $item['TAG'];
    return $this->musicStorage->hasAlbumArt($tag, 1);
  }

  private function downloadImage($type, $location, $url, $ext='png') {
    // No time limit
    set_time_limit(0);

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

  }

}