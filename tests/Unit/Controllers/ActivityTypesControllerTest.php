<?php

namespace Tests\Unit\Controllers;

use Tests\FeatureTestCase;
use App\Models\Contact\ActivityType;
use App\Models\Contact\ActivityTypeCategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActivityTypesControllerTest extends FeatureTestCase
{
    use DatabaseTransactions;

    protected $jsonStructureActivityType = [
        'id',
        'name',
        'location_type',
    ];

    public function test_it_gets_activity_types()
    {
        $user = $this->signin();

        $activityTypeCategory = factory(ActivityTypeCategory::class)->create([
            'account_id' => $user->account->id,
        ]);

        $activityTypes = factory(ActivityType::class, 10)->create([
            'account_id' => $user->account_id,
            'activity_type_category_id' => $activityTypeCategory->id,
        ]);

        $response = $this->json('GET', '/settings/personalization/activitytypes/'.$activityTypeCategory->id);

        $response->assertJsonStructure([
            '*' => $this->jsonStructureActivityType,
        ]);
    }

    public function test_it_stores_a_activity_type()
    {
        $user = $this->signin();

        $activityTypeCategory = factory(ActivityTypeCategory::class)->create([
            'account_id' => $user->account->id,
        ]);

        $response = $this->json('POST', '/settings/personalization/activitytypes', [
                            'name' => 'Movies',
                            'activity_type_category_id' => $activityTypeCategory->id,
                        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('activity_types', [
            'name' => 'Movies',
            'activity_type_category_id' => $activityTypeCategory->id,
        ]);
    }

    public function test_it_updates_a_activity_type()
    {
        $user = $this->signin();

        $activityTypeCategory = factory(ActivityTypeCategory::class)->create([
            'account_id' => $user->account->id,
        ]);

        $activityType = factory(ActivityType::class)->create([
            'account_id' => $user->account->id,
            'activity_type_category_id' => $activityTypeCategory->id,
        ]);

        $response = $this->json('PUT', '/settings/personalization/activitytypes', [
                            'id' => $activityType->id,
                            'name' => 'Movies',
                        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('activity_types', [
            'name' => 'Movies',
        ]);
    }
}
