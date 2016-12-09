<?php
/**
 * @package  Bitbucket
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  1.1.0
 */

namespace Plugin\Bitbucket;

class Controller extends \Controller {

	/**
	 * Handle HTTP POST request
	 * @param Base  $f3
	 * @param array $params
	 */
	public function post($f3, $params) {
		if($f3->get("GET.token") == $f3->get("site.plugins.bitbucket.token")) {
			$post = file_get_contents('php://input');

			// Parse URL-encoded payload if given
			if(substr($post, 0, 8) == 'payload=') {
				parse_str($post, $data);
				$post = $data['payload'];
			}
			$json = json_decode($post);

			if($f3->get("DEBUG")) {
				$log = new \Log("bitbucket.log");
			}

			if(!is_object($json)) {
				$log->write("Bad JSON data: " . $post);
				$f3->error(400);
				return;
			}

			$usermap = array();
			if(is_file(__DIR__ . "/usermap.ini")) {
				$usermap = parse_ini_file(__DIR__ . "/usermap.ini", false, INI_SCANNER_RAW);
			}

			foreach($json->push->changes as $change){
				foreach($change->commits as $commit) {

					if($f3->get("DEBUG")) {
						$log->write("Commit found: {$commit->raw_node} {$commit->message}");
					}

					// Match commits with issue IDs
					if(preg_match("/#[0-9]+/", $commit->message, $matches)) {
						$id = intval(ltrim($matches[0], "#"));
						$issue = new \Model\Issue;
						$issue->load($id);
						if($issue->id) {

							// Find matching user
							if(preg_match("/<[^ ]+@[^ ]+>/", $commit->author->raw, $matches)) {
								$email = trim($matches[0], "<>");
								if(array_key_exists($email, $usermap)) {
									$email = $usermap[$email];
								}
								$user = new \Model\User;
								$user->load(array("email = ?", $email));
								if(!$user->id) {
									$log->write("No user found for email: {$email}, raw: {$commit->author->raw}");
									if($f3->exists("site.plugins.bitbucket.default_user_id")) {
										$user->load((int)$f3->get("site.plugins.bitbucket.default_user_id"));
									}
									if(!$user->id) {
										$f3->error(417);
										return;
									}
								}
								$f3->set("user", $user->cast());
								$f3->set("user_obj", $user);
							} elseif($f3->get("DEBUG")) {
								$log->write('No author match for: ' . $commit->author->raw);
								return;
							}

							$updated = false;

							// Check for status changes
							// Completed: #resolve #resolved #fix #fixed #close #closed
							if(!$issue->closed_date && preg_match("/#(resolve|fix|close)/i", $commit->message)) {
								$status = new \Model\Issue\Status;
								$status->load(array("closed = ?", 1));
								$issue->status = $status->id;
								$issue->closed_date = $this->now();
								$updated = true;
							}
							// New: #reopen #re-open #new #broken
							elseif($issue->closed_date && preg_match("/#(re-?open|new|broken)/i", $commit->message)) {
								$status = new \Model\Issue\Status;
								$status->load(array("closed = ?", 0));
								$issue->status = $status->id;
								$issue->closed_date = null;
								$updated = true;
							}

							// Check for hours spent updates
							if(preg_match("/@[0-9\.]+h/i", $commit->message, $matches)) {
								$hours = floatval(trim($matches[0], "@Hh"));
								if($hours) {
									$issue->hours_spent = $issue->hours_spent + $hours;
									$updated = true;
								}
							}

							// Generate text for comment, using Markdown if enabled, then Textile, then plain text
							$url = $commit->links->html->href;
							$message = preg_replace("/ ?#{$issue->id} ?/", "", $commit->message);
							if($f3->get("parse.markdown")) {
								$text = "This issue was mentioned in a [commit]($url):\n\n" . $message;
							} elseif($f3->get("parse.textile")) {
								$text = "This issue was mentioned in a \"commit\":$url:\n" . $message;
							} else {
								$text = "This issue was mentioned in a commit - $url:\n" . $message;
							}

							// Generate comment
							$comment = new \Model\Issue\Comment;
							$comment->issue_id = $issue->id;
							$comment->text = $text;
							$comment->created_date = $this->now();
							$comment->user_id = $user->id;
							$comment->save();

							// Save issue if any fields changed
							if($updated) {
								$f3->set("update_comment", $comment);
								$issue->save();
							}
						}

					}

				}
			}

		} else {
			$f3->error(403);
		}
	}

}
