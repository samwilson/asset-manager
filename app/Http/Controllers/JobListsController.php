<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Model\Asset;
use App\Model\Crew;
use App\Model\JobList;
use App\Model\Job;
use App\Model\JobType;
use App\Model\Tag;

class JobListsController extends Controller {

    public function index(Request $request) {
        $this->view->title = 'Job Lists';
        $this->view->breadcrumbs = [
            'job-lists' => 'Job Lists',
        ];
        $this->view->job_types = JobType::orderBy('name', 'ASC')->get();
        $jobLists = JobList::query();

        // Type.
        $this->view->selected_type = $request->input('type');
        if ($this->view->selected_type !== null) {
            $jobLists->where('type_id', $this->view->selected_type);
        }

        $this->view->job_lists = $jobLists->paginate(50);
        return $this->view;
    }

    public function create(Request $request) {
        $this->view->assets = $this->getAssets($request);
        $this->view->title = "Create a Job List";
        $this->view->breadcrumbs = [
            'job-lists' => 'Job Lists',
            'assets' => 'Create',
        ];
        $this->view->job_types = JobType::orderBy('name', 'ASC')->get();
        return $this->view;
    }

    public function edit($id) {
        $this->view->job_list = JobList::find($id);
        $this->view->title = 'Edit Job List #' . $this->view->job_list->id;
        $this->view->job_types = JobType::orderBy('name', 'ASC')->get();
        $this->view->crews = Crew::orderBy('name', 'ASC')->get();
        $this->view->breadcrumbs = [
            'job-lists' => 'Job Lists',
            'job-lists/' . $this->view->job_list->id => $this->view->job_list->name,
            'job-lists/' . $this->view->job_list->id . '/edit' => 'Edit',
        ];
        return $this->view;
    }

    public function saveNew(Request $request) {
        DB::beginTransaction();
        $jobList = new JobList();
        $jobList->name = $request->input('name');
        $jobList->type_id = $request->input('type_id');
        $jobList->due_date = $request->input('due_date');
        $jobList->crew_id = $request->input('crew_id');
        $jobList->comments = $request->input('comments');
        $jobList->save();
        $jobList->tags()->sync(Tag::getIds($request->input('tags')));
        // Then save all assets.
        $assets = $this->getAssets($request);
        foreach ($assets as $asset) {
            $job = new Job();
            $job->job_list_id = $jobList->id;
            $job->asset_id = $asset->id;
            $job->date_added = date('Y-m-d');
            $job->save();
        }
        DB::commit();
        return redirect('job-lists/' . $jobList->id);
    }

    public function saveExisting(Request $request, $id) {
        DB::beginTransaction();
        $jobList = JobList::find($id);
        $jobList->name = $request->input('name');
        $jobList->type_id = $request->input('type_id');
        $jobList->due_date = $request->input('due_date');
        $jobList->crew_id = $request->input('crew_id');
        $jobList->comments = $request->input('comments');
        $jobList->save();
        $jobList->tags()->sync(Tag::getIds($request->input('tags')));
        DB::commit();
        return redirect('job-lists/' . $jobList->id);
    }

    public function view(Request $request, $id) {
        $jobList = JobList::with('type')->find($id);
        $this->view->job_list = $jobList;
        //$this->view->jobs = $jobList->jobs()->orderBy('start_date', 'DESC')->get();
        $this->view->title = "Job List '" . $jobList->name . "'";
        $this->view->breadcrumbs = [
            'job-lists' => 'Job Lists',
            'job-lists/' . $jobList->id => $jobList->name,
        ];
        return $this->view;
    }

    protected function getAssets($request) {
        // Get search terms.
        $assetIdentifiers = preg_split('/(\n|\r)/', $request->input('identifiers', ''), NULL, PREG_SPLIT_NO_EMPTY);
        $identifiers = array_map('trim', $assetIdentifiers);
        $identifier = trim($request->input('identifier'));
        $categoryIds = collect($request->input('category_ids'));

        // Build and execute query.
        $assets = Asset::query();
        if (!empty($identifiers)) {
            $assets->whereIn('identifier', $identifiers);
        }
        if (!empty($identifier)) {
            $assets->where('identifier', 'LIKE', "%$identifier%");
        }
        if ($categoryIds->count() > 0) {
            $assets->whereHas('categories', function ($query) {
                $query->whereIn('id', $categoryIds);
            })->get();
        }
        return $assets->get();
    }

    public function scheduled(Request $reqest) {
        $this->view->title = "Job Schedule";
        $this->view->breadcrumbs = [
            'job-lists' => 'Job Lists',
            'job-lists/schedule' => 'Schedule',
        ];
        $startDate = new \DateTime($reqest->input('start_date'));
        $endDate = new \DateTime($reqest->input('end_date'));
        if ($startDate->diff($endDate, true)->d < 7) {
            $endDate->add(new \DateInterval('P14D'));
        }
        $datePeriod = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate);
        $this->view->dates = $datePeriod;
        $this->view->crews = Crew::query()->orderBy('name')->get();
        return $this->view;
    }

}
