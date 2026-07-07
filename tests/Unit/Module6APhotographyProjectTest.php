<?php

namespace Tests\Unit;

use Cs85\Module6A\Models\PhotographyProject;
use PHPUnit\Framework\TestCase;

class Module6APhotographyProjectTest extends TestCase
{
    public function test_photography_project_calculates_quote_deposit_and_decision(): void
    {
        $project = new PhotographyProject(
            clientName: 'Northline Studio',
            serviceType: 'fashion',
            package: 'premium',
            hours: 5.0,
            editedPhotos: 35,
            locationType: 'studio',
            rushDelivery: true,
            depositPaidCents: 20000,
            projectNote: 'Lookbook shoot with fast delivery.'
        );

        $this->assertSame(79350, $project->quoteTotalCents());
        $this->assertSame(3805, $project->depositDueCents());
        $this->assertSame('High complexity', $project->complexityLabel());
        $this->assertSame('Send quote and collect deposit before confirming the session.', $project->workflowDecision());
        $this->assertStringContainsString('Northline Studio booked a Fashion Premium session', $project->summary());
    }
}
