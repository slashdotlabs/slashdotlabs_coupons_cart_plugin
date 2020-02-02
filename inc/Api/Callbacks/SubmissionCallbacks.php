<?php

namespace Slash\Api\Callbacks;

use \Slash\Base\BaseController;

class SubmissionCallbacks extends BaseController
{
	
	public function submissionDashboard()
	{
		return require_once("$this->plugin_path/templates/submission.php");
	}
}