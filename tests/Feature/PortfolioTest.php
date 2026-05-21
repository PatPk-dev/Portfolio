<?php

namespace Tests\Feature;

use App\Models\PortfolioProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the home page (portfolio index) displays successfully.
     */
    public function test_portfolio_page_can_be_rendered(): void
    {
        $response = $this->get(route('portfolio.index'));

        $response->assertStatus(200);
        $response->assertSee('My Creative Portfolio');
    }

    /**
     * Test that the admin dashboard displays successfully.
     */
    public function test_admin_dashboard_can_be_rendered(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('แผงควบคุมระบบ (Admin Panel)');
    }

    /**
     * Test validation on metadata fetching API.
     */
    public function test_fetch_metadata_requires_a_valid_url(): void
    {
        $response = $this->postJson(route('admin.fetch-metadata'), [
            'url' => 'not-a-valid-url'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['url']);
    }

    /**
     * Test that a portfolio project can be saved and deleted in the database.
     */
    public function test_project_can_be_stored_and_deleted(): void
    {
        $projectData = [
            'original_url' => 'https://github.com',
            'title' => 'GitHub - Home of Code',
            'description' => 'A place to store my repositories.',
            'image_url' => 'https://github.githubassets.com/images/modules/open_graph/github-logo.png',
            'status' => 'published',
        ];

        // Store project
        $response = $this->post(route('admin.projects.store'), $projectData);
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseHas('portfolio_projects', [
            'title' => 'GitHub - Home of Code',
            'original_url' => 'https://github.com'
        ]);

        $project = PortfolioProject::first();

        // Delete project
        $deleteResponse = $this->delete(route('admin.projects.destroy', $project->id));
        $deleteResponse->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseMissing('portfolio_projects', [
            'id' => $project->id
        ]);
    }
}
