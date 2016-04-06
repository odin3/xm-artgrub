<?php
class MusicStorage extends Model {
  private $TABLE  = 'XTracks1';
  private $folder = 'arts';
  private $fields = '`TAG`, `Artist1`, `Title`';

	public $db;
	
  public function getItemsCount() {
    return $this->db->count($this->TABLE);
  }

  public function getItems($limit=0, $offset=0) {
    return $this->db->GetRows("SELECT `TAG`, `Artist1`, `Title` FROM $this->TABLE ORDER BY `TAG` DESC LIMIT $limit OFFSET $offset;");
  }

  public function getGroupPath($track) {
    $group = intval( intval($track['TAG']) / 1000) * 1000;
    return APPDATA.$this->folder.DS.strval($group).DS.$track['TAG'];
  }

  public function getTrackByProperty($param, $value) {
    return $this->db->GetRow("SELECT $this->fields FROM $this->TABLE WHERE `$param` = '$value'");
  }

  public function getTrackByTag($tag) {
    return $this->getTrackByProperty('TAG', $tag);
  }

  public function deleteItem($where) {
    return $this->db->Delete($this->TABLE, $where);
  }

  public function hasAlbumArt($tag, $hasArt=1) {
    return $this->db->Update($this->TABLE, 
      array('HasArt' => $hasArt), 
      array('TAG' => $tag));
  }

  function __construct() {
      $this->db = new Database;
  }


}