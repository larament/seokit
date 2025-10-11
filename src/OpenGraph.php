<?php

declare(strict_types=1);

namespace Larament\SeoKit;

use Larament\SeoKit\Enums\OpenGraphType;
use Larament\SeoKit\Support\Util;

final class OpenGraph
{
    private array $properties = [];

    private array $images = [];

    private array $videos = [];

    private array $audios = [];

    /**
     * Add an Open Graph property.
     */
    public function add(string $property, mixed $values): self
    {
        $this->properties[$property] = $values;

        return $this;
    }

    /**
     * Add an Open Graph property when the condition is true.
     */
    public function addWhen(bool $condition, string $property, mixed $values): self
    {
        if ($condition) {
            return $this->add($property, $values);
        }

        return $this;
    }

    /**
     * Remove a property by name from the Open Graph properties.
     */
    public function remove(string $property): self
    {
        unset($this->properties[$property]);

        return $this;
    }

    /**
     * Set the type of the object.
     */
    public function type(string|OpenGraphType $type): self
    {
        if (is_string($type)) {
            $type = OpenGraphType::tryFrom($type) ?? OpenGraphType::Website;
        }

        return $this->add('og:type', $type->value);
    }

    /**
     * Set the canonical URL of the object.
     */
    public function url(string $url): self
    {
        return $this->add('og:url', $url);
    }

    /**
     * Set the open graph title of the object.
     */
    public function title(string $title): self
    {
        return $this->add('og:title', $title);
    }

    /**
     * Set the open graph description of the object.
     */
    public function description(string $description): self
    {
        return $this->add('og:description', $description);
    }

    /**
     * Set the open graph site name.
     */
    public function siteName(string $name): self
    {
        return $this->add('og:site_name', $name);
    }

    /**
     * Set the locale of the object.
     */
    public function locale(string $locale): self
    {
        return $this->add('og:locale', $locale);
    }

    /**
     * Add alternate locales for the object.
     */
    public function localeAlternate(array $locales): self
    {
        return $this->addWhen(! empty($locales), 'og:locale:alternate', $locales);
    }

    /**
     * Set the determiner for the object title.
     * Valid values: 'a', 'an', 'the', '', 'auto'
     */
    public function determiner(string $determiner): self
    {
        if (in_array($determiner, ['a', 'an', 'the', '', 'auto'], true)) {
            return $this->add('og:determiner', $determiner);
        }

        return $this;
    }

    /**
     * Add an Open Graph image.
     */
    public function image(
        string $url,
        ?string $secureUrl = null,
        ?string $type = null,
        ?int $width = null,
        ?int $height = null,
        ?string $alt = null
    ): self {
        $this->images[] = [
            'og:image' => $url,
            'og:image:secure_url' => $secureUrl,
            'og:image:type' => $type,
            'og:image:width' => $width,
            'og:image:height' => $height,
            'og:image:alt' => $alt,
        ];

        return $this;
    }

    /**
     * Add an Open Graph video.
     */
    public function video(
        string $url,
        ?string $secureUrl = null,
        ?string $type = null,
        ?int $width = null,
        ?int $height = null
    ): self {
        $this->videos[] = [
            'og:video' => $url,
            'og:video:secure_url' => $secureUrl,
            'og:video:type' => $type,
            'og:video:width' => $width,
            'og:video:height' => $height,
        ];

        return $this;
    }

    /**
     * Add an Open Graph audio.
     */
    public function audio(string $url, ?string $secureUrl = null, ?string $type = null): self
    {
        $this->audios[] = [
            'og:audio' => $url,
            'og:audio:secure_url' => $secureUrl,
            'og:audio:type' => $type,
        ];

        return $this;
    }

    /**
     * Set Article properties.
     */
    public function article(
        ?string $publishedTime = null,
        ?string $modifiedTime = null,
        ?string $expirationTime = null,
        array $authors = [],
        ?string $section = null,
        array $tags = []
    ): self {
        $this->type('article')
            ->addWhen(! empty($publishedTime), 'article:published_time', $publishedTime)
            ->addWhen(! empty($modifiedTime), 'article:modified_time', $modifiedTime)
            ->addWhen(! empty($expirationTime), 'article:expiration_time', $expirationTime)
            ->addWhen(! empty($section), 'article:section', $section)
            ->addWhen(! empty($authors), 'article:author', $authors)
            ->addWhen(! empty($tags), 'article:tag', $tags);

        return $this;
    }

    /**
     * Set Profile properties.
     */
    public function profile(
        string $firstName,
        string $lastName,
        ?string $username = null,
        ?string $gender = null
    ): self {
        $this->type('profile')
            ->add('profile:first_name', $firstName)
            ->add('profile:last_name', $lastName)
            ->addWhen(! empty($username), 'profile:username', $username)
            ->addWhen($gender && in_array($gender, ['male', 'female']), 'profile:gender', $gender);

        return $this;
    }

