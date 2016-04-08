<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 08/04/16
 * Time: 10:20
 */
class MusicData extends Model
{
    private $tb_tracksInfo  = 'iTracks';
    private $tb_tags        = 'iTags';
    private $tb_artists     = 'iArtists';
    private $tb_albums      = 'iAlbums';

    private $db;

    public function ifAlbumExists($mbid) {
        return ( $this->db->Count($this->tb_albums, 'mbid', "where `mbid` = '$mbid'") > 0 );
    }

    public function ifArtistExists($mbid) {
        return ( $this->db->Count($this->tb_artists, 'mbid', "where `mbid` = '$mbid'") > 0 );
    }
    

    public function __construct() {
        $this->db = new Database();
    }
}