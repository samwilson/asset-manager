<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Crew;
use App\Model\CrewMember;

class CrewsController extends Controller {

    public function index(Request $request) {
        $this->view->title = 'Crews';
        $this->view->breadcrumbs = [
            'crews' => 'Crews',
        ];
        $this->view->crews = Crew::orderBy('name', 'ASC')->get();
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
        $this->view->crew = $crew;
        return $this->view;
    }

    public function save(Request $request, $id) {
        if (!$this->user->isAdmin()) {
            return redirect('crews');
        }
        $crew = Crew::find($id);
        $crew->name = $request->input('name');
        foreach (explode(',', $request->input('members')) as $member) {
            
        }
    }

}
