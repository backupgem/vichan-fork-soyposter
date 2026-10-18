<?php

require 'info.php';

function stats_build($action, $settings) {
    global $config;

    // Rebuild when a post/thread is created or everything is rebuilt.
    if (!(
        $action == 'all' ||
        $action == 'post' ||
        $action == 'post-thread'
    )) {
        return;
    }

    $variants = array(
        //array('hour', 3600),
        array('day', 3600 * 24),
        //array('3 days', 3600 * 24 * 3),
        array('week', 3600 * 24 * 7),
        array('month', 3600 * 24 * 30)
    );

    $stats = array();

    $q = query("SELECT uri FROM ``boards``");

    while ($board = $q->fetch()) {
	if ($board['uri'] == "conversation" || $board['uri'] == "janitor") continue;
        $row = array(
            'uri' => $board['uri'],
            'counts' => array()
        );

        foreach ($variants as $variant) {
            list($term, $time) = $variant;

            $qq = query(sprintf(
                "SELECT COUNT(*) AS count
                 FROM ``posts_%s``
                 WHERE time > %d",
                $board['uri'],
                time() - $time
            ));

            $count = $qq->fetch()['count'];

            $row['counts'][] = array(
                'term' => $term,
                'count' => $count
            );
        }

        $stats[] = $row;
    }

    file_write(
        $config['dir']['home'] . 'stats.html',
Element($config['file_page_template'], Array(
        'title' => "Statistics",
        'config' => $config,
        'body' => Element('themes/stats/stats.html', Array(
                'stats' => $stats,
                'variants' => $variants
        )),
        'boardlist' => createBoardlist(0),
))    );
}

?>
