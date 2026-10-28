<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the root URL loads the landing page with browse button and tutorial.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Browse All Jobs');
        $response->assertSee('How to Use Findjobph');

        $jobsResponse = $this->get('/jobs');
        $jobsResponse->assertStatus(200);
    }
}
