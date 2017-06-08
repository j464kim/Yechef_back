<?php

namespace App\Traits;

use App\Models\Reaction;

trait Reactionable {

	/**
	 * @param null $userId
	 * @return mixed
	 */
	public function getReactions($userId = null)
	{
		$reactions = Reaction::where('reactionable_type', get_class($this))
			->where('reactionable_id', $this->id)
			->get();

		if ($userId) {
			$reactions = $reactions->where('user_id', $userId);
		}

		return $reactions;
	}
}