<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Crew;
use App\Model\CrewMember;
use App\Model\User;

class CrewsController extends Controller {

    public function index(Request $request) {
        $this->view->title = 'Crews';
        $this->view->breadcrumbs = [
            'crews' => 'Crews',
        ];
        $this->view->crews = Crew::orderBy('name', 'ASC')->get();
        $calendar = new \CalendR\Calendar();
        $this->view->months = [];
        $now = new \DateTime('First day of this month');
        for ($i = 0; $i < 6; $i++) {
            $this->view->months[] = $calendar->getMonth($now);
            $now->add(new \DateInterval('P1M'));
        }
        return $this->view;
    }

    public function create() {
        if (!$this->user || !$this->user->isAdmin()) {
            return redirect('crews');
        }
        $this->view->title = 'Create a new Crew';
        $this->view->breadcrumbs = [
            'crews' => 'Crews',
            'crews/create' => 'Create',
        ];
        $this->view->action = "crews/create";
        return $this->view;
    }

    public function edit(Request $request, $id) {
        $crew = Crew::find($id);
        $this->view->title = 'Crew ' . $crew->id;
        $this->view->breadcrumbs = [
            'crews' => 'Crews',
            //'crews/' . $crew->id => $crew->name,
            'crews/' . $crew->id . '/edit' => 'Edit',
        ];
        $this->view->action = "crews/$id/edit";
        $members = [];
        foreach ($crew->members as $member) {
            $members[] = $member->user->username;
        }
        $this->view->members = join(',', $members);
        $this->view->crew = $crew;
        $this->view->unavailabilities = $crew->crewUnavailabilities()->orderBy('start_date')->get();
        return $this->view;
    }

    public function save(Request $request, $id = null) {
        if (!$this->user->isAdmin()) {
            return redirect('crews');
        }
        \DB::beginTransaction();

        $crew = Crew::findOrNew($id);
        $crew->name = $request->input('name');
        $crew->comments = $request->input('comments');
        $crew->save();

        // Save crew members.
        \DB::table('crew_members')->where('crew_id', '=', $crew->id)->delete();
        foreach (explode(',', $request->input('members')) as $member) {
            $user = User::whereUsername($member)->first();
            if ($user) {
                $crewMember = new CrewMember();
                $crewMember->user_id = $user->id;
                $crewMember->crew_id = $crew->id;
                $crewMember->save();
            }
        }

        // Save availability dates.
        \DB::table('crew_unavailabilities')->where('crew_id', '=', $crew->id)->delete();
        foreach ($request->input('unavailabilities') as $unavail) {
            if (empty($unavail['start_date']) && empty($unavail['end_date'])) {
                continue;
            }
            $date = new \App\Model\CrewUnavailability();
            $date->crew_id = $crew->id;
            $date->start_date = $unavail['start_date'];
            $date->end_date = $unavail['end_date'];
            $date->save();
        }

        \DB::commit();
        return redirect('crews');
    }

}
