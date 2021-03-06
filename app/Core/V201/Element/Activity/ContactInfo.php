<?php namespace App\Core\V201\Element\Activity;

use App\Core\Elements\BaseElement;
use App\Models\Activity\Activity;

/**
 * Class ContactInfo
 * @package app\Core\V201\Element\Activity
 */
class ContactInfo extends BaseElement
{
    /**
     * @return contact info form
     */
    public function getForm()
    {
        return "App\Core\V201\Forms\Activity\MultipleContactInfo";
    }

    /**
     * @return contact Info repository
     */
    public function getRepository()
    {
        return App('App\Core\V201\Repositories\Activity\ContactInfo');
    }

    /**
     * @param $activity
     * @return array
     */
    public function getXmlData(Activity $activity)
    {
        $activityData = [];
        $contacts     = (array) $activity->contact_info;
        foreach ($contacts as $contact) {
            $activityData[] = [
                '@attributes'     => [
                    'type' => $contact['type']
                ],
                'organisation'    => [
                    'narrative' => $this->buildNarrative($contact['organization'][0]['narrative'])
                ],
                'department'      => [
                    'narrative' => $this->buildNarrative($contact['department'][0]['narrative'])
                ],
                'person-name'     => [
                    'narrative' => $this->buildNarrative($contact['person_name'][0]['narrative'])
                ],
                'job-title'       => [
                    'narrative' => $this->buildNarrative($contact['job_title'][0]['narrative'])
                ],
                'telephone'       => [
                    '@value' => (isset($contact['telephone'][0]['telephone'])) ? $contact['telephone'][0]['telephone'] : ''
                ],
                'email'           => [
                    '@value' => (isset($contact['email'][0]['email'])) ? $contact['email'][0]['email'] : ''
                ],
                'website'         => [
                    '@value' => (isset($contact['website'][0]['website'])) ? $contact['website'][0]['website'] : ''
                ],
                'mailing-address' => [
                    'narrative' => $this->buildNarrative(isset($contact['mailing_address'][0]['narrative']) ? $contact['mailing_address'][0]['narrative'] : [])
                ]
            ];
        }

        return $activityData;
    }
}
