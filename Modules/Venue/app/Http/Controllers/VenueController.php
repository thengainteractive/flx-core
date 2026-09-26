<?php

namespace Modules\Venue\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Venue\Models\Location;
use Modules\Venue\Models\Venue;
use Modules\Venue\Models\VenueType;

class VenueController extends Controller
{
    /**
     * Display a listing of the venues.
     */
    public function index(Request $request)
    {
        $query = Venue::query()
            ->with(['media', 'location', 'venueType'])
            ->where('verified', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('location')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('slug', $request->input('location'));
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('venueType', function ($q) use ($request) {
                $q->where('slug', $request->input('type'));
            });
        }

        $venues = $query->paginate(20)->through(function ($venue) {
            return [
                'id' => $venue->id,
                'name' => $venue->name,
                'slug' => $venue->slug,
                'tagline' => $venue->tagline,
                'city_area' => $venue->city_area,
                'price' => $venue->price,
                'pricing_unit' => $venue->pricing_unit,
                'rating' => $venue->rating,
                'review_count' => $venue->review_count,
                'thumbnail' => $venue->getFirstMediaUrl('gallery'),
                'venue_type' => $venue->venueType?->name,
                'location' => $venue->location
                    ? collect([$venue->location->city, $venue->location->district])->filter()->join(', ')
                    : null,
            ];
        });

        return response()->json($venues);
    }

    /**
     * Display the specified venue.
     */
    public function show($slug)
    {
        $venue = Venue::with(['spaces', 'media', 'location', 'venueType'])
            ->where('slug', $slug)
            ->where('verified', true)
            ->firstOrFail();

        return response()->json([
            'id' => $venue->id,
            'name' => $venue->name,
            'slug' => $venue->slug,
            'tagline' => $venue->tagline,
            'description' => $venue->description,
            'city_area' => $venue->city_area,
            'price' => $venue->price,
            'pricing_unit' => $venue->pricing_unit,
            'rating' => $venue->rating,
            'review_count' => $venue->review_count,
            'max_floating_capacity' => $venue->max_floating_capacity,
            'max_seated_capacity' => $venue->max_seated_capacity,
            'rooms_available' => $venue->rooms_available,
            'total_area_sq_ft' => $venue->total_area_sq_ft,
            'parking_spots' => $venue->parking_spots,
            'catering_policy' => $venue->catering_policy,
            'alcohol_policy' => $venue->alcohol_policy,
            'highlights' => $venue->highlights,
            'amenities' => $venue->amenities,
            'rules' => $venue->rules,
            
            // Formatted location as "city, district"
            'formatted_location' => $venue->location
                ? collect([$venue->location->city, $venue->location->district])->filter()->join(', ')
                : null,

            // Clean related Location data
            'location' => $venue->location ? [
                'id' => $venue->location->id,
                'city' => $venue->location->city,
                'district' => $venue->location->district,
                'state' => $venue->location->state,
                'slug' => $venue->location->slug,
            ] : null,

            // Clean related Venue Type data
            'venue_type' => $venue->venueType ? [
                'id' => $venue->venueType->id,
                'name' => $venue->venueType->name,
                'slug' => $venue->venueType->slug,
            ] : null,

            // Only expose clean image URLs
            'gallery' => $venue->getMedia('gallery')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                ];
            }),

            // Format spaces if necessary
            'spaces' => $venue->spaces->map(function ($space) {
                return [
                    'id' => $space->id,
                    'name' => $space->name,
                    'description' => $space->description,
                    'capacity' => $space->capacity,
                    'area_sq_ft' => $space->area_sq_ft,
                ];
            })
        ]);
    }

    /**
     * Display dynamic dropdown options for frontend search UI.
     */
    public function filters()
    {
        $locations = Location::select('id', 'city', 'district', 'state', 'slug')->get();
        $types = VenueType::select('id', 'name', 'slug')->get();

        return response()->json([
            'locations' => $locations,
            'types' => $types,
        ]);
    }
}
