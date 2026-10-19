<?php

/*
 *  Instance Configuration
 *  ----------------------
 *  Edit this file and not config.php for imageboard configuration.
 *
 *  You can copy values from config.php (defaults) and paste them here.
 */



	// Database stuff
	$config['db']['type']		= 'mysql';
	$config['db']['server']		= 'localhost';
	$config['db']['user']		= '';
	$config['db']['password']	= '';
	$config['db']['database']	= '';
	
	//$config['root']				= '/';
	
	@include('inc/secrets.php');

	$config['flood_time'] = 15;
	$config['flood_time_ip'] = 120;
	$config['flood_time_same'] = 3600;
	$config['max_body'] = 1800;
	$config['reply_limit'] = 500;
	$config['max_links'] = 20;
	$config['thumb_width'] = 250;
	$config['thumb_height'] = 250;
	$config['max_width'] = 10000;
	$config['max_height'] = 10000;
	$config['max_filesize'] = 20971520;
	$config['threads_per_page'] = 50;
	$config['max_pages'] = 15;
	$config['threads_preview'] = 6;
	$config['thumb_method'] = 'gm+gifsicle';
	$config['gnu_md5'] = '1';

 // An alternative function for generating image filenames, instead of the default UNIX timestamp.
//$config['filename_func'] = function($post) {
//	return sprintf("%s", time() . substr(microtime(), 2, 3));
//};

// flush out cache for stylesheets and shit
$config['resource_version'] = 30;

// Custom stylesheets available for the user to choose. See the "stylesheets/" folder for a list of
        // available stylesheets (or create your own).
        $config['stylesheets'] = [
                // Default; there is no additional/custom stylesheet for this.
                'Denmark' => 'denmark.css',
		'Yotsuba B' => '',
                'XenForo Beta' => 'xenforo.css',
                'Shemmy Dark' => 'miku-dark.css',
                'Shemmy' => 'miku.css',
                'Yotsuba' => 'yotsuba.css',
                'Dark' => 'dark.css',
		'Spooky' => 'spooky.css',
                'Shemmy OG' => 'shemmy.css'
        ];
        // $config['stylesheets']['Futaba'] = 'futaba.css';

// The default stylesheet to use.
	$config['default_stylesheet'] = array('Spooky', $config['stylesheets']['Spooky']);


$config['filename_func'] = function($post) { 
    // Converts the current Unix timestamp directly into an 8-character hex string
    return sprintf("%08x", time()/* . substr(microtime(), 2, 3) ## this is for milliseconds */); 
};

$config['boards'] = [['home'=>'/', 'popular'=>'/popular', 'overboard'=>'/o/'], ['soy', 'nas', 'polint', 'm'], ['rules'=>'/info/rules', 'stats'=>'/info/stats', 'contact'=>'/info/contact']];

$config['ipcrypt_prefix'] = 'Clk';


$config['anonymous'] = array('Soyposter', 'Hail Wiggers', 'Mozambican', 'Soyjak', 'Chud', 'Danish Win', 'Denmark', 'Reliable Person', 'Spoon', 'Mameson', 'Massjak', 'Desu', 'Kawaii', 'Jakker', 'Osaka', 'Mailman', 'Poster', 'Pin', 'Genius', 'Ordinary', 'Root', 'Cat', 'Bob Ross', 'Manager', 'Developer', 'Athanatos');
//$config['url_banner'] = '/banner.png';
$bannerarray = ['/banner.png', '/ptb/src/6ac17c7d%20-%20soyposter.party.png'];

$config['url_banner'] = $bannerarray[array_rand($bannerarray)];


// $config['mod']['flood'] = DISABLED;

$config['thumb_ext'] = '';
include_once "underattack.php";

$config['threads_preview_sticky'] = 1;
$config['report_limit'] = 3;
$config['allow_subtitle_html'] = true;

$config['image_reject_repost'] = false;
$config['image_reject_repost_in_thread'] = false;
$config['blotterlines'][] = "i pooped in the toilet yesterday";
$config['blotterlines'][] = "^unfunny manager coal";
$config['webm']['use_ffmpeg'] = true;
$config['webm']['allow_audio'] = true;
$config['webm']['max_length'] = 600;
$config['webm']['ffmpeg_path'] = "/usr/bin/ffmpeg";
$config['webm']['ffprobe_path'] = "/usr/bin/ffprobe";

