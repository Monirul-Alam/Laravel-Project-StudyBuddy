<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
     public function index() {
        return view('listings.index',[
            'heading' => 'latest listing',
         'listings'=> Listing::latest()->filter(request(['tag', 'search']))->simplepaginate(6)
     ]); 

     }


     public function show(Listing $listing) {
        return view('listings.show',[
            'listing' => $listing
        ]);
     }
     // Show create form
     public function create() { 
       return view('listings.create');
     }

     // Store listing data
     public function store(Request $request) { 
        // dd($request->file('logo'));
        $formFields =$request->validate(
            [
                'title' => 'required',
                'company' => ['required', Rule::unique('listings','company')],
                'location' => 'required',
                'website' => 'required',
                'email' => ['required', 'email'],
                'tags' => 'required',
                'description' => 'required'

            ]); 

            if($request->hasfile('logo')) {
                $formFields['logo'] = $request->file('logo')->store('logos','public');
            }

            $formFields['user_id'] = auth()->id();
           

            Listing::create($formFields);
            return redirect('/')->with('message', 'Listing Created Sucessfully');
      }
      // Show Edit form
      public function edit(Listing $listing) {
        return view('listings.edit',['listing' => $listing]);
      }
      // Updating  listing data
     public function update(Request $request , Listing $listing) { 
        // dd($request->file('logo'));
        // Admin owner
        if($listing->user_id != auth()->id()){
          abort(403,'Unauthorized Action');
        }
        $formFields =$request->validate(
            [
                'title' => 'required',
                'company' => ['required'],
                'location' => 'required',
                'website' => 'required',
                'email' => ['required', 'email'],
                'tags' => 'required',
                'description' => 'required'

            ]); 

            if($request->hasfile('logo')) {
                $formFields['logo'] = $request->file('logo')->store('logos','public');
            }

            $listing->update($formFields);
            return back()->with('message', 'Listing updated Sucessfully');
      }

      // Delete Listing
      public function destroy(Listing $listing) {
        if($listing->user_id != auth()->id()){
          abort(403,'Unauthorized Action');
        }
        $listing->delete();
        return redirect('/')->with('message','Listing Delated Successfully');

      }
      public function manage() {
        return view('listings.manage',['listings'=>auth()->user()->listings()->get()]);
      }

}
