<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Location;

final class LocationView
{
    private ImageView $imageView;

    private array $locales;

    public function __construct(
        ImageView $imageView,
        string $locales
    ) {
        $this->imageView = $imageView;
        $this->locales = explode('|', $locales);
    }

    public function editView(Location $location): array
    {
        $view = $this->defaultData($location);
        $view['translations'] = $this->getTranslationValues($location, false);
        $view['selected_images'] = $this->getImages($location);

        return $view;
    }

    public function view(
        Location $location,
        array $imageFilters = ['location', 'location_thumb']
    ): array {
        $view = $this->defaultData($location);

        $view['translations'] = $this->getTranslationValues($location, true);

        foreach ($imageFilters as $filter) {
            $view['media']['images'][$filter] = $this->getImages($location, $filter);
        }

        foreach ($view['translations'] as &$translation) {
            $translation['address_text'] = sprintf(
                '%s, %s %s %s',
                $translation['street'],
                $location->getZipCode(),
                $translation['city'],
                $translation['country']
            );

            $translation['title_text'] = sprintf(
                '%s, %s',
                mb_strtoupper($translation['city']),
                $translation['street']
            );
        }

        return $view;
    }

    public function getForOptions(Location $location, string $locale ): array
    {
        $trans = $location->getByLocale($locale);

        return [
            'title' => $trans->getTitle(),
            'value' => $location->getId(),
            'address' => $trans->getStreet().', '. $trans->getCity().','. $trans->getCountry(),
        ];
    }

    private function defaultData(Location $location): array
    {
        return [
            'id' => $location->getId(),
            'email' => $location->getEmail(),
            'zip_code' => $location->getZipCode(),
            'coordinates' => [
                'lng' => $location->getLng(),
                'lat' => $location->getLat(),
            ],
            'telephone' => $location->getTelephone(),
            'work_time' => [
                'work_days' => $location->getWorkingTime(),
                'saturday' => $location->getSaturday() ?? '',
                'sunday' => $location->getSunday() ?? '',
            ],
        ];
    }

    private function getTranslationValues(Location $location, bool $fixDescription): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $translation = $location->getByLocale($locale);

            $translations[$locale] = [
                'id' => $translation->getId(),
                'title' => $translation->getTitle(),
                'slug' => $translation->getSlug(),
                'city' => $translation->getCity(),
                'country' => $translation->getCountry(),
                'street' => $translation->getStreet(),
                'address' => $translation->getStreet().', '. $translation->getCity().','. $translation->getCountry(),
                'short_description' => $fixDescription ?
                    str_replace(["\r\n", PHP_EOL], '<br>', $translation->getShortDescription()) :
                    $translation->getShortDescription(),
            ];
        }

        return $translations;
    }

    private function getImages(Location $location, $filter = null): array
    {
        $images = [];

        foreach ($location->getLocationHasImages() as $hasImage) {
            $images[] = $this->imageView->view($hasImage->getImage(), 'location', $filter);
        }

        return $images;
    }
}
