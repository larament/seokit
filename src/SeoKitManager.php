<?php

declare(strict_types=1);

namespace Larament\SeoKit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;

final class SeoKitManager
{
    use Macroable;

    private Model $model;

    public function __construct(
        private MetaTags $meta,
        private OpenGraph $opengraph,
        private TwitterCards $twitter,
        private Analytics $analytics,
        private RobotsTxt $robots,
        private ContentAnalysis $contentAnalysis,
    ) {}

    public function meta(): MetaTags
    {
        return $this->meta;
    }

    public function opengraph(): OpenGraph
    {
        return $this->opengraph;
    }

    public function twitter(): TwitterCards
    {
        return $this->twitter;
    }

    public function analytics(): Analytics
    {
        return $this->analytics;
    }

    public function robots(): RobotsTxt
    {
        return $this->robots;
    }

    public function contentAnalysis(): ContentAnalysis
    {
        return $this->contentAnalysis;
    }

    /**
     * Set the title for all the meta tags.
     */
    public function title(string $title): static
    {
        $this->meta->title($title);
        $this->opengraph->title($title);
        $this->twitter->title($title);

        return $this;
    }

    /**
     * Set the description for all the meta tags.
     */
    public function description(string $description): static
    {
        $this->meta->description($description);
        $this->opengraph->description($description);
        $this->twitter->description($description);

        return $this;
    }

    /**
     * Set the image for OG and Twitter.
     */
    public function image(string $image): static
    {
        $this->opengraph->image($image);
        $this->twitter->image($image);

        return $this;
    }

    public function forModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function render(bool $minify = false): string
    {
        $output = [
            $this->meta->render($minify),
        ];

        if (config('seokit.opengraph.enabled')) {
            $output[] = $this->opengraph->render($minify);
        }

        if (config('seokit.twitter.enabled')) {
            $output[] = $this->twitter->render($minify);
        }

        return implode($minify ? '' : PHP_EOL, $output);
    }
}