$config['additional_javascript'][] = 'js/jquery-ui.min.js';
$config['additional_javascript'][] = 'js/jquery-ui.custom.min.js';

$config['additional_javascript'][] = 'js/ruffle/ruffle.js';

$config['additional_javascript'][] = 'js/wPaint/wPaint.min.js';
$config['additional_javascript'][] = 'js/wPaint/8ch.js';
$config['additional_javascript'][] = 'js/wpaint.js';

$config['additional_javascript'][] = 'js/expand.js';

$config['additional_javascript'][] = 'js/post-hover.js';
// (optional; must come first)
$config['additional_javascript'][] = 'js/show-backlinks.js';



$config['additional_javascript'][] = 'js/inline-expanding.js';
$config['additional_javascript'][] = 'js/expand-swf.js';
$config['additional_javascript'][] = 'js/webm-settings.js';
$config['additional_javascript'][] = 'js/expand-video-old.js';
//$config['additional_javascript'][] = 'js/expand-video.js';
$config['additional_javascript'][] = 'js/expand-all-images.js';
$config['additional_javascript'][] = 'js/archive-buttons.js';

// image file extensions

	$config['allowed_ext'][] = 'jpg';
	$config['allowed_ext'][] = 'jpeg';
	$config['allowed_ext'][] = 'jfif';
	$config['allowed_ext'][] = 'bmp';
	$config['allowed_ext'][] = 'gif';
	$config['allowed_ext'][] = 'png';
	$config['allowed_ext'][] = 'bmp';
	$config['allowed_ext'][] = 'svg';
	$config['allowed_ext'][] = 'webp';

// additonal files

	$config['allowed_ext_files'][] = 'txt';
	$config['allowed_ext_files'][] = 'css';
	$config['allowed_ext_files'][] = 'svg';
	$config['allowed_ext_files'][] = 'mp4';
	$config['allowed_ext_files'][] = 'm4a';
	$config['allowed_ext_files'][] = 'mp3';
	$config['allowed_ext_files'][] = 'mp2';
	$config['allowed_ext_files'][] = 'midi';
	$config['allowed_ext_files'][] = 'wplace';


        $config['allowed_ext_files'][] = 'wav';
	$config['allowed_ext_files'][] = 'flac';
	//$config['allowed_ext_files'][] = 'pdf';
	$config['allowed_ext_files'][] = 'mov';
	$config['allowed_ext_files'][] = 'webm';
	$config['allowed_ext_files'][] = 'swf';
	$config['allowed_ext_files'][] = 'mkv';
        $config['allowed_ext_files'][] = 'aiff';
        $config['allowed_ext_files'][] = 'flv';
        $config['allowed_ext_files'][] = 'midi';
        $config['allowed_ext_files'][] = 'mid';
        $config['allowed_ext_files'][] = 'mp2';
        $config['allowed_ext_files'][] = 'ogg';
        $config['allowed_ext_files'][] = 'flv';

	$config['video_ext'] = array();
	$config['video_ext'][] = 'mp4';
	$config['video_ext'][] = 'webm';
	$config['video_ext'][] = 'mov';
	$config['video_ext'][] = 'm4a';
	$config['video_ext'][] = 'mp3';
	$config['video_ext'][] = 'wav';
	$config['video_ext'][] = 'flac';
        $config['video_ext'][] = 'aiff';
        $config['video_ext'][] = 'midi';
        $config['video_ext'][] = 'mid';
        $config['video_ext'][] = 'mp2';
        $config['video_ext'][] = 'ogg';
        $config['video_ext'][] = 'flv';
        $config['video_ext'][] = 'mkv';


// Enable or disable Font Awesome icons globally across the boards
$config['font_awesome'] = true;

// Define the relative path to the Font Awesome stylesheet
$config['font_awesome_css'] = 'stylesheets/font-awesome/css/font-awesome.min.css';

