<?php

namespace App\Listeners;

use App\Events\ReactionableDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Reaction;

class ReactionableDeletedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReactionableDeleted  $event
     * @return void
     */
    public function handle(ReactionableDeleted $event)
    {
        $reactionable = $event->reactionable;
		$reactionableType = get_class($reactionable);
		$reactionableId = $reactionable->id;

		DB::table('reactions')
			->where('reactionable_type', $reactionableType)
			->where('reactionable_id', $reactionableId)
			->delete();
    }
}
