<?php

namespace App\Http\Resources\Concerns;
trait HasMedia
{
     protected function media(string $collection, string $conversion = null)
    {
        return $this->getMedia($collection)->map(fn ($media) => [
            'id'  => $media->id,
            'url' => $conversion
                ? $media->getUrl($conversion)
                : $media->getUrl(),
        ]);
    }

    protected function firstMedia(
        string $collection,
        string $conversion = null
    ): ?string {
        return $this->getFirstMediaUrl($collection, $conversion);
    }
}
