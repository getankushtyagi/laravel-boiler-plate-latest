<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use function fractal;

trait ControllerTrait
{
    /**
     * Get transformed data of a particular Model.
     *
     * @param Model|array $object Object to transform
     * @param TransformerAbstract $transformer Transformer class instance
     * @param array<string> $parseIncludes Additional relationships to include
     * @return array<string, mixed>
     */
    public function getTransformedData(
        Model|array $object,
        TransformerAbstract $transformer,
        array $parseIncludes = []
    ): array {
        $includes = $this->getIncludes($parseIncludes);

        return fractal($object, $transformer)
            ->serializeWith(new ArraySerializer())
            ->parseIncludes($includes)
            ->toArray();
    }

    /**
     * Get transformed data for collection.
     *
     * @param Collection<int, mixed> $collection Collection of models
     * @param TransformerAbstract $transformer Transformer class instance
     * @param array<string, mixed> $meta Additional metadata
     * @param array<string> $parseIncludes Additional relationships to include
     * @return array<string, mixed>
     */
    public function getTransformedCollectionData(
        Collection $collection,
        TransformerAbstract $transformer,
        array $meta = [],
        array $parseIncludes = []
    ): array {
        $includes = $this->getIncludes($parseIncludes);

        return fractal()
            ->collection($collection, $transformer)
            ->serializeWith(new ArraySerializer())
            ->parseIncludes($includes)
            ->addMeta($meta)
            ->toArray();
    }

    /**
     * Get transformed data with pagination.
     *
     * @param LengthAwarePaginator $paginatedObject Paginated collection of models
     * @param TransformerAbstract $transformer Transformer class instance
     * @param array<string, mixed> $meta Additional metadata
     * @param array<string> $parseIncludes Additional relationships to include
     * @return array<string, mixed>
     */
    public function getTransformedDataWithPagination(
        LengthAwarePaginator $paginatedObject,
        TransformerAbstract $transformer,
        array $meta = [],
        array $parseIncludes = []
    ): array {
        $includes = $this->getIncludes($parseIncludes);

        // Append query parameters to pagination links
        if ($paginatedObject !== null) {
            $paginatedObject->appends(
                request()->except(['_token', '_method'])
            );
        }

        $collection = $paginatedObject->getCollection();

        return fractal()
            ->collection($collection, $transformer)
            ->serializeWith(new ArraySerializer())
            ->parseIncludes($includes)
            ->paginateWith(new IlluminatePaginatorAdapter($paginatedObject))
            ->addMeta($meta)
            ->toArray();
    }

    /**
     * Get transformed data with metadata.
     *
     * @param Model|array|mixed $object Object to transform
     * @param TransformerAbstract $transformer Transformer class instance
     * @param array<string, mixed> $meta Additional metadata
     * @param array<string> $parseIncludes Additional relationships to include
     * @return array<string, mixed>
     */
    public function getTransformedDataWithMeta(
        Model|array $object,
        TransformerAbstract $transformer,
        array $meta = [],
        array $parseIncludes = []
    ): array {
        $includes = $this->getIncludes($parseIncludes);

        return fractal($object, $transformer)
            ->serializeWith(new ArraySerializer())
            ->parseIncludes($includes)
            ->addMeta($meta)
            ->toArray();
    }

    /**
     * Get combined includes from request and provided array.
     *
     * @param array<string> $additionalIncludes Additional includes to merge
     * @return array<string>
     */
    protected function getIncludes(array $additionalIncludes = []): array
    {
        $requestIncludes = request()->get('includes', '');
        $requestIncludesArray = is_string($requestIncludes) ? explode(',', $requestIncludes) : [];

        return array_unique(array_merge(
            $additionalIncludes,
            array_filter($requestIncludesArray)
        ));
    }
}
