<?php
class LastFmProvider extends Model {
  private $URL    = 'http://ws.audioscrobbler.com/2.0/';
  private $APIKey = '6c66aab0827367749820083f04136b79';
  private $Secret = '8bbf87d4c640199336740318a74ce007';



  private function request($method, $args) {
    $requestURI = "$this->URL?method=$method&api_key=$this->APIKey";

    foreach($args as $key => $val) {
      $requestURI .= "&$key=".urlencode($val);
    }

    return json_decode( file_get_contents($requestURI), true );
    //return new SimpleXMLElement($requestURI, null, true);
  }

  public function getTrackInfo($artist, $trackName) {
    return $this->request('track.getInfo', array(
        'artist'  => $artist,
        'track'   => $trackName,
        'format'  => 'json'
      ));
  }

  public function getTrackAlbumArt($artist, $track) {
    $artist = strtolower($artist);
    $track = strtolower($track);
    $data = $this->getTrackInfo($artist, $track);

    if(!isset($data['track'])) return array();
    
    $data = $data['track'];


    if(!isset($data['album'])) return array();
    $data = $data['album']['image'];
    $out = array();
    foreach($data as $i => $v) {
      $size = $v['size'];
      $url  = $v['#text'];

      $out[$size] = $url;
    }
    return $out;
  }

}