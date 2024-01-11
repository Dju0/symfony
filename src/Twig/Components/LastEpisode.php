<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use App\Repository\EpisodeRepository;

#[AsTwigComponent()]
class LastEpisode
{
    private EpisodeRepository $episodeRepository;

    public function __construct(EpisodeRepository $episodeRepository)
    {
        $this->episodeRepository = $episodeRepository;
    }

    public function getLatestEpisodes(): array
    {
        return $this->episodeRepository->findBy([], ['id' => 'DESC'], 3);
    }
}