// embedding shit

	// Enable embedding (see below).
	$config['enable_embedding'] = true;

	// Youtube.js embed HTML code
	$config['youtube_js_html'] = '<div class="video-container" data-video="$2">'.
		'<a href="https://youtu.be/$2" target="_blank" class="file">'.
		'<img style="width:252px;height:189px;" src="https://img.youtube.com/vi/$2/0.jpg" class="post-image"/>'.
		'</a></div>';

	// Custom embedding (YouTube, vimeo, etc.)
	// It's very important that you match the entire input (with ^ and $) or things will not work correctly.
	// Be careful when creating a new embed, because depending on the URL you end up exposing yourself to an XSS.
	$config['embedding'] = array(
		array(
			'/^https?:\/\/(\w+\.)?youtube\.com\/watch\?v=([a-zA-Z0-9\-_]{10,11})?$/i',
//			'<iframe allow="cross-origin-isolated" style="float: left; margin: 10px 20px;" width="%%tb_width%%" height="%%tb_height%%" frameborder="0" id="ytplayer" src="https://invidious.fdn.fr/embed/$2"></iframe>'
			$config['youtube_js_html']
		),
		array(
			'/^https?:\/\/(\w+\.)?youtu\.be\/([a-zA-Z0-9\-_]{10,11})?$/i',
//			'<iframe allow="cross-origin-isolated" style="float: left; margin: 10px 20px;" width="%%tb_width%%" height="%%tb_height%%" frameborder="0" id="ytplayer" src="https://invidious.fdn.fr/embed/$2"></iframe>'
			$config['youtube_js_html']
		),
		array(
			'/^https?:\/\/(\w+\.)?vimeo\.com\/(\d{2,10})(\?.+)?$/i',
			'<iframe allow="cross-origin-isolated" style="float: left; margin: 10px 20px;" width="%%tb_width%%" height="%%tb_height%%" frameborder="0" src="https://player.vimeo.com/video/$2"></iframe>'
		),
		array(
			'/^https?:\/\/(\w+\.)?dailymotion\.com\/video\/([a-zA-Z0-9]{2,10})(_.+)?$/i',
			'<iframe allow="cross-origin-isolated" style="float: left; margin: 10px 20px;" width="%%tb_width%%" height="%%tb_height%%" frameborder="0" src="https://www.dailymotion.com/embed/video/$2" allowfullscreen></iframe>'
		),
		array(
			'/^https?:\/\/(\w+\.)?metacafe\.com\/watch\/(\d+)\/([a-zA-Z0-9_\-.]+)\/(\?[^\'"<>]+)?$/i',
			'<iframe allow="cross-origin-isolated" style="float: left; margin: 10px 20px;" width="%%tb_width%%" height="%%tb_height%%" frameborder="0"  src="https://www.metacafe.com/embed/$2/$3/" allowfullscreen></iframe>'
		),
		array(
                        '/^https?:\/\/(\w+\.)?vocaroo\.com\/([a-zA-Z0-9]{2,12})$/i',
                        '<iframe allow="cross-origin-isolated" style="float: left; margin: 10px 20px;" width="300" height="60" frameborder="0" src="https://vocaroo.com/embed/$2"></iframe>'
		)
	);

	// Embedding width and height.
	$config['embed_width'] = 300;
	$config['embed_height'] = 250;

// end of embedding shit

