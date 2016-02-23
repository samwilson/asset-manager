<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Asset;
use App\Model\Job;
use App\Model\JobList;

class SearchController extends Controller
{

    public function search(Request $request)
    {
        $searchTerm = $request->query('quick_s');
        $searchType = $request->query('quick_t');
        switch ($searchType) {
            case 'a':
                $asset = Asset::whereIdentifier($searchTerm)->first();
                if (isset($asset->id)) {
                    return redirect('assets/' . $asset->id);
                } else {
                    return redirect("assets?identifier=$searchTerm");
                }
                break;
            case 'j':
                $job = Job::whereId($searchTerm)->first();
                if (isset($job->id)) {
                    return redirect('jobs/' . $job->id);
                } else {
                    return redirect("jobs?id=$searchTerm");
                }
                break;
            case 'jl':
                $jobList = JobList::whereName($searchTerm)->first();
                if (isset($jobList->id)) {
                    return redirect('job-lists/' . $jobList->id);
                } else {
                    return redirect("job-lists?name=$searchTerm");
                }
                break;
            default:
                $this->view->quick_t = $searchType;
                $this->view->quick_s = $searchTerm;
                return $this->view;
        }
    }
}
