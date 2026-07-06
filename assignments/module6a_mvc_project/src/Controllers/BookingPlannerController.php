<?php

declare(strict_types=1);

namespace Cs85\Module6A\Controllers;

use Cs85\Module6A\Models\PhotographyProject;

final class BookingPlannerController
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function handle(array $input): string
    {
        [$project, $errors] = $this->buildProjectFromInput($input);

        return $this->render('booking-planner', [
            'project' => $project,
            'errors' => $errors,
            'input' => $input,
            'serviceTypes' => [
                'portrait' => 'Portrait',
                'fashion' => 'Fashion',
                'love_story' => 'Love story',
                'family' => 'Family',
                'content' => 'Content',
                'commercial' => 'Commercial',
            ],
            'packages' => [
                'mini' => 'Mini',
                'standard' => 'Standard',
                'premium' => 'Premium',
            ],
            'locationTypes' => [
                'outdoor' => 'Outdoor',
                'studio' => 'Studio',
                'client_place' => 'Client place',
                'out_of_city' => 'Out of city',
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{PhotographyProject, array<string, string>}
     */
    private function buildProjectFromInput(array $input): array
    {
        $errors = [];
        $clientName = trim((string) ($input['client_name'] ?? 'Maya Chen'));
        $serviceType = (string) ($input['service_type'] ?? 'portrait');
        $package = (string) ($input['package'] ?? 'standard');
        $hours = filter_var($input['hours'] ?? 2, FILTER_VALIDATE_FLOAT);
        $editedPhotos = filter_var($input['edited_photos'] ?? 20, FILTER_VALIDATE_INT);
        $locationType = (string) ($input['location_type'] ?? 'outdoor');
        $depositPaid = filter_var($input['deposit_paid'] ?? 0, FILTER_VALIDATE_FLOAT);
        $projectNote = trim((string) ($input['project_note'] ?? 'Cinematic editorial portraits for a personal brand refresh.'));

        $allowedServiceTypes = ['portrait', 'fashion', 'love_story', 'family', 'content', 'commercial'];
        $allowedPackages = ['mini', 'standard', 'premium'];
        $allowedLocationTypes = ['outdoor', 'studio', 'client_place', 'out_of_city'];

        if ($clientName === '' || mb_strlen($clientName) < 2) {
            $errors['client_name'] = 'Client name must be at least 2 characters.';
            $clientName = 'Maya Chen';
        }

        if (! in_array($serviceType, $allowedServiceTypes, true)) {
            $errors['service_type'] = 'Choose a valid service type.';
            $serviceType = 'portrait';
        }

        if (! in_array($package, $allowedPackages, true)) {
            $errors['package'] = 'Choose a valid package.';
            $package = 'standard';
        }

        if (! is_float($hours) && ! is_int($hours)) {
            $errors['hours'] = 'Session hours must be a number.';
            $hours = 2.0;
        }

        $hours = max(1.0, min((float) $hours, 8.0));

        if (! is_int($editedPhotos)) {
            $errors['edited_photos'] = 'Edited photos must be a whole number.';
            $editedPhotos = 20;
        }

        $editedPhotos = max(5, min($editedPhotos, 80));

        if (! in_array($locationType, $allowedLocationTypes, true)) {
            $errors['location_type'] = 'Choose a valid location type.';
            $locationType = 'outdoor';
        }

        if (! is_float($depositPaid) && ! is_int($depositPaid)) {
            $errors['deposit_paid'] = 'Deposit paid must be a number.';
            $depositPaid = 0.0;
        }

        if ($projectNote === '') {
            $errors['project_note'] = 'Project note is required.';
            $projectNote = 'Client wants a polished SERGIOARTG photography session.';
        }

        $project = new PhotographyProject(
            clientName: $clientName,
            serviceType: $serviceType,
            package: $package,
            hours: $hours,
            editedPhotos: $editedPhotos,
            locationType: $locationType,
            rushDelivery: isset($input['rush_delivery']),
            depositPaidCents: (int) round(max(0, (float) $depositPaid) * 100),
            projectNote: $projectNote
        );

        return [$project, $errors];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function render(string $view, array $data): string
    {
        $viewPath = dirname(__DIR__, 2).'/views/'.$view.'.php';

        if (! is_file($viewPath)) {
            return 'View not found.';
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;

        return (string) ob_get_clean();
    }
}
