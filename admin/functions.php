<?php

/* 
 * Copyright (C) 2022 boomer
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
function codelighter_get_styles_list_object() {
	$codelighter_curl_init = curl_init( 'https://api.github.com/repos/highlightjs/highlight.js/git/trees/cb8292f8e1ff39b922de12012908d9f67bc73346' );
	curl_setopt( $codelighter_curl_init, CURLOPT_RETURNTRANSFER, 'true');
	curl_setopt( $codelighter_curl_init, CURLOPT_HTTPHEADER, array('User-Agent: WordPress', 'POST / HTTP/1.1'));
	$codelighter_file_list_array = curl_exec( $codelighter_curl_init );
	$codelighter_curl_errno = curl_errno($codelighter_curl_init);
	$codelighter_curl_error = curl_error($codelighter_curl_init);
	curl_close($codelighter_curl_init);
	if ($codelighter_curl_errno > 0) {
		return "cURL Error ($codelighter_curl_errno): $codelighter_curl_error\n";
	} else {
		return json_decode( $codelighter_file_list_array );
	}
}