<?php
/**
 * @package  Bitbucket
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  1.0.0
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
			$json = json_decode($post);

			foreach($json->commits as $commit) {

				// Match commits with issue IDs
				if(preg_match("/#[0-9]+/", $commit->message, $matches)) {
					$id = intval(ltrim($matches[0], "#"));
					$issue = new \Model\Issue;
					$issue->load($id);
					if($issue->id) {

						// Check for status changes
						// Completed: #resolve #resolved #fix #fixed #close #closed
						if(!$issue->closed_date && preg_match("/#(resolve|fix|close)/i", $commit->message)) {
							$status = new \Model\Issue\Status;
							$status->load(array("closed = ?", 1));
							$issue->status = $status->id;
							$issue->closed_date = $this->now();
						}
						// New: #reopen #re-open #new #broken
						elseif($issue->closed_date && preg_match("/#(re-?open|new|broken)/i")) {
							$status = new \Model\Issue\Status;
							$status->load(array("closed = ?", 0));
							$issue->status = $status->id;
							$issue->closed_date = null;
						}

						// Check for hours spent updates
						if(preg_match("/@[0-9\.]h/i", $commit->message, $matches)) {
							$hours = floatval(ltrim($matches[0], "@"));
							if($hours) {
								$issue->hours_spent = $issue->hours_spent + $hours;
							}
						}

						// Generate comment
						$comment = new \Model\Comment;
						$comment->issue_id = $issue->id;
						if(preg_match("/<[^ ]+@[^ ]+>/", $commit->raw_author, $matches)) {
							$user = new \Model\User;
							$user->load(array("email = ?", trim($matches[0], "<>")));
							if($user->id) {
								$comment->user_id = $user->id;
							}
						}
						$comment->text = "This issue was mentioned in a \"commit\":{$json->canon_url}{$json->repository->absolute_url}commits/{$commit->raw_node}:\n" . $commit->message;
						$comment->created_date = $this->now();
						$comment->save();
					}

				}

			}

		} else {
			$f3->error(403);
		}
	}

}
