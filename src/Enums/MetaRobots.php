<?php

declare(strict_types=1);

namespace Larament\SeoKit\Enums;

enum MetaRobots: string
{
    case Index = 'index';
    case Noindex = 'noindex';
    case Follow = 'follow';
    case NoFollow = 'nofollow';
    case NoArchive = 'noarchive';
    case NoImageIndex = 'noimageindex';
    case NoSnippet = 'nosnippet';
}
