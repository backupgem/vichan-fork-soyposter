<?php
	require 'info.php';

	function hotthreads_build($action, $settings, $board) {
		HotThreads::build($action, $settings);
	}

	class HotThreads {
		public static function build($action, $settings) {
			global $config, $board;

			if ($action == 'all' || $action == 'boards' || $action == 'post' ||
				$action == 'post-thread' || $action == 'post-delete') {
				file_write(
					$config['dir']['home'] . $settings['file'],
					HotThreads::homepage($settings)
				);
			}
		}

		public static function homepage($settings) {
			global $config, $board;

			$boards = listBoards();
			$excluded = array_filter(explode(' ', trim($settings['exclude'])));

			$cutoff = time() - ((int)$settings['hours'] * 60 * 60);
			$threads = Array();

			foreach ($boards as $_board) {
				if (in_array($_board['uri'], $excluded))
					continue;

				$query = sprintf(
					"SELECT `thread`, COUNT(*) AS `replies`, MAX(`time`) AS `last_reply`
					 FROM ``posts_%s``
					 WHERE `thread` != 0 AND `time` >= %d
					 GROUP BY `thread`",
					$_board['uri'],
					$cutoff
				);

				$result = query($query) or error(db_error());

				while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
					$threads[] = Array(
						'board_uri' => $_board['uri'],
						'thread' => (int)$row['thread'],
						'replies' => (int)$row['replies'],
						'last_reply' => (int)$row['last_reply']
					);
				}
			}

			usort($threads, function($a, $b) {
				if ($a['replies'] == $b['replies'])
					return $b['last_reply'] - $a['last_reply'];

				return $b['replies'] - $a['replies'];
			});

			$threads = array_slice($threads, 0, (int)$settings['limit']);

			$hot_threads = Array();

			foreach ($threads as $thread) {
				openBoard($thread['board_uri']);

				$query = sprintf(
					"SELECT * FROM ``posts_%s`` WHERE `id` = %d LIMIT 1",
					$thread['board_uri'],
					$thread['thread']
				);

				$result = query($query) or error(db_error());
				$op = $result->fetch(PDO::FETCH_ASSOC);

				if (!$op)
					continue;

				$thread['board_name'] = $board['name'];
				$thread['subject'] = isset($op['subject']) ? $op['subject'] : '';

				if ($thread['subject'] == '') {
					if (isset($op['body']) && $op['body'] != '') {
						$thread['subject'] = pm_snippet($op['body'], 100);
					}
					else {
						$thread['subject'] = '<em>' . _('no subject') . '</em>';
					}
				}

				$thread['link'] = $config['root'] . $board['dir'] .
					$config['dir']['res'] . link_for($op);

				$hot_threads[] = $thread;
			}

			return Element($config['file_page_template'], Array(
				'title' => $settings['title'],
				'config' => $config,
				'subtitle' => $settings['subtitle'],
				'body' => Element('themes/hotthreads/index.html', Array(
					'settings' => $settings,
					'hot_threads' => $hot_threads
				)),
				'boardlist' => createBoardlist(isset($mod) ? $mod : false),
			));
		}
	};
?>
