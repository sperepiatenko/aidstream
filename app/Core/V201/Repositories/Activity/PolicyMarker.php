<?php namespace App\Core\V201\Repositories\Activity;

use App\Models\Activity\Activity;

/**
 * Class PolicyMarker
 * @package App\Core\V201\Repositories\Activity
 */
class PolicyMarker
{
    /**
     * @param Activity $activity
     */
    function __construct(Activity $activity)
    {
        $this->activity = $activity;
    }

    /**
     * @param array    $activityDetails
     * @param Activity $activity
     * @return bool
     */
    public function update(array $activityDetails, Activity $activity)
    {
        $activity->policy_marker = $activityDetails['policy_marker'];

        return $activity->save();
    }

    /**
     * @param $activityId
     * @return mixed
     */
    public function getPolicyMarkerData($activityId)
    {
        return $this->activity->find($activityId)->policy_marker;
    }
}