// mod links

	$config['mod']['link_delete'] = '[<i id="modlink" class="fa fa-trash"></i>]';
	$config['mod']['link_ban'] = '[<i id="modlink" class="fa fa-ban"></i>]';
	$config['mod']['link_threadban'] = '[TB]';
	$config['mod']['link_bandelete'] = '[<i id="modlink" class="fa fa-ban"></i>&amp;<i id="modlink" class="fa fa-trash"></i>]';
	$config['mod']['link_deletefile'] = '[<i id="modlink" class="fa fa-minus-square"></i>]';
	$config['mod']['link_spoilerimage'] = '[<i id="modlink" class="fa fa-eye-slash"></i>]';
	$config['mod']['link_marknsfw'] = '[NSFW]';
	$config['mod']['link_approvefile'] = '[Approve]';
	$config['mod']['link_rejectfile'] = '[Reject]';
	$config['mod']['link_deletebyip'] = '[<i id="modlink" class="fa fa-trash"></i>B]';
	$config['mod']['link_deletebyip_global'] = '[<i id="modlink" class="fa fa-trash"></i><i id="modlink" class="fa fa-globe"></i>]';
	$config['mod']['link_deletebyip_thread'] = '[DT]';
	$config['mod']['link_sticky'] = '[<i id="modlink" class="fa fa-sticky-note"></i>]';
	$config['mod']['link_desticky'] = '[-<i id="modlink" class="fa fa-sticky-note"></i>]';
	$config['mod']['link_lock'] = '[<i id="modlink" class="fa fa-lock"></i>]';
	$config['mod']['link_unlock'] = '[<i id="modlink" class="fa fa-unlock"></i>]';
	$config['mod']['link_bumplock'] = '[<i id="modlink" class="fa fa-arrow-circle-down"></i>]';
	$config['mod']['link_bumpunlock'] = '[-<i id="modlink" class="fa fa-arrow-circle-down"></i>]';
	$config['mod']['link_editpost'] = '[<i id="modlink" class="fa fa-edit"></i>]';
	$config['mod']['link_move'] = '[<i id="modlink" class="fa fa-share"></i>]';
	$config['mod']['link_cycle'] = '[<i id="modlink" class="fa fa-history"></i>]';
	$config['mod']['link_uncycle'] = '[-<i id="modlink" class="fa fa-history"></i>]';
	$config['mod']['link_invincible'] = '[<i id="modlink" class="fa fa-sync"></i>]';
	$config['mod']['link_uninvincible'] = '[-<i id="modlink" class="fa fa-sync"></i>]';






$config['hide_sage'] = false;






// SHIT I DONT FEEL COMFORTABLE TOUCHING BELOW!!!!! --manager



// mod ?/eval page (not implemented yet)
$config['show_filename'] = true;
$config['mod']['admin_is_retarded'] = true;
$config['mod']['view_filenames'] = ADMIN;

//$config['global_message'] = "We are currently working on fresh new CSS and JS that will make soyposter.party feel and look very snappy, will be released in a few days.";
$config['global_message'] = "RIP Summy and Kuzzy, you did well :) <br> <video src='/static/rip.m4a' loop='' controls='' height='50' style='position: static; pointer-events: inherit; display: inline;'>browser is cucked</video>";
$config['global_message'] .= "<hr>MPA has been added";

if (($config['free_hosting'] ?? null) === false) {
$config['mod']['dashboard_links']['Statistics'] = '?/stats';
$config['mod']['custom_pages']['/stats'] = function() {
	global $config;
	if (!hasPermission($config['mod']['rawhtml']))
		error($config['error']['noaccess']);
	$statistics = `php /var/www/soyposter.party/tools/stats.php`;
	die("<PLAINTEXT>".$statistics);
};
$config['mod']['dashboard_links']['Header debug'] = '?/header-debug';
$config['mod']['custom_pages']['/modlogtest'] = function() {
	global $mod;
	modLog("#test\n`test`");
};
$config['mod']['custom_pages']['/header-debug'] = function() {
    global $config;

    if (!hasPermission($config['mod']['rawhtml']))
        error($config['error']['noaccess']);

	$users_ua_hash = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $config['header_salt']);
	$users_accept_language_hash = hash('sha256', $_SERVER['HTTP_ACCEPT_LANGUAGE'] . $config['header_salt']);
	echo "<p>Your UA hash: <a href=\"?/UAH/$users_ua_hash\">$users_ua_hash</a><br>Your AL hash: <a href=\"?/ALH/$users_accept_language_hash\">$users_accept_language_hash</a></p>";

    $tables = ['posts_soy', 'posts_polint', 'posts_nas'];

    foreach (['chrome_hash' => 'CH', 'ua_hash' => 'UAH', 'accept_language_hash' => 'ALH', 'accept_hash' => 'AH', 'password' => 'PH'] as $hash => $title) {
        $sql = implode(' UNION ALL ', array_map(
            fn($table) => "SELECT $hash, ip FROM $table WHERE $hash != ''",
            $tables
        ));

        $q = query("
            SELECT $hash, COUNT(DISTINCT ip) AS ips
            FROM ($sql) hashes
            GROUP BY $hash
            ORDER BY ips DESC
        ");

        echo "<h2>$title</h2><ul>";

        while ($f = $q->fetch())
            echo "<li><a href=\"?/{$title}/{$f[$hash]}\">{$f[$hash]}</a> - used by {$f['ips']} IPs</li>";

        echo "</ul>";
    }
};
