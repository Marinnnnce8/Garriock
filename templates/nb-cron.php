<?php namespace ProcessWire;

/**
 * NB Cron
 *
 * This isn't a cronjob, but a page that could be called by a cronjob using file_get_contents(),
 * which then performs actions based on the get variables specified.
 *
 */

// A simple key to prevent unauthorised requests
$key = base64_encode($nb->siteUrl);

if($input->get->text("key") == $key) {

	$action = $input->get->text("action");

	if($action) {

		$id = $input->get->int("id");

		if($id)
			$p = $pages->get($id);

		switch($action) {

			case "publish":
			case "unpublish":
			case "hide":
			case "unhide":

				if($p->id)
					$nb->{"{$action}Page"}($p);

				break;

			case "procache":

				if($procache->cacheOn) {

					if($p->id) {

						// Clear the cache for a single page
						if($procache->pageInfo($p))
							$procache->clearPage($p);

					} else {

						// Clear the cache for all pages
						$procache->clearAll();
					}
				}

				break;

			default:
				$nb->autoStatus();
				break;
		}
	}

} else {
	throw new Wire404Exception();
}
