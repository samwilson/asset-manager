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
            $now->add(new \DateInterval('P' . $i . 'M'));
            $this->view->months[] = $calendar->getMonth($now);
        }
        return $this->view;
    }

    public function create() {
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
        return $this->view;
    }

    public function save(Request $request, $id = null) {
        if (!$this->user->isAdmin()) {
            return redirect('crews');
        }
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
        foreach ($request->input('dates') as $d) {
            $date = new \App\Model\CrewDate();
            $date->crew_id = $crew->id;
            $date->start_date = $d['start_date'];
            $date->end_date = $d['end_date'];
            $date->save();
        }

        return redirect('crews');
    }

}
