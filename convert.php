#!/usr/bin/env php
<?php

// converts a directory from wav to mp3

$wav = glob("*.wav");
foreach($wav as $f) {
	$output = str_replace('.wav', '.mp3', $f);

	passthru('ffmpeg -i "'.$f.'" -codec:a libmp3lame -qscale:a 2 "'.$output.'"');
	unlink($f);
}