    /**
     * Set Book properties.
     */
    public function book(
        array $author = [],
        ?string $isbn = null,
        ?string $releaseDate = null,
        array $tags = []
    ): self {
        $this->type('book')
            ->addWhen(! empty($author), 'book:author', $author)
            ->addWhen(! empty($isbn), 'book:isbn', $isbn)
            ->addWhen(! empty($releaseDate), 'book:release_date', $releaseDate)
            ->addWhen(! empty($tags), 'book:tag', $tags);

        return $this;
    }

    /**
     * Set Music Song properties.
     *
     * @param  int  $duration  The song's length in seconds
     * @param  array  $album  List of album URLs (music.album)
     * @param  int|null  $albumDisc  Which disc of the album this song is on
     * @param  int|null  $albumTrack  Which track this song is
     * @param  array  $musician  List of musician profile URLs
     */
    public function musicSong(
        int $duration,
        array $album = [],
        ?int $albumDisc = null,
        ?int $albumTrack = null,
        array $musician = []
    ): self {
        $this->type('music.song')
            ->add('music:duration', $duration)
            ->addWhen(! empty($album), 'music:album', $album)
            ->addWhen(! empty($albumDisc), 'music:album:disc', $albumDisc)
            ->addWhen(! empty($albumTrack), 'music:album:track', $albumTrack)
            ->addWhen(! empty($musician), 'music:musician', $musician);

        return $this;
    }

    /**
     * Set Music Album properties.
     *
     * @param  array  $song  List of song URLs on this album (music.song)
     * @param  array  $songDisc  List of disc numbers (same as music:album:disc but in reverse)
     * @param  array  $songTrack  List of track numbers (same as music:album:track but in reverse)
     * @param  array  $musician  List of musician profile URLs
     * @param  string|null  $releaseDate  The date the album was released (ISO 8601)
     */
    public function musicAlbum(
        array $song = [],
        array $songDisc = [],
        array $songTrack = [],
        array $musician = [],
        ?string $releaseDate = null
    ): self {
        $this->type('music.album')
            ->addWhen(! empty($song), 'music:song', $song)
            ->addWhen(! empty($songDisc), 'music:song:disc', $songDisc)
            ->addWhen(! empty($songTrack), 'music:song:track', $songTrack)
            ->addWhen(! empty($musician), 'music:musician', $musician)
            ->addWhen(! empty($releaseDate), 'music:release_date', $releaseDate);

        return $this;
    }

    /**
     * Set Music Playlist properties.
     *
     * @param  array  $song  List of song URLs in this playlist (music.song)
     * @param  array  $songDisc  List of disc numbers
     * @param  array  $songTrack  List of track numbers
     * @param  array  $creator  List of creator profile URLs
     */
    public function musicPlaylist(
        array $song = [],
        array $songDisc = [],
        array $songTrack = [],
        array $creator = []
    ): self {
        $this->type('music.playlist')
            ->addWhen(! empty($song), 'music:song', $song)
            ->addWhen(! empty($songDisc), 'music:song:disc', $songDisc)
            ->addWhen(! empty($songTrack), 'music:song:track', $songTrack)
            ->addWhen(! empty($creator), 'music:creator', $creator);

        return $this;
    }

    /**
     * Set Music Radio Station properties.
     *
     * @param  array  $creator  List of creator profile URLs
     */
    public function musicRadioStation(array $creator = []): self
    {
        $this->type('music.radio_station')
            ->addWhen(! empty($creator), 'music:creator', $creator);

        return $this;
    }

    /**
     * Set Video Movie properties.
     *
     * @param  array  $actor  List of actor profile URLs
     * @param  array  $actorRole  List of roles the actors played
     * @param  array  $director  List of director profile URLs
     * @param  array  $writer  List of writer profile URLs
     * @param  int|null  $duration  The movie's length in seconds
     * @param  string|null  $releaseDate  The date the movie was released (ISO 8601)
     * @param  array  $tag  List of tag words associated with this movie
     */
    public function videoMovie(
        array $actor = [],
        array $actorRole = [],
        array $director = [],
        array $writer = [],
        ?int $duration = null,
        ?string $releaseDate = null,
        array $tag = []
    ): self {
        $this->type('video.movie')
            ->addWhen(! empty($actor), 'video:actor', $actor)
            ->addWhen(! empty($actorRole), 'video:actor:role', $actorRole)
            ->addWhen(! empty($director), 'video:director', $director)
            ->addWhen(! empty($writer), 'video:writer', $writer)
            ->addWhen(! empty($duration), 'video:duration', $duration)
            ->addWhen(! empty($releaseDate), 'video:release_date', $releaseDate)
            ->addWhen(! empty($tag), 'video:tag', $tag);

        return $this;
    }

