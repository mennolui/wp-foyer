/**
 * Gets the video ID and service based on a video URL.
 *
 * Source: https://github.com/radiovisual/get-video-id
 *
 * Adapted to work without require.
 *
 * @since	1.2.0
 *
 * @param	string	str		The URL of the video to get the ID for.
 * @return	object			The ID and service of the video, if the URL matched a service.
 */
function foyer_get_video_id(str) {
	if (typeof str !== 'string') {
		throw new TypeError('get-video-id expects a string');
	}

	// remove the '-nocookie' flag from youtube urls
	str = str.replace('-nocookie', '');

	var metadata;

	if (/youtube|youtu\.be/.test(str)) {
		metadata = {
			id: foyer_get_video_id_youtube(str),
			service: 'youtube'
		};
	} else if (/vimeo/.test(str)) {
		metadata = {
			id: foyer_get_video_id_vimeo(str),
			service: 'vimeo'
		};
	} else if (/vine/.test(str)) {
		metadata = {
			id: foyer_get_video_id_vine(str),
			service: 'vine'
		};
	} else if (/videopress/.test(str)) {
		metadata = {
			id: foyer_get_video_id_videopress(str),
			service: 'videopress'
		};
	}
	return metadata;
};

/**
 * Get the vimeo id.
 * @param {string} str - the url from which you want to extract the id
 * @returns {string|undefined}
 */
function foyer_get_video_id_vimeo(str) {
	if (str.indexOf('#') > -1) {
		str = str.split('#')[0];
	}
	if (str.indexOf('?') > -1) {
		str = str.split('?')[0];
	}

	var id;
	if (/https?:\/\/vimeo\.com\/[0-9]+$|https?:\/\/player\.vimeo\.com\/video\/[0-9]+$/igm.test(str)) {
		var arr = str.split('/');
		if (arr && arr.length) {
			id = arr.pop();
		}
	}
	return id;
}

/**
 * Get the vine id.
 * @param {string} str - the url from which you want to extract the id
 * @returns {string|undefined}
 */
function foyer_get_video_id_vine(str) {
	var regex = /https:\/\/vine\.co\/v\/([a-zA-Z0-9]*)\/?/;
	var matches = regex.exec(str);
	return matches && matches[1];
}

/**
 * Get the Youtube Video id.
 * @param {string} str - the url from which you want to extract the id
 * @returns {string|undefined}
 */
function foyer_get_video_id_youtube(str) {
	// shortcode
	var shortcode = /youtube:\/\/|https?:\/\/youtu\.be\//g;

	if (shortcode.test(str)) {
		var shortcodeid = str.split(shortcode)[1];
		return foyer_get_video_id_stripParameters(shortcodeid);
	}

	// /v/ or /vi/
	var inlinev = /\/v\/|\/vi\//g;

	if (inlinev.test(str)) {
		var inlineid = str.split(inlinev)[1];
		return foyer_get_video_id_stripParameters(inlineid);
	}

	// v= or vi=
	var parameterv = /v=|vi=/g;

	if (parameterv.test(str)) {
		var arr = str.split(parameterv);
		return arr[1].split('&')[0];
	}

	// embed
	var embedreg = /\/embed\//g;

	if (embedreg.test(str)) {
		var embedid = str.split(embedreg)[1];
		return foyer_get_video_id_stripParameters(embedid);
	}

	// user
	var userreg = /\/user\//g;

	if (userreg.test(str)) {
		var elements = str.split('/');
		return foyer_get_video_id_stripParameters(elements.pop());
	}

	// attribution_link
	var attrreg = /\/attribution_link\?.*v%3D([^%&]*)(%26|&|$)/;

	if (attrreg.test(str)) {
		return str.match(attrreg)[1];
	}
}

/**
 * Get the VideoPress id.
 * @param {string} str - the url from which you want to extract the id
 * @returns {string|undefined}
 */
function foyer_get_video_id_videopress(str) {
	var idRegex;
	if (str.indexOf('embed') > -1) {
		idRegex = /embed\/(\w{8})/;
		return str.match(idRegex)[1];
	}

	idRegex = /\/v\/(\w{8})/;
	return str.match(idRegex)[1];
}

/**
 * Strip away any parameters following `?`
 * @param str
 * @returns {*}
 */
function foyer_get_video_id_stripParameters(str) {
	if (str.indexOf('?') > -1) {
		return str.split('?')[0];
	}
	return str;
}