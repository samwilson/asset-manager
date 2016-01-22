<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Contact;
use App\Model\Asset;

class ContactsController extends \App\Http\Controllers\Controller
{

    public function index(Request $request)
    {
        $this->view->title = 'Contacts';
        $this->view->breadcrumbs = [
            'contacts' => 'Contacts',
        ];
        $this->view->contacts = Contact::paginate(50);
        return $this->view;
    }

    public function create()
    {
        $this->view->title = 'Create a new Contact';
        $this->view->breadcrumbs = [
            'contacts' => 'Contacts',
            'contacts/create' => 'Create',
        ];
        return $this->view;
    }

    public function createForAsset(Request $request)
    {
        // Get the Asset.
        $assetId = $request->input('asset_id');
        $asset = Asset::find($assetId);
        if (!$asset) {
            $this->alert('error', "No Asset #$assetId", false);
            return $this->view;
        }
        // Save the Contact.
        $contact = new Contact();
        $contact->name = $request->input('name');
        $contact->phone_1 = $request->input('phone_1');
        $contact->phone_2 = $request->input('phone_2');
        $contact->save();
        // Then connect the two.
        $asset->contacts()->attach($contact->id);
        return redirect("assets/$asset->id#contacts");
    }

    public function edit()
    {
        
    }
}
