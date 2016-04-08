<?php
class LastFmProvider extends Model {
  private $URL    = 'http://ws.audioscrobbler.com/2.0/';
  private $APIKey = '6c66aab0827367749820083f04136b79';
  private $Secret = '8bbf87d4c640199336740318a74ce007';



  private function request($method, $args = array() ) {

    // Always request as JSON
    $args['format'] = 'json';

    // Request URL
    $requestURI = "$this->URL?method=$method&api_key=$this->APIKey";

    foreach($args as $key => $val) {
      $requestURI .= "&$key=".urlencode($val);
    }

    $item = json_decode( file_get_contents($requestURI), true );
    return (isset($item['error'])) ? array() : $item;
  }

  public function getTrack($artist, $trackName) {
    $artist     = strtolower($artist);
    $trackName  = strtolower($trackName);
    return $this->request('track.getInfo', array(
        'artist'  => $artist,
        'track'   => $trackName
      ));
  }

  public function getTrackTags($data) {
      if(!isset($data['toptags']) || !isset($data['toptags']['tag']) ) return array();
      $ar_tags = $data['toptags']['tag'];
      $tags = array();

      foreach($ar_tags as $tag) {
        if(isset($tag['name'])) array_push($tags, $tag['name']);
      }

      return $tags;
  }

  public function extractTrackInfo($data, $tag) {
    if(!isset($data['track'])) return array();

    $result = array();
    $track  = $data['track'];

    $result['art']        = $this->getTrackAlbumArt($data);

    $result['mbid']       = (isset($track['mbid'])) ? $track['mbid'] : $tag;

    $result['url']        = $track['url'];
    $result['duration']   = $track['duration'];


    if(isset($track['artist'])) {
      $result['artist']   = $track['artist'];
      if(!isset($result['artist']['mbid'])) $result['artist']['mbid'] = $tag;
    }

    if(isset($track['album'])) {
      $result['album']   = $track['album'];
    } else {
      $result['album'] = array();
    }

    $result['tags']       = $this->getTrackTags($track);

    return $result;
    


  }


  public function getTrackAlbumArt($data) {

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