    /**
     * Set Video Episode properties.
     *
     * @param  string|null  $series  The TV show this episode belongs to (video.tv_show URL)
     * @param  array  $actor  List of actor profile URLs
     * @param  array  $actorRole  List of roles the actors played
     * @param  array  $director  List of director profile URLs
     * @param  array  $writer  List of writer profile URLs
     * @param  int|null  $duration  The episode's length in seconds
     * @param  string|null  $releaseDate  The date the episode was released (ISO 8601)
     * @param  array  $tag  List of tag words associated with this episode
     */
    public function videoEpisode(
        ?string $series = null,
        array $actor = [],
        array $actorRole = [],
        array $director = [],
        array $writer = [],
        ?int $duration = null,
        ?string $releaseDate = null,
        array $tag = []
    ): self {
        $this->type('video.episode')
            ->addWhen(! empty($series), 'video:series', $series)
            ->addWhen(! empty($actor), 'video:actor', $actor)
            ->addWhen(! empty($actorRole), 'video:actor:role', $actorRole)
            ->addWhen(! empty($director), 'video:director', $director)
            ->addWhen(! empty($writer), 'video:writer', $writer)
            ->addWhen(! empty($duration), 'video:duration', $duration)
            ->addWhen(! empty($releaseDate), 'video:release_date', $releaseDate)
            ->addWhen(! empty($tag), 'video:tag', $tag);

        return $this;
    }

    /**
     * Set Video TV Show properties.
     * A multi-episode TV show. The metadata is identical to video.movie.
     *
     * @param  array  $actor  List of actor profile URLs
     * @param  array  $actorRole  List of roles the actors played
     * @param  array  $director  List of director profile URLs
     * @param  array  $writer  List of writer profile URLs
     * @param  int|null  $duration  The show's length in seconds
     * @param  string|null  $releaseDate  The date the show was released (ISO 8601)
     * @param  array  $tag  List of tag words associated with this show
     */
    public function videoTvShow(
        array $actor = [],
        array $actorRole = [],
        array $director = [],
        array $writer = [],
        ?int $duration = null,
        ?string $releaseDate = null,
        array $tag = []
    ): self {
        $this->type('video.tv_show')
            ->addWhen(! empty($actor), 'video:actor', $actor)
            ->addWhen(! empty($actorRole), 'video:actor:role', $actorRole)
            ->addWhen(! empty($director), 'video:director', $director)
            ->addWhen(! empty($writer), 'video:writer', $writer)
            ->addWhen(! empty($duration), 'video:duration', $duration)
            ->addWhen(! empty($releaseDate), 'video:release_date', $releaseDate)
            ->addWhen(! empty($tag), 'video:tag', $tag);

        return $this;
    }

    /**
     * Set Video Other properties.
     * A video that doesn't belong in any other category.
     *
     * @param  array  $actor  Array of actor profile URLs
     * @param  array  $actorRole  Array of roles the actors played
     * @param  array  $director  Array of director profile URLs
     * @param  array  $writer  Array of writer profile URLs
     * @param  int|null  $duration  The video's length in seconds
     * @param  string|null  $releaseDate  The date the video was released (ISO 8601)
     * @param  array  $tag  Array of tag words associated with this video
     */
    public function videoOther(
        array $actor = [],
        array $actorRole = [],
        array $director = [],
        array $writer = [],
        ?int $duration = null,
        ?string $releaseDate = null,
        array $tag = []
    ): self {
        $this->type('video.other')
            ->addWhen(! empty($actor), 'video:actor', $actor)
            ->addWhen(! empty($actorRole), 'video:actor:role', $actorRole)
            ->addWhen(! empty($director), 'video:director', $director)
            ->addWhen(! empty($writer), 'video:writer', $writer)
            ->addWhen(! empty($duration), 'video:duration', $duration)
            ->addWhen(! empty($releaseDate), 'video:release_date', $releaseDate)
            ->addWhen(! empty($tag), 'video:tag', $tag);

        return $this;
    }

    public function toArray(): array
    {
        return $this->properties;
    }

    public function clear(): self
    {
        $this->properties = [];
        $this->images = [];
        $this->videos = [];
        $this->audios = [];

        return $this;
    }

    public function has(string $property): bool
    {
        return array_key_exists($property, $this->properties);
    }

    public function get(string $property): ?string
    {
        return $this->properties[$property] ?? null;
    }

    public function toHtml(bool $minify = false): string
    {
        $output = [];

        // Add regular properties
        foreach ($this->properties as $property => $values) {
            $values = is_array($values) ? $values : [$values];
            foreach ($values as $value) {
                $output[] = sprintf('<meta property="%s" content="%s" />', $property, Util::cleanString((string) $value));
            }
        }

        // Add structured image properties
        foreach ($this->images as $image) {
            foreach ($image as $property => $value) {
                if ($value) {
                    $output[] = sprintf('<meta property="%s" content="%s" />', $property, Util::cleanString((string) $value));
                }
            }
        }

        // Add structured video properties
        foreach ($this->videos as $video) {
            foreach ($video as $property => $value) {
                if (! empty($value)) {
                    $output[] = sprintf('<meta property="%s" content="%s" />', $property, Util::cleanString((string) $value));
                }
            }
        }

        // Add structured audio properties
        foreach ($this->audios as $audio) {
            foreach ($audio as $property => $value) {
                if (! empty($value)) {
                    $output[] = sprintf('<meta property="%s" content="%s" />', $property, Util::cleanString((string) $value));
                }
            }
        }

        return implode($minify ? '' : PHP_EOL, $output);
    }
}
