use XmusiX;
select XTracks1.* from XTracks1 left join xm2_songs_with_aa
ON (XTracks1.`TAG` = xm2_songs_with_aa.tag) WHERE xm2_songs_with_aa.tag is null;



use XmusiX;
delete XTracks1.* from XTracks1 left join xm2_songs_with_aa
ON (XTracks1.`TAG` = xm2_songs_with_aa.tag) WHERE XTracks1.`TAG` = xm2_songs_with_aa.tag;


DELETE FROM iTracks; DELETE FROM iTags; DELETE FROM iArtists; DELETE FROM iAlbums;