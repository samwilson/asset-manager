<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Controllers\Controller;
use App\Model\Asset;
use App\Model\Category;
use App\Model\File;
use App\Model\Tag;
use App\Model\State;
use App\Model\Suburb;

class AssetsController extends Controller
{

    public function map(Request $request)
    {
        $this->view->title = 'Asset map';
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/map' => 'Map',
        ];
        return $this->view;
    }

    public function index(Request $request)
    {

        // Get search terms.
        $assetIdentifiers = preg_split('/(\n|\r)/', $request->input('identifiers', ''), null, PREG_SPLIT_NO_EMPTY);
        $this->view->identifiers = array_map('trim', $assetIdentifiers);
        $this->view->identifier = trim($request->input('identifier'));
        $this->view->tagged = $request->input('tagged');
        $this->view->not_tagged = $request->input('not_tagged');
        $this->view->categoryIds = collect($request->input('category_ids'));

        // No assets at all?
        if (Asset::count() === 0) {
            $this->alert('info', trans('app.no-assets-yet'), false);
        }

        // Build and execute query.
        if ($this->view->identifiers || $this->view->identifier || $this->view->tagged) {
            $assets = Asset::query();
            if (!empty($this->view->identifiers)) {
                $assets->whereIn('identifier', $this->view->identifiers);
            }
            if (!empty($this->view->identifier)) {
                $assets->where('identifier', 'LIKE', '%' . $this->view->identifier . '%');
                $this->view->quick_s = $this->view->identifier;
            }
            if ($this->view->tagged) {
                $assets->tagged($this->view->tagged);
            }
            if ($this->view->categoryIds->count() > 0) {
                $assets->whereHas('categories', function ($query) {
                    $query->whereIn('id', $this->view->categoryIds);
                })->get();
            }
            $this->view->assets = $assets->paginate(50);
            if ($this->view->assets->total() === 0) {
                $this->alert('success', 'No assets found with the given criteria.', false);
            }
        }

        // Add extra view data, and return.
        $this->view->categories = Category::where('parent_id', null)->orderBy('name', 'ASC')->get();
        $this->view->title = 'Assets';
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
        ];
        $this->view->quick_t = 'a';
        return $this->view;
    }

    public function import()
    {
        $this->view->title = 'Import';
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/import' => 'Import',
        ];
        return $this->view;
    }

    public function importPost(Request $request)
    {
        if (!$this->user || !$this->user->isAdmin()) {
            $this->alert('info', 'Only admins can import.');
            return redirect('/assets/import');
        }
        if (empty($_FILES['file']['tmp_name'])) {
            return redirect('/assets/import');
        }

        $fileInfo = $_FILES['file'];
        $csv = new \App\Csv($fileInfo['tmp_name']);
        if (!$csv->hasHeader('Identifier')) {
            throw new HttpException(500, "'Identifier' column missing from CSV.");
        }
        $imported = 0;
        $tagIds = Tag::getIds($request->input('tags'));
        while ($csv->next()) {
            $identifier = $csv->get('Identifier');
            $asset = Asset::firstOrNew(['identifier' => $identifier]);
            $stateName = $csv->get('State', true);
            if (!empty($stateName)) {
                $asset->state_id = State::firstOrCreate(['name' => $stateName])->id;
            }
            $suburbName = $csv->get('Suburb', true);
            if (!empty($suburbName)) {
                $asset->suburb_id = Suburb::firstOrCreate(['name' => $suburbName])->id;
            }
            $asset->street_address = $csv->get('Street address', true);
            $asset->location_description = $csv->get('Location description', true);
            $asset->latitude = $csv->get('Latitude', true);
            $asset->longitude = $csv->get('Longitude', true);
            $asset->comments = $csv->get('Comments', true);
            $asset->save();
            $asset->addTags($csv->get('Tags', true));
            $asset->addTags($tagIds);
            $imported++;
        }
        $this->alert('success', "$imported assets imported.");
        return redirect('/assets');
    }

    public function view($id)
    {
        $asset = Asset::find($id);
        if (!$asset) {
            $this->alert('warning', "Asset #$id does not exist.");
            return redirect('assets');
        }
        $this->view->title = 'Asset ' . $asset->identifier;
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/' . $asset->id => $asset->identifier,
        ];
        $this->view->asset = $asset;
        $this->view->quick_s = $asset->identifier;
        return $this->view;
    }

    public function edit($id)
    {
        if (!$this->user || !$this->user->isClerk()) {
            $this->alert('warning', 'Only Clerks are allowed to edit Assets.');
            return redirect("assets/$id");
        }
        $this->view->asset = Asset::find($id);
        $this->view->title = 'Editing Asset ' . $this->view->asset->identifier;
        $this->view->categories = Category::where('parent_id', null)->orderBy('name', 'ASC')->get();
        $this->view->states = State::orderBy('name')->get();
        $this->view->suburbs = Suburb::orderBy('name')->get();
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/' . $this->view->asset->id => $this->view->asset->identifier,
            'assets/' . $this->view->asset->id . '/edit' => 'Edit',
        ];
        $this->view->selectedCategories = $this->view->asset->categories;
        return $this->view;
    }

    public function create()
    {
        if (!$this->user || !$this->user->isClerk()) {
            $this->alert('info', 'Only Managers can create assets.', false);
        }
        $this->view->title = 'Create Asset';
        $this->view->breadcrumbs = [
            'assets' => 'Assets',
            'assets/create' => 'Create',
        ];
        $this->view->categories = Category::where('parent_id', null)->orderBy('name', 'ASC')->get();
        $this->view->states = State::all();
        $this->view->suburbs = Suburb::all();
        $this->view->asset = new Asset();
        return $this->view;
    }

    public function save(Request $request)
    {
        if (!$this->user || !$this->user->isClerk()) {
            $this->alert('warning', 'Only Clerks are allowed to edit assets.', false);
            return $this->view;
        }
        $asset = Asset::firstOrNew(['id' => $request->input('id')]);
        $asset->identifier = $request->input('identifier');
        $asset->state_id = $request->input('state_id');
        $asset->suburb_id = $request->input('suburb_id');
        $asset->street_address = $request->input('street_address');
        $asset->location_description = $request->input('location_description');
        $asset->latitude = $request->input('latitude');
        $asset->longitude = $request->input('longitude');
        $asset->comments = $request->input('comments');
        $asset->save();
        $asset->tags()->sync(Tag::getIds($request->input('tags')));
        $asset->categories()->sync($request->input('category_ids', array()));
        $file = File::createFromUploaded($request->file('file'));
        if ($file) {
            $asset->files()->attach($file->id);
        }
        return redirect('assets/' . $asset->id);
    }
}
