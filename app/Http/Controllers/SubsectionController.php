<?php

namespace App\Http\Controllers;

use App\Models\Subsection;
use App\Models\Section;use App\Models\SubsectionImage;use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubsectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subsection::with('section')->withCount(['items', 'images']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('section', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'order_no');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $items = $query->paginate($perPage);

        $columns = [
            'name' => 'Subsection Name',
            'section' => 'Section',
            'description' => 'Description',
            'order_no' => 'Order',
            'items_count' => 'Items',
            'images_count' => 'Images',
            'is_active' => 'Status',
            'actions' => 'Actions',
        ];

        $bulkEnabled = true;

        // Table configuration
        $config = [
            'pageHeader' => 'Subsections Management',
            'tableTitle' => 'All Subsections',
            'createRoute' => route('subsections.create'),
            'createText' => 'Create Subsection',
            'editRoute' => 'subsections.edit',
            'destroyRoute' => 'subsections.destroy',
            'bulkDeleteRoute' => route('subsections.bulk-delete'),
            'searchPlaceholder' => 'Search subsections...',
        ];

        return view('subsections.index', compact('items', 'columns', 'bulkEnabled', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Section::where('is_active', true)->orderBy('order_no')->get();
        return view('subsections.create', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,svg,webp|max:5120',
            'order_no' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Remove images from validated array as we'll handle them separately
        $images = $validated['images'] ?? [];
        unset($validated['images']);

        // Create subsection
        $subsection = Subsection::create($validated);

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('subsection_images', $fileName, 'public');

                SubsectionImage::create([
                    'subsection_id' => $subsection->id,
                    'image_path' => $imagePath,
                    'order_no' => $index + 1,
                ]);
            }
        }

        return redirect()->route('subsections.index')
            ->with('success', 'Subsection created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subsection $subsection)
    {
        $sections = Section::where('is_active', true)->orderBy('order_no')->get();
        $subsection->load('images');
        return view('subsections.edit', compact('subsection', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subsection $subsection)
    {
        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,svg,webp|max:5120',
            'order_no' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Remove images from validated array
        unset($validated['images']);

        // Update subsection
        $subsection->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            // Get the current max order number
            $maxOrderNo = $subsection->images()->max('order_no') ?? 0;

            foreach ($request->file('images') as $index => $image) {
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('subsection_images', $fileName, 'public');

                SubsectionImage::create([
                    'subsection_id' => $subsection->id,
                    'image_path' => $imagePath,
                    'order_no' => $maxOrderNo + $index + 1,
                ]);
            }
        }

        return redirect()->route('subsections.index')
            ->with('success', 'Subsection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subsection $subsection)
    {
        $subsection->delete();

        return redirect()->route('subsections.index')
            ->with('success', 'Subsection deleted successfully.');
    }

    /**
     * Remove a specific image from subsection.
     */
    public function deleteImage(SubsectionImage $image)
    {
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.'
        ]);
    }

    /**
     * Remove multiple resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return redirect()->route('subsections.index')
                ->with('error', 'No subsections selected.');
        }

        Subsection::whereIn('id', $ids)->delete();

        return redirect()->route('subsections.index')
            ->with('success', count($ids) . ' subsection(s) deleted successfully.');
    }
}
