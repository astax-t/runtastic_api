<?php
require_once("config.php");
require_once("classes/Runtastic.php");


set_time_limit(0);

$rt = new Runtastic();
try {
	$rt->Login($cfg_username, $cfg_password);
	//$rt->ReopenSession('51c377635d3ad');

	$tracks = $rt->GetTracks();
	//var_export($tracks);
	echo count($tracks)." tracks found\n";

	$cnt = 0;
	foreach ($tracks as $track_id)
	{
		if ($cfg_tracks_number_limit > 0 && ++$cnt > $cfg_tracks_number_limit)
			break;
		echo "Downloading $track_id... ";
		list($track_name, $track_data) = $rt->GetTrackTCX($track_id);
		file_put_contents('tracks/'.$track_name, $track_data);
		echo "saved $track_name\n";
		flush();
	}

	$rt->Logout();
} catch (Exception $e)
{
	echo "ERROR: ".$e->getMessage();
}
$rt->Close();
