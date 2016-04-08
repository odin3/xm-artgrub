<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 08/04/16
 * Time: 10:20
 */
class MusicData extends Model
{
    private $tb_tracks      = 'iTracks';
    private $tb_tags        = 'iTags';
    private $tb_artists     = 'iArtists';
    private $tb_albums      = 'iAlbums';

    private $db;

    /**
     * If item with MBID exists in database
     *
     * @param $mbid String Item MBID
     * @param $table String Target table
     * @return bool Exists
     */
    private function ifMbIdExists($mbid, $table) {
        return ( $this->db->Count($table, 'mbid', "where `mbid` = '$mbid'") > 0 );
    }

    /**
     * Check if album exists
     * @param $mbid String Album's MBID
     * @return bool
     */
    public function albumExists($mbid) {
        return $this->ifMbIdExists($mbid, $this->tb_albums);
    }

    
    public function artistExists($mbid) {
        return $this->ifMbIdExists($mbid, $this->tb_artists);
    }

    public function trackExists($mbid) {
        return $this->ifMbIdExists($mbid, $this->tb_tracks);
    }

    public function tagExists($trackMBID, $tagName) {
      return ( $this->db->Count($this->tb_tags,
          '`trackId`',
          "where `trackId` = '$trackMBID' and `tagName` = '$tagName'") > 0 );
    }


    /**
     * Create an album in database
     *
     * @param $mbid String Album's MBID
     * @param $name String Album's Name
     * @param $artistId String MBID of Artist
     * @return DataBase
     */
    public function createAlbum($mbid, $name, $artistId, $hasArt=0) {
        return $this->db->Insert( $this->tb_albums, array(
            'mbid'      => $mbid,
            'name'      => $name,
            'artistId'  => $artistId,
            'hasArt'    => $hasArt
        ) );
    }

    /**
     * Create an artist
     * @param $mbid String Artist's mbid
     * @param $name String Artist's name
     * @return DataBase
     */
    public function createArtist($mbid, $name) {
        return $this->db->Insert( $this->tb_artists, array(
            'mbid'      => $mbid,
            'name'      => $name
        ) );
    }


    /**
     * Create an track in database
     *
     * @param $tag String Track Tag
     * @param $mbid String Track MBID
     * @param $duration int Duration (ms)
     * @param $artistId String Artist's MBID
     * @param $albumId  String Album's MBID
     * @return DataBase
     */
    public function createTrack($tag, $mbid, $duration, $artistId, $albumId=null) {
        $data = array(
          'tag'       => $tag,
          'mbid'      => $mbid,
          'duration'  => intval($duration),
          'artist'    => $artistId
        );

      if($albumId != null) $data['album'] = $albumId;
      return $this->db->Insert( $this->tb_tracks, $data);
    }


    /**
     * Create a tag for track
     *
     * @param $trackMBID String Track's MBID
     * @param $tagName  String Tag
     * @return DataBase
     */
    public function createTag($trackMBID, $tagName) {
        return $this->db->Insert( $this->tb_tags, array(
            'trackId'   => $trackMBID,
            'tagName'   => $tagName
        ) );
    }

    public function reset() {
      $this->db->Query('DELETE FROM iTracks;');
      $this->db->Query('DELETE FROM iTags;');
      $this->db->Query('DELETE FROM iArtists;');
      $this->db->Query('DELETE FROM iAlbums;');
    }


    public function __construct() {
        $this->db = new Database();
    }
